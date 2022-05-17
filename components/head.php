<?php

use ScssPhp\ScssPhp\Compiler;

// Create a new instance of the SCSS compiler
$compiler = new Compiler();
$compiler->setImportPaths(projectRoot() . '/scss');

$res = "";
$index = "";

// Loop over every file in our SCSS directory,
foreach (new DirectoryIterator(projectRoot() . '/scss') as $file) {
  // Does the filename start with a dot? Skip it.
  if ($file->isDot()) continue;

  // Get the file contents, compile it, and push it to the appropriate string.
  $content = file_get_contents($file->getRealPath());
  if ($file->getBasename() == "index.scss") {
    $index .= $compiler->compileString($content)->getCss();
  } else {
    $res .= $compiler->compileString($content)->getCss();
  }
}

// Merge the index file with all the others.
$res = $index . $res;

// Construct the page title.
// Whoever has the pleasure of viewing this, yes, I am keeping the nested
// ternary expression.
$title = isset($pageTitle)
  ? (!empty($pageTitle)
    ? "{$pageTitle} | Tijdsregistratie"
    : "Tijdsregistratie")
  : "Tijdsregistratie";
?>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= $title ?></title>

  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?= $title ?>" />

  <meta name="twitter:title" content="<?= $title ?>" />
  <meta name="twitter:site" content="@lexisother" />
  <meta name="twitter:creator" content="@lexisother" />
  <meta name="twitter:card" content="summary_large_image" />

  <style>
    <?php echo $res; ?>
  </style>
</head>
