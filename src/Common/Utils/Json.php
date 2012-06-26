<?php

namespace Common\Utils;


/**
 * Utility methods for JSON
 *
 */
class Json
{

  /**
   * Returns if a string is a json or not
   *
   * @param string $string
   * @return bool
   */
  public static function is_json( $string )
  {
    json_decode( $string );
    return ( json_last_error() == JSON_ERROR_NONE );
  }



  /**
   * Returns a description of the last json error
   *
   * @return string
   */
  public static function get_json_last_error()
  {
    switch( \json_last_error() )
    {
      case JSON_ERROR_DEPTH:
        return 'Maximum stack depth exceeded';

      case JSON_ERROR_CTRL_CHAR:
        return 'Unexpected control character found';

      case JSON_ERROR_SYNTAX:
        return 'Syntax error, malformed JSON';

      case JSON_ERROR_NONE:
        return '';

      case 5: //JSON_ERROR_UTF8 (PHP 5.3.1)
        return 'Malformed UTF-8 characters, possibly incorrectly encoded';
    }

    return '';
  }

}
