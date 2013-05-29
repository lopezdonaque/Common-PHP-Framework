<?php

namespace Common\Api\Annotations;


/**
 * Auth annotation
 *
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
class Auth
{

  /**
   * Defines if the target method requires authentication or not
   *
   * @var bool
   */
  public $required = true;


  /**
   * Access types
   *
   * @var array
   */
  public $access;

}
