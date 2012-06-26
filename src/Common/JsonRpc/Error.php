<?php

namespace Common\JsonRpc;

/**
 * When a rpc call encounters an error, the Response Object MUST contain the error
 * member with a value that is a Object with the following members
 *
 * @author  Androme Iberica 2011
 * @package common
 */
class Error
{

  /**
   * Parse error. Invalid JSON was received by the server.
   * An error occurred on the server while parsing the JSON text.
   *
   * @var integer
   */
  const PARSE_ERROR = - 32700;


  /**
   * Invalid Request. The JSON sent is not a valid Request object.
   *
   * @var integer
   */
  const INVALID_REQUEST = - 32600;


  /**
   * Method not found. The method does not exist / is not available
   *
   * @var integer
   */
  const METHOD_NOT_FOUND = - 32601;


  /**
   * Invalid params. Invalid method parameter(s).
   *
   * @var integer
   */
  const INVALID_PARAMS = - 32602;


  /**
   * Internal error. Internal JSON-RPC error.
   *
   * @var integer
   */
  const INTERNAL_ERROR = - 32603;


  /**
   * Server error  Reserved for implementation-defined server-errors.
   *
   * @var integer
   */
  const SERVER_ERROR_BASE = - 32000;


  /**
   * A Number that indicates the error type that occurred.
   * This MUST be an integer.
   *
   * @var integer
   */
  public $code;


  /**
   * Error message
   *
   * @var string
   */
  public $message;


  /**
   * Data
   *
   * @var mixed
   */
  public $data;
}
