<?php
include __DIR__ . '/../../src/autoload.php';

$app = new \PhpExpress\Application();

$app->param('username', '[a-zA-Z\d]{1,32}');
$app->param('wildcard', '.*');

$app->get('/', function ($req, $res) {
    $link = 'user/riverside/pictures/123456/Test-98_7/ea416ed0759d46a8de58f63a59077499';
    $res->send('<p>Pattern: user/:username/pictures/:id/:wildcard/:hash</p><p>Link: <a href="'.$link.'">'.$link.'</a></p>');
});

$app->get('user/:username/pictures/:id/:wildcard/:hash', function ($req, $res) {
    $res->send('<a href="./../../../../../">&laquo; Back</a><pre>'.print_r($req->params, true));
});

$app->run();