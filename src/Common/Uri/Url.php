<?php

namespace Common\Uri;


/**
 * Represents a Uniform Resource Locator
 * Caveat: Uses internally the parse_url PHP function, so it does not
 * check for URL sanity.
 * Caveat2: Might not be ready for UTF-8 or UTF-16 URLs
 *
 * <code>
 * $url = new \Common\Uri\Url( 'http://foo:bar@host.com/path/to/file.jpg?var1=val1' );
 * $url->get_scheme(); // 'http'
 * $url->get_password(); // 'bar'
 * // ...
 *
 * </code>
 *
 */
class Url extends Uri
{

  /**#@+
   * Protocol scheme constants
   *
   * @var string
   */

  /** HTTP */
  const SCHEME_HTTP = 'http';

  /** HTTPS */
  const SCHEME_HTTPS = 'https';

  /** FTP */
  const SCHEME_FTP = 'ftp';

  /** Secure FTP */
  const SCHEME_SFTP = 'sftp';
  /**#@-*/


  /**
   * Accepted schemes
   *
   * @var string[]
   */
  protected static $accepted_schemes = array
  (
    self::SCHEME_HTTP,
    self::SCHEME_HTTPS,
    self::SCHEME_FTP,
    self::SCHEME_SFTP
  );


  /**
   * Wether it's a relative URL or an absolute one
   *
   * @var boolean
   */
  private $_is_relative = false;


  /**
   * Host name part of the URL
   *
   * @var string
   */
  private $_host;


  /**
   * Port part of the URL. If null, it depends on the scheme property.
   *
   * @var int
   */
  private $_port = null;


  /**
   * User part of the URL.
   *
   * @var string
   */
  private $_user;


  /**
   * Password part of the URL
   *
   * @var string
   */
  private $_password;


  /**
   * Path part of the URL, that MIGHT include a trailing slash
   *
   * @var string
   */
  private $_path;


  /**
   * Parameters of the url
   *
   * @var string[string]
   */
  private $_parameters;


  /**
   * Fragment part (after a #) of the url
   *
   * @var string
   */
  private $_fragment;



  /**
   * Build a uri from the given parameter
   *
   * @param string $url
   */
  public function __construct( $url = '' )
  {
    parent::__construct( self::SCHEME_HTTP );

    if( $url != '' )
    {
      $this->parse( $url );
    }
  }



  /**
   * Reset the URL (for re-parses)
   */
  private function _reset()
  {
    $this->_is_relative = false;
    $this->set_scheme( self::SCHEME_HTTP );
    $this->_host = null;
    $this->_port = null;
    $this->_user = null;
    $this->_password = null;
    $this->_path = null;
    $this->_fragment = null;
    $this->_parameters = null;
  }



  /**
   * Parse a url, set values into class.
   *
   * @param string $url
   * @return bool
   */
  public function parse( $url )
  {
    $this->_reset();
    $parts = parse_url( $url );

    if( $parts == false )
    {
      return false;
    }

    if( !isset( $parts['scheme'] ) )
    {
      $this->set_scheme( self::SCHEME_HTTP );
    }
    else
    {
      $this->set_scheme( $parts['scheme'] );
    }

    if( !isset( $parts['host'] ) )
    {
      $this->set_is_relative( true );
    }
    else
    {
      $this->set_is_relative( false );
      $this->set_host( $parts['host'] );
    }

    if( !$this->get_is_relative() && isset( $parts['port'] ) )
    {
      $this->set_port( $parts['port'] );
    }

    if( isset( $parts['user'] ) )
    {
      $this->set_user( $parts['user'] );
    }

    if( isset( $parts['pass'] ) )
    {
      $this->set_password( $parts['pass'] );
    }

    if( isset( $parts['path'] ) )
    {
      $this->set_path( $parts['path'] );
    }

    if( isset( $parts['query'] ) )
    {
      $query = $parts['query'];
      $this->set_parameters( $this->get_string_as_parameters( $query ) );
    }

    if( isset( $parts['fragment'] ) )
    {
      $this->set_fragment( $parts['fragment'] );
    }

    return true;
  }



