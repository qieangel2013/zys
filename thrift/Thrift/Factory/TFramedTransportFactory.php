<?php

namespace Thrift\Factory;

use Thrift\Transport\TFramedTransport;
use Thrift\Transport\Transport;

class TFramedTransportFactory extends TTransportFactory{
  /**
   * @static
   * @param TTransport $transport
   * @return TTransport
   */
  public static function getTransport(\Thrift\Transport\TTransport $transport) {
    return new TFramedTransport($transport);
  }
}
