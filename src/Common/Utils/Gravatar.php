<?php

namespace Common\Utils;


/**
 * Class to manage Gravatar URLs
 *
 */
class Gravatar
{
  const GRAVATAR_URL = 'http://www.gravatar.com/avatar/';
  const GRAVATAR_SECURE_URL = 'https://secure.gravatar.com/avatar/';


  /**
   * Get the Gravatar icon URL with the given size and parameters
   *
   * @static
   * @param string $email the user email address
   * @param int $size [optional] the preferred size (80,120 up to 512), default 120
   * @param string $default [optional] the default icon in case there is none "retro" by default. Use "404" if you don't need a default image.
   * @param bool $secure [optional] Defines if return secure URL or not
   * @param string $rating [optional] the accepted rating of the icon (G,PG,R...). G by default.
   * @return string the URL
   */
  public static function get_url( $email, $size = 120, $default = 'retro', $secure = true, $rating= 'g' )
  {
    $url = $secure ? self::GRAVATAR_SECURE_URL : self::GRAVATAR_URL;

    $query_params = http_build_query(
    [
      's' => $size,
      'r' => $rating,
      'd' => $default
    ]);

    $url .= md5( $email ) . '?' . $query_params;
    return $url;
  }

}
