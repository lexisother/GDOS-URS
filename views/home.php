<?php
// TODO: See the note in index.php

include "components/head.php";

$connection = new mysqli("localhost", "root", "root", "URS");

if ($connection->connect_error) {
  throw new RuntimeException("Connection failed: ", $connection->connect_error);
}

// Get the lists of items for the dropdowns, note that mysqli_result implements
// IteratorAggregate, so it is iterable
$activities = $connection->query("SELECT * FROM activiteit");
$members = $connection->query("SELECT * FROM medewerker WHERE actief = 'ja'");

if (isset($_POST["name"])) {
  foreach ($_POST as $key => $value) {
    if (!str_starts_with($key, "min-")) continue;
    $name = str_replace("min-", "", $key);
    $finalName = str_replace("-", " ", $name);

    $minuten = $_POST[$key];
    if (!$minuten) continue;

    $activiteit = $connection->query("SELECT * FROM activiteit WHERE naam = '{$finalName}'")->fetch_assoc();

    $medewerker = $connection->query("SELECT * FROM medewerker WHERE naam = '{$_POST["name"]}'")->fetch_assoc();

    $sql = "INSERT INTO urenregistratie (medewerker_id,datum,activiteit_id,minuten)
     VALUES ('{$medewerker["medewerker_id"]}', DATE '{$_POST["date"]}', '{$activiteit["activiteit_id"]}', '{$minuten}')";

    $connection->query($sql);
  }
}

?>

<form action="/" method="post">
  <label>Naam</label>
  <select name="name" id="name">
    <?php
    foreach ($members as $member) {
      echo "<option required value='{$member["naam"]}'>{$member["naam"]}</option>";
    }
    ?>
  </select>
  <br />
  <?php
  foreach ($activities as $activity) {
    $name = str_replace(" ", "-", $activity["naam"]);
    echo "<label>{$activity["naam"]} </label>";
    echo "<input placeholder='aantal minuten...' type='number' name='min-{$name}' />";
    echo "<br>";
  }
  ?>
  <br />
  <input required type="date" name="date" id="date" min=<?php echo date('Y-m-d') ?> max=<?php echo date('Y') . "-12-31" ?> />

  <input type="submit" />
</form>

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

<br />
<br />
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
