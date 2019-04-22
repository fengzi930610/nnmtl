<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Auth extends Model
{
    public $staff_id = 0;
    public $name = '';
    public $staff = array();
    public $token = null;
    public function token($token=null)
    {
        $token = $token ?  $token : $this->cookie->get('STAFF_TOKEN');
        if($token){
            if($this->_check_token($token)){
                $a = array('STAFF_TOKEN'=>$token,'AGENT'=>$_SERVER['HTTP_USER_AGENT']);
                K::$system->OTOKEN = K::M('secure/crypt')->arrhex($a);
                return true;
            }
            $this->cookie->delete('STAFF_TOKEN');
        }
        return false;
    }
    /**
     * 用户登录
     * @param   $u  staff_id/手机号
     * @param   $p  密码{明文密码}
     */
    public function login($u, $p, $l=null, $ismd5=false, $keep=false)
    {
        $passwd =$ismd5 ? $p : md5($p);
        if($l === null){
            if(K::M('verify/check')->vietnamMobile($u)){
                $l = 'mobile';
            }else{
                $l = 'staff_id';
            }
        }
        if(!$staff = K::M('staff/staff')->staff($u, $l)){
            $this->msgbox->add('手机号不存在',111);
        }else if($staff['passwd'] != $passwd){
            $this->msgbox->add('登录密码不正确',112);
        }else if($staff['closed']){
            $this->msgbox->add('很抱歉,该用户关闭不能登录',113);
        }else{
            $this->staff_id = $staff['staff_id'];
            $this->staff = $staff;
            $expire = $keep ? 2592000 : 0;
            $token = $this->create_token($this->staff_id, $passwd);
            $this->cookie->delete('STAFF_TOKEN');
            $this->cookie->set('STAFF_TOKEN', $token);
            $this->token = $token;
            $staff['token'] = $token;
            K::M('staff/staff')->update($staff['staff_id'], array('lastlogin'=>__CFG::TIME, 'loginip'=>__IP), true);
            return $staff;
        }
        return false;       
    }
    /**
     * 用户退出登录
     */
    public function loginout()
    {
        $this->cookie->delete('STAFF_TOKEN');
        return true;            
    }
    /**
     * 检测用户是否存在
     * @param $staff_id
     */
    public function manager($staff_id){
        $staff_id = (int)$staff_id;
        if(!$staff = K::M('staff/staff')->detail($staff_id)){
           return false;
        }else{
            $token = $this->create_token($staff_id, $staff['passwd']);
            $this->cookie->delete('STAFF_TOKEN');
            $this->cookie->set('STAFF_TOKEN', $token);
            return true;
        }
    }
    
    //生成TOKEN
    public function create_token($staff_id, $pwd)
    {
     //   $s = strtoupper(md5($_SERVER['HTTP_USER_AGENT'].$staff_id.md5(__CFG::SECRET_KEY.$pwd,true)));
        $s = strtoupper(md5($staff_id.md5(__CFG::SECRET_KEY.$pwd,true)));
        $token = "{$staff_id}-KT{$s}";
        return $token;
    }
    /**
     * 更新密码
     * @param $pwd,新密码
     */
    public function update_passwd($pwd, $ismd5=true)
    {
        $pwd = trim($pwd);
        $md5pwd = $ismd5 ? $pwd : md5($pwd);
        if(!$this->staff_id){
             $this->msgbox->add('你没有权限修改密码',401);
        }else if($ismd5 && !preg_match("/^[0-9a-f]{32}$/i", $pwd)){
            $this->msgbox->add('密码的格式不正确',402);
        }else if(!$ismd5 && !preg_match('/^[\x20-\x7E]{6,16}$/',$pwd)){
            $this->msgbox->add('密码的格式不正确',403);
        }else if(K::M('staff/staff')->update($this->staff_id, array('passwd'=>$md5pwd))){
            $this->passwd = $md5pwd;
            $cookie = self::$system->cookie;
            $expire = $cookie->get('TOKEN-KEEP') ? NULL : 86400;
            $token = $this->create_token($this->staff_id, $this->passwd);
            $this->token = $token;
            $cookie->delete('STAFF_TOKEN');
            $cookie->set('STAFF_TOKEN', $token, $expire);
            return true;
        }
        return false;
    }
    protected function _check_token($token)
    {
      
        $a = explode('-',$token);
        if(!$staff_id = intval($a[0])){
            return false;
        }
        if(!$staff = K::M('staff/staff')->detail($staff_id)){
            return false;
        }else if($this->create_token($staff['staff_id'],$staff['passwd']) != $token){
            return false;
        }else if($staff['closed']){
            return false;
        }
        $this->staff_id = $staff['staff_id'];
        $this->staff = $staff;
        return true;    
    }        
}