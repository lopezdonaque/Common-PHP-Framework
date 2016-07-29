<?php

namespace Common\Api;


/**
 * Api exception codes
 *
 */
class ExceptionCodes
{

  /**#@+
   * General errors 0-99
   *
   * @var int
   */

  /** Internal error */
  const GENERAL_INTERNAL_ERROR = 0;

  /** Database exception */
  const GENERAL_DATABASE_EXCEPTION = 1;

  /** Invalid argument */
  const GENERAL_INVALID_ARGUMENT = 2;

  /** Data not found */
  const GENERAL_DATA_NOT_FOUND = 4;

  /** Data already exists */
  const GENERAL_DATA_ALREADY_EXISTS = 5;

  /** Denied access */
  const GENERAL_DENIED_ACCESS = 6;
  /**#@-*/



  /**
   * Call request errors 100-109
   *
   * @var int
   */

  /** Missing transaction id */
  const CALL_MISSING_TRANSACTION_ID = 100;

  /** Missing entity */
  const CALL_MISSING_ENTITY = 101;

  /** Missing method */
  const CALL_MISSING_METHOD = 102;

  /** Missing arguments */
  const CALL_MISSING_ARGUMENTS = 103;

  /** Missing format */
  const CALL_MISSING_FORMAT = 104;

  /** Missing jsonp callback */
  const CALL_MISSING_JSONP_CALLBACK = 105;

  /** Entity not found */
  const CALL_ENTITY_NOT_FOUND = 106;

  /** Method not found */
  const CALL_METHOD_NOT_FOUND = 107;
  /**#@-*/



  /**
   * Authentication errors 200-209
   *
   * @var int
   */

  /** Invalid credentials */
  const AUTHENTICATION_INVALID_CREDENTIALS = 200;

  /** Token not found */
  const AUTHENTICATION_TOKEN_NOT_FOUND = 201;

  /** Token expired */
  const AUTHENTICATION_TOKEN_EXPIRED = 202;

  /** SSL/TLS access for authentication is mandatory */
  const AUTHENTICATION_SECURE_REQUIRED = 203;

  /** Access not allowed */
  const AUTHENTICATION_ACCESS_NOT_ALLOWED = 204;
  /**#@-*/

}
