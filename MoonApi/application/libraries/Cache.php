<?php

/**
 * 缓存操作类
 */
class Cache {

    /**
     * 单件实例
     * @var Cache 
     */
    private static $_instance = null;

    /**
     * 缓存类型
     * @var string 
     */
    public static $_type = "memcached";

    /**
     * cache服务器
     * @var type 
     */
    private static $_server = 'localhost';

    /**
     * cache服务器端口
     * @var type 
     */
    private static $_port = 11211;

    /**
     * 获得实例
     * @param string $type
     * @return Cache
     */
    public static function getInstance($type = null) {
        if (!self::$_instance) {
            self::$_instance = self::init($type);
        }
        return self::$_instance;
    }

    /**
     * 初始化cache
     */
    public static function init($type) {
        if (empty($type)) {
            $type = self::$_type;
        }
        $cacheConfig = APPPATH . "config/cache.php";
        if (file_exists($cacheConfig)) {
            $cache = require $cacheConfig;
            if (isset($cache)) {
                self::$_server = $cache['server'];
                self::$_port = $cache['port'];
            }
        }
        if ($type == 'memcached') {
            $instance = self::initMemcache();
        } elseif (!$type) {
            $message = "Does not specify a cache service type";
            throw new Exception($message);
        }
        return $instance;
    }

    /**
     * 设置cache服务器
     * @param type $server
     */
    public static function setServer($server) {
        if ($server) {
            self::$_server = $server;
        }
    }

    /**
     * 设置cache服务器端口
     * @param type $port
     */
    public static function setPort($port) {
        if ($port) {
            self::$_port = $port;
        }
    }

    /**
     * 初始化memcached
     * @return Memcached
     */
    public static function initMemcache() {
        $memcached = new Memcached();
        $memcached->addServer(self::$_server, self::$_port);
        return $memcached;
    }

    /**
     * 设置
     * @param type $key key
     * @param type $value   值
     * @param type $expiration  过期 秒
     * @param type $isMd5   key是否MD5
     * @return boolean
     */
    public static function set($key, $value, $expiration = 0, $isMd5 = false) {
        if (!config_item('enable_cache')) {
            return;
        }
        $newKey = $isMd5 ? md5($key) : $key;
        $result = self::getInstance()->set($newKey, $value, $expiration);
        $message = "\tSet " . ($result ? 'Success' : 'Failure') . "\n\tOrigin key: $key\n\tKey: $newKey\n\tExpiration: $expiration\n\tValue: " . serialize( $value )."\n";
        Logger::cacheLog($message);
        return $result;
    }

    /**
     * 获取
     * @param type $key key
     * @param type $isMd5   key是否MD5
     * @return boolean
     */
    public static function get($key, $isMd5 = false) {
        if (!config_item('enable_cache')) {
            return false;
        }
        $key = $isMd5 ? md5($key) : $key;
        return self::getInstance()->get($key);
    }

    /**
     * 删除
     * @param type $key key
     * @param type $isMd5   key是否MD5
     * @return boolean
     */
    public static function delete($key, $isMd5 = false) {
        if (!config_item('enable_cache')) {
            return;
        }
        $key = $isMd5 ? md5($key) : $key;
        return self::getInstance()->delete($key);
    }

    /**
     * 替换
     * @param type $key key
     * @param type $value   值
     * @param type $isMd5   key是否MD5
     * @param type $expiration  过期 秒
     * @return boolean
     */
    public static function replace($key, $value, $expiration = 0, $isMd5 = false) {
        if (!config_item('enable_cache')) {
            return;
        }
        $key = $isMd5 ? md5($key) : $key;
        return self::getInstance()->replace($key, $value, $expiration);
    }

    /**
     * 清空cache
     * @param type $delay   延时 秒
     * @return boolean
     */
    public static function flush($delay = 0) {
        if (!config_item('enable_cache')) {
            return;
        }
        return self::getInstance()->flush($delay);
    }

}

?>
