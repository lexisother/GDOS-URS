<?php

includeWithVariables(projectRoot() . '/templates/base.php', ['pageTitle' => 'Totaal']);

$connection = getConn();

?>

<h1>Totaalaantallen</h1>
<table>
  <tr>
    <th>
      Medewerker
    </th>
    <th>
      Activiteit
    </th>
    <th>
      Totaal
    </th>
  </tr>
  <?php
  $totalItems = $connection->query("
      SELECT
          m.naam AS medewerker,
          a.naam AS activiteit,
          SUM(u.minuten) AS totaal
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
      GROUP BY
          m.naam,
          a.naam
      ORDER BY
          3
      DESC;
    ");
  foreach ($totalItems as $item) {
    echo "<tr>";
    echo "<td>{$item["medewerker"]}</td>";
    echo "<td>{$item["activiteit"]}</td>";
    echo "<td>{$item["totaal"]}</td>";
    echo "</tr>";
  }
  ?>
</table>
