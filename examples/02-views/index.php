<?php
include __DIR__ . '/../../src/autoload.php';

$app = new \PhpExpress\Application();
$app->set("views", __DIR__ . "/views");

$app->get('/', function ($req, $res) {
    $res->render('home');
});

$app->get('about.html', function ($req, $res) {
    $res->render('about');
});

$app->run();