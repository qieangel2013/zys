<?php
namespace Thrift\Transport;

use Thrift\Transport\TTransport;
use Thrift\Exception\TException;
use Thrift\Exception\TTransportException;

class TNonblockingSocket extends TTransport {

  /**
   * Handle to PHP socket
   *
   * @var resource
   */
  private $handle_ = null;

  /**
   * Send timeout in milliseconds
   *
   * @var int
   */
  private $sendTimeout_ = 5000;

  /**
   * Recv timeout in milliseconds
   *
   * @var int
   */
  private $recvTimeout_ = 750;

  /**
   * Is send timeout set?
   *
   * @var bool
   */
  private $sendTimeoutSet_ = FALSE;

  /**
   * Persistent socket or plain?
   *
   * @var bool
   */
  private $persist_ = FALSE;

  /**
   * Debugging on?
   *
   * @var bool
   */
  protected $debug_ = FALSE;

  /**
   * Debug handler
   *
   * @var mixed
   */
  protected $debugHandler_ = null;

  /**
   * Socket constructor
   *
   * @param string $socket       resource of php socket
   * @param string $debugHandler Function to call for error logging
   */
  public function __construct($socket, $debugHandler = 'error_log') {
	  $this->handle_ = $socket;
  }

  /**
   * Sets the send timeout.
   *
   * @param int $timeout  Timeout in milliseconds.
   */
  public function setSendTimeout($timeout) {
    $this->sendTimeout_ = $timeout;
  }

  /**
   * Sets the receive timeout.
   *
   * @param int $timeout  Timeout in milliseconds.
   */
  public function setRecvTimeout($timeout) {
    $this->recvTimeout_ = $timeout;
  }

  /**
   * Sets debugging output on or off
   *
   * @param bool $debug
   */
  public function setDebug($debug) {
    $this->debug_ = $debug;
  }

  /**
   * Tests whether this is open
   *
   * @return bool true if the socket is open
   */
  public function isOpen() {
    return is_resource($this->handle_);
  }

  /**
   * Connects the socket.
   */
  public function open() {
    // Connect failed?
    if ($this->handle_ === FALSE) {
      $error = 'TSocket: Could not connect';
      if ($this->debug_) {
        call_user_func($this->debugHandler_, $error);
      }
      throw new TException($error);
    }

    stream_set_timeout($this->handle_, 0, $this->sendTimeout_*1000);
    $this->sendTimeoutSet_ = TRUE;
  }

  /**
   * Closes the socket.
   */
  public function close() {
	  // close socket
	  stream_socket_shutdown($this->handle_, STREAM_SHUT_RDWR);
	  fclose($this->handle_);
  }

  /**
   * Uses stream get contents to do the reading
   *
   * @param int $len How many bytes
   * @return string Binary data
   */
  public function readAll($len) {
    if ($this->sendTimeoutSet_) {
      stream_set_timeout($this->handle_, 0, $this->recvTimeout_*1000);
      $this->sendTimeoutSet_ = FALSE;
    }
    // This call does not obey stream_set_timeout values!
    // $buf = @stream_get_contents($this->handle_, $len);

    $pre = null;
    while (TRUE) {
      $buf = fread($this->handle_, $len);
      if ($buf === FALSE || $buf === '') {
        $md = stream_get_meta_data($this->handle_);
        if ($md['timed_out']) {
          throw new TException('TSocket: timed out reading '.$len.' bytes from ');
        } else {
          throw new TException('TSocket: Could not read '.$len.' bytes from ');
        }
      } else if (($sz = strlen($buf)) < $len) {
        $md = stream_get_meta_data($this->handle_);
        if ($md['timed_out']) {
          throw new TException('TSocket: timed out reading '.$len.' bytes from ');
        } else {
          $pre .= $buf;
          $len -= $sz;
        }
      } else {
        return $pre.$buf;
      }
    }
  }

  /**
   * Read from the socket
   *
   * @param int $len How many bytes
   * @return string Binary data
   */
  public function read($len) {
    if ($this->sendTimeoutSet_) {
      stream_set_timeout($this->handle_, 0, $this->recvTimeout_*1000);
      $this->sendTimeoutSet_ = FALSE;
    }
    $data = fread($this->handle_, $len);
    if ($data === FALSE || $data === '') {
      $md = stream_get_meta_data($this->handle_);
      if ($md['timed_out']) {
        throw new TException('TSocket: timed out reading '.$len.' bytes');
      } else {
        throw new TException('TSocket: Could not read '.$len.' bytes');
      }
    }
    return $data;
  }

  /**
   * Write to the socket.
   *
   * @param string $buf The data to write
   */
  public function write($buf) {
	  /*
    if (!$this->sendTimeoutSet_) {
      stream_set_timeout($this->handle_, 0, $this->sendTimeout_*1000);
      $this->sendTimeoutSet_ = TRUE;
    }
	*/
    while (strlen($buf) > 0) {
      $got = fwrite($this->handle_, $buf);
      if ($got === 0 || $got === FALSE) {
        $md = stream_get_meta_data($this->handle_);
        if ($md['timed_out']) {
          throw new TException('TSocket: timed out writing '.strlen($buf));
        } else {
            throw new TException('TSocket: Could not write '.strlen($buf));
        }
      }
      $buf = substr($buf, $got);
    }
  }

  /**
   * Flush output to the socket.
   */
  public function flush() {
    $ret = fflush($this->handle_);
  }
}

