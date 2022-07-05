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

$router->group(['namespace' => 'App\Controllers', 'prefix' => 'api'], function (Router $router) {
    $router->post("/submit", 'ApiController@submit');
});
