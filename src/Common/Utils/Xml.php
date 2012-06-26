<?php

namespace Common\Utils;


/**
 * Class to manage XML utility methods
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
   * @return string
   */
  public static function get_xml_from_mixed_array( $array, $start_element = 'root' )
  {
    $xml = new \XmlWriter();
    $xml->openMemory();
    $xml->startDocument( '1.0', 'UTF-8' );
    $xml->startElement( $start_element );
    self::_write( $xml, $array );
    $xml->endElement();
    return $xml->outputMemory( true );
  }



  /**
   * Writes XML data
   *
   * @param \XMLWriter $xml
   * @param mixed $data
   */
  private static function _write( \XMLWriter $xml, $data )
  {
    foreach( $data as $key => $value )
    {
      // Numeric keys are invalid
      if( is_numeric( $key ) )
      {
        $key = 'unknownNode_' . (string) $key;
      }

      if( is_array( $value ) )
      {
        $xml->startElement( $key );
        self::_write( $xml, $value );
        $xml->endElement();
        continue;
      }

      if( is_object( $value ) )
      {
        $xml->startElement( $key );
        self::_write( $xml, \Common\Utils\Arrays::object_to_array( $value ) );
        $xml->endElement();
        continue;
      }

      if( ! $value )
      {
        $xml->writeElement( $key, '' );
      }
      else
      {
        $xml->writeElement( $key, $value );
      }
    }
  }



  /**
   * Validates and returns XML element from text
   *
   * @param string $text
   * @param string $schema
   * @return \SimpleXMLElement
   */
  public static function get_and_validate_xml_from_text( $text, $schema )
  {
    $doc = new \DOMDocument();

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

}
