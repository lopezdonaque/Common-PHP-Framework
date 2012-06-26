<?php

namespace Common\Middleware;


/**
 * Interface to be implemented by request-authentication modules
 *
 */
interface Authenticator
{

  /**
   * Check the passed credentials in the request to authenticate
   *
   * @abstract
   * @param \Common\Middleware\Request $request
   * @return boolean true if authenticated, false if not
   */
  public function authenticate( $request );

}
