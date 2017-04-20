<?php

namespace Thrift\Server;

use Thrift\Server\TServer;
use Thrift\Exception\TTransportException;

class TNonblockingServer extends TServer {

	public function serve() {
		$this->transport_->setCallback(array($this, 'handleRequest'));
		$this->transport_->listen();
	}

	public function stop() {
		$this->transport_->close();
	}

	public function handleRequest(TTransport $transport) {
        $inputTransport = $this->inputTransportFactory_->getTransport($transport);
        $outputTransport = $this->outputTransportFactory_->getTransport($transport);
        $inputProtocol = $this->inputProtocolFactory_->getProtocol($inputTransport);
        $outputProtocol = $this->outputProtocolFactory_->getProtocol($outputTransport);
        $this->processor_->process($inputProtocol, $outputProtocol);
		//$protocol = new TBinaryProtocol($transport, true, true);
		//$this->processor_->process($protocol, $protocol);
	}

}

