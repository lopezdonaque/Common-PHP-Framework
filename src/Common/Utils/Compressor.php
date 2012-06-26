<?php

namespace Common\Utils;


/**
 * Static class (singleton) to compress a code string (html, css, javascript)
 *
 * Example usage:
 *
 * $contents = \Common\Utils\Compressor::compress_css( $contents );
 *
 */
class Compressor
{

  /**
   * Remove tabs, spaces, newlines, etc.
   *
   * @param string $contents
   * @return string
   */
  public static function to_one_line( $contents )
  {
    $contents = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $contents );
    return $contents;
  }



  /**
   * Remove css comments
   *
   * @param string $contents
   * @return string
   */
  public static function remove_css_comments( $contents )
  {
    $contents = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents );
    return $contents;
  }



  /**
   * Remove javascript comments
   *
   * @param string $contents
   * @return string
   */
  public static function remove_javascript_comments( $contents )
  {
    $contents = preg_replace( "/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", '', $contents );
    return $contents;
  }



  /**
   * Remove html comments
   *
   * @param string $contents
   * @return string
   */
  public static function remove_html_comments( $contents )
  {
    $contents = preg_replace( "/<!--.*-->/U", "", $contents );
    return $contents;
  }



  /**
   * Compress css code
   *
   * @param string $contents
   * @return string
   */
  public static function compress_css( $contents )
  {
    $contents = self::remove_css_comments( $contents );
    $contents = self::to_one_line( $contents );
    return $contents;
  }



  /**
   * Compress javascript code
   *
   * @param string $contents
   * @return string
   */
  public static function compress_javascript( $contents )
  {
    $contents = self::remove_javascript_comments( $contents );
    $contents = self::to_one_line( $contents );
    return $contents;
  }



  /**
   * Compress html code
   *
   * @param string $contents
   * @return string
   */
  public static function compress_html( $contents )
  {
    $contents = self::remove_html_comments( $contents );
    $contents = self::to_one_line( $contents );
    return $contents;
  }

}