  /**
   * Get a named parameter
   *
   * @param string $name
   * @return string the parameter or null if not found
   */
  public function get_parameter( $name )
  {
    if( isset( $this->_parameters[ $name ] ) )
    {
      return $this->_parameters[ $name ];
    }

    return false;
  }



  /**
   * Set a parameter value
   *
   * @param string $name the parameter name
   * @param string $value the value
   */
  public function set_parameter( $name, $value )
  {
    $this->_parameters[ $name ] = $value;
  }



  /**
   * Get the whole URI as a string
   *
   * @return string
   */
  public function get_uri()
  {
    return $this->__toString();
  }



  /**
   * Get the full URL as a string
   *
   * @return string
   */
  public function __toString()
  {
    if( $this->_is_relative )
    {
      return $this->get_relative_part();
    }
    else
    {
      return $this->get_full_url();
    }
  }



  /**
   * Get the URL escaped in a format embeddable into XML
   * (e.g. replacing & with &amp; to avoid clashes)
   *
   * @param bool $use_server if true (by default) for relative URLs use _SERVER for host part.
   * @param bool $with_params if true (by default) include parameters.
   * @return string
   */
  public function get_xml_url( $use_server = true, $with_params = true )
  {
    $text = $this->get_full_url( $use_server, $with_params );
    return htmlspecialchars( $text );
  }



  /**
   * Get the whole url as a string.
   *
   * If the URL is relative, this function will return null, except if $use_server is set. In that case it will use the
   * current $_SERVER values to compose the URL.
   *
   * @param bool $use_server If true (by default) use _SERVER for host part.
   * @param bool $with_params If true (by default) include parameters.
   * @return string
   */
  public function get_full_url( $use_server = true, $with_params = true )
  {
    if( $this->_is_relative == true )
    {
      if( !$use_server )
      {
        return null;
      }
      elseif( !isset( $_SERVER ) )
      {
        return null;
      }
    }

    if( $this->_is_relative )
    {
      $scheme = $_SERVER[ 'HTTPS' ] != '' ? self::SCHEME_HTTPS : self::SCHEME_HTTP;
      $host = $_SERVER[ 'SERVER_NAME' ];
      $port = $_SERVER[ 'SERVER_PORT' ];
      $user = '';
      $password = '';
    }
    else
    {
      $scheme = $this->get_scheme();
      $host = $this->get_host();
      $port = $this->get_port();
      $user = $this->get_user();
      $password = $this->get_password();
    }

    if( $port == '80' && $scheme == self::SCHEME_HTTP )
    {
      $port = '';
    }

    if( $port == '443' && $scheme == self::SCHEME_HTTPS )
    {
      $port = '';
    }

    $url = $scheme . '://';

    if( $user != '' )
    {
      $url .= $user;

      if( $password != '' )
      {
        $url .= ':' . $password;
      }

      $url .= '@';
    }

    $url .= $host;

    if( $port != '' )
    {
      $url .= ':' . $port;
    }

    $url .= $this->get_relative_part( $with_params );
    return $url;
  }



  /**
   * Get the relative (path+parameters) part of the URL
   *
   * @param bool $with_params if true (by default) include parameters
   * @return string
   */
  public function get_relative_part( $with_params = true )
  {
    $base = $this->get_path();

    if( count( $this->get_parameters() ) > 0 && $with_params )
    {
      $base .= '?' . $this->get_parameters_as_string();
    }

    if( $this->get_fragment() != '' )
    {
      $base .= '#' . $this->get_fragment();
    }

    return $base;
  }



  /**
   * Get the parameters as an URL string of var=value separated by '&'
   *
   * @param array $parameters
   * @return string
   */
  public function get_parameters_as_string( $parameters = null )
  {
    $params = ( $parameters == null )?$this->_parameters:$parameters;

    if( $params == null || count( $params ) == 0 )
    {
      return '';
    }

    $parms = array();

    foreach( $params as $id => $value )
    {
      $parms[] = rawurlencode( $id ) . ( $value != ''?'=' . rawurlencode( $value ):'' );
    }

    return implode( '&', $parms );
  }



