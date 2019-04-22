<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/22
 * Time: 18:12
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Pei_Auth extends  Model {

    public $uid = 0;
    public $uname = '';
    public $member = array();
    public $token = null;
    public function token($token=null)
    {
        $token = $token !== null ? $token : $this->cookie->get('GROUP-TOKEN');
        if($token){
            if($this->_check_token($token)){
                $a = array('GROUP-TOKEN'=>$token,'AGENT'=>$_SERVER['HTTP_USER_AGENT']);
                K::$system->OTOKEN = K::M('secure/crypt')->arrhex($a);
                return true;
            }else{

                $this->cookie->delete('GROUP-TOKEN');
            }

        }
        return false;
    }
    /**
     * 用户登录
     * @param   $u  uid/手机号
     * @param   $p  密码{明文密码}
     */
    public function login($u, $p, $l=null, $ismd5=false, $keep=false)
    {
        $passwd  = $ismd5 ? $p : md5($p);
        if($l === null){
            if(K::M('verify/check')->vietnamMobile($u)){
                $l = 'mobile';
            }else{
                $l = 'uid';
            }
        }

        if(!$m = K::M('pei/group')->member($u, $l)){
            $this->msgbox->add('手机号不存在!!',111);
        }else if($m['passwd'] != $passwd){
            $this->msgbox->add('登录密码不正确!!',112);
        }else if($m['closed']){
            $this->msgbox->add('很抱歉,该用户已锁定不能登录',113);
        }else{
            $this->uid = $m['group_id'];
            $this->member = $m;
            $expire = $keep ? 2592000 : 0;
            $token = $this->create_token($this->uid, $passwd);

            $this->cookie->set('GROUP-TOKEN', $token);
            $this->token = $token;

            return $m;
        }
        return false;
    }
    public function loginout()
    {

        $this->cookie->delete('GROUP-TOKEN');
        return true;
    }

    public function manager($uid)
    {
        $uid = (int)$uid;
        if(!$member = K::M('pei/group')->detail($uid)){
            return false;
        }else{
            $token = $this->create_token($uid, $member['passwd']);
            //$this->cookie->delete('TOKEN');
            $this->cookie->set('GROUP-TOKEN', $token);
            $this->token = $token;
            return true;
        }
    }

    //生成TOKEN
    public function create_token($uid, $pwd)
    {
        //$s = strtoupper(md5($_SERVER['HTTP_USER_AGENT'].$uid.md5(__CFG::SECRET_KEY.$pwd,true)));
        $s = strtoupper(md5($uid.md5(__CFG::SECRET_KEY.$pwd,true)));
        /*if(strpos($_SERVER['HTTP_USER_AGENT'],"com.jhcms.ios")){  //判断是否是APP调用WEBVIEW
            $s = strtoupper(md5($uid.md5(__CFG::SECRET_KEY.$pwd,true)));
        }else{
            $s = strtoupper(md5($_SERVER['HTTP_USER_AGENT'].$uid.md5(__CFG::SECRET_KEY.$pwd,true)));
        }*/
        $token = "{$uid}-KT{$s}";
        return $token;
    }
    public function update_passwd($pwd, $ismd5=true)
    {
        $pwd = trim($pwd);
        if(!$this->uid){
            $this->msgbox->add("你没有权限修改密码",401);
        }else if($ismd5 && !preg_match("/^[0-9a-f]{32}$/i", $pwd)){
            $this->msgbox->add("密码的格式不正确",402);
        }else if(!$ismd5 && !preg_match('/^[\x20-\x7E]{6,16}$/',$pwd)){
            $this->msgbox->add("密码的格式不正确",403);
        }else if(K::M('pei/group')->update_passwd($this->uid, $pwd)){
            $this->passwd = md5($pwd);
            $cookie = self::$system->cookie;
            $expire = $cookie->get('TOKEN-KEEP') ? NULL : 86400;
            $token = $this->create_token($this->uid, $this->passwd);
            //$this->cookie->delete('TOKEN');
            $cookie->set('GROUP-TOKEN', $token, $expire);

            return true;
        }
        return false;
    }

    protected function _check_token($token)
    {

        $a = explode('-',$token);
        if(!$uid = intval($a[0])){
            return false;
        }
        if(!$m = K::M('pei/group')->member($uid)){
            return false;
        }else if($this->create_token($m['group_id'],$m['passwd']) != $token){
            return false;
        }else if($m['closed']){
            return false;
        }
        $this->uid = $m['group_id'];
        $this->member = $m;
        $this->token = $token;
        return true;
    }



}