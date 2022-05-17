<?php

includeWithVariables(projectRoot() . '/templates/base.php', ['pageTitle' => 'Overview']);

$connection = getConn();

?>

<h1>Overzicht</h1>
<table>
    <tr>
        <th>
            Activiteit
        </th>
        <th>
            Medewerker
        </th>
        <th>
            Minuten
        </th>
        <th>
            Datum
        </th>
    </tr>
    <?php
    // Amazingly verbose query for fetching the data in exactly the structure we
    // need.
    $overviewItems = $connection->query("
      SELECT
          m.naam AS medewerker,
          a.naam AS activiteit,
          u.datum,
          u.minuten
      FROM
          urenregistratie AS u
      LEFT JOIN medewerker AS m
      ON
          (
              u.medewerker_id = m.medewerker_id
          )
      LEFT JOIN activiteit AS a
      ON
          (
              u.activiteit_id = a.activiteit_id
          )
      WHERE
          m.actief = 'ja'
      ORDER BY
          3
      DESC
          ,
          2,
          1;
    ");
    foreach ($overviewItems as $item) {
        echo "<tr>";
        echo "<td>{$item["activiteit"]}</td>";
        echo "<td>{$item["medewerker"]}</td>";
        echo "<td>{$item["minuten"]}</td>";
        echo "<td>{$item["datum"]}</td>";
        echo "</tr>";
    }
    ?>
</table>
