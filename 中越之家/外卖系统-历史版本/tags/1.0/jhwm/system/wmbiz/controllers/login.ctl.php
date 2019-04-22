<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Login extends Ctl
{

    public function index()
    {
        if($data = $this->checksubmit('data')){
            if(!$mobile = K::M('verify/check')->mobile($data['mobile'])){
                $this->msgbox->add('手机号不正确', 211);
            }elseif(isset($data['sms_code'])){
                $sms_code = $data['sms_code'];
                /*if(empty($sms_code) || ($sms_code != K::M('system/session')->start()->get('code_'.$mobile))){
                    $this->msgbox->add('短信验证码不正确或已经过期', 212);
                }*/if(empty($sms_code) || ($sms_code != K::M('cache/cache')->get('code_'.$mobile))){
                    $this->msgbox->add('短信验证码不正确或已经过期', 212);
                }elseif(!$shop = K::M('shop/shop')->shop($mobile, 'mobile')){
                    $this->msgbox->add('登录帐号不存在', 213);
                }else if(K::M('shop/auth')->manager($shop['shop_id'])){
                    $this->msgbox->add('登录成功');
                    $this->msgbox->set_data('forward', $this->mklink('index'));
                }
            }elseif(!$passwd = $data['passwd']){
                $this->msgbox->add('登录密码不正确', 213);
            }elseif($shop = K::M('shop/auth')->login($mobile, $passwd, 'mobile')){
                $this->msgbox->add('登录成功');
                $redirect = K::M('waimai/waimai')->get_redirect($shop['shop_id']);
                if($redirect == 1){
                    $this->msgbox->set_data('forward', $this->mklink('newreg/index'));
                }else{
                    $this->msgbox->set_data('forward', $this->mklink('index'));
                }
                //$this->msgbox->set_data('forward', $this->mklink('index'));
            }
        }else{
            $this->tmpl = 'login/index.html';
        }
    }

	// 商户申请入驻
	public function signup()
	{
	    //$session =K::M('system/session')->start();
        $session =K::M('cache/cache');
        if($data = $this->checksubmit('data')){
            if(!$data = $this->check_fields($data, 'mobile,passwd,code')){
                $this->msgbox->add('非法的数据提交', 210);
            }elseif (empty($data['mobile'])) {
                $this->msgbox->add('手机号不能为空', 212);
            }else if(empty($data['code'])){
                $this->msgbox->add('验证码不能为空', 213);
            }else if($data['code'] != $session->get('code_'.$data['mobile'])){
                $this->msgbox->add('验证码不正确', 214);
            }else if(!$data['passwd']){
                $this->msgbox->add('登录密码不正确', 215);
            }else if($shop = K::M('shop/shop')->find(array('mobile'=>$data['mobile']))){
                $this->msgbox->add('该手机号码已存在', 216)->response();
            }else{
                $datas = array('mobile'=>$data['mobile'],'passwd'=>md5($data['passwd']));
                if($shop_id = K::M('shop/shop')->create($datas)){
                   $this->msgbox->add('申请入驻成功');
                   K::M('shop/auth')->login( $data['mobile'] , $data['passwd'], 'mobile');
                }else{
                    $this->msgbox->add('申请入驻失败，系统错误',217);
                }
            }
        }else{
            $this->tmpl = 'login/signup.html';
        }
	}

    // 退出登录
    public function loginout()
    {
        K::M('shop/auth')->loginout();
        header("Location:".$this->mklink('login'));
    }

    // 发送短信
    public function sendsms()
    {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            if (!$mobile = $this->GP('mobile')) {
                $this->msgbox->add('手机号不能为空', 211);
            }else if(!$mobile = K::M('verify/check')->mobile($mobile)){
                $this->msgbox->add('手机号有误', 212);
            }else if(!$img_code = $this->GP('img_code')){
                $this->msgbox->add('请输入图形验证码',250);
            }else if(!K::M('magic/verify')->check($img_code)){
                $this->msgbox->add('图形验证码错误',251);
            } else{
                $code = rand(100000,999999);
                //$session = K::M('system/session')->start();
                $session =K::M('cache/cache');
                $session->set('code_'.$mobile, $code,180); //15分钟缓存换成3分钟
                $smsdata =  array('code'=>$code);
                if(K::M('sms/sms')->send($mobile, 'login', $smsdata) || $code){
                    if(__DEBUG){
                        $this->msgbox->add('短信发送成功');
                    }else{
                        $this->msgbox->add('短信发送成功');
                    }
                }
            }
        }
    }

    // 找回密码(身份验证)
    public function forgot()
    {
        //$session = K::M('system/session')->start();
        $session =K::M('cache/cache');
        if($data = $this->checksubmit('data')){
            if (!$data = $this->check_fields($data, 'mobile,code')) {
                $this->msgbox->add('非法的数据提交', 211);
            }elseif (empty($data['mobile'])) {
                $this->msgbox->add('手机号不能为空', 212);
            }else if(empty($data['code'])){
                $this->msgbox->add('验证码不能为空', 213);
            }else if($data['code'] != $session->get('code_'.$data['mobile'])){
                $this->msgbox->add('验证码不正确', 214);
            }else{
                $this->cookie->set('BIZ-PWD-CODE', $data['code'], 300);
                $this->cookie->set('BIZ-MOBILE', $data['mobile'], 300);
                $this->msgbox->add('验证成功！');
                $this->msgbox->set_data('forward', $this->mklink('login:setpwd'));
            }
        }else{
            $this->tmpl = 'login/forgot.html';
        }
    }

    // 找回密码(设置密码)
    public function setpwd()
    {
        //$session =K::M('system/session')->start();
        $session =K::M('cache/cache');
        $mobile = $this->cookie->get('BIZ-MOBILE');
        if($data = $this->checksubmit('data')){
            if (!$data = $this->check_fields($data, 'new_passwd,new_passwd2')) {
                $this->msgbox->add('非法的数据提交', 211);
            }else if(empty($data['new_passwd'])){
                $this->msgbox->add('新密码不能为空', 212);
            }else if(empty($data['new_passwd2'])){
                $this->msgbox->add('确认密码不能为空', 213);
            }else if($data['new_passwd'] != $data['new_passwd2']){
                $this->msgbox->add('两次新密码输入不一致', 214);
            }else if(!$shop = K::M('shop/shop')->shop($mobile,'mobile')){
                $this->msgbox->add('该商家不存在', 215);
            }else if($shop['passwd'] == md5($data['new_passwd'])){
                $this->msgbox->add('新密码不能和旧密码一致', 216);
            }else if(K::M('shop/shop')->update($shop['shop_id'],array('passwd'=>md5($data['new_passwd'])))){
                $this->msgbox->add('修改登录密码成功');
            }else{
                $this->msgbox->add('登录密码修改失败',217);
            }
        }else{
            if (!($code = $this->cookie->get('BIZ-PWD-CODE')) || $code != $session->get('code_'.$mobile)) {
                header("location:" . $this->mklink('login:forgot', null, null));
            }
            $this->tmpl = 'login/setpwd.html';
        }
    }

    // 找回密码(重置成功)
    public function setpwd_success()
    {
        //$session =K::M('system/session')->start();
        $session =K::M('cache/cache');
        $mobile = $this->cookie->get('BIZ-MOBILE');
        if (!($code = $this->cookie->get('BIZ-PWD-CODE')) || $code != $session->get('code_'.$mobile)) {
            header("location:" . $this->mklink('login:forgot', null, null));
        }
        $session->set('code_'.$mobile, null);
        $this->tmpl = 'login/setpwd_success.html';
    }
    
    //异步上传文件
    public function uploadimg(){
        $upload = K::M('magic/upload')->upload($_FILES['file']);
        $this->msgbox->set_data('file',$upload);
        $this->msgbox->json();
    }
    
    public function password(){
        if($data = $this->checksubmit('data')){
            if (!$data = $this->check_fields($data, 'old_passwd,new_passwd,new_passwd2')) {
                $this->msgbox->add('非法的数据提交', 211);
            }else if($this->shop['passwd'] != md5($data['old_passwd'])){
                $this->msgbox->add('密码不正确', 210);
            }else if(empty($data['new_passwd'])){
                $this->msgbox->add('新密码不能为空', 212);
            }else if(empty($data['new_passwd2'])){
                $this->msgbox->add('确认密码不能为空', 213);
            }else if($data['new_passwd'] != $data['new_passwd2']){
                $this->msgbox->add('两次新密码输入不一致', 214);
            }else if($this->shop['passwd'] == md5($data['new_passwd'])){
                $this->msgbox->add('新密码不能和旧密码一致', 216);
            }else if(K::M('shop/shop')->update($this->shop_id,array('passwd'=>md5($data['new_passwd'])))){
                $this->msgbox->add('修改登录密码成功');
            }else{
                $this->msgbox->add('登录密码修改失败',217);
            }
        }
    }

    public function verify(){
        K::M('magic/verify')->output(80,30);
    }


}