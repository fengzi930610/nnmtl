<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: view.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::M('module/base');
class Mdl_Module_View extends Mdl_Module_Base
{
	
	public function fetch_all()
	{
		if(self::$modules !== null){
			return $modules;
		}
		if(($modules = $this->cache->get('admin/module'))===false){
			$modules = array();
			$sql = "SELECT * FROM ".$this->table($this->_table)." ORDER BY level ASC,`orderby` ASC,mod_id ASC";
			if($rs = $this->db->Execute($sql)){
				while($row = $rs->fetch()){
					$modules[$row['mod_id']] = $row;
				}
			}
			$this->cache->set('admin/module', $modules);
		}
		return $modules;
	}
	public function tree()
	{
		if($modules = $this->fetch_all()){
			$tree = array();
			foreach($modules as $k=>$v){
				if($v['module'] == 'top'){
					$tree[$k] = $v;
				}
			}
			foreach($modules as $k=>$v){	
				if($v['module'] == 'menu'){
					$tree[$v['parent_id']]['menu'][$k] = $v;
				}
			}
			foreach($modules as $k=>$v){	
				if($v['module'] == 'module'){
					$ppk = $modules[$v['parent_id']]['parent_id'];
					$tree[$ppk]['menu'][$v['parent_id']]['menu'][$k] = $v;
				}
			}
			return $tree;
		}
		return false;
	}
	public function top_menu()
	{
		if($modules = $this->fetch_all()){
			$tree = array();
			foreach($modules as $k=>$v){
				if($v['module'] == 'top'){
					$tree[$k] = $v;
				}
			}
			return $tree;
		}
		return false;		
	}
	public function menu_tree()
	{
		if($modules = $this->fetch_all()){
			$tree = array();
			foreach($modules as $k=>$v){
				if($v['module'] == 'menu'){
					$tree[$k] = $v;
				}else if($v['module'] == 'module' && $v['visible']){
					$tree[$v['parent_id']]['menu'][$k] = $v;
				}
			}
			return $tree;
		}
		return false;
	}
	public function module($ID)
	{
		$modules = $this->fetch_all();
		if(($mod = $modules[$ID]) /*&& $mod['module'] == 'module'*/){
			return $mod;
		}
		return false;
	}
	public function ctlmap($ctl, $act='index')
	{
		if($modules = $this->fetch_all()){
			foreach($modules as $k=>$v){
				if($v['module'] == 'module' && $v['ctl'] == $ctl && $v['act'] == $act){
					return $v;
				}
			}
		}
		return false;
	}
	public function modules($PID)
	{
		$modules = array();
		if($mod_list = $this->fetch_all()){
			foreach($mod_list as $k=>$v){
				if($v['parent_id'] == $PID && $v['module'] == 'module'){
					$modules[$k] = $v;
				}
			}			
		}
		return $modules;
	}
}