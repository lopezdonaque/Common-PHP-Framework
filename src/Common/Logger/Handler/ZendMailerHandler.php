<?php

namespace Common\Logger\Handler;


/**
 * Uses Zend Framework 2 to send the emails
 *
 */
class ZendMailerHandler extends \Monolog\Handler\MailHandler
{

  /**
   * To
   *
   * @var array|string
   */
  protected $to;


  /**
   * Subject
   *
   * @var string
   */
  protected $subject;


  /**
   * From
   *
   * @var string
   */
  protected $from;


  /**
   * Reply to
   *
   * @var string
   */
  protected $reply_to;


  /**
   * Headers
   *
   * @var array
   */
  protected $headers = [];


  /**
   * Attachments config
   *
   * @var array
   */
  protected $attachments_config = [];



  /**
   * Constructor
   *
   * @param string|array $to             The receiver of the mail
   * @param string       $subject        The subject of the mail
   * @param string       $from           The sender of the mail
   * @param string       $reply_to       The reply-to of the mail
   * @param integer      $level          The minimum logging level at which this handler will be triggered
   * @param array        $attachments_config
   */
  public function __construct( $to, $subject, $from, $reply_to, $level = \Monolog\Logger::ERROR, $attachments_config = [ 'contents', 'record' ] )
  {
    parent::__construct( $level, true );
    $this->to = is_array( $to ) ? $to : [ $to ];
    $this->subject = $subject;
    $this->from = $from;
    $this->reply_to = $reply_to;
    $this->attachments_config = $attachments_config;
  }



  /**
   * Adds a header
   *
   * @param string|array $headers Custom added headers
   * @throws \InvalidArgumentException
   */
  public function addHeader( $headers )
  {
    foreach( (array) $headers as $header )
    {
      if( strpos( $header, "\n" ) !== false || strpos( $header, "\r" ) !== false )
      {
        throw new \InvalidArgumentException( 'Headers can not contain newline characters for security reasons' );
      }

      $this->headers[] = $header;
    }
  }



  /**
   * {@inheritdoc}
   */
  protected function send( $content, array $records )
  {
    foreach( $records as $record )
    {
      $attachments = [];
      $uid = @$record[ 'extra' ][ 'uid' ] ?: microtime();
      $date = $record[ 'datetime' ]->format( 'YmdHis' );

      foreach( $this->attachments_config as $attachment_config )
      {
        switch( $attachment_config )
        {
          case 'contents':
            $attachments[] =
            [
              'filename' => "log_html_{$uid}_{$date}.html",
              'contents' => $content
            ];
            break;

          case 'record':
            $attachments[] =
            [
              'filename' => "log_json_{$uid}_{$date}.json",
              'contents' => @json_encode( $record )
            ];
            break;
        }
      }

      \Common\Utils\Mail::send(
      [
        'from' => $this->from,
        'to' => $this->to,
        'subject' => $this->subject,
        'html' => $content,
        'headers' => $this->headers,
        'reply_to' => $this->reply_to,
        'attachments' => $attachments
      ]);
    }
  }

}
