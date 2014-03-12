<?php


echo "holaaaaaaa";

require 'lib/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->get('/hello/:name', function ($name) {
    echo "Hello222222, $name";
});
$app->run();
?>