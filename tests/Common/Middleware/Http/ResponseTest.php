<?php

namespace Common\Middleware\Http;

class ResponseTest extends  \PHPUnit_Framework_TestCase
{
  public function test_get_header()
  {
    $request = new Request();
    $request->headers = array( 'first' => 'firstvalue',
                               'second' => 'secondvalue; withargs');
    $this->assertEquals( 'firstvalue', $request->get_header( 'FIRST' ) );
    $this->assertEquals( 'secondvalue; withargs', $request->get_header( 'SECOND' ) );
    $this->assertEquals( 'secondvalue', $request->get_header( 'second', false ) );
  }
}
