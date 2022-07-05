<?php

namespace Extersia\App;

use Extersia\Filesystem\Filesystem;
use Extersia\View\FileViewFinder;
use RuntimeException;

/**
 * The main App class, the one who oversees all.
 */
class App
{
  /**
   * @var \Extersia\Filesystem\Filesystem
   */
  private static $files;

  /**
   * @var \Extersia\View\FileViewFinder
   */
  private static $viewFinder;

  /**
   * The application namespace.
   * 
   * @var string
   */
  protected static $namespace;

  /**
   * Return the App's Filesystem instance.
   *
   * @return \Extersia\Filesystem\Filesystem
   */
  public static function getFiles()
  {
    return self::$files;
  }

  /**
   * Return the App's FileViewFinder instance.
   *
   * @return \Extersia\View\FileViewFinder
   */
  public static function getViewFinder()
  {
    return self::$viewFinder;
  }

  /**
   * Initialize the App.
   */
  public static function initialize()
  {
    self::$files = new Filesystem;
    self::$viewFinder = new FileViewFinder(self::$files, [projectRoot() . '/views']);
  }

  /**
   * Get the application namespace.
   * 
   * @return string
   * 
   * @throws \RuntimeException
   */
  public static function getNamespace()
  {
    if (!is_null(self::$namespace)) {
      return self::$namespace;
    }

    $composer = json_decode(file_get_contents(fullRoot() . '/composer.json'), true);

    foreach ((array) data_get($composer, 'autoload.psr-4') as $namespace => $path) {
      foreach ((array) $path as $pathChoice) {
        if (realpath(fullRoot() . '/src') == realpath(fullRoot() . '/' . $pathChoice)) {
          return self::$namespace = $namespace;
        }
      }
    }

    throw new RuntimeException('Unable to detect application namespace.');
  }
}
