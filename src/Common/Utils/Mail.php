<?php

namespace Common\Utils;


/**
 * Mail utilities
 *
 */
class Mail
{

  /**
   * Sends an email using ZF2
   *
   * @param array $options
   */
  public static function send( $options )
  {
    self::_validate_options( $options );
    $message = self::_get_message( $options );

    // Create the multipart/alternative Message (text + html)
    $alternative_mime = new \Zend\Mime\Message();
    self::_add_content_parts( $alternative_mime, $options );

    // Create the multipart/alternative Part
    $alternative_part = new \Zend\Mime\Part( $alternative_mime->generateMessage() );
    $alternative_part->type = \Zend\Mime\Mime::MULTIPART_ALTERNATIVE;
    $alternative_part->boundary = $alternative_mime->getMime()->boundary();
    $alternative_part->charset = 'utf-8';

    $body = new \Zend\Mime\Message();
    $body->addPart( $alternative_part );
    self::_add_attachments( $body, $options );
    $message->setBody( $body );

    if( self::_has_inline_content( $options ) )
    {
      $message->getHeaders()->get( 'content-type' )->setType( 'multipart/related' );
    }
    else
    {
      $message->getHeaders()->get( 'content-type' )->setType( 'multipart/mixed' );
    }

    $transport = new \Zend\Mail\Transport\Sendmail();
    $transport->send( $message );
  }



  /**
   * Returns the message instance
   *
   * @param array $options
   * @return \Zend\Mail\Message
   */
  private static function _get_message( $options )
  {
    $message = new \Zend\Mail\Message();
    $message->setFrom( $options[ 'from' ], @$options[ 'from_name' ] ?: null );
    $message->addTo( $options[ 'to' ] );
    $message->setSubject( $options[ 'subject' ] );
    $message->setEncoding( 'UTF-8' );

    if( isset( $options[ 'reply_to' ] ) )
    {
      $message->addReplyTo( $options[ 'reply_to' ] );
    }

    if( isset( $options[ 'cc' ] ) )
    {
      $message->addCc( $options[ 'cc' ] );
    }

    if( isset( $options[ 'bcc' ] ) )
    {
      $message->addBcc( $options[ 'bcc' ] );
    }

    if( isset( $options[ 'headers' ] ) )
    {
      foreach( $options[ 'headers' ] as $key => $value )
      {
        $message->getHeaders()->addHeaderLine( $key, $value );
      }
    }

    return $message;
  }



  /**
   * Adds attachments to message
   *
   * @param \Zend\Mime\Message $part
   * @param array $options
   */
  private static function _add_attachments( $part, $options )
  {
    foreach( $options[ 'attachments' ] as $attachment )
    {
      $file_part = new \Zend\Mime\Part( $attachment[ 'contents' ] );
      $file_part->type = @$attachment[ 'type' ] ?: \Zend\Mime\Mime::TYPE_OCTETSTREAM;
      $file_part->disposition = @$attachment[ 'disposition' ] ?: \Zend\Mime\Mime::DISPOSITION_ATTACHMENT;
      $file_part->encoding = @$attachment[ 'encoding' ] ?: \Zend\Mime\Mime::ENCODING_BASE64;
      $file_part->filename = @$attachment[ 'filename' ] ?: '';
      $file_part->id = @$attachment[ 'id' ] ?: null;

      $part->addPart( $file_part );
    }
  }



  /**
   * Adds contents (text and html) to message
   *
   * @param \Zend\Mime\Message $alternative_mime
   * @param array $options
   */
  private static function _add_content_parts( $alternative_mime, $options )
  {
    if( isset( $options[ 'text' ] ) )
    {
      $text_part = new \Zend\Mime\Part( $options[ 'text' ] );
      $text_part->type = \Zend\Mime\Mime::TYPE_TEXT;
      $alternative_mime->addPart( $text_part );
    }

    if( isset( $options[ 'html' ] ) )
    {
      $html_part = new \Zend\Mime\Part( $options[ 'html' ] );
      $html_part->type = \Zend\Mime\Mime::TYPE_HTML;
      $alternative_mime->addPart( $html_part );
    }
  }



  /**
   * Returns the some attachment is an inline content
   *
   * @param array $options
   * @return bool
   */
  private static function _has_inline_content( $options )
  {
    foreach( $options[ 'attachments' ] as $attachment )
    {
      if( isset( $attachment[ 'id' ] ) )
      {
        return true;
      }
    }

    return false;
  }



  /**
   * Validates options
   *
   * @param array $options
   */
  private static function _validate_options( &$options )
  {
    $options[ 'text' ] = @$options[ 'text' ] ?: ' ';
    $options[ 'html' ] = @$options[ 'html' ] ?: ' ';

    if( !isset( $options[ 'attachments' ] ) )
    {
      $options[ 'attachments' ] = [];
    }
  }

}
