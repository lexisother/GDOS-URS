<?php

use Illuminate\Routing\Router;

/** @var Router $router The router from index.php. */

$router->get('/', function () {
    view('home');
})->name('home');

$router->group(['prefix' => 'overview'], function (Router $router) {
    $router->get('/', function () {
        view('overview');
    });

    $router->get('/total', function () {
        view('total');
    });
});
