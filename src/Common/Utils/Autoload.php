<?php

namespace Common\Utils;


/**
 * Loader class to autoload namespaced classes
 *
 */
class Autoload
{

  /**
   * Registers autoload function
   */
  public function register()
  {
    spl_autoload_register( [ $this, 'loadClass' ] );
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
      require $file;
    }
  }

}
