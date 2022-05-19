<?php
// TODO: See the note in index.php

require "templates/base.php";

$connection = getConn();

if ($connection->connect_error) {
  throw new RuntimeException("Connection failed: ", $connection->connect_error);
}

// Get the lists of items for the dropdowns, note that mysqli_result implements
// IteratorAggregate, so it is iterable
$activities = $connection->query("SELECT * FROM activiteit");
$members = $connection->query("SELECT * FROM medewerker WHERE actief = 'ja'");
?>
<div class="container">
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

  <form action=" /api/submit" method="post">
    <div class="itemgrid">
      <div>
        <label>Naam</label>
        <select class="griditem input-box shadow-md" name="name" id="name">
          <?php
          foreach ($members as $member) {
            echo "<option required value='{$member["naam"]}'>{$member["naam"]}</option>";
          }
          ?>
        </select>
      </div>
      <div>
        <label>datum</label>
        <input required class="griditem flex-none shadow-md" type="date" name="date" id="date" min=<?php echo date('Y-m-d') ?> max=<?php echo date('Y') . "-12-31" ?> />
      </div>
      <?php
      foreach ($activities as $activity) {
        $name = str_replace(" ", "-", $activity["naam"]);
        echo "<div>";
        echo "<label>{$activity["naam"]} </label>";
        echo "<input class='griditem input-box shadow-md' placeholder='aantal minuten...' type='number' name='min-{$name}' />";
        echo "</div>";
      }
      ?>
    </div>
    <br />
    <input class="input-box griditem flex-none shadow-md" type="submit" />
  </form>
</div>
<?php $_SESSION = []; ?>
