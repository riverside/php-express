<?php
include __DIR__ . '/../../src/autoload.php';

$app = new \PhpExpress\Application();

$app->get('/', function ($req, $res) {
    $res->send('<h1>Page 1</h1><a href="page-2.html">Next &raquo;</a>');
});

$app->get('page-2.html', function ($req, $res) {
    $res->send('<h1>Page 2</h1><a href="./">&laquo; Prev</a>');
});

$app->run();