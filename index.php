<?php
// Load Composer
require __DIR__ . "/vendor/autoload.php";

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

// Initialize the session
session_start();

// Setup our error handler screen
$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();

// Create our service container
$container = app();

// Create a request from server variables, and bind it to the container (this is optional)
$request = Request::capture();
$container->instance('Illuminate\Http\Request', $request);

// Any class that implements Illuminate\Contracts\Event\Dispatcher is allowed
$events = new Dispatcher($container);

// Create the router instance
$router = new Router($events, $container);

// Load our routes
require_once 'routes.php';

// Create the redirect instance
// It can be used like this:
// return $redirect->home();
// return $redirect->back();
// return $redirect->to('/');
$redirect = new Redirector(new UrlGenerator($router->getRoutes(), $request));
$container->bind('redirect', function () use ($redirect) {
    return $redirect;
});

// Dispatch the request through the router
$response = $router->dispatch($request);

// Send the response back to the browser
$response->send();
