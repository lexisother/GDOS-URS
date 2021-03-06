<?php
// Load Composer
require __DIR__ . "/vendor/autoload.php";

use Bramus\Router\Router;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

$router = new Router();

session_start();

// TODO: Split up POST logic to another route
$router->all('/', function () {
  view("home");
});

$router->mount("/overview", function () use ($router) {
  $router->all("/", function () {
    view("overview");
  });

  $router->all("/total", function () {
    view("total");
  });
});

$router->mount("/api", function () use ($router) {
  $router->post("/submit", function () {
    $connection = getConn();
    if ($connection->connect_error) {
      throw new RuntimeException("Connection failed: ", $connection->connect_error);
    }

    if (isset($_POST["name"])) {
      foreach ($_POST as $key => $_) {
        if (!str_starts_with($key, "min-")) continue;
        $name = str_replace("min-", "", $key);
        $finalName = str_replace("-", " ", $name);

        $minuten = $_POST[$key];
        if (!$minuten) continue;

        $sql = "
          INSERT INTO
              urenregistratie (
                  medewerker_id,
                  datum,
                  activiteit_id,
                  minuten
              )
          VALUES (
              (SELECT medewerker_id FROM medewerker WHERE naam = '{$_POST["name"]}'),
              DATE '{$_POST["date"]}',
              (SELECT activiteit_id FROM activiteit WHERE naam = '{$finalName}'),
              {$minuten}
          )
        ";

        $connection->query($sql);

        if (!$connection->error) {
          $_SESSION["success"] = "ok";
        } else {
          $_SESSION["error"] = $connection->error;
        }
        header("Location: /");
      }
    }
  });
});

$router->run();
