<?php
include __DIR__ . '/../../src/autoload.php';

$app = new \Riverside\Express\Application();

$testVerbs = function(\Riverside\Express\Request $req, \Riverside\Express\Response $res) {
    if ($req->method == 'HEAD')
    {
        $res->end();
    }
    $res->json([
        'method' => $req->method,
        'content-type' => $req->get("content-type"),
        'params' => $req->params,
        'body' => $req->body,
    ]);
};

$app->get('/', function($req, $res) {
    $html = <<<EEE
<form action="" method="post">
    <input type="hidden" name="foo" value="bar">
    <button type="submit" name="method" value="get">GET</button>
    <button type="submit" name="method" value="head">HEAD</button>
    <button type="submit" name="method" value="post">POST</button>
    <button type="submit" name="method" value="put">PUT</button>
    <button type="submit" name="method" value="patch">PATCH</button>
    <button type="submit" name="method" value="delete">DELETE</button>
</form>
<code id="result"></code>
<script>
let method;
const result = document.querySelector("#result");
function handleFormSubmit(event) {
    event.preventDefault();
    
    const options = {
        method: method,
        headers: {
            "accept": "application/json"
        }
    };
    if (["post", "put", "patch"].includes(method)) {
        options.headers["content-type"] = "application/x-www-form-urlencoded";
        options.body = new URLSearchParams(new FormData(this)).toString();
    } else if (method === "head") {
        options.headers.accept = "*/*";
    }
    fetch("contact/1", options)
        .then(function(response) {
            return response.ok ? (method === "head" ? response.headers : response.json()) : Promise.reject(response);
        })
        .then(function(data) {
            if (method === "head") {
                const tmp = {};
                for (let [key, value] of data.entries()) {
                    tmp[key] = value;
                }
                data = tmp;
            }
            result.innerText = JSON.stringify(data, null, 4);
        })
        .catch(function(reason) {
            console.warn(reason);
        });
}

function handleButtonClick(event) {
    method = this.value;
}

[].forEach.call(document.querySelectorAll("button[type='submit']"), function(btn) {
    btn.addEventListener("click", handleButtonClick);
});
document.querySelector("form").addEventListener("submit", handleFormSubmit);
</script>
EEE;
    $res->send($html);
});
$app->get('contact/:id', $testVerbs);
$app->head('contact/:id', $testVerbs);
$app->post('contact/:id', $testVerbs);
$app->put('contact/:id', $testVerbs);
$app->patch('contact/:id', $testVerbs);
$app->delete('contact/:id', $testVerbs);

$app->run();
