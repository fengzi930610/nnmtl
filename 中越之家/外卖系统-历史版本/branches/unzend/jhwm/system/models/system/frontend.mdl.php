<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: frontend.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
require(__CFG::DIR.'libs/smarty/Smarty.class.php');
class Mdl_System_Frontend extends Smarty
{
	
	//private $system = null;
    
    public $widgets_mdl = null;
	public $__MDL = 'Mdl_System_Frontend';
	public function __construct(&$system)
	{
		parent::__construct();
		$this->_init();
	}
	private function _init()
	{
	
		$this->left_delimiter='<{';
        $this->right_delimiter='}>';
        $this->setTemplateDir(__CFG::DIR.'themes/default')
               ->addPluginsDir(__CFG::DIR.'plugins/smarty')
               ->setCompileDir(__CFG::DIR.'data/tplcache')
               ->setCacheDir(__CFG::DIR.'data/cache');
		$this->compile_check = true;
		$this->registerResource('widget',new Smarty_Resource_Widget());
        $this->registerResource('view', new Smarty_Resource_App('home'));
        $this->registerResource('biz', new Smarty_Resource_App('biz'));
        $this->registerResource('merchant', new Smarty_Resource_App('merchant'));
        $this->registerResource('admin', new Smarty_Resource_App('admin'));
        $this->registerResource('weidian', new Smarty_Resource_App('weidian'));
        $this->registerResource('fenxiao', new Smarty_Resource_App('fenxiao'));
		if(defined('IN_ADMIN')){
			//K::$system->check_listion();
		}
	}
}
//Widget resource 
class Smarty_Resource_Widget extends Smarty_Resource_Custom
{
	
	protected function fetch($name, &$source, &$mtime)
	{
		$file = __CFG::DIR."plugins/widgets/{$name}";
		if(file_exists($file)){
			$source = file_get_contents($file);
			$mtime = filemtime($file);
		}else{
			$source = null;
			$mtime = null;
		}
	}
	protected function fetchTimestamp($name)
	{
		$file = __CFG::DIR."plugins/widgets/{$name}";
		if(file_exists($file)){
			return filemtime($file);
		}
		return null;
	}
}
//Plugin resource 
class Smarty_Resource_Plugin extends Smarty_Resource_Custom
{
	
	protected function fetch($name, &$source, &$mtime)
	{
		$file = __CFG::DIR."plugins/{$name}";
		if(file_exists($file)){
			$source = file_get_contents($file);
			$mtime = filemtime($file);
		}else{
			$source = null;
			$mtime = null;
		}
	}
	protected function fetchTimestamp($name)
	{
		$file = __CFG::DIR."plugins/{$name}";
		if(file_exists($file)){
			return filemtime($file);
		}
		return null;
	}
}
//Admin View resource 
class Smarty_Resource_App extends Smarty_Resource_Custom
{
	
	protected $_app = null;
	protected $_path = null;
	public function __construct($app)
	{
		$this->_app = $app;
		$this->_path = $path = __CFG::DIR.$this->_app."/view/";
	}
	protected function fetch($name, &$source, &$mtime)
	{
		$file = $this->_path.$name;
		if(file_exists($file)){
			$source = file_get_contents($file);
			$mtime = filemtime($file);
		}else{
			$source = null;
			$mtime = null;
		}
	}
	protected function fetchTimestamp($name)
	{
		$file = $this->_path.$name;
		if(file_exists($file)){
			return filemtime($file);
		}
		return null;
	}
}

class Smarty_CacheResource_Memcache extends Smarty_CacheResource_KeyValueStore
{
    protected $cache = null;
    public function __construct()
    {
        $this->cache = K::M('cache/cache');
    }

    protected function read(array $keys)
    {
        $_keys = $lookup = array();
        foreach ($keys as $k) {
            $_k = sha1($k);
            $_keys[] = $_k;
            $lookup[$_k] = $k;
        }
        $_res = array();
        $res = $this->cache->get($_keys);
        foreach ($res as $k => $v) {
            $_res[$lookup[$k]] = $v;
        }
        return $_res;
    }

    protected function write(array $keys, $expire=null)
    {
        foreach ($keys as $k => $v) {
            $k = sha1($k);
            $this->cache->set($k, $v, 0, $expire);
        }
        return true;
    }

    protected function delete(array $keys)
    {
        foreach ($keys as $k) {
            $k = sha1($k);
            $this->cache->delete($k);
        }
        return true;
    }

    protected function purge()
    {
        return $this->cache->flush();
    }
}