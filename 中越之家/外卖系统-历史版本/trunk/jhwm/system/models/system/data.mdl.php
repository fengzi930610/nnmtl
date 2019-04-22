<?php
/**
 * Copy Right zyzjgzh.cn
 * Author Vast<277256756@qq.com>
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_System_Data
{   
	protected $__CACHE = array();
	protected $data_dir = null;

	public function __construct()
	{
        $this->data_dir = __CFG::DIR.'data/data';
    }

    protected function get_data_file($key)
    {
        $hash = md5($key);
        return $this->data_dir."/".substr($hash, 0, 3).DIRECTORY_SEPARATOR.'data_'.$hash.'.php';
    }
    
    public function set($key, $val)
    {
        $this->__CACHE[$key] = $val;
        $hash = md5($key);
        $file = $this->get_data_file($key);
        $data = is_array($val) ? var_export($val, true) : "'{$val}'";
$tmpl = '<?php
/**
 * Copy Right zyzjgzh.cn
 * note:System data file, DO NOT modify me!
 * hash:{hash}:{key};
 */
if(!defined("__CORE_DIR")){ exit("Access Denied"); } return {data};';
        $content = str_replace(array('{hash}', '{data}', '{key}'), array($hash, $data, $key), $tmpl);
        K::M('io/dir')->create(dirname($file));
        file_put_contents($file, $content);
    }

    public function get($key)
    {
        if($data = $this->__CACHE[$key]){
            return $data;
        }

    	$fun = 'data_'.md5($key);
        $file = $this->get_data_file($key);
        if(file_exists($file)){
            $this->__CACHE[$key] = @include($file);
            return $this->__CACHE[$key];
        }
        return NULL;
    }

    public function delete($key)
    {
        unset($this->__CACHE[$key]);
    	K::M('io/file')->remove($this->get_cache_file($key));
    }
}