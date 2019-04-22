<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: handler.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::M('module/base');
class Mdl_Module_Handler extends Mdl_Module_Base
{
	
	public function create($data, $checked=false)
	{
		if(!$checked && !$data = $this->_check($data)){
			return false;
		}else if($ctlmap = K::M('module/view')->ctlmap($data['ctl'], $data['act'])){
			$this->msgbox->add("您要添加的控制模型已经存在[{$data[ctl]}:{$data[act]}]",410);
			return false;
		}
		$data['module'] = 'module';
		$data['level'] = 3;
		$data['orderby'] = $data['orderby'] ? $data['orderby'] : 50;
		$data['dateline'] = __CFG::TIME;
		if($ID = $this->db->insert($this->_table, $data, true)){
			$this->flush();
		}
		return $ID;
	}
	public function update($ID, $data, $checked=false)
	{
		if(!$checked && !$this->_check($data, $ID)){
			return false;
		}
		unset($data['level'], $data['module']); //创建后不可修改
		if($this->db->update($this->_table, $data, "mod_id='$ID'")){
			if($this->db->affected_rows()){
				$this->flush();
			}
			return true;
		}
		return false;
	}
	public function remove($IDS)
	{
		if(!K::M('verify/check')->ids($IDS)){
			$this->msgbox->add('传递的参数不正确',401);
			return false;
		}
		$sql = "DELETE FROM ".$this->table($this->_table)." WHERE mod_id IN($IDS) AND level='3'";
		if($this->db->Execute($sql)){
			if($this->db->Affected_Rows()){
				$this->flush();
			}
		}
		return true;
	}
	protected function _check($data, $ID=null)
	{
		//check title
		if(isset($data['title']) || !$ID){
			if(empty($data['title'])){
				$this->msgbox->add('控制模块的标题不能为空',401);
				return false;
			}
		}
		//check ctl
		if(isset($data['ctl']) || !$ID){
			if(!preg_match('/^[\w\/]+$/i', $data['ctl'])){
				$this->msgbox->add("控制器输入不合法",402);
				return false;
			}
			/*
			$file = __APP_DIR."controllers/{$data['ctl']}.ctl.php";
			if(!is_string($data['ctl']) || !file_eists($file)){
				$this->msgbox->add("要添加的控制器不存在,请确定文件:{$file}存在",402);
				return false;
			}
			*/		
		}
		//check act
		if(isset($data['act']) || !$ID){
			if(!preg_match('/^[a-z\_]\w+$/i', $data['act'])){
				$this->msgbox->add("Action的名称不合法",403);
				return false;
			}
		}
		//check parent
		if(isset($data['parent_id']) || !$ID){
			if($pid = intval($data['parent_id'])){
				if(!$parent = K::M('module/view')->module($pid)){
					$this->msgbox->add('指定的父级菜单不存在',404);
					return false;
				}else if($parent['module'] != 'menu'){
					$this->msgbox->add('控制模块只能添加在Menu下',405);
					return false;
				}			
			}
		}
		if(isset($data['orderby'])){
			$data['orderby'] = intval($data['orderby']);
		}
		if(isset($data['visible']) || !$ID){
			$data['visible'] = $data['visible'] ? 1 : 0;
		}
		return parent::_check($data);
	}
}