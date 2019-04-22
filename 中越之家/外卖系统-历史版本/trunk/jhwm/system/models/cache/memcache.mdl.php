<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: memcache.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Cache_Memcache extends Memcache
{
    protected static $memcache = null;
    protected $_prefix = '';



    
    public function __construct(&$system)
    {
        $cfg = explode(':', __CFG::MEMCACHE);
        $this->_prefix = __CFG::CACHE_PREFIX;
        $this->connect($cfg[0], $cfg[1]);

    }
    
    public function set($key, $val, $ttl=0)
    {   
        return parent::set($this->_prefix.$key, $val, false, $ttl);
    }

    public function get($key)
    {
        return parent::get($this->_prefix.$key);
    }

    public function delete($key)
    {
        return parent::delete($this->_prefix.$key, 0);
    }

    public function flush()
    {
        return parent::flush();
    }

    public function islock($key,$ttl ){
       return parent::add($this->_prefix.$key, '1', false, $ttl);
    }

    public function unlock($key){

        return $this->delete($key);
    }

    //专门处理day_num 模型
    public function incy($key,$incr,$ttl){
        if(!$this->get($key)){//没有key 需要设置一个key
            
        }else{//有key 则自动增长key

        }


    }














}