<?php
/**
 * Copy Right zyzjgzh.cn
 * Author vast<277256756@qq.com>
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Cache_Redis
{
    static private $redis = NULL;

    public function __construct()
    {
        if(self::$redis === NULL)
        {
            self::$redis = new Redis();
            $srvAddr = '127.0.0.1';
            if(!defined("MERBER_LOGIN_EVN") || MERBER_LOGIN_EVN!=="DEV")
                $srvAddr = '192.168.0.1';
            self::$redis->connect($srvAddr,6379);
        }
    }
    
    public function set($key, $val, $ttl=0)
    {
        $key = trim($key);
        if($key === "")
            return false;
        $val = serialize($val);
        if(!self::$redis->set($key,$val))
            return false;
        $ttl = (int)$ttl;
        if($ttl > 0)
            self::$redis->expireAt($key,time()+$ttl);
        return true;
    }

    public function get($key)
    {
        $key = trim($key);
        if($key === "")
            return NULL;
        $val = self::$redis->get($key);
        if(!$val)
            return NULL;
        $val = unserialize($val);
        return $val;
    }

    public function delete($key)
    {
        $key = trim($key);
        if($key === "")
            return 0;
        return self::$redis->del($key);
    }

    public function queue_push($key,$val)
    {
        $key = trim($key);
        if($key === "")
            return false;
        return self::$redis->rpush($key,$val);
    }

    public function queue_pop($key)
    {
        $key = trim($key);
        if($key === "")
            return NULL;
        return self::$redis->lpop($key);
    }

    public function queue_len($key)
    {
        $key = trim($key);
        if($key === "")
            return 0;
        return (int)self::$redis->lSize($key);
    }
}