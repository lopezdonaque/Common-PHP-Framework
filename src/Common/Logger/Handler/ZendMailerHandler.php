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
  protected $headers = array();


  /**
   * Defines if the mail must attach the log as an attachment
   *
   * @var bool
   */
  protected $attach_log = true;



  /**
   * Constructor
   *
   * @param string|array $to             The receiver of the mail
   * @param string       $subject        The subject of the mail
   * @param string       $from           The sender of the mail
   * @param string       $reply_to       The reply-to of the mail
   * @param integer      $level          The minimum logging level at which this handler will be triggered
   */
  public function __construct( $to, $subject, $from, $reply_to, $level = \Monolog\Logger::ERROR )
  {
    parent::__construct( $level, true );
    $this->to = is_array( $to ) ? $to : array( $to );
    $this->subject = $subject;
    $this->from = $from;
    $this->reply_to = $reply_to;
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
      $attachments = array();

      if( $this->attach_log )
      {
        $uid = isset( $record[ 'extra' ][ 'uid' ] ) ? $record[ 'extra' ][ 'uid' ] : microtime();
        $date = $record[ 'datetime' ]->format( 'YmdHis' );

        // Json attachment
        $attachments[] = array
        (
          'filename' => "log_json_{$uid}_{$date}.json",
          'contents' => @json_encode( $record )
        );

        // Plain attachment
        $attachments[] = array
        (
          'filename' => "log_html_{$uid}_{$date}.html",
          'contents' => $content
        );
      }

      \Common\Utils\Mail::send( array
      (
        'from' => $this->from,
        'to' => $this->to,
        'subject' => $this->subject,
        'html' => $content,
        'headers' => $this->headers,
        'reply_to' => $this->reply_to,
        'attachments' => $attachments
      ));
    }
  }
}
