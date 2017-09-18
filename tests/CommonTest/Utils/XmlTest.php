<?php

namespace CommonTest\Utils;


/**
 * Test for class "Xml"
 */
class XmlTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Test for method "get_xml_from_mixed_array"
   */
  public function test_get_xml_from_mixed_array()
  {
    $array =
    [
      'item1' => 'foo1',
      'item2' => 'foo2'
    ];

    $expected_xml = <<<XML
      <root>
        <item1>foo1</item1>
        <item2>foo2</item2>
      </root>
XML;

    $xml = \Common\Utils\Xml::get_xml_from_mixed_array( $array );
    $this->assertXmlStringEqualsXmlString( $expected_xml, $xml );
  }



  /**
   * Test for method "is_valid_xml"
   */
  public function test_is_valid_xml()
  {
    $xml = <<<XML
      <root>
        <item1>foo1</item1>
        <item2>foo2</item2>
      </root>
XML;

    $is_valid = \Common\Utils\Xml::is_valid_xml( $xml );
    $this->assertTrue( $is_valid );

    $xml = "wrong xml";
    $is_valid = \Common\Utils\Xml::is_valid_xml( $xml );
    $this->assertFalse( $is_valid );

    $is_valid = \Common\Utils\Xml::is_valid_xml( $xml, $errors );
    $this->assertFalse( $is_valid );
    $this->assertTrue( is_array( $errors ) );
  }

}
