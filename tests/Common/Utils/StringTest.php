<?php

/**
 * String test case
 *
 */
class StringTest extends \PHPUnit_Framework_TestCase
{


  /**
   * Tests \Common\Utils\String::strcut()
   */
  public function testStrcut()
  {
    $this->assertEquals( "", \Common\Utils\String::strcut( "", 4 ) );
    $this->assertEquals( "T...", \Common\Utils\String::strcut( "Test string", 4 ) );
    $this->assertEquals( "Test string", \Common\Utils\String::strcut( "Test string", 12 ) );
  }


  /**
   * Tests \Common\Utils\String::highlight()
   */
  public function testHighlight()
  {
    $this->assertEquals( 'Test string', \Common\Utils\String::highlight( 'foo', 'Test string' ) );
    $this->assertEquals( "<em>Test</em> string", \Common\Utils\String::highlight( 'test', 'Test string' ) );
    $this->assertEquals( "Test <em>string</em>", \Common\Utils\String::highlight( 'string', 'Test string' ) );
  }


  /**
   * Tests \Common\Utils\String::nl2remove()
   */
  public function testNl2remove()
  {
    $this->assertEquals( "String", \Common\Utils\String::nl2remove( "String" ) );
    $this->assertEquals( "StringwithCRandLF", \Common\Utils\String::nl2remove( "String\nwith\nCR\rand\nLF" ) );
  }


  /**
   * Tests \Common\Utils\String::format_name()
   */
  public function testFormat_name()
  {
    $this->assertEquals( 'Perez, Juan', \Common\Utils\String::format_name( 'Juan', 'Perez' ) );
    $this->assertEquals( 'Juan', \Common\Utils\String::format_name( 'Juan', '' ) );
    $this->assertEquals( '', \Common\Utils\String::format_name( '', '' ) );
  }


  /**
   * Tests \Common\Utils\String::js_escape()
   */
  public function testJs_escape()
  {
    $this->assertEquals( '', \Common\Utils\String::js_escape( '' ) );
    $this->assertEquals( 'abc', \Common\Utils\String::js_escape( 'abc' ) );
    $this->assertEquals( 'a\"bc', \Common\Utils\String::js_escape( 'a"bc' ) );
    $this->assertEquals( "\\'abc", \Common\Utils\String::js_escape( "'abc" ) );
    $this->assertEquals( '\<abc\>', \Common\Utils\String::js_escape( '<abc>' ) );
    $this->assertEquals( '\<script\> BAD \</script\>', \Common\Utils\String::js_escape( '<script> BAD </script>' ) );
  }


  /**
   * Tests \Common\Utils\String::replace_variables()
   */
  public function testReplace_variables()
  {
    $this->assertEquals( 'Mary had a little lamb', \Common\Utils\String::replace_variables( 'Mary had a little lamb', array() ) );
    $this->assertEquals( 'Mary had a bloody chainsaw', \Common\Utils\String::replace_variables( 'Mary had a little lamb', array(
        'little' => 'bloody', 'lamb' => 'chainsaw' ) ) );
  }


  /**
   * Tests \Common\Utils\String::starts_with()
   */
  public function testStarts_with()
  {
    $this->assertTrue( \Common\Utils\String::starts_with( '123', '1' ) );
    $this->assertFalse( \Common\Utils\String::starts_with( '321', '1' ) );
  }


  /**
   * Tests \Common\Utils\String::ends_with()
   */
  public function testEnds_with()
  {
    $this->assertTrue( \Common\Utils\String::ends_with( '123', '3' ) );
    $this->assertFalse( \Common\Utils\String::ends_with( '321', '3' ) );
  }


  /**
   * Tests \Common\Utils\String::get_bytes()
   */
  public function testGet_bytes()
  {
    $this->assertEquals( 1, \Common\Utils\String::get_bytes( '1' ) );
    $this->assertEquals( 1024, \Common\Utils\String::get_bytes( '1k' ) );
    $this->assertEquals( 1024 * 1024, \Common\Utils\String::get_bytes( '1m' ) );
    $this->assertEquals( 1024 * 1024 * 1024, \Common\Utils\String::get_bytes( '1g' ) );
    $this->assertEquals( 3 * 1024, \Common\Utils\String::get_bytes( '3k' ) );
    $this->assertEquals( 4 * 1024 * 1024, \Common\Utils\String::get_bytes( '4m' ) );
    $this->assertEquals( 5 * 1024 * 1024 * 1024, \Common\Utils\String::get_bytes( '5g' ) );
  }


  /**
   * Tests \Common\Utils\String::parse_template()
   */
  public function testParse_template()
  {
    $this->assertEquals( '{test}', \Common\Utils\String::parse_template( '{test}', array() ) );
    $this->assertEquals( 'foo', \Common\Utils\String::parse_template( '{test}', array(
        'test' => 'foo' ) ) );
    $this->assertEquals( 'This foo is a bar', \Common\Utils\String::parse_template( 'This {thing} is a {test}', array(
        'thing' => 'foo', 'test' => 'bar' ) ) );
  }

}

