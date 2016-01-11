<?php

require_once __DIR__ . '/vendor/autoload.php';

// database
if (isset($env) && $env === 'test') {
    $db = 'sqlite:todo.db';
} else {
    $db = 'sqlite:todo_test.db';
}
ORM::configure($db);

// app
$app = new Silex\Application();
$app['debug'] = true;

// Content-Type: json or shunter-json
if ((isset($env) && $env === 'test') || (isset($_GET['json']) && $_GET['json'] === 'true')) {
    $app['content-type'] = 'application/json';
} else {
    $app['content-type'] = 'application/x-shunter-json';
}

// routes
$app->get('/',           'Todo\Controller\TodoController::indexAction');
$app->get('/item/{id}',  'Todo\Controller\TodoController::singleAction');
$app->post('/add',       'Todo\Controller\TodoController::addAction');
$app->post('/edit/{id}', 'Todo\Controller\TodoController::editAction');

if (isset($env) && $env === 'test') {
    return $app;
} else {
    $app->run();
}
