<?php
/**
 * Copy	Right Anhuike.com
 * $Id smarty_cacheresource_memcache.php shzhrui<anhuike@gmail.com>
 */

class Smarty_CacheResource_Memcache extends Smarty_CacheResource_KeyValueStore
{

    protected $memcache = null;
    
    public function __construct()
    {
		$this->memcache = K::M('stage/memcache');
    }    

    protected function read(array $keys)
    {
        $_keys = $lookup = array();
        foreach ($keys as $k) {
            $_k = 'smarty_'.sha1($k);
            $_keys[] = $_k;
            $lookup[$_k] = $k;
        }
        $_res = array();
        $res = $this->memcache->get($_keys);
        foreach ($res as $k => $v) {
            $_res[$lookup[$k]] = $v;
        }
        return $_res;
    }
    
    protected function write(array $keys, $expire=null)
    {
        foreach ($keys as $k => $v) {
            $k = 'smarty_'.sha1($k);
            $this->memcache->set($k, $v, 0, $expire);
        }
        return true;
    }

	protected function delete(array $keys)
    {
        foreach ($keys as $k) {
            $k = sha1($k);
            $this->memcache->delete($k);
        }
        return true;
    }


    protected function purge()
    {
        return $this->memcache->flush();
    }
}