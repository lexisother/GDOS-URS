<?php
// TODO: See the note in index.php

$connection = new mysqli("localhost", "root", "root", "URS");

if ($connection->connect_error) {
  throw new RuntimeException("Connection failed: ", $connection->connect_error);
}

// Get the lists of items for the dropdowns, note that mysqli_result implements
// IteratorAggregate, so it is iterable
$activities = $connection->query("SELECT * FROM activiteit");
$members = $connection->query("SELECT * FROM medewerker WHERE actief = 'ja'");

// If we are POSTing, do the database pushing
if (isset($_POST["name"])) {
  $user = $connection->query("SELECT * FROM medewerker WHERE naam = '{$_POST["name"]}'")->fetch_assoc();
  $activity = $connection->query("SELECT * FROM activiteit WHERE naam = '{$_POST['activity']}'")->fetch_assoc();

  $connection->query("INSERT INTO urenregistratie(medewerker_id, datum, activiteit_id, minuten) VALUES ({$user["medewerker_id"]}, DATE '{$_POST['date']}', {$activity['activiteit_id']}, {$_POST['time']})");
  if ($connection->error) {
    throw new RuntimeException($connection->error);
  }
}
?>

<form action="/" method="post">
  <select name="name" id="name">
    <?php
    foreach ($members as $member) {
      echo "<option required value='{$member["naam"]}'>{$member["naam"]}</option>";
    }
    ?>
  </select>
  <select name="activity" id="activity">
    <?php
    foreach ($activities as $activity) {
      echo "<option required value='{$activity["naam"]}'>{$activity["naam"]}</option>";
    }
    ?>
  </select>
  <input required type="date" name="date" id="date" min=<?php echo date('Y-m-d') ?> max=<?php echo date('Y') . "-12-31" ?> />

  <input required type="number" name="time" id="time" />

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

<style>
  table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
  }

  td,
  th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
  }
</style>
