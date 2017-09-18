<?php

namespace CommonTest\Middleware\Http;


/**
 * Test for Middleware HTTP Request
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{

  /**
   * Test for get_header
   */
  public function test_get_header()
  {
    $request = new \Common\Middleware\Http\Request();
    $request->headers = [ 'first' => 'firstvalue', 'second' => 'secondvalue; withargs' ];
    $this->assertEquals( 'firstvalue', $request->get_header( 'FIRST' ) );
    $this->assertEquals( 'secondvalue; withargs', $request->get_header( 'SECOND' ) );
    $this->assertEquals( 'secondvalue', $request->get_header( 'second', false ) );
  }

}
