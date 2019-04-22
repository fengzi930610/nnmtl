<?php
/**
 * Copy	Right Anhuike.com
 * $Id auth.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_Admin_Auth extends Model
{
	public $admin_id = 0;
	public $admin_name = '';
	public $admin = array();
	public $role = null;
	public $_menu_tree = null;
	
	public function token()
	{
		if($token = $this->cookie->get('ATOKEN')){
			if($this->_check_token($token)){
                $a = array('ATOKEN'=>$token,'AGENT'=>$_SERVER['HTTP_USER_AGENT']);
                K::$system->OATOKEN = K::M('secure/crypt')->arrhex($a);
				return true;
			}
			$this->cookie->delete('ATOKEN');
		}
		return false;
	}
	/**
	 * 用户登录
	 * @param   $u  用户名/邮箱
	 * @param   $p  密码{明文密码}
	 */
	public function login($u,$p)
	{
		$passwd = md5($p);
		if(!$m = K::M('admin/view')->admin(0,$u)){
			$this->msgbox->add('登录名或密码不正确!!',111);
		}else if($m['passwd'] != $passwd){
			$this->msgbox->add('登录名或密码不正确!!',112);
		}else if($m['closed'] == 3){
			$this->msgbox->add('很抱歉,访用户已经删除!!',112);
		}else if($m['closed'] == 2){
			$this->msgbox->add('很抱歉,该用户已锁定不能登录',113);
		}else{
			$this->admin_id = $m['admin_id'];
			$this->admin_name = $m['admin_name'];
			$this->admin = $m;
			$token = $this->create_token($this->admin_id,$passwd);
			K::M('admin/handler')->update_login($this->admin_id);
			$this->cookie->set('ATOKEN', $token, 0);
			$this->cookie->set('ADMIN', $this->admin_name, NULL);
			return true;
		}
		return false;
	}
	
	public function loginout()
	{
		$this->cookie->delete('ATOKEN');
		return true;		
	}
	public function modifypasswd($odlpasswd, $newpasswd)
	{
		if(md5($odlpasswd) != $this->admin['passwd']){
			$this->msgbox->add('原密码输入不正确', 421);
			return false;
		}else if(!preg_match('/^[\x21-\x7E]{6,15}$/', $newpasswd)){
            $this->msgbox->add('用户密码只包含(数字,大小写字母,特殊符号,不含空格)长度6~15字符', 422);
            return false;
        }
        $passwd = md5($newpasswd);
        return K::M('admin/handler')->update_passwd($this->admin_id, $passwd);
	}
  
	//生成TOKEN
	public function create_token($uid,$pwd)
	{
		//$s = md5($_SERVER['HTTP_USER_AGENT'].$uid.md5(__CFG::SECRET_KEY.$pwd.__IP,true),true);
		//$s = K::M('secure/crypt')->strh);
		//$s = strtoupper(md5($_SERVER['HTTP_USER_AGENT'].$uid.md5(__CFG::SECRET_KEY.$pwd.__IP,true)));
		$s = strtoupper(md5($_SERVER['HTTP_USER_AGENT'].$uid.md5(__CFG::SECRET_KEY.$pwd,true)));
		$token = "{$uid}-KT{$s}";
		return $token;
	}
	private function _check_token($token)
	{
		$a = explode('-',$token);
		if(!$uid = intval($a[0])){
			return false;
		}
		if(!$m = K::M('admin/view')->admin($uid,'admin_id')){
			return false;
		}else if($this->create_token($m['admin_id'],$m['passwd']) != $token){
			return false;
		}else if(!in_array($m['closed'],array(0,1))){
			return false;
		}
		$this->admin_id = $m['admin_id'];
		$this->admin_name = $m['admin_name'];
		$this->admin = $m;
		$this->role = K::M('admin/role')->role($m['role_id']);
		$this->admin['role_name'] = $this->role['role_name'];
		$this->admin['role'] = $this->role['role'];
		$this->admin['priv'] = $this->role['priv'];
		/*
		if($this->role['priv']){
			$this->priv = explode(',',$this->role['priv']);
		}else{
			$this->priv = array();
		}
		*/
		return true;    
	}

	public function tree()
	{
		if($this->_menu_tree !== null){
			return $this->_menu_tree;
		}
		if(!$this->admin['priv'] && $this->admin['role'] != 'system'){
			return false; 
		}
		if(!$tree = K::M('module/view')->tree()){
			return false;
		}
		$menu = array();
		foreach($tree as $k=>$v){
			$a = array();
			foreach((array)$v['menu'] as $kk=>$vv){
				$b = array();
				foreach((array)$vv['menu'] as $kkk=>$vvv){
					if($vvv['visible'] && $this->check_priv($kkk)){
						$b[$kkk] = $vvv;
					}
				}
				if(!empty($b) || $this->role['role'] == 'system'){ //系统管理员显示全部菜单即使没有子及
					$vv['menu'] = $b;
					$a[$kk] = $vv;
				}
			}
			if(!empty($a) || $this->role['role'] == 'system'){ //系统管理员显示全部菜单即使没有子及
				$v['menu'] = $a;
				$menu[$k] = $v;
			}
		}
		$this->_menu_tree = $menu;
		return $menu;
	}
	public function check_priv($mod_id)
	{
		if($this->admin['role'] == 'system'){
			return true;
		}else if(in_array($mod_id, $this->admin['priv'])){
			return true;
		}
		return false;
	}
}