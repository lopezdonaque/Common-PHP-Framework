<?php

namespace Common\Utils;


/**
 * XML utility methods
 *
 */
class Xml
{

  /**
   * Converts an associative array with objects, arrays or simple elements inside into xml string
   * http://snippets.dzone.com/posts/show/3391
   *
   * @param array $array
   * @param string $start_element
   * @param string $numeric_index_prefix
   * @return string
   * @throws \Exception
   */
  public static function get_xml_from_mixed_array( $array, $start_element = 'root', $numeric_index_prefix = 'item_' )
  {
    $xml = new \XmlWriter();
    $xml->openMemory();
    $xml->startDocument( '1.0', 'UTF-8' );
    $xml->startElement( $start_element );
    self::_write( $xml, $array, $numeric_index_prefix );
    $xml->endElement();

    $xml_string = $xml->outputMemory( true );

    if( !self::is_valid_xml( $xml_string, $errors ) )
    {
      throw new \Exception( "Invalid generated XML [$xml_string] [{$errors[0]}]" );
    }

    return $xml_string;
  }



  /**
   * Writes XML data
   *
   * @param \XMLWriter $xml
   * @param mixed $data
   * @param string $numeric_index_prefix
   */
  private static function _write( \XMLWriter $xml, $data, $numeric_index_prefix )
  {
    foreach( $data as $key => $value )
    {
      // Numeric keys are invalid
      if( is_numeric( $key ) )
      {
        $key = $numeric_index_prefix . (string) $key;
      }

      // Empty keys are invalid
      if( !$key )
      {
        $key = 'empty_' . date( 'YmdHis' ) . '_' . mt_rand( 10000, 99999 );
      }

      if( is_array( $value ) || is_object( $value ) )
      {
        // Start element checking if the key is valid
        if( @$xml->startElement( $key ) === false )
        {
          $key = 'wrong_key_' . date( 'YmdHis' ) . '_' . mt_rand( 10000, 99999 );
          $xml->startElement( $key );
        }

        if( is_array( $value ) )
        {
          self::_write( $xml, $value, $numeric_index_prefix );
        }
        else
        {
          self::_write( $xml, \Common\Utils\Arrays::object_to_array( $value ), $numeric_index_prefix );
        }

        $xml->endElement();
        continue;
      }

      // Use "@" to ignore "Invalid Element Name" errors
      @$xml->writeElement( $key, is_bool( $value ) ? var_export( $value, true ) : strval( $value ) );
    }
  }



  /**
   * Validates and returns XML element from text
   *
   * @param string $text
   * @param string $schema
   * @return \SimpleXMLElement
   * @throws \Exception
   */
  public static function get_and_validate_xml_from_text( $text, $schema )
  {
    // Trim node value
    $text = preg_replace( "/> +<\//", "></", $text );

    $doc = new \DOMDocument();
    $doc->preserveWhiteSpace = false;

    if( !$doc->loadXML( $text ) )
    {
      throw new \Exception( 'Cannot load given XML:' . $text );
    }

    libxml_use_internal_errors( true );

    if( !$doc->schemaValidate( $schema ) )
    {
      $errors = libxml_get_errors();
      $error_messages = array_map( function( $obj ){ return $obj->message; }, $errors );
      libxml_clear_errors();
      throw new \Exception( sprintf( 'Errors validation XML: [%s]. Given XML is not valid: %s', implode( ',', $error_messages ), $text ) );
    }

    return simplexml_load_string( $doc->saveXML() );
  }



  /**
   * Compress an XML string
   *
   * @param string $xml
   * @return string
   */
  public static function compress_xml( $xml )
  {
    return implode( '', array_map( 'trim', explode( "\n", $xml ) ) );
  }



  /**
   * Returns if the xml is valid or not
   *
   * @param string $xml_str
   * @param string[] $errors
   * @return bool
   */
  public static function is_valid_xml( $xml_str, &$errors = null )
  {
    libxml_use_internal_errors( true );
    $is_valid = true;
    simplexml_load_string( $xml_str );
    $errors = libxml_get_errors();

    if( !empty( $errors ) )
    {
      $errors = array_map( function( $error ){ return trim( $error->message ); }, $errors );
      libxml_clear_errors();
      $is_valid = false;
    }

    libxml_use_internal_errors( false );
    return $is_valid;
  }

}
