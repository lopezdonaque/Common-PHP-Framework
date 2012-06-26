<?php
namespace Common\Middleware;

/**
 * Middleware Request container. By default it contains placeholders for HTTP\Request and JsonRpc\Request
 *
 * @author Androme Iberica 2011
 * @package common
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
