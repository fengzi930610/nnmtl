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
class Mdl_Shop_Auth extends Model
{   
    public $shop_id = 0;
    public $shop = array();
    public $token =  null;
    public function token($token=null)
    {
        $token = $token !== null ? $token : $this->cookie->get('BIZ_TOKEN');
        if($token){
            if($this->_check_token($token)){
                $a = array('BIZ_TOKEN'=>$token,'AGENT'=>$_SERVER['HTTP_USER_AGENT']);
                K::$system->OTOKEN = K::M('secure/crypt')->arrhex($a);
                $this->cookie->set('BIZ_TOKEN', $token);
                return true;
            }
            $this->cookie->delete('BIZ_TOKEN');
        }
        return false;
    }
    /**
     * 用户登录
     * @param   $u  shop_id/手机号
     * @param   $p  密码{明文密码}
     */
    public function login($u, $p, $l=null, $ismd5=false, $keep=false)
    {
        $passwd =$ismd5 ? $p : md5($p);
        if($l === null){
            if(K::M('verify/check')->vietnamMobile($u)){
                $l = 'mobile';
            }else{
                $l = 'shop_id';
            }
        }
        if(!$shop = K::M('shop/shop')->shop($u, $l)){

            $this->msgbox->add('手机号不存在!!',111);
        }else if($shop['passwd'] != $passwd){
            $this->msgbox->add('登录密码不正确!!',112);
        }else if($shop['closed']){
            $this->msgbox->add('很抱歉,该用户已锁定不能登录',113);
        }else{
            $this->shop_id = $shop['shop_id'];
            $this->shop = $shop;
            $expire = $keep ? 2592000 : 0;
            $token = $this->create_token($this->shop_id, $passwd);
            $this->cookie->delete('BIZ_TOKEN');
            $this->cookie->set('BIZ_TOKEN', $token, $expire);
            $this->token = $token;
            return $shop;
        }
        return false;       
    }
    public function loginout()
    {
        $this->cookie->delete('BIZ_TOKEN');
        return true;            
    }
    
    public function manager($shop_id,$keep=false){
        $shop_id = (int)$shop_id;
        if(!$shop = K::M('shop/shop')->detail($shop_id)){
           return false;
        }else{
            $expire = $keep ? 2592000 : 0;
            $token = $this->create_token($shop_id, $shop['passwd']);
            $this->cookie->delete('BIZ_TOKEN');
            $this->cookie->set('BIZ_TOKEN', $token,$expire);
            return true;
        }
    }
    
    //生成TOKEN
    public function create_token($shop_id, $pwd)
    {
        //$s = strtoupper(md5($_SERVER['HTTP_USER_AGENT'].$shop_id.md5(__CFG::SECRET_KEY.$pwd.__IP,true)));
        $s = strtoupper(md5($shop_id.md5(__CFG::SECRET_KEY.$pwd,true)));
        //$s = strtoupper(md5($_SERVER['HTTP_USER_AGENT'].$shop_id.md5(__CFG::SECRET_KEY.$pwd,true)));
        $token = "{$shop_id}-KT{$s}";
        return $token;
    }
    public function update_passwd($pwd, $ismd5=true)
    {
        $pwd = trim($pwd);
        if(!$this->shop_id){
             $this->msgbox->add("你没有权限修改密码",401);
        }else if($ismd5 && !preg_match("/^[0-9a-f]{32}$/i", $pwd)){
            $this->msgbox->add("密码的格式不正确",402);
        }else if(!$ismd5 && !preg_match('/^[\x20-\x7E]{6,16}$/',$pwd)){
            $this->msgbox->add("密码的格式不正确",403);
        }else{
            $this->passwd = $ismd5 ? $pwd : md5($pwd);
            if(K::M('shop/shop')->update($this->shop_id, array('passwd'=>$this->passwd))){
                $this->passwd = md5($pwd);
                $cookie = self::$system->cookie;
                $expire = $cookie->get('TOKEN-KEEP') ? NULL : 86400;
                $token = $this->create_token($this->shop_id, $this->passwd);
                $this->cookie->delete('BIZ_TOKEN');
                $cookie->set('BIZ_TOKEN', $token, $expire);  
                return true;
            }            
        } 
        return false;
    }

    protected function _check_token($token)
    {
        $a = explode('-',$token);
        if(!$shop_id = intval($a[0])){
            return false;
        }
        if(!$shop = K::M('shop/shop')->shop($shop_id)){
            return false;
        }else if($this->create_token($shop['shop_id'], $shop['passwd']) != $token){
            return false;
        }else if($shop['closed']){
            return false;
        }
        $this->shop_id = $shop['shop_id'];
        $this->shop = $shop;
        $this->token = $token;
        return true;    
    }
}