<?php
// TODO: See the note in index.php

include "templates/base.php";

$connection = getConn();

if ($connection->connect_error) {
  throw new RuntimeException("Connection failed: ", $connection->connect_error);
}

// Get the lists of items for the dropdowns, note that mysqli_result implements
// IteratorAggregate, so it is iterable
$activities = $connection->query("SELECT * FROM activiteit");
$members = $connection->query("SELECT * FROM medewerker WHERE actief = 'ja'");
?>



<?php if (isset($_SESSION["error"])) { ?>
  <div class="notice red">
    <p>Er ging iets mis!</p>
  </div>
<?php } ?>


<?php if (isset($_SESSION["success"])) { ?>
  <div class="notice green">
    <p>Alles klaar!</p>
  </div>
<?php } ?>

<form action="/api/submit" method="post">
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

<?php $_SESSION = []; ?>
