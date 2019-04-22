<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: cookie.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_System_Cookie extends Model
{
	public $_COOKIE;
	public $GUID = null;
	public function __construct(&$system)
	{
		self::$system = &$system;
		$this->Instance();
		//register_shutdown_function(array(&$this,'update'));
	}
	
	public function Instance()
	{
		$_clen = strlen(__CFG::C_PREFIX);
		$filter = K::M('content/filter');
		foreach($_COOKIE as $k => $v) {
			if(substr($k, 0, $_clen) == __CFG::C_PREFIX) {
				$this->_COOKIE[(substr($k, $_clen))] = $filter->addslashes($v);
			}
		}
		if(!$this->GUID = $this->_COOKIE['GUID']){
			$this->GUID = 'KT-'.K::GUID('cookie');
			$this->set('GUID',$this->GUID);
		}
	}
	public function set($key, $value, $life=NULL, $prefix = '')
	{
		$prefix = $prefix ? $prefix : __CFG::C_PREFIX;
		if($life === NULL){ //默认30天过期
			$life = __CFG::TIME + __CFG::C_EXPIRE;
		}else{
			$life = $life ? ($life + __CFG::TIME) : 0;
		}
		$https = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
		$https = 0;
		$httponly = __CFG::C_HTTPONLY ? true : false;
		$cookiepath = ($httponly && PHP_VERSION < '5.2.0') ? __CFG::C_PATH.'; HttpOnly' : __CFG::C_PATH;
		$site = K::$system->config->get('site');
        $domain = __CFG::C_DOMAIN;
		if(PHP_VERSION < '5.2.0') {
			setcookie($prefix.$key, $value, $life, $cookiepath, $domain, $https);
            if(empty($value) || $life < 0){
                setcookie($prefix.$key, $value, $life, $cookiepath, null, $https);
            }
		} else {
			setcookie($prefix.$key, $value, $life, $cookiepath, $domain, $https, $httponly);
            if(empty($value) || $life < 0){
                setcookie($prefix.$key, $value, $life, $cookiepath, null, $https, $httponly);
            }
		}
		$this->_COOKIE[$key] = $value;
	}
	public function get($key)
	{
		return $this->_COOKIE[$key];
	}
	public function delete($key)
	{
		$this->set($key, '', -86400);
	}
	public function clear()
	{
		foreach($this->_COOKIE as $k=>$v){
			$this->set($k, '', -86400);
		}
	}
	public function update()
	{
	
	}
	
	public function fetch_all()
	{
		return $this->_COOKIE;
	}
}