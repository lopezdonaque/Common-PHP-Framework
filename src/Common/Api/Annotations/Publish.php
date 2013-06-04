<?php

namespace Common\Api\Annotations;


/**
 * Publish event annotation
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class Publish
{

  /**
   * Override event name, by default the method invoked
   *
   * @var string
   */
  public $event;


  /**
   * Channel name
   *
   * @var string
   */
  public $channel;


  /**
   * Mandatory path annotation. External id of the object being altered
   *
   * @var string
   */
  public $id;


  /**
   * Payload expression
   *
   * @var string
   */
  public $payload;

}
