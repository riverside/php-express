<?php
include __DIR__ . '/../../src/autoload.php';

$router = new \PhpExpress\Router();

$router->get('/', function ($req, $res) {
    $res->send('<h1>Route 1</h1><a href="route-2.html">Next &raquo;</a>');
});

$router->get('route-2.html', function ($req, $res) {
    $res->send('<h1>Route 2</h1><a href="./">&laquo; Prev</a></a>');
});

$router->run();