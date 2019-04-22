<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: database.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::I('cache');
class Mdl_Cache_Database extends Mdl_Table implements Cache_Interface
{
	
	protected $_table = 'system_cache';
	public function __construct(&$system)
	{
		parent::__construct($system);
		$this->_init();
	}
	private function _init()
	{
		$sql = "CREATE TABLE IF NOT EXISTS ".$this->table($this->_table)." (`k` char(32) NOT NULL,`v` mediumtext,`t` int(10) DEFAULT NULL, PRIMARY KEY (`k`)) ENGINE=MyISAM DEFAULT CHARSET=gbk";
		return $this->db->Execute($sql);
	}
	public function set($k, $v, $t=86400)
	{		
		$k = md5($k);
		$v = serialize($v);
		$ltime = $this->system->timestamp+$t;
		$sql = "REPLACE INTO ".$this->table('system_cache')." VALUES('$k','$v', '$ltime')";
		return $this->db->Execute($sql);
	}
	public function get($k)
	{	
		$k = md5($k);
		$time = $this->system->timestamp;
		$sql = "SELECT v FROM ".$this->table('system_cache')." WHERE k='$k' AND t>='$time'";
		if(!$v = $this->db->GetOne($sql)){
			return false;
		}
		return unserialize($v);
	}
	public function delete($k)
	{
		$k = md5($k);
		return $this->db->Execute("DELETE FROM ".$this->table('system_cache')." WHERE k='$k'");
	}
	public function flush()
	{
		return $this->db->Execute("TRUNCAT TABLE ".$this->table('system_cache'));
	}
	public function update($k,$v,$t=0)
	{
		$k = md5($k);
		$ltime = $this->system->timestamp + $t;
		return $this->db->Execute("UPDATE ".$this->table('system_cache')."SET v='$v', t='$ltime' WHERE $k='$k'");
	}
}