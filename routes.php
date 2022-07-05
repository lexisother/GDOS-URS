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

$router->group(['prefix' => 'api'], function (Router $router) {
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
