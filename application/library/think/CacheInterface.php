<?php

namespace think;

/**
 * 数据库缓存接口
 * Interface CacheInterface
 * @author : evalor <master@evalor.cn>
 * @package think
 */
interface CacheInterface
{
    function get($name, $default = false);

    function set($name, $value, $expire = null);

    function rm($name);
}