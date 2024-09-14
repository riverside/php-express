<?php
include __DIR__ . '/../../src/autoload.php';

spl_autoload_register(function ($class) {
    $parts = explode('\\', $class);
    $file = 'controllers/' . end($parts) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

$app = new \Riverside\Express\Application();
$app->set("views", __DIR__ . "/views");

$app->get('/', '\controllers\basic@home');

$app->get('about.html', '\controllers\basic@about');

$app->run();