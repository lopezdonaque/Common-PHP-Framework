<?php

namespace Common\Utils;


/**
 * Class to manage cookies
 *
 */
class Cookies
{

  /**
   * Returns a cookie value
   *
   * @param string $name
   * @return string|boolean
   */
  public static function get( $name )
  {
    if( !isset( $_COOKIE[ $name ] ) )
    {
      return false;
    }

    return $_COOKIE[ $name ];
  }



  /**
   * Sets a cookie
   *
   * @param string $name
   * @param string $value
   * @param string $expire
   * @param string $path
   * @param string $domain
   * @param string $secure
   * @param string $httponly
   */
  public static function set( $name, $value, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null )
  {
    setcookie( $name, $value, $expire, $path, $domain, $secure, $httponly );
  }



  /**
   * Removes a cookie
   *
   * @param string $name
   */
  public static function remove( $name )
  {
    if( isset( $_COOKIE[ $name ] ) )
    {
      unset( $_COOKIE[ $name ] );
      setcookie( $name, NULL, -1 );
    }
  }

}