  /**
   * Returns the parameter's array of a query string
   *
   * @param string $query
   * @return array
   */
  public function get_string_as_parameters( $query )
  {
    $params = array();
    $items = explode( '&', $query );

    foreach( $items as $item )
    {
      // Verify if the "=" exists
      if( !strpos( $item, '=' ) )
      {
        continue;
      }

      list( $var, $val ) = explode( '=', $item, 2 );
      $params[ rawurldecode( $var ) ] = rawurldecode( $val );
    }

    return $params;
  }



  /**
   * Build the URL based on _SERVER variables
   *
   * @return bool True on success, false if _SERVER info wasn't available.
   */
  public function build_from_server()
  {
    if( !isset( $_SERVER ) )
    {
      return false;
    }

    $proto = ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] != 'off' ) ? self::SCHEME_HTTPS : self::SCHEME_HTTP;
    $host = $_SERVER[ 'SERVER_NAME' ];
    $rest = $_SERVER[ 'REQUEST_URI' ];
    $url = $proto . '://' . $host . $rest;
    $this->parse( $url );

    return true;
  }



  /**
   * Copy the parameters from the $url object
   *
   * @param url $url
   */
  public function copy_parameters( $url )
  {
    $parameters = $url->get_parameters();

    foreach( $parameters as $param_name => $param_value )
    {
      $this->set_parameter( $param_name, $param_value );
    }
  }



  /**
   * Returns the base domain
   * Beware: buggy. Domains like xxxx.co.uk are not returned right.
   *
   * @param string $full_domain
   * @return string
   */
  public static function get_base_domain( $full_domain = null )
  {
    if( !$full_domain )
    {
      // "HTTP_HOST" is empty on "http/1.0" connections
      $full_domain = isset( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : $_SERVER[ 'SERVER_NAME' ];
    }

    list( $a, $b ) = array_reverse( explode( '.', $full_domain ) );
    return "$b.$a";
  }



  /**
   * Returns the current URL based on $_SERVER info
   *
   * @return \Common\Uri\Url
   */
  public static function get_current_url()
  {
    $url = new self();
    $url->build_from_server();
    return $url;
  }


  ///////////////////////
  // Getters & Setters //
  ///////////////////////

  /**
   * @return string
   */
  public function get_host()
  {
    return $this->_host;
  }

  /**
   * @return bool
   */
  public function get_is_relative()
  {
    return $this->_is_relative;
  }

  /**
   * @return array[string]string
   */
  public function get_parameters()
  {
    return $this->_parameters;
  }

  /**
   * @return string
   */
  public function get_password()
  {
    return $this->_password;
  }

  /**
   * @return string
   */
  public function get_path()
  {
    return $this->_path;
  }

  /**
   * @return int
   */
  public function get_port()
  {
    return $this->_port;
  }

  /**
   * @return string
   */
  public function get_user()
  {
    return $this->_user;
  }

  /**
   * @param string $host
   */
  public function set_host( $host )
  {
    $this->_host = $host;
  }

  /**
   * @param bool $is_relative
   */
  public function set_is_relative( $is_relative )
  {
    $this->_is_relative = $is_relative;
  }

  /**
   * @param string[] $parameters
   */
  public function set_parameters( $parameters )
  {
    $this->_parameters = $parameters;
  }

  /**
   * @param string $password
   */
  public function set_password( $password )
  {
    $this->_password = $password;
  }

  /**
   * @param string $path
   */
  public function set_path( $path )
  {
    $this->_path = $path;
  }

  /**
   * @param int $port
   */
  public function set_port( $port )
  {
    $this->_port = $port;
  }

  /**
   * @param string $scheme
   */
  public function set_scheme( $scheme )
  {
    $this->_scheme = $scheme;
  }

  /**
   * @param string $user
   */
  public function set_user( $user )
  {
    $this->_user = $user;
  }

  /**
   * @return string
   */
  public function get_fragment()
  {
    return $this->_fragment;
  }

  /**
   * @param string $fragment
   */
  public function set_fragment( $fragment )
  {
    $this->_fragment = $fragment;
  }

}
