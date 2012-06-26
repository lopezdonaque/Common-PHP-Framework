<?php
namespace Common\Middleware;

/**
 * Middleware
 *
 * A middleware executor class that can be used to build HTTP (or similar) request pipelines.
 * The listeners are called in the order they've been added.
 *
 * Example:<code>
 * $middleware = new Middleware();
 * $middleware->add( new HTTP\Decoder( true ) ); // Decode the HTTP request
 * $middleware->add( new JsonRpc\Decoder() ); // Decode a JSON-RPC request
 * $middleware->add( new JsonRpc\Executor( new handler() ); // Execute the request by calling found method on a "handler" object
 * $middleware->add( new JsonRpc\Encoder() ); // Encode a JSON-RPC response
 * $middleware->add( new HTTP\Encoder() ); // And print out the HTTP response
 * // We forgot auth!
 * $middleware->insert_before( new HTTP\BasicAuth(true, new authenticator() ), '\Common\Middleware\JsonRpc\Decoder' );
 *
 * $middleware->run( new Request(), new Response() ); // Run the middleware
 * </code>
 *
 * @package common
 */
class Middleware
{

  /**
   * Middleware listener pipeline
   *
   * @var Listener[]
   */
  private $m_line;


  /**
   * Create a new middleware pipeline
   *
   * @param \Common\Middleware\Listener[] listeners in order of execution.
   */
  public function __construct( $listeners = array() )
  {
    $this->m_line = $listeners;
  }


  /**
   * Add a listener to the call pipeline, after the last one
   *
   * @param \Common\Middleware\Listener $listener
   */
  public function add( Listener $listener )
  {
    $this->m_line[ ] = $listener;
  }


  /**
   * Insert before a given listener (either by fully-scoped class name or by object instance)
   *
   * @param \Common\Middleware\Listener $listener
   * @param string|\Common\Middleware\Listener $before
   */
  public function insert_before( Listener $listener, $before )
  {
    $res = array();

    if( is_string( $before ) )
    {
      $comparer = function( $bef, $item )
      {
        return get_class( $item ) == $bef;
      };
    }
    else
    {
      $comparer = function( $bef, $item )
      {
        return $item == $bef;
      };
    }
    $inserted = false;
    foreach( $this->m_line as $line_listener )
    {
      if( !$inserted && $comparer( $before, $line_listener ) )
      {
        $res[ ] = $listener;
        $inserted = true;
      }
      $res[ ] = $line_listener;
    }
    if( !$inserted )
    {
      $res[ ] = $listener;
    }
    $this->m_line = $res;
  }


  /**
   * Insert after a given listener (either by full-scoped class name or by object).
   * Note that if a string is used, the listener is inserted after the FIRST instance of this kind of listener
   *
   * @param \Common\Middleware\Listener $listener
   * @param string|\Common\Middleware\Listener $after
   * @return void
   */
  public function insert_after( \Common\Middleware\Listener $listener, $after )
  {
    $res = array();

    /** @var $is_the_same Closure */
    $is_the_same = $this->get_comparer( $after );

    $inserted = false;
    foreach( $this->m_line as $line_listener )
    {
      $res[ ] = $line_listener;
      if( !$inserted && $is_the_same( $after, $line_listener ) )
      {
        $res[ ] = $listener;
        $inserted = true;
      }

    }
    if( !$inserted )
    {
      $res[ ] = $listener;
    }
    $this->m_line = $res;
  }


  /**
   * Return a comparison function that tests equality of class name
   * if the passed argument is a string, or actual object equality if
   * the argument is an object.
   *
   * @param mixed $object
   * @return Closure
   */
  private function get_comparer( $object )
  {
    if( is_string( $object ) )
    {
      $comparer = function( $bef, $item )
      {
        return get_class( $item ) == $bef;
      };
    }
    else
    {
      $comparer = function( $bef, $item )
      {
        return $item == $bef;
      };
    }
    return $comparer;
  }


  /**
   * Swap the given listener by a new one. If $target is a string, the first one found will be replaced.
   *
   * @param string|Listener $target
   * @param Listener $replacement
   * @return boolean true if replaced, false if not found
   */
  public function swap( $target, Listener $replacement )
  {
    $res = array();

    $is_the_same = $this->get_comparer( $target ); /** @var $is_the_same Closure */

    $replaced = false;

    foreach( $this->m_line as $line_listener )
    {
      if( !$replaced && $is_the_same( $target, $line_listener ) )
      {
        $res[ ] = $target;
        $replaced = true;
      }
      else
      {
        $res[ ] = $line_listener;
      }
    }

    $this->m_line = $res;
    return $replaced;
  }



  /**
   * Run the middleware pipeline
   *
   * @param Request $request [optional] an incoming request
   * @param Response $response [optional] an incoming response
   * @return Response|\Exception the generated response or any captured exception
   */
  public function run( Request &$request = null, Response &$response = null )
  {
    if( $request == null )
    {
      $request = new Request();
    }

    if( $response == null )
    {
      $response = new Response();
    }

    $called = array();

    foreach( $this->m_line as $listener )
    {
      try
      {
        $listener->call( $request, $response );
        array_unshift( $called, $listener );
      }
      catch( \Exception $e )
      {
        /** @var $listerr Listener */
        foreach( $called as $listerr )
        {
          $listerr->abort( $request, $response, $e );
        }

        return $e;
      }
    }

    return $response;
  }

}
