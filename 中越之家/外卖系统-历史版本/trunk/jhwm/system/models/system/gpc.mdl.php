<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: gpc.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_System_Gpc extends Model
{
	
	static private $__GET;
	static private $__POST;
	static private $__COOKIE;
	public function __construct(&$system)
	{
		parent::__construct($system);
		$filter = K::M('content/filter');
		self::$__GET = $filter->Addslashes($_GET);
		self::$__POST = $filter->Addslashes($_POST);
		self::$__COOKIE = K::M('system/cookie')->_COOKIE;
	}
	public function get($key,$gpc='g')
	{
		if('g' == $gpc){
			return self::$__GET[$key];
		}else if('p' == $gpc){
			return self::$__POST[$key];
		}else if('c' == $gpc){
			return self::$__COOKIE[$key];
		}else if('gp' == $gpc){
            return isset(self::$__POST[$key]) ? self::$__POST[$key] : self::$__GET[$key];
        }
	}
	public function g($key)
	{
		return $this->get($key,'g');
	}
	public function p($key)
	{
		return $this->get($key,'p');
	}
	public function c($key)
	{
		return $this->get($key,'c');
	}
	
	//优先级POST,GET,COOKIE
	public function gpc($k)
	{
		return isset(self::$__POST[$k]) ? self::$__POST[$k] : (isset(self::$__GET[$k]) ? self::$__GET[$k] : self::$__COOKIE[$k]);		
	}
	public function set($key,$value,$gpc='g')
	{
		if('g' == $gpc){
			self::$__GET[$key] = $value;
		}else if('p' == $gpc){
			self::$__POST[$key] = $value;
		}else if('c' == $gpc){
			//只是对_COOKIE属性赋值，并未调用setcookie写入cookie
			self::$__COOKIE[$key] = $value;
		}
		return true;
	}
	public function set_g($key)
	{
		return $this->set($key, 'g');
	}
	public function set_p($key)
	{
		return $this->set($key, 'p');
	}
	public function set_c($key)
	{
		return $this->set($key, 'c');
	}
    
    public function fetch_all($gpc='g')
    {
        $gpc = strtolower($gpc);
        if('g' == $gpc){
            return self::$__GET;
        }else if('p' == $gpc){
            return self::$__POST;
        }else if('c' == $gpc){
            return $__COOKIE;
        }
    }
}