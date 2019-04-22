<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: cache.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Cache_Cache
{   
	public $__CACHE = array();
	public $cache = null;
	public function __construct()
	{
        if(__CFG::CACHE_TYPE == 'memcache'){
            $this->cache = K::M('cache/memcache');
        }else{
            $this->cache = K::M('cache/mfile');
        }
    }
    
    public function set($key, $val, $ttl=0)
    {
        $ttl = intval($ttl);
        $this->__CACHE[$key] = $val;
        if($ttl < 0  ){
            return false;
        }        
    	return $this->cache->set($key, $val, $ttl);
    }
    public function get($key)
    {
        if($data = $this->__CACHE[$key]){
            return $data;
        }
    	return $this->cache->get($key);
    }
    public function delete($key)
    {
        unset($this->__CACHE[$key]);
    	return $this->cache->delete($key);
    }
    public function flush()
    {
        $this->__CACHE = array();
    	return $this->cache->flush();
        $this->cache->set('cache_version', __TIME);
    }


    public function clean()
    {
        $this->__CACHE = array();
        $this->cache->clean();
        $this->cache->set('cache_version', __TIME);
    }

    public function version()
    {
        if(!$version = $this->cache->get('cache_version')){
            $this->cache->set('cache_version', __TIME);
            $version = __TIME;
        }
        return $version;
    }

    public function islock($key,$ltime = 2){
	   return  $this->cache->islock($key,$ltime);
    }

    public function unlock($key){
        return $this->cache->delete($key);

    }






}