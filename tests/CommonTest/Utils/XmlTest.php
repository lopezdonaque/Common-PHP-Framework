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
    $array = array
    (
      'item1' => 'foo1',
      'item2' => 'foo2'
    );

    $expected_xml = <<<XML
      <root>
        <item1>foo1</item1>
        <item2>foo2</item2>
      </root>
XML;

    $xml = \Common\Utils\Xml::get_xml_from_mixed_array( $array );
    $this->assertXmlStringEqualsXmlString( $expected_xml, $xml );
  }

}
