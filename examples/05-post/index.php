<?php
include __DIR__ . '/../../src/autoload.php';

$router = new \PhpExpress\Router();

$router->get('/', function ($req, $res) {
    $html = <<<EEE
<form action="./" method="post">
    <input type="text" name="name" placeholder="Name" value="John Doe">
    <input type="number" name="age" placeholder="Age" value="32">
    <button type="submit">Submit</button>
</form>
EEE;
    $res->send($html);
});

$router->post('/', function ($req, $res) {
    $res->send('<pre>'.var_export($_POST, true));
});

$router->run();