<?php
// TODO: See the note in index.php

include "components/head.php";

$connection = getConn();

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
