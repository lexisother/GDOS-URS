<?php
// TODO: See the note in index.php

$connection = new mysqli("localhost", "root", "root", "URS");

if ($connection->connect_error) {
  throw new RuntimeException("Connection failed: ", $connection->connect_error);
}

$activities = $connection->query("SELECT * FROM activiteit");
$members = $connection->query("SELECT * FROM medewerker WHERE actief = 'ja'");

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
  </tr>
  <?php
  $items = $connection->query("SELECT * FROM urenregistratie");
  foreach ($items as $item) {
    $activity = $connection->query("SELECT activiteit.naam FROM activiteit WHERE activiteit.activiteit_id = '{$item["activiteit_id"]}'")->fetch_assoc()["naam"];
    $user = $connection->query("SELECT medewerker.naam FROM medewerker WHERE medewerker.medewerker_id = '{$item["medewerker_id"]}'")->fetch_assoc()["naam"];
    $duration = $connection->query("SELECT urenregistratie.minuten FROM urenregistratie WHERE urenregistratie.activiteit_id = '{$item["activiteit_id"]}'")->fetch_assoc()["minuten"];

    echo "<tr>";
    echo "<td>{$activity}</td>";
    echo "<td>{$user}</td>";
    echo "<td>{$duration}</td>";
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
