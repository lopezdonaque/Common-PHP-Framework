<?php

namespace Common\Middleware\Api;

//$middleware->add( \Common\Middleware\Api\Cache::get_instance( json_decode( file_get_contents( 'apicache.json' ), true ) ) );
//$middleware->add( \Common\Middleware\Api\Cache::get_instance() );
/**
 * apicache.json
 *
 * {
   "cacheable_methods":
   [
     "utils.get_languages",
     "endusers.get_endusers",
     "consultations.get_consultation",
     "customers.get_customer_by_name",
     "portals.get_portal_by_name"
   ],

   "invalidator_methods":
   {
     "endusers.create_enduser":
     [
       "endusers.get_endusers"
     ],

     "consultations.create_appointment":
     [
       "consultations.get_consultation"
     ]
   }
 }

 *
 */
/**
 * Middleware to cache API requests and responses.
 * This layer should be applied before and after the Executor component.
 *
 */
class Cache implements \Common\Middleware\Listener
{

  /**
   * Options
   *
   * @var array
   */
  private $_options = array
  (
    'cacheable_methods' => array(),
    'invalidator_methods' => array()
  );


  /**
   * Cache manager
   *
   * @var \Zend\Cache\Storage\Adapter\Memcached
   */
  private $_cache;


  /**
   * Object instance
   *
   * @var Cache
   */
  private static $_instance;



  /**
   * Returns object instance
   *
   * @param array $options
   * @return Cache
   */
  public static function get_instance( $options = array() )
  {
    if( is_null( self::$_instance ) )
    {
      self::$_instance = new self( $options );
    }

    return self::$_instance;
  }



  /**
   * Constructor
   *
   * @param array $options Define options is only allowed in the TYPE_READ
   */
  public function __construct( $options = array() )
  {
    $this->_options = $options;

    $this->_cache = new \Zend\Cache\Storage\Adapter\Memcached( array
    (
      'servers' => array( array( '127.0.0.1', 11211 ) ),
      'namespace' => 'api_cache'
    ));
  }



  /**
   * Execute the parsed API request (if available).
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   */
  public function call( &$request, &$response )
  {
    if( $response->fullfilled || $response->api_exception )
    {
      return;
    }

    if( !$response->api_executed )
    {
      $this->_read_cache( $request, $response );
    }
    else
    {
      $this->_write_cache( $request, $response );
    }
  }



  /**
   * Reads the cache
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   */
  private function _read_cache( &$request, &$response )
  {
    $item_storage_key = $request->api_entity . '.' . $request->api_method;

    // Check if the method is cacheable and exists in the cache
    if( in_array( $item_storage_key, $this->_options[ 'cacheable_methods' ] ) )
    {
      $key = md5( json_encode( json_decode( $request->httpRequest->parameters[ 'arguments' ], true ) ) ); // $request->api_arguments could be changed

      if( ( $item_storage = $this->_cache->getItem( $item_storage_key ) ) && isset( $item_storage[ $key ] ) )
      {
        $response->api_executed = true;
        $response->api_return_value = $item_storage[ $key ];
      }
    }

    // Check if the method is an invalidator
    if( array_key_exists( $item_storage_key, $this->_options[ 'invalidator_methods' ] ) )
    {
      foreach( $this->_options[ 'invalidator_methods' ][ $item_storage_key ] as $item_storage_key_to_invalidate )
      {
        $this->_cache->removeItem( $item_storage_key_to_invalidate );
      }
    }
  }



  /**
   * Writes the cache
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   */
  private function _write_cache( &$request, &$response )
  {
    $item_storage_key = $request->api_entity . '.' . $request->api_method;

    // Check if the method is cacheable and has been executed
    if( in_array( $item_storage_key, $this->_options[ 'cacheable_methods' ] ) && !is_null( $response->api_execution_time ) )
    {
      $key = md5( json_encode( json_decode( $request->httpRequest->parameters[ 'arguments' ], true ) ) );
      $item_storage = $this->_cache->getItem( $item_storage_key ) ?: array();
      $item_storage[ $key ] = $response->api_return_value;
      $this->_cache->setItem( $item_storage_key, $item_storage );
    }
  }



  /**
   * Middleware "abort" callback.
   * Will be called if someone aborts the pipeline down the road to allow rolling back or logging.
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   * @param \Exception $exception
   */
  public function abort( &$request, &$response, &$exception ){}

}
