<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: auth.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Ditui_Auth extends Model
{   
    public $ditui_id = 0;
    public $ditui = array();
    public $token =  null;
    public function token($token=null)
    {
        $token = $token !== null ? $token : $this->cookie->get('DITUI_TOKEN');
        if($token){
            if($this->_check_token($token)){
                $a = array('DITUI_TOKEN'=>$token,'AGENT'=>$_SERVER['HTTP_USER_AGENT']);
                K::$system->OTOKEN = K::M('secure/crypt')->arrhex($a);
                return true;
            }
            $this->cookie->delete('DITUI_TOKEN');
        }
        return false;
    }
    /**
     * 用户登录
     * @param   $u  ditui_id/手机号
     * @param   $p  密码{明文密码}
     */
    public function login($u, $p, $l=null, $ismd5=false, $keep=false)
    {
        $passwd =$ismd5 ? $p : md5($p);
        if($l === null){
            if(K::M('verify/check')->mobile($u)){
                $l = 'mobile';
            }else{
                $l = 'ditui_id';
            }
        }
        if(!$ditui = K::M('ditui/ditui')->ditui($u, $l)){
            $this->msgbox->add('手机号不存在!!',111);
        }else if($ditui['passwd'] != $passwd){
            $this->msgbox->add('登录密码不正确!!',112);
        }else if($ditui['audit'] !=1){
            $this->msgbox->add('很抱歉,该人员未审核不能登录',113);
        }else if($ditui['closed'] == 1){
            $this->msgbox->add('很抱歉,账户已被删除',114);
        }else{
            $this->ditui_id = $ditui['ditui_id'];
            $this->ditui = $ditui;
            $expire = $keep ? 2592000 : 0;
            $token = $this->create_token($this->ditui_id, $passwd);
            $this->cookie->delete('DITUI_TOKEN');
            $this->cookie->set('DITUI_TOKEN', $token, $expire);
            $this->token = $token;
            return $ditui;
        }
        return false;       
    }
    public function loginout()
    {
        $this->cookie->delete('DITUI_TOKEN');
        return true;            
    }
    
    public function manager($ditui_id){
        $ditui_id = (int)$ditui_id;
        if(!$ditui = K::M('ditui/ditui')->detail($ditui_id)){
           return false;
        }else{
            $token = $this->create_token($ditui_id, $ditui['passwd']);
            $this->cookie->delete('DITUI_TOKEN');
            $this->cookie->set('DITUI_TOKEN', $token);
            return true;
        }
    }
    
    //生成TOKEN
    public function create_token($ditui_id, $pwd)
    {
        //$s = strtoupper(md5($_SERVER['HTTP_USER_AGENT'].$ditui_id.md5(__CFG::SECRET_KEY.$pwd.__IP,true)));
        $s = strtoupper(md5($_SERVER['HTTP_USER_AGENT'].$ditui_id.md5(__CFG::SECRET_KEY.$pwd,true)));
        $token = "{$ditui_id}-KT{$s}";
        return $token;
    }
    public function update_passwd($pwd, $ismd5=true)
    {
        $pwd = trim($pwd);
        if(!$this->ditui_id){
             $this->msgbox->add("你没有权限修改密码",401);
        }else if($ismd5 && !preg_match("/^[0-9a-f]{32}$/i", $pwd)){
            $this->msgbox->add("密码的格式不正确",402);
        }else if(!$ismd5 && !preg_match('/^[\x20-\x7E]{6,16}$/',$pwd)){
            $this->msgbox->add("密码的格式不正确",403);
        }else{
            $this->passwd = $ismd5 ? $pwd : md5($pwd);
            if(K::M('ditui/ditui')->update($this->ditui_id, array('passwd'=>$this->passwd))){
                $this->passwd = md5($pwd);
                $cookie = self::$system->cookie;
                $expire = $cookie->get('TOKEN-KEEP') ? NULL : 86400;
                $token = $this->create_token($this->ditui_id, $this->passwd);
                $this->cookie->delete('DITUI_TOKEN');
                $cookie->set('DITUI_TOKEN', $token, $expire);  
                return true;
            }            
        } 
        return false;
    }

    protected function _check_token($token)
    {
        $a = explode('-',$token);
        if(!$ditui_id = intval($a[0])){
            return false;
        }
        if(!$ditui = K::M('ditui/ditui')->ditui($ditui_id)){
            return false;
        }else if($this->create_token($ditui['ditui_id'], $ditui['passwd']) != $token){
            return false;
        }else if($ditui['closed']){
            return false;
        }
        $this->ditui_id = $ditui['ditui_id'];
        $this->ditui = $ditui;
        $this->token = $token;
        return true;    
    }
}