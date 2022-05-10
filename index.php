<?php
# Load Composer
require __DIR__ . "/vendor/autoload.php";

use Bramus\Router\Router;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

$router = new Router();

// TODO: Split up POST logic to another route
$router->all('/', function () {
  view("home");
});

$router->run();
