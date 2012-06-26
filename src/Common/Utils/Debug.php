<?php

namespace Common\Utils;


/**
 * Class to manage Debug utility methods
 *
 */
class Debug
{

  /**
   * Returns the level name
   *
   * @static
   * @param int $intval
   * @param string $separator
   * @return string
   */
  public static function error_level_tostring( $intval, $separator = null )
  {
    $errorlevels = array
    (
      2047 => 'E_ALL',
      1024 => 'E_USER_NOTICE',
      512 => 'E_USER_WARNING',
      256 => 'E_USER_ERROR',
      128 => 'E_COMPILE_WARNING',
      64 => 'E_COMPILE_ERROR',
      32 => 'E_CORE_WARNING',
      16 => 'E_CORE_ERROR',
      8 => 'E_NOTICE',
      4 => 'E_PARSE',
      2 => 'E_WARNING',
      1 => 'E_ERROR'
    );

    $result = '';

    foreach( $errorlevels as $number => $name )
    {
      if( ( $intval & $number ) == $number )
      {
        $result .= ( $result != '' ? $separator : '' ) . $name;
      }
    }

    return $result;
  }



  /**
   * Converts an exception to an array
   *
   * @param \Exception $e
   * @return array
   */
  public static function exception_to_array( $e )
  {
    return array
    (
      'Message' => $e->getMessage(),
      'Code' => $e->getCode(),
      'File' => $e->getFile(),
      'Line' => $e->getLine(),
      'Trace' => $e->getTraceAsString()
    );
  }

}
