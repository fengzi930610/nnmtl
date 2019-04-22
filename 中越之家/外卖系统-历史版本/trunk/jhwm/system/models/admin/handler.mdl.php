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
Import::M('admin/base');
class Mdl_Admin_Handler extends Mdl_Admin_Base
{
	
	public function create($data)
	{
		if(!$data = $this->_check($data)){
			return false;
		}
		$data['dateline'] = __CFG::TIME;
		return $this->db->insert($this->_table, $data, true);
	}
	public function update($ID, $data, $checked=false)
	{
		if(!$checked && !($data = $this->_check($data, $ID))){
			return false;
		}
		$ID = intval($ID);
		return $this->db->update($this->_table, $data, "admin_id='$ID'");
	}
	public function update_passwd($admin_id, $passwd)
	{
		if(!$admin_id = (int)$admin_id){
			return false;
		}else if(!preg_match('/^[0-9a-z]{32}$/i', $passwd)){
			$this->msgbox->add('密码格式不正确', 451);
			return false;
		}
		return $this->db->update($this->_table, array('passwd'=>$passwd), "admin_id='$admin_id'");
	}
	public function update_login($uid)
	{
		$uid = intval($uid);
		$a = array('last_ip'=>__IP, 'last_login'=>__CFG::TIME);
		return $this->db->update($this->_table, $a, "admin_id='$uid'");
	}
	public function remove($IDS)
	{
        if(!K::M('verify/check')->ids($IDS)){
            return false;
        }
        $sql = "UPDATE ".$this->table($this->_table)." SET closed=3 WHERE role_id IN($IDS)";
        if($this->db->Execute($sql)){
            if($this->db->affected_rows){
                $this->flush();
            }
            return true;
        }
        return false;		
	}
	protected function _check($data, $ID=null)
	{
		if($ID = intval($ID)){
			if(!$admin = K::M('admin/view')->admin($ID)){
				$this->msgbox->add('您要修改的管理员不存在',401);
			}
			$role = K::M('admin/role')->role($admin['role_id']);
			if($role['role'] == 'system' && K::$system->admin->role['role'] != 'system'){
				$this->msgbox->add('你没有权限修改系统管理员',405);
				return false;
			}
		}
		if(isset($data['admin_name']) || !$ID){
			if(!$data['admin_name']){
				$this->msgbox->add('管理员名称不能为空',401);
				return false;
			}else if($admin = K::M('admin/view')->admin(0, $data['admin_name'])){
				if(!$ID || $ID!=$admin['admin_id']){
					$this->msgbox->add('管理员名称已经存在',402);
					return false;		
				}
			}
			$data['admin_name'] = K::M('content/html')->text($data['admin_name']);
		}	
		if(isset($data['role_id']) || !$ID){
			if(!is_numeric($data['role_id'])){
				$this->msgbox->add('没有指定管理员所属角色',403);
				return false;	
			}else if(!$a = K::M('admin/role')->role($data['role_id'])){
				$this->msgbox->add('管理员所属角色不存在或已经删除',404);
				return false;	
			}
			//对系统管理员作权限判断
			if($a['role'] == 'system' && K::$system->admin->role['role'] != 'system'){
				if($admin){
					$this->msgbox->add('你没有权限设置系统管理员角色',405);
				}else{
					$this->msgbox->add('你没有权限添加系统管理员角色',405);
				}
				return false;		
			}
		}
		if(isset($data['passwd']) || !$ID){
			if(strlen($data['passwd'])<6 || strlen($data['passwd'])>20){
				$this->msgbox->add('管理员登录密码长度为6~20个字符',406);
				return false;
			}
			$data['passwd'] = md5($data['passwd']);
		}
		if(isset($data['closed'])){
			$data['closed'] = intval($data['closed']);
		}		
		unset($data['admin_id'],$data['last_login'],$data['dateline']);
		return parent::_check($data);
	}
}