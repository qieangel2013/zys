<?php
/**********************************************************\
|                                                          |
|                          hprose                          |
|                                                          |
| Official WebSite: http://www.hprose.com/                 |
|                   http://www.hprose.org/                 |
|                                                          |
\**********************************************************/

/**********************************************************\
 *                                                        *
 * Hprose/Swoole/Socket/Server.php                        *
 *                                                        *
 * hprose swoole socket server library for php 5.3+       *
 *                                                        *
 * LastModified: Jul 29, 2016                             *
 * Author: Ma Bingyao <andot@hprose.com>                  *
 *                                                        *
\**********************************************************/
require_once __DIR__ . '/Service.php';
class Server extends Service {
    public $server;
    public $settings = array();
    public $noDelay = true;
    private $type;
    private function parseUrl($uri) {
        $result = new stdClass();
        $p = parse_url($uri);
        if ($p) {
            switch (strtolower($p['scheme'])) {
                case 'tcp':
                case 'tcp4':
                case 'ssl':
                case 'sslv2':
                case 'sslv3':
                case 'tls':
                    $result->type = SWOOLE_SOCK_TCP;
                    $result->host = $p['host'];
                    $result->port = $p['port'];
                    break;
                case 'tcp6':
                    $result->type = SWOOLE_SOCK_TCP6;
                    $result->host = $p['host'];
                    $result->port = $p['port'];
                    break;
                case 'unix':
                    $result->type = SWOOLE_UNIX_STREAM;
                    $result->host = $p['path'];
                    $result->port = 0;
                    break;
                default:
                    throw new Exception("Can't support this scheme: {$p['scheme']}");
            }
        }
        else {
            throw new Exception("Can't parse this uri: " . $uri);
        }
        return $result;
    }
    public function __construct($uri, $mode = SWOOLE_BASE) {
        parent::__construct();
        $url = $this->parseUrl($uri);
        $this->type = $url->type;
        $this->server = new swoole_server($url->host, $url->port, $mode, $url->type);
    }
    public function setNoDelay($value) {
        $this->noDelay = $value;
    }
    public function isNoDelay() {
        return $this->noDelay;
    }
    public function set($settings) {
        $this->settings = array_replace($this->settings, $settings);
    }
    public function on($name, $callback) {
        $this->server->on($name, $callback);
    }
    public function addListener($uri) {
        $url = $this->parseUrl($uri);
        $this->server->addListener($url->host, $url->port, $url->type);
    }
    public function listen($host, $port, $type = SWOOLE_SOCK_TCP) {
        return $this->server->listen($host, $port, $type);
    }
    public function start() {
        if ($this->type !== SWOOLE_UNIX_STREAM) {
            $this->settings['open_tcp_nodelay'] = $this->noDelay;
        }
        $this->settings['open_eof_check'] = false;
        $this->settings['open_length_check'] = false;
        $this->settings['open_eof_split'] = false;
        $this->server->set($this->settings);
        $this->socketHandle($this->server);
        $this->server->start();
    }
}
