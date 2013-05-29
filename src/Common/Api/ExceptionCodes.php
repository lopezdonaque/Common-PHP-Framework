<?php

namespace Common\Api;


/**
 * Api exception codes
 *
 */
class ExceptionCodes
{

  #region General errors 0-99

  /**
   * Internal error
   *
   * @var int
   */
  const GENERAL_INTERNAL_ERROR = 0;


  /**
   * Database exception
   *
   * @var int
   */
  const GENERAL_DATABASE_EXCEPTION = 1;


  /**
   * Invalid argument
   *
   * @var int
   */
  const GENERAL_INVALID_ARGUMENT = 2;


  /**
   * Data not found
   *
   * @var int
   */
  const GENERAL_DATA_NOT_FOUND = 4;


  /**
   * Data already exists
   *
   * @var int
   */
  const GENERAL_DATA_ALREADY_EXISTS = 5;

  #endregion


  #region Call request errors 100-109

  /**
   * Missing transaction id
   *
   * @var int
   */
  const CALL_MISSING_TRANSACTION_ID = 100;


  /**
   * Missing entity
   *
   * @var int
   */
  const CALL_MISSING_ENTITY = 101;


  /**
   * Missing method
   *
   * @var int
   */
  const CALL_MISSING_METHOD = 102;


  /**
   * Missing arguments
   *
   * @var int
   */
  const CALL_MISSING_ARGUMENTS = 103;


  /**
   * Missing format
   *
   * @var int
   */
  const CALL_MISSING_FORMAT = 104;


  /**
   * Missing jsonp callback
   *
   * @var int
   */
  const CALL_MISSING_JSONP_CALLBACK = 105;


  /**
   * Entity not found
   *
   * @var int
   */
  const CALL_ENTITY_NOT_FOUND = 106;


  /**
   * Method not found
   *
   * @var int
   */
  const CALL_METHOD_NOT_FOUND = 107;

  #endregion


  #region Authentication errors 200-209

  /**
   * Invalid credentials
   *
   * @var int
   */
  const AUTHENTICATION_INVALID_CREDENTIALS = 200;


  /**
   * Token not found
   *
   * @var int
   */
  const AUTHENTICATION_TOKEN_NOT_FOUND = 201;


  /**
   * SSL/TLS access for authentication is mandatory
   *
   * @var int
   */
  const AUTHENTICATION_SECURE_REQUIRED = 203;


  /**
   * Access not allowed
   *
   * @var int
   */
  const AUTHENTICATION_ACCESS_NOT_ALLOWED = 204;

  #endregion

}
