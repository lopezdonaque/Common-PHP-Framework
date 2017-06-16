<?php

namespace Common\Utils;


/**
 * Server utility methods
 *
 */
class Server
{

  /**
   * Is request over HTTPS?
   *
   * @return boolean
   */
  public static function is_https()
  {
    return ( @$_SERVER[ 'HTTPS' ] == 'on' );
  }



  /**
   * Returns if the current request is an Ajax request
   *
   * @return bool
   */
  public static function is_ajax_request()
  {
    return ( !empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest' );
  }



  /**
   * Returns request protocol string, with leader "://" ("http://" or "https://")
   *
   * @return string
   */
  public static function get_protocol()
  {
    // If PHP script is not running from webserver return https as default
    if( !isset( $_SERVER[ 'HTTP_HOST' ] ) )
    {
      return 'https://';
    }

    return self::is_https() ? 'https://' : 'http://';
  }



  /**
   * Get the remote address IP, even behind proxies (REMOTE_ADDR if not proxied)
   *
   * @return string
   */
  public static function get_remote_ip()
  {
    if( php_sapi_name() == 'cli' ) // Running from command line
    {
      return '127.0.0.1';
    }

    if( self::_is_valid_public_ip( self::_get_server_value( 'HTTP_CLIENT_IP' ) ) )
    {
      return self::_get_server_value( 'HTTP_CLIENT_IP' );
    }

    foreach( explode( ',', self::_get_server_value( 'HTTP_X_FORWARDED_FOR' ) ) as $ip )
    {
      if( self::_is_valid_public_ip( trim( $ip ) ) )
      {
        return $ip;
      }
    }

    if( self::_is_valid_public_ip( self::_get_server_value( 'HTTP_X_FORWARDED' ) ) )
    {
      return self::_get_server_value( 'HTTP_X_FORWARDED' );
    }

    if( self::_is_valid_public_ip( self::_get_server_value( 'HTTP_FORWARDED_FOR' ) ) )
    {
      return self::_get_server_value( 'HTTP_FORWARDED_FOR' );
    }

    if( self::_is_valid_public_ip( self::_get_server_value( 'HTTP_FORWARDED' ) ) )
    {
      return self::_get_server_value( 'HTTP_FORWARDED' );
    }

    return self::_get_server_value( 'REMOTE_ADDR' );
  }



  /**
   * Check if an IP is valid and public
   *
   * @param string $ip
   * @return bool
   */
  private static function _is_valid_public_ip( $ip )
  {
    if( !empty( $ip ) && ip2long( $ip ) != -1 )
    {
      $reserved_ips = array
      (
        array( '0.0.0.0', '2.255.255.255' ),
        array( '10.0.0.0', '10.255.255.255' ),
        array( '127.0.0.0', '127.255.255.255' ),
        array( '169.254.0.0', '169.254.255.255' ),
        array( '172.16.0.0', '172.31.255.255' ),
        array( '192.0.2.0', '192.0.2.255' ),
        array( '192.168.0.0', '192.168.255.255' ),
        array( '255.255.255.0', '255.255.255.255' )
      );

      foreach( $reserved_ips as $r )
      {
        $min = ip2long( $r[0] );
        $max = ip2long( $r[1] );

        if( ( ip2long( $ip ) >= $min ) && ( ip2long( $ip ) <= $max ) )
        {
          return false;
        }
      }

      return true;
    }
    else
    {
      return false;
    }
  }



  /**
   * Get a variable from $_SERVER
   *
   * @param string $name
   * @return string or null if not found
   */
  private static function _get_server_value( $name )
  {
    return @$_SERVER[ $name ] ?: null;
  }

}
