<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: menu.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::M('module/base');
class Mdl_Module_Menu extends Mdl_Module_Base
{
	
	public function create($data, $checked=false)
	{
		if(!$checked && !$data = $this->_check($data)){
			return false;
		}
		$data['orderby'] = $data['orderby'] ? $data['orderby'] : 50;
		$data['dateline'] = __CFG::TIME;
		if($ID = $this->db->insert($this->_table,$data,true)){
			$this->flush();
		}
		return $ID;
	}
	public function update($ID, $data, $checked=false)
	{
		if(!$checked && !$data = $this->_check($data, $ID)){
			return false;
		}
		if($this->db->update($this->_table, $data, "mod_id='$ID'")){
			if($this->db->Affected_Rows()){
				$this->flush();
			}
			return true;
		}else{
			$this->msgbox->add('服务器内部错误',501);
			return false;
		}
	}
	public function remove($IDS)
	{
		if(!K::M('verify/check')->ids($IDS)){
			$this->msgbox->add('传递的参数不正确',401);
			return false;
		}
		$sql = "SELECT COUNT(1) FROM ".$this->table($this->_table)." WHERE parent_id IN($IDS)";
		if($this->db->GetOne($sql)){
			$this->msgbox->add('要删除的菜单中含有子项目,请先移动或删除子项目',402);
			return false;			
		}
		$sql = "DELETE FROM ".$this->table($this->_table)." WHERE mod_id IN($IDS) AND level IN(1,2)";
		if($this->db->Execute($sql)){
			if($this->db->Affected_Rows()){
				$this->flush();
			}
			return true;			
		}
		return false;
	}
	protected function _check($data, $ID=null)
	{
		if(isset($data['title']) || !$ID){
			if(empty($data['title'])){
				$this->msgbox->add('导航菜单标题不能为空',401);
				return false;
			}
		}
		if(isset($data['parent_id']) || !$ID){
			if($pid = intval($data['parent_id'])){
				if(!$parent = K::M('module/view')->module($pid)){
					$this->msgbox->add('指定的父级菜单不存在',402);
					return false;
				}else if($parent['module'] == 'module'){
					$this->msgbox->add('指定的父级菜不能为控制模型',403);
					return false;
				}			
				$data['level'] = 2;
				$data['module'] = 'menu';
				$data['parent_id'] = $pid;
			}else{
				$data['level'] = 1;
				$data['module'] = 'top';
				$data['parent_id'] = 0;
			}
		}
		if(isset($data['orderby'])){
			$data['orderby'] = intval($data['orderby']);
		}
		return parent::_check($data);
	}
}