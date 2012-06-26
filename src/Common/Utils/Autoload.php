<?php

namespace Common\Utils;


/**
 * Loader class to autoload namespaced classes
 *
 * The class is not explicitly used, but called by __autoload().
 *
 */
class Autoload
{

  /**
   * Registers autoload function
   *
   */
  public function register()
  {
    spl_autoload_register( array( $this, 'loadClass' ) );
  }



  /**
   * Loads class
   *
   * @param string $class
   */
  public function loadClass( $class )
  {
    // Check "Common" namespace
    if( substr( $class, 0, strlen( 'Common' ) ) === 'Common' )
    {
      $file = str_replace( '\\', DIRECTORY_SEPARATOR, $class ) . '.php';

      // Workaroud while Common library is inside portals libraries folder
      // $file = 'Common' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $file;

      require $file;
    }
  }

}
