<?php

namespace CommonTest\Utils;


/**
 * Test for class "Arrays"
 */
class ArraysTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Test for method "utf8_decode_array"
   */
  public function test_utf8_decode_array()
  {
    $outarray = array( iconv( 'UTF-8', 'ISO-8859-1', 'àçÖ' ) );
    $inarray = array( 'àçÖ' );
    \Common\Utils\Arrays::utf8_decode_array( $inarray );
    $this->assertEquals( $inarray, $outarray );
  }



  /**
   * Test for method "utf8_encode_array"
   */
  public function test_utf8_encode_array()
  {
    $inarray = array( iconv( 'UTF-8', 'ISO-8859-1', 'àçÖ' ) );
    $outarray = array( 'àçÖ' );
    \Common\Utils\Arrays::utf8_encode_array( $inarray );
    $this->assertEquals( $inarray, $outarray );
  }



  /**
   * Test for method "is_associative"
   */
  public function test_is_associative()
  {
    $this->assertFalse( \Common\Utils\Arrays::is_associative( null ) );
    $this->assertFalse( \Common\Utils\Arrays::is_associative( array() ) );
    $this->assertTrue( \Common\Utils\Arrays::is_associative( array( 'a' => 1 ) ) );
    $this->assertFalse( \Common\Utils\Arrays::is_associative( array( 1, 2, 3 ) ) );
  }



  /**
   * Test for method "array_key_multi_sort"
   */
  public function testArray_key_multi_sort()
  {
    $empty = array();
    $oneelment = array( 'k' => 'a' );
    $several = array( 'a' => 'a', 'c' => 'c', 'b' => 'b' );
    \Common\Utils\Arrays::array_key_multi_sort( $empty, 'k' );
    $this->assertEquals( 0, count( $empty ) );

    \Common\Utils\Arrays::array_key_multi_sort( $oneelment, 'k' );
    $this->assertEquals( 1, count( $oneelment ) );

    \Common\Utils\Arrays::array_key_multi_sort( $several, 'k' );
    $this->assertEquals( 3, count( $several ) );
    $this->assertEquals( array( 'a' => 'a', 'b' => 'b', 'c' => 'c' ), $several );
  }



  /**
   * Test for method "array_element_multi_sort"
   */
  public function testArray_element_multi_sort()
  {
    $empty = array();
    \Common\Utils\Arrays::array_element_multi_sort( $empty, 'k' );
    $this->assertEquals( 0, count( $empty ) );

    $ela = new \stdClass();
    $ela->value = 'a';

    $elb = new \stdClass();
    $elb->value = 'b';

    $elc = new \stdClass();
    $elc->value = 'c';

    $several = array( $ela, $elc, $elb );

    $ela2 = new \stdClass();
    $ela2->value = 'a';

    $elb2 = new \stdClass();
    $elb2->value = 'b';

    $elc2 = new \stdClass();
    $elc2->value = 'c';

    $expected = array( $ela2, $elb2, $elc2 );

    \Common\Utils\Arrays::array_element_multi_sort( $several, 'value' );
    $this->assertEquals( 3, count( $several ) );
    $this->assertEquals( $expected[ 0 ], $several[ 0 ] );
    $this->assertEquals( $expected[ 2 ], $several[ 1 ] );
    $this->assertEquals( $expected[ 1 ], $several[ 2 ] );


    $ela = new \stdClass();
    $ela->value = 0;

    $elb = new \stdClass();
    $elb->value = 1;

    $elc = new \stdClass();
    $elc->value = 2;

    $several = array( $ela, $elc, $elb );

    $ela2 = new \stdClass();
    $ela2->value = 0;

    $elb2 = new \stdClass();
    $elb2->value = 1;

    $elc2 = new \stdClass();
    $elc2->value = 2;

    $expected = array( $ela2, $elb2, $elc2 );

    \Common\Utils\Arrays::array_element_multi_sort( $several, 'value' );
    $this->assertEquals( 3, count( $several ) );
    $this->assertEquals( $expected[ 0 ], $several[ 0 ] );
    $this->assertEquals( $expected[ 2 ], $several[ 1 ] );
    $this->assertEquals( $expected[ 1 ], $several[ 2 ] );
  }



  /**
   * Test for method "array_map_assoc"
   */
  public function test_array_map_assoc()
  {
    $original = array( 'one' => 1, 'two' => 2, 'three' => 3 );
    $result_toupper = array( 'ONE' => 1, 'TWO' => 2, 'THREE' => 3 );
    $result_toupper_plusone = array( 'ONE' => 2, 'TWO'   => 3, 'THREE' => 4 );

    $toupper = \Common\Utils\Arrays::array_map_assoc( $original, function( $k, $v ){ return array( strtoupper( $k ), $v ); } );
    $this->assertEquals( $result_toupper, $toupper, "Failed mapping keys to uppercase" );

    $toupper_plusone = \Common\Utils\Arrays::array_map_assoc( $original, function( $k, $v ){ return array( strtoupper( $k ), $v + 1 ); } );
    $this->assertEquals( $result_toupper_plusone, $toupper_plusone, "Failed mapping keys to uppercase and values+1" );
  }

}
