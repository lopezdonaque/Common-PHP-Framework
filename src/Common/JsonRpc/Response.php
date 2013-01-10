<?php

namespace Common\JsonRpc;


/**
 * A JSON-RPC response, according to jsonrpc.org/spec.html
 *
 */
class Response
{

  /**
   * Response identifier
   *
   * @var string
   */
  public $id;


  /**
   * JSON-RPC version response (copy of request)
   *
   * @var string
   */
  public $jsonrpc;


  /**
   * Result of the operation, MUST be JSON-encodeable.
   * Should be null on error.
   *
   * @var mixed
   */
  public $result;


  /**
   * Error in operation. SHOULD be null if no error.
   * If there is an error,
   *
   * @var \Common\JsonRpc\Error
   */
  public $error;

}
