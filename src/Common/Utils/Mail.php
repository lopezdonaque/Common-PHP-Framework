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
    $body = new \Zend\Mime\Message();

    if( isset( $options[ 'text' ] ) )
    {
      $text_part = new \Zend\Mime\Part( $options[ 'text' ] );
      $text_part->type = \Zend\Mime\Mime::TYPE_TEXT;
      $body->addPart( $text_part );
    }

    if( isset( $options[ 'html' ] ) )
    {
      $html_part = new \Zend\Mime\Part( $options[ 'html' ] );
      $html_part->type = \Zend\Mime\Mime::TYPE_HTML;
      $body->addPart( $html_part );
    }

    if( isset( $options[ 'attachments' ] ) )
    {
      foreach( $options[ 'attachments' ] as $attachment )
      {
        $file_part = new \Zend\Mime\Part( $attachment[ 'contents' ] );
        $file_part->type = \Zend\Mime\Mime::TYPE_OCTETSTREAM;
        $file_part->disposition = \Zend\Mime\Mime::DISPOSITION_ATTACHMENT;
        $file_part->encoding = \Zend\Mime\Mime::ENCODING_BASE64;
        $file_part->filename = $attachment[ 'filename' ];
        $body->addPart( $file_part );
      }
    }

    $message = new \Zend\Mail\Message();
    $message->setFrom( $options[ 'from' ], isset( $options[ 'from_name' ] ) ? $options[ 'from_name' ] : null );
    $message->addTo( $options[ 'to' ] );
    $message->setSubject( $options[ 'subject' ] );
    $message->setEncoding( 'UTF-8' );
    $message->setBody( $body );

    if( isset( $options[ 'text' ] ) && isset( $options[ 'html' ] ) )
    {
      $message->getHeaders()->get( 'content-type' )->setType( 'multipart/alternative' );
    }

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

    $transport = new \Zend\Mail\Transport\Sendmail();
    $transport->send( $message );
  }

}
