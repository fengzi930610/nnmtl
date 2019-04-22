<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: file.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::I('cache');
class Mdl_Cache_MFile implements Cache_Interface
{
	public function __construct(&$system)
	{
		$this->system = &$system;
		$this->cache_dir = __CFG::DIR.'data/cache/';
	}
	public function set($key, $val, $ttl=0)
	{
		$time = $ttl==0 ? 0 : (__CFG::TIME + $ttl);
		$hash = md5($key);
		$file = $this->get_cache_file($key);
		$data = is_array($val) ? var_export($val, true) : "'{$val}'";
$tmpl = '<?php
/**
 * Copy Right IJH.CC
 * note:System cache file, DO NOT modify me!
 * hash:{hash}:{key};
 * time:{time}
 */
if(!defined("__CORE_DIR")){	exit("Access Denied");}if({time}===0 || __TIME<{time}){return {data};}return false;';
		$content = str_replace(array('{hash}','{time}', '{data}', '{key}'), array($hash, $time, $data, $key), $tmpl);
		K::M('io/dir')->create(dirname($file));
		file_put_contents($file, $content);
	}

	public function get($key)
	{
		$fun = 'cache_'.md5($key);
		$file = $this->get_cache_file($key);
		if(file_exists($file)){
			return @include($file);
		}
		return false;
	}

	public function delete($key)
	{

		K::M('io/file')->remove($this->get_cache_file($key));
	}

	public function flush()
	{
		$this->clean();
	}

	public function clean()
	{
        if(!$handler = opendir($this->cache_dir)){
            return false;
        }
        while(false !== ($file = readdir($handler))){
            if($file == '.' || $file == '..') {
                continue;
            }
            if(is_dir($this->cache_dir.$file)){
                K::M('io/dir')->remove($this->cache_dir . $file);
            }else{
                @unlink($this->cache_dir.$file);
            }
        }
        closedir($handler);
		return true;
	}
	
	protected function get_cache_file($key)
	{
		$hash = md5($key);
		return $this->cache_dir.substr($hash, 0, 3).DIRECTORY_SEPARATOR.'cache_'.$hash.'.php';
	}

	public function islock($key,$ttl){
	    return true;

    }

    public function unlock(){

    }



}