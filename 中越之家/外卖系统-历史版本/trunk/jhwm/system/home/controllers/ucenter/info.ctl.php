<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Ucenter_Info extends Ctl_Ucenter
{
    public function index()
    {
        if($reback = htmlspecialchars($this->GP('reback'))){
            $this->pagedata['reback'] = $reback;
        }
	   $this->tmpl = "ucenter/info/index.html";
    }

    public function upload_face(){
        if($attach = $_FILES['avatar']){
            $data = array();
            if($a = K::M('magic/upload')->upload($attach, 'face')){
                $data['face'] = $a['photo'];
            }
            //修改头像
            if($up = K::M('member/member')->update($this->uid,$data)){
                $this->msgbox->add('头像设置成功');
                $this->msgbox->set_data("forward", $this->mklink('ucenter/info:index',null));
            }else{
                $this->msgbox->add('设置失败',211);
            }
        }
    }

    public function update_nickname() {
		$this->tmpl = "ucenter/info/update_nickname.html";
	}

    public function set_nickname(){
        $nickname = $this->GP('nickname');
        if(!$nickname){
            $this->msgbox->add('没有填写昵称!',211);
        }else if(strlen($nickname) > 18){
            $this->msgbox->add('昵称过长!',211);
        }else if(!$up = K::M('member/member')->update($this->uid,array('nickname'=>$nickname))){
            $this->msgbox->add('设置失败',211);
        }else{
             $this->msgbox->add('昵称设置成功');
             $this->msgbox->set_data("forward", $this->mklink('ucenter/info:index',null));
        }
    }

    public function update_mobile() {
        $this->pagedata['mobile'] = $this->MEMBER['mobile'];
		$this->tmpl = "ucenter/info/update_mobile.html";
	}

    //===== 20181129 新增 绑定手机号页面
    public function bind_mobile() {
        $this->MEMBER['mobile'] = trim($this->MEMBER['mobile']);

        //=====20181130因为可以修改手机号，所以不再使用自动路转
        // if($this->MEMBER['mobile']!=="" && (string)$this->MEMBER['mobile']!==(string)$this->MEMBER['uid'])    //如果已经设置手机号，则跳转回用户中心或返回地址
        // {
        //     $backUrl = trim($this->GP('rebackurl'));
        //     if(!$backUrl)
        //         $backUrl = $this->mklink('waimai/ucenter/member',null);
        //     header("location:".$backUrl);
        //     exit;
        // }
        //==================================================

        $this->tmpl = "ucenter/info/bind_mobile.html";
    }

    //更新手机事情，仅限于未绑定的用户进行首次手机号绑定使用！
    public function upt_mobile() {
        //=====20181130 因为可以更改手机号，所以不再需要检测
        // if($this->MEMBER['mobile']!=="" && (string)$this->MEMBER['mobile']!==(string)$this->MEMBER['uid'])
        // {
        //     $this->msgbox->add('您已绑定手机号');
        //     $this->msgbox->set_data("forward", $this->mklink('waimai/ucenter/member',null));
        //     return;
        // }
        //==================================================

        $session =K::M('cache/cache');
        $new_mobile = $this->GP('new_mobile');
        // $yzm     =  $this->GP('yzm');    //=====20181129 不再使用手机验证码
        if($new_mobile === $this->MEMBER['mobile']) {
            $this->msgbox->add("未修改手机号",250);
        }else if(!K::M('verify/check')->vietnamMobile($new_mobile)){
            $this->msgbox->add('新手机号不正确', 211);
        // }else if(!$yzm || ($yzm!=$session->get('code_'.$new_mobile))){//=====20181129 不再使用手机验证码
        //     $this->msgbox->add('验证码不正确', 211);//=====20181129 不再使用手机验证码
        }else if(K::M('member/account')->update_mobile($this->uid, $new_mobile)){
            $this->msgbox->add('绑定手机成功');
            $this->msgbox->set_data("forward", $this->mklink('waimai/ucenter/member',null));
        }else{
            $this->msgbox->add('绑定手机失败', 250);
        }
    }

    //=====================================================

    //更换手机号码
    public function set_mobile()
    {
        //$session = K::M('system/session')->start();
        $session =K::M('cache/cache');
        $pswd = $this->GP('pswd');
        $new_mobile = $this->GP('new_mobile');
        $yzm     =  $this->GP('yzm');
        $old_mobile =  $this->GP('old_mobile');
        if($this->MEMBER['passwd'] != md5($pswd)){
            $this->msgbox->add('登录密码不正确', 210);
        }elseif(!K::M('verify/check')->mobile($new_mobile)){
            $this->msgbox->add('新手机号不正确'.$new_mobile, 211);
        }else if(!$yzm || ($yzm!=$session->get('code_'.$old_mobile))){
            $this->msgbox->add('验证码不正确', 211);
        }else if(K::M('member/account')->update_mobile($this->uid, $new_mobile)){
            $this->msgbox->add('修改手机成功');
            $this->msgbox->set_data("forward", $this->mklink('ucenter/info:index',null));
        }else{
            $this->msgbox->add('修改手机失败', 250);
        }
    }

    public function update_passwd(){
        $data = K::M('member/member')->detail($this->uid);
        $this->pagedata['user'] = $data;
        $this->tmpl = "ucenter/info/update_passwd.html";
    }

    public function set_passwd()
    {
        $data = K::M('member/member')->detail($this->uid);
        //$session = K::M('system/session')->start();
        $session =K::M('cache/cache');
        $mobile = $this->GP('mobile');
        $passwd1 = $this->GP('passwd1');
        $passwd2 = $this->GP('passwd2');
        $yzm = $this->GP('yzm');
        if(!K::M('verify/check')->mobile($mobile)){
            $this->msgbox->add('手机号码不正确'.$mobile, 211);
        }else if(!$yzm || ($yzm!=$session->get('code_'.$mobile))){
            $this->msgbox->add('验证码不正确', 211);
        }else if(strlen($passwd1) <6){
            $this->msgbox->add('密码至少6位', 211);
        }else if($passwd1 != $passwd2){
            $this->msgbox->add('两次输入密码不一样', 211);
        }else if($data['mobile']!=$mobile){
          $this->msgbox->add('手机号码错误',213);
        } else if(K::M('member/account')->up_passwd($this->uid, $passwd2)){
            $this->msgbox->add('修改密码成功');
            //$this->msgbox->set_data("forward", $this->mklink('ucenter',null));
            $this->msgbox->set_data("forward", $this->mklink('ucenter/member', null, null, 'waimai'));
        }
    }

    // 绑定微信
    public function wx_bind()
    {
        if($wx_openid = $this->cookie->get('wx_openid')) {
            if($member = K::M('member/member')->detail($this->uid)) {
                if(!$member['wx_openid']) {
                    if(K::M('member/member')->update($this->uid,array('wx_openid'=>$wx_openid))) {
                        $this->msgbox->add('已绑定',210);
                    }
                }else {
                    //=====20181127去除微信解绑功能！
                    $this->msgbox->add("已绑定",210);
                    //==================================
                    
                    // if(K::M('member/member')->update($this->uid,array('wx_openid'=>''))) {
                    //     $this->msgbox->add('已解除绑定',211);
                    // }
                }
            }
        }else {
            $this->msgbox->add('请从微信登录后再进行绑定解绑操作',212);
        }
    }
}