<?php
include __DIR__ . '/../../src/autoload.php';

$router = new \PhpExpress\Router();

$router->get('/', function ($req, $res) {
    $html = <<<EEE
<form action="./" method="post">
    <p>
        <label for="name">Name</label>
        <input type="text" name="name" id="name" placeholder="Name" value="John Doe">
    </p>
    <p>
        <label for="age">Age</label>
        <input type="number" name="age" id="age" placeholder="Age" min="0" step="1" value="32">
    </p>
    <button type="submit">Submit</button>
</form>
EEE;
    $res->send($html);
});

$router->post('/', function ($req, $res) {
    $res->send('<pre>'.print_r($_POST, true));
});

$router->run();