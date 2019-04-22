<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: interface.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
interface Cache_Interface
{
	public function set($key, $val, $ttl=0);
	public function get($key);
	public function delete($key);
	public function flush();
	//public function update();
}