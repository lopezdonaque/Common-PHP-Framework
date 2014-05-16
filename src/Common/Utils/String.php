<?php

namespace Common\Utils;


/**
 * Class to manage string methods
 *
 */
class String
{

  /**
   * Retails a string and add '...' if necessary
   *
   * @param string $value
   * @param integer $max_length
   * @return string containing retailed string if needed.
   */
  public static function strcut( $value, $max_length )
  {
    if( mb_strlen( $value ) > $max_length )
    {
      $value = mb_substr( $value, 0, $max_length - 3 );
      $value .= '...';
    }

    return $value;
  }



  /**
   * Returns in HTML bold style all the coincidences in a text, case insensitively
   *
   * @param string $search
   * @param string $text
   * @return string
   */
  public static function highlight( $search, $text )
  {
    if( $search == '' )
    {
      return $text;
    }

    return preg_replace( '/' . preg_quote( $search, '/' ) . '/i', '<em>\\0</em>', $text );
  }



  /**
   * Removes \r \n from a text to return a single line string.
   *
   * @param string $text Text to be parsed and cleaned.
   * @return string
   */
  public static function nl2remove( $text )
  {
    $text = str_replace( "\r", '', $text );
    $text = str_replace( "\n", '', $text );
    return $text;
  }



  /**
   * Escape a string for JavaScript (quotes escaped, \' or \")
   *
   * @param string $str
   * @return string
   */
  public static function js_escape( $str )
  {
    $ret = str_replace( '"', '\"', $str );
    $ret = str_replace( "'", "\\'", $ret );
    $ret = str_replace( "<", "\\<", $ret );
    $ret = str_replace( ">", "\\>", $ret );

    return $ret;
  }



  /**
   * Returns the string replacing all the variables with values
   *
   * @param string $text
   * @param array[string]string $vars
   * @return string
   */
  public static function replace_variables( $text, $vars )
  {
    foreach( $vars as $name => $value )
    {
      $text = str_replace( $name, $value, $text );
    }

    return $text;
  }



  /**
   * Returns if the string start with required text
   *
   * @param string $haystack
   * @param string $needle
   * @return boolean
   */
  public static function starts_with( $haystack, $needle )
  {
    $length = strlen($needle);
    return ( substr( $haystack, 0, $length ) === $needle );
  }



  /**
   * Returns if the string ends with required string
   *
   * @param string $haystack
   * @param string $needle
   * @return boolean
   */
  public static function ends_with( $haystack, $needle )
  {
    $length = strlen($needle);
    $start  = $length * -1; // negative
    return ( substr( $haystack, $start ) === $needle );
  }



  /**
   * Returns bytes depending on php size string. eg. 1k = 1024, 1m = 1024*1024...
   *
   * @param string $val
   * @return integer
   */
  public static function get_bytes( $val )
  {
    $val = trim( $val );
    $last = strtolower( substr( $val, -1 ) );

    if( $last == 'g' )
      $val = $val * 1024 * 1024 * 1024;

    if( $last == 'm' )
      $val = $val * 1024 * 1024;

    if( $last == 'k' )
      $val = $val * 1024;

    return $val;
  }



  /**
   * Parse template text and return formatted text
   *
   * For example:
   * $html = \Common\Utils\String::parse_template( '<span>{name}</span>', array( 'name' => $enduser_name ) );
   *
   * @param string $text
   * @param string[string] $data
   * @return string
   */
  public static function parse_template( $text, $data )
  {
    foreach( $data as $key => $value )
    {
      $text = str_replace( '{' . $key . '}', $value, $text );
    }

    return $text;
  }

}
