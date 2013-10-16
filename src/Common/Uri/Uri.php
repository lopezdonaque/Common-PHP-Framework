<?php

namespace Common\Uri;


/**
 * Class representing a URI, according to http://www.ietf.org/rfc/rfc2396.txt
 *
 */
class Uri
{

  /**
   * Schemes accepted by the subtype.
   * If null, anything goes.
   *
   * @var string[]
   */
  protected static $accepted_schemes = null;


  /**
   * Scheme for the uri
   *
   * @var string
   */
  protected $_scheme;


  /**
   * Rest of the uri, when used with parse
   *
   * @var string
   */
  private $_rest;



  /**
   * Constructor
   *
   * @param string $scheme scheme for the URI
   */
  public function __construct( $scheme = null )
  {
    $this->_scheme = $scheme;
  }



  /**
   * Check if this URI subtype accepts a given scheme
   *
   * @param string $scheme
   * @return bool True if this subtype accepts this scheme, false if not
   */
  public static function accepts_scheme( $scheme )
  {
    if( self::$accepted_schemes == null )
    {
      return true;
    }

    if( is_array( self::$accepted_schemes ) )
    {
      return in_array( $scheme, self::$accepted_schemes );
    }

    return false;
  }



  ///////////////////////
  // Getters & Setters //
  ///////////////////////

  /**
   * Method to set scheme value
   *
   * @param string $scheme
   */
  public function set_scheme( $scheme )
  {
    $this->_scheme = $scheme;
  }



  /**
   * Method to get schema value
   *
   * @return string
   */
  public function get_scheme()
  {
    return $this->_scheme;
  }



  /**
   * Get a representation of the URI as a string
   *
   * @return string
   */
  public function get_uri()
  {
    return $this->get_scheme() . ':' . $this->_rest;
  }



  /**
   * Return a string representation of the given URI
   *
   * @return string
   */
  public function __toString()
  {
    return $this->get_scheme() . ':' . $this->_rest;
  }



  /**
   * Parse the given string to self
   * Override on child
   *
   * @param string $string
   * @return bool
   */
  public function parse( $string )
  {
    if( strstr( $string, ':' ) )
    {
      list( $sch, $rest ) = explode( ':', $string, 2 );
      $this->set_scheme( $sch );
      $this->_rest = $rest;
      return true;
    }

    return false;
  }



  /**
   * Get an instance of a child of Uri depending on the schema
   *
   * @param string $string
   * @return Uri|Url or null if no schema found.
   */
  public static function get_instance( $string )
  {
    $uri = new self();

    if( !$uri->parse( $string ) )
    {
      return null;
    }

    switch( $uri->get_scheme() )
    {
      case Url::SCHEME_FTP:
      case Url::SCHEME_HTTP:
      case Url::SCHEME_HTTPS:
      case Url::SCHEME_SFTP:
        $concrete = new Url();
        break;

      default:
        return $uri;
        break;
    }

    if( $concrete->parse( $string ) )
    {
      return $concrete;
    }

    return null;
  }

}
