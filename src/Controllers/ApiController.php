<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use RuntimeException;

class ApiController
{
    public function submit(Request $request)
    {
        $connection = getConn();
        if ($connection->connect_error) {
            throw new RuntimeException("Connection failed: ", $connection->connect_error);
        }

        if ($request->get('name')) {
            foreach ($request->all() as $key => $_) {
                if (!str_starts_with($key, "min-")) continue;
                $name = str_replace("min-", "", $key);
                $finalName = str_replace("-", " ", $name);

                $minuten = $request->get($key);
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
                    $_SESSION['success'] = 'ok';
                } else {
                    $_SESSION['error'] = $connection->error;
                }
                // header('Location: /');
                echo "<script>window.location.href='/';</script>";
            }
        }
    }
}
