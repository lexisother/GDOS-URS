<?php
// Load Composer
require __DIR__ . "/vendor/autoload.php";

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Database\Schema\Builder;
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

#region Illuminate setup {{{
$capsule = new Capsule();
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => getenv('GITHUB_API_URL') ? 'mariadb' : 'localhost',
    'database'  => 'URS',
    'username'  => 'root',
    'password'  => 'root',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
], 'default');
$capsule->setEventDispatcher(new Dispatcher(new Container()));
$capsule->setAsGlobal();
$capsule->bootEloquent();

// $connection = $capsule->getConnection('default');
// $builder = new Builder($connection);
// $builder->table('urenregistratie', function (Blueprint $table) {
//     $table->integer('urenregistratie_id', true)->primary();
//     $table->integer('medewerker_id');
//     $table->date('datum');
//     $table->integer('activiteit_id');
//     $table->integer('minuten');

//     $table->foreign('medewerker_id')->references('medewerker_id')->on('medewerker');
//     $table->foreign('activiteit_id')->references('activiteit_id')->on('activiteit');
// });
#endregion }}}

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
