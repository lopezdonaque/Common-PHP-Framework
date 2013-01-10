<?php

namespace Common\Middleware;


/**
 * Middleware Request container. By default it contains placeholders for HTTP\Request and JsonRpc\Request
 *
 */
class Request
{

  /**
   * HTTP Request
   *
   * @var \Common\Middleware\Http\Request
   */
  public $httpRequest;


  /**
   * Request
   *
   * @var \Common\JsonRpc\Request
   */
  public $jsonRpcRequest;

}
