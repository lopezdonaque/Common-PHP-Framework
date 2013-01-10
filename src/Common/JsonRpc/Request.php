<?php

namespace Common\JsonRpc;


/**
 * A JSON-RPC request, according to jsonrpc.org/spec.html
 * A "extensions" member is added for additional members of the  request object.
 *
 */
class Request
{

  /**
   * JSON/RPC version
   *
   * @var string
   */
  public $jsonrpc;


  /**
   * RPC Request id
   *
   * @var string
   */
  public $id;


  /**
   * Called Method
   *
   * @var string
   */
  public $method;


  /**
   * Parsed arguments
   *
   * @var array
   */
  public $params;


  /**
   * Any extensions passed in the JSON object (nonstandard elements).
   * Indexed by its identifier in the object.
   *
   * @var mixed[string]
   */
  public $extensions;



  /**
   * Is this request a notification (i.e. has no id)?
   *
   * @return bool
   */
  public function is_notification()
  {
    return $this->id == null;
  }

}
