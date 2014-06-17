<?php

namespace Common\Middleware\Api;


/**
 * Response encoding for API JSONRPC requests
 *
 */
class JsonRpcResponseEncoder extends BaseResponseEncoder
{

  /**
   * Returns the output
   *
   * @param \Common\Middleware\Api\Request $request
   * @param \Common\Middleware\Api\Response $response
   * @return mixed
   */
  protected function _get_output( &$request, &$response )
  {
    if( $response->api_exception )
    {
      $output = new \stdClass();
      $output->transaction_id = $request->api_transaction_id;
      $output->error = true;
      $output->message = $response->api_exception->getMessage();
      $output->code = $response->api_exception->getCode();
      $output->type = ''; // ONLY for backward compatibility with JavaConnector
    }
    else
    {
      $output = $response->api_return_value;
    }

    return $output;
  }

}

