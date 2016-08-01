<?php
namespace swoole;
use Thrift;
use Thrift\Server\TNonblockingServer;

class Server extends TNonblockingServer
{
    protected $processor = null;
    protected $serviceName = 'rpc';

    function onStart()
    {
        echo "RpcServer Start\n";
    }

    function notice($log)
    {
        echo $log."\n";
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        $processor_class = "\\Bin\\" . $this->serviceName . "\\" . $this->serviceName . 'Processor';
        $handler_class = "\\Bin\\" . $this->serviceName . "\\Handler";

        $handler = new $handler_class();
        $this->processor = new $processor_class($handler);

        $socket = new Socket();
        $socket->setHandle($fd);
        $socket->buffer = $data;
        $socket->server = $serv;
        $protocol = new Thrift\Protocol\TBinaryProtocol($socket, false, false);

        try {
            $protocol->fname = $this->serviceName;
            $this->processor->process($protocol, $protocol);
        } catch (\Exception $e) {
            $this->notice('CODE:' . $e->getCode() . ' MESSAGE:' . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    function serve()
    {
        $serv = new \swoole_server(SERVERHOST,SERVERPORT);
        $serv->on('workerStart', [$this, 'onStart']);
        $serv->on('receive', [$this, 'onReceive']);
        $serv->set(array(
            'worker_num'            => 1,
            'dispatch_mode'         => 1, //1: 轮循, 3: 争抢
            'open_length_check'     => true, //打开包长检测
            'package_max_length'    => 8192000, //最大的请求包长度,8M
            'package_length_type'   => 'N', //长度的类型，参见PHP的pack函数
            'package_length_offset' => 0,   //第N个字节是包长度的值
            'package_body_offset'   => 4,   //从第几个字节计算长度
        ));
        $serv->start();
    }
}
