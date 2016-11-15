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
 * Hprose/Socket/Timer.php                                *
 *                                                        *
 * hprose socket Timer class for php 5.3+                 *
 *                                                        *
 * LastModified: Aug 10, 2016                             *
 * Author: Ma Bingyao <andot@hprose.com>                  *
 *                                                        *
\**********************************************************/
class Timer {
    public function setTimeout($callback, $delay) {
        return swoole_timer_after($delay, $callback);
    }
    public function clearTimeout($timerid) {
        return swoole_timer_clear($timerid);
    }
    public function setImmediate($callback) {
        swoole_event_defer($callback);
    }
}
