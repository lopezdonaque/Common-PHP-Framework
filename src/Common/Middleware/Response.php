<?php

namespace Common\Middleware;


/**
 * Middleware response container.By default it contains placeholders for HTTP\Response and JsonRpc\Response
 *
 */
class Response
{

  /**
   * Wether the request is fullfilled and should be ignored (except by final modules).
   *
   * @var boolean
   */
  public $fullfilled = false;


  /**
   * HTTP Response
   *
   * @var \Common\Middleware\Http\Response
   */
  public $httpResponse;


  /**
   * Response
   *
   * @var \Common\JsonRpc\Response
   */
  public $jsonRpcResponse;

}
