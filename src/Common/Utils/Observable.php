<?php

namespace Common\Utils;

//http://dhotson.tumblr.com/post/14608885912/whats-new-in-php-5-4

/**
 * Trait to add Observable methods
 *
 */
trait Observable
{

  /**
   * Listeners
   *
   * @var array
   */
  private $_listeners = array();



  /**
   * Adds a calllback to execute on event fired
   *
   * @param string $event
   * @param array|callback $callback A function or an array composed of either ("object", "method") or ($object, "method").
   */
  public function on( $event, $callback )
  {
    if( !isset( $this->_listeners[ $event ] ) )
    {
      $this->_listeners[ $event ] = array();
    }

    $this->_listeners[ $event ][] = $callback;
  }



  /**
   * Fires an event
   *
   * @param string $event
   * @param array $data
   */
  public function fireEvent( $event, $data )
  {
    if( isset( $this->_listeners[ $event ] ) )
    {
      foreach( $this->_listeners[ $event ] as $listener )
      {
        $cb = $listener[ 0 ];

        if( is_callable( $handler ) )
        {
          call_user_func_array( $handler, array( $this, $data ) );
        }
        else
        {
          if( is_array( $handler ) && count( $handler ) == 2 )
          {
            list( $objectname, $method ) = $handler;

            if( is_string( $objectname ) && is_string( $method ) )
            {
              $obj = new $objectname();
              call_user_func_array( array( $obj, $method ), array( $this, $data ) );
            }
          }
        }
      }
    }
  }

}
