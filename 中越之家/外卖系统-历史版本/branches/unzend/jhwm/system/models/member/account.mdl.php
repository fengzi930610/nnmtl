<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: account.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Member_Account extends Model
{   
    public function create($data, $wx=false)
    { 
        
       if(!defined('IN_ADMIN')){
            if(!$this->check_signup_count()){
                return false;
            }
        }
        if($wx == false){
           if(!$mobile = $this->check_mobile($data['mobile'])){
                return false;
           }   
        }
        if(!$passwd = $this->check_passwd($data['passwd'])){
            return false;
        }
        $a = array('mobile'=>$mobile, 'passwd'=>md5($passwd),'paypasswd'=>md5($passwd),'nickname'=>$data['nickname'],'wx_openid'=>$data['wx_openid'],'pmid'=>$data['pmid']);
        if($data['wx_unionid']){
            $a['wx_unionid'] = $data['wx_unionid'];
        }    
        $a['dateline'] = __CFG::TIME;
        $a['regip'] = __IP;
        $a['register_from'] = $data['register_from'];
        if(!$uid = K::M('member/member')->create($a, true)){
            return false;
        }        
        if(!defined('IN_ADMIN')){
           K::$system->auth->login($mobile, $passwd, 'mobile'); 
        }
        return $uid;
    }

    public function check_mobile($mobile)
    {
        if(!K::M('verify/check')->vietnamMobile($mobile)){
            $this->msgbox->add('手机号码格式不正确', 511);
            return false;
        }else if($member = K::M('member/member')->member($mobile, 'mobile')){
            $this->msgbox->add('此手机号已被占用', 512);
            return false;
        }
        return $mobile;
    }
    
     public function check_uname($uname)
    {
        $ret = $uname;
        if($ret > 0) {
            return $uname;
        }else if($ret == -1) {
            $this->msgbox->add('用户名不合法', 281);
        } else if($ret == -2) {
            $this->msgbox->add('包含要不允许注册的词语', 282);
        }else if($ret == -3) {
            $this->msgbox->add('用户名已经存在', 283);
        }
        return false;   
    }
    
    
    public function check_passwd($passwd)
    {
        
       if(!preg_match('/^[\x21-\x7E]{6,32}$/', $passwd)){
            $this->msgbox->add('用户密码只包含(数字,大小写字母,特殊符号,不含空格)长度6~32字符', 401);
            return false;
        }
        
        return $passwd;
    }
    //passwd 为明文的密码,非MD5后的。
    public function update_passwd($uid, $passwd)
    {
        if(!$uid = (int)$uid){
            return false;
        }else if(!$passwd = $this->check_passwd($passwd)){
            return false;
        }else if(!$member = K::M('member/member')->member($uid)){
            return false;
        }
        return K::M('member/member')->update($uid, array('passwd'=>md5($passwd)), true);
    }
    
     public function update_paypasswd($uid, $passwd)
    {
        if(!$uid = (int)$uid){
            return false;
        }else if(!$passwd = $this->check_passwd($passwd)){
            return false;
        }else if(!$member = K::M('member/member')->member($uid)){
            return false;
        }
        return K::M('member/member')->update($uid, array('paypasswd'=>md5($passwd)), true);
    }
    public function update_mobile($uid, $mobile)
    {
       
        if(!$uid = (int)$uid){
            return false;
        }else if(!$mobile = $this->check_mobile($mobile)){
            return false;
        }else if(!$member = K::M('member/member')->member($uid)){
            return false;
        }
        return K::M('member/member')->update($uid, array('mobile'=>$mobile), true);
    }
    
    
    public function up_passwd($uid, $passwd)
    {
       
        if(!$uid = (int)$uid){
            return false;
        }else if(!$passwd = $this->check_passwd($passwd)){
            return false;
        }else if(!$member = K::M('member/member')->member($uid)){
            return false;
        }
        return K::M('member/member')->update($uid, array('passwd'=>md5($passwd)), true);
    }
    
    
    public function check_signup_count()
    {
        $access = K::$system->config->get('access');
        if($signup_count = (int)$access['signup_count']){
            if($signup_count < K::M('member/member')->count(array('regip'=>__IP, 'dateline'=>'>:'.(__TIME-86400)))){
                $this->msgbox->add('同一IP24小时只能注册'.$signup_count.'用户', 501);
                return false;
            }
        }
        if($signup_time = (int)$access['signup_time']){
            $time = __TIME - $signup_time*60;
            if(K::M('member/member')->count(array('regip'=>__IP, 'dateline'=>'>:'.$time))){
                $this->msgbox->add('同一IP两次注册间隔'.$signup_time.'分钟', 502);
                return false;
            }
        }
        return true;
    }
    
}
