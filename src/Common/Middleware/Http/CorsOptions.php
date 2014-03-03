<?php

namespace Common\Middleware\Http;


/**
 * CORS options
 *
 * @package common
 */
class CorsOptions
{

  /**
   * Accepted origins (e.g. "*", "domain.com")
   *
   * @var string|array
   */
  public $origins = '*';


  /**
   * List of accepted HTTP methods
   *
   * @var string[]
   */
  public $methods = array( 'GET', 'POST', 'OPTIONS' );


  /**
   * Maximum time (seconds) the CORS response can be cached. 20 days as default.
   *
   * @var int
   */
  public $maxage = 1728000;


  /**
   * Wether the resource supports user credentials
   *
   * @var boolean
   */
  public $supports_credentials = true;


  /**
   * Exposed headers
   *
   * @var string|array
   */
  public $expose_headers;



  /**
   * Constructor
   *
   * @param array $options
   */
  public function __construct( $options = array() )
  {
    foreach( $options as $option => $value )
    {
      if( property_exists( $this, $option ) )
      {
        $this->$option = $value;
      }
    }
  }

}
