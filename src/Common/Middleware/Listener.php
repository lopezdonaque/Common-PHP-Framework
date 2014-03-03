<?php

namespace Common\Middleware;

/**
 * Interface that all middleware listeners shall implement.
 * By default "call(request,response)" will be called. If one module
 * throws an exception "abort(request,response)" will be called for all already executed listeners.
 *
 * @package common
 */
interface Listener
{

  /**
   * Middleware "call" callback
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   */
  public function call( &$request, &$response );


  /**
   * Middleware "abort" callback. will be called if someone aborts the
   * pipeline down the road to allow rolling back or logging.
   *
   * @param \Common\Middleware\Request $request
   * @param \Common\Middleware\Response $response
   * @param \Exception $exception
   */
  public function abort( &$request, &$response, &$exception );

}
