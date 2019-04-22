<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2015-07-22 01:25:14Z wanglei $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Passport extends Ctl
{
    public function index()
    {
        $this->login();
    }
    //授权登录
    public function login($type=null)
    {
        //=====20181127 增加，如果是微信浏览器访问，则跳转到微信登录wxloing()进行处理
        if(defined("IN_WEIXIN") || !defined("MERBER_LOGIN_EVN") || MERBER_LOGIN_EVN!=="DEV")
        {
            header("location:".$this->mklink('passport/wxlogin', null, array('rebackurl'=>$this->getrebackurl())));
            exit;
        }
        //==============================================================


        $this->pagedata['rebackurl'] = $this->getrebackurl();
        if($this->request['IN_APP_CLIENT']){
            $this->tmpl = 'passport/applogin.html';
        }else if($this->GP('give_wx') === 'shouquan'){
            if(!defined('WX_OPENID')){
                $this->getwxopenid();
            }
            $this->pagedata['open_id'] = WX_OPENID;
            $this->tmpl = 'passport/login.html';
        }else{
            $yzm_config = $this->system->config->get('access');
            if($yzm_config['verifycode']['reg'] == 'on'){
                $this->pagedata['reg_yzm'] = $yzm_config['verifycode']['reg'];
            }
            if($this->uid){
                $this->tmpl = 'passport/login.html';
            }elseif($type == 'quick'){
                $this->tmpl = 'passport/quick.html';
            }else{
                $this->tmpl = 'passport/login.html';
            }
        }
    }

    public function dologin()
    {
        if(!defined("MERBER_LOGIN_EVN") || MERBER_LOGIN_EVN!=="DEV")
        {
            $this->msgbox->add('系统不支持该登录方式!', 1)->response();
            exit;
        }
        if(!$this->checksubmit()){
            $this->msgbox->add('非法请求!', 211)->response();
        }else if(!$mobile = $this->GP('mobile')){
            $this->msgbox->add('手机号码不能为空',202)->response();
        }else if(!$mobile = K::M('verify/check')->mobile($mobile)){
            $this->msgbox->add('手机号码有误', 203)->response();
        }else if($passwd = $this->GP('passwd')){
            if($this->auth->login($mobile, $passwd)){
                $this->msgbox->add('欢迎您回来！');
                $this->msgbox->set_data('forward', $this->getrebackurl());
            }/*else{
                $this->msgbox->add('帐号或密码错误!',205)->response();
            }*/
        }else if($sms_code = $this->GP('yzm')){
            /*if(K::M('system/session')->start()->get('code_'.$mobile) != $sms_code){
                $this->msgbox->add('短信验证码有误',204)->response();
            }*/if(K::M('cache/cache')->get('code_'.$mobile) != $sms_code){
                $this->msgbox->add('短信验证码有误',204)->response();
            }else {
                if($member = K::M('member/member')->member($mobile, 'mobile')){
                    $this->auth->manager($member['uid']);
                    $this->msgbox->add('欢迎您回来！');
                    $this->msgbox->set_data('forward', $this->getrebackurl());
                }else{
                    $this->msgbox->add('该手机号未注册', 205)->response();
                }
            }
        }else{
            $this->msgbox->add('密码不能为空', 206)->response();
        }
    }

    //微信登录
    public function wxlogin()
    {
        $wx_openid = $this->check_wxopenid();
        if(defined('WX_UNIONID') && WX_UNIONID){
            $member = K::M('member/member')->member(WX_UNIONID, 'wx_unionid');
        }
        if(empty($member)){
            $member = K::M('member/member')->member(WX_OPENID, 'wx_openid');
        }

        //=====20181127 增加，没有找到用户，清除当前的cookie，并跳转到微信自动登录getwxopenid()处理
        if(empty($member))
        {
            $this->cookie->delete("wx_openid");
            $this->cookie->delete("wx_unionid");
            header("location:".$this->mklink('passport/getwxopenid', null, array('rebackurl'=>$this->getrebackurl())));
            exit;
        }
        //======================================================

        $subscribe_open = 0;
        if($wx_info = $this->wechat_client()->getUserInfoById(WX_OPENID)){           
            if(!$wx_info['subscribe']){
                $_wxcfg = $this->system->config->get('wechat');
                $_cfg = $this->system->config->get('site');
                $subscribe_qr = $_wxcfg['subscribe_qr'] ? $_wxcfg['subscribe_qr'] : $_cfg['weixinqr'];
                if($subscribe_qr){
                    $subscribe_open = $_wxcfg['subscribe_open'] ? $_wxcfg['subscribe_open'] : 0;
                }
            }
        }
        //$rebackurl = $this->getrebackurl($this->mklink('index',null,array(),'waimai'));
        $rebackurl = $this->getrebackurl();
        if($member){
            $this->system->auth->manager($member['uid']);
            //header("Location:".$rebackurl);
            if($subscribe_open){
                $this->pagedata['subscribe_qr'] = $subscribe_qr;
                $this->pagedata['rebackurl'] = $rebackurl;
                $this->tmpl = "passport/subscribe.html";
            }else{
                header("Location:".$rebackurl);
                exit;
            }
        }else if($this->uid){
            //已经登录的用户直接绑定
            K::M('member/member')->update($this->uid, array('wx_openid'=>WX_OPENID, 'wx_unionid'=>WX_UNIONID));
            //header("Location:".$rebackurl);
            if($subscribe_open){
                $this->pagedata['subscribe_qr'] = $subscribe_qr;
                $this->pagedata['rebackurl'] = $rebackurl;
                $this->tmpl = "passport/subscribe.html";
            }else{
                header("Location:".$rebackurl);
                exit;
            } 
        }else{
            //未登录跳转到绑定页面
            header("Location:".$this->mklink('passport/wxbind', null, array('rebackurl'=>$rebackurl)));
            exit;
        }
    }

    //微信关注公众号
    public function wxsubscribe()
    {
        $_wxcfg = $this->system->config->get('wechat');
        $_cfg = $this->system->config->get('site');
        $subscribe_qr = $_wxcfg['subscribe_qr'] ? $_wxcfg['subscribe_qr'] : $_cfg['weixinqr'];

        $this->pagedata['subscribe_qr'] = $subscribe_qr;
        $this->pagedata['rebackurl'] = $this->getrebackurl();
        $this->tmpl = "passport/subscribe.html";
    }

    //微信绑定
    public function wxbind()
    {
        $rebackurl = $this->getrebackurl();
        $wx_openid = $this->check_wxopenid();
        if($this->checksubmit()){
            if(!$mobile = K::M('verify/check')->mobile($this->GP('mobile'))){
                $this->msgbox->add('手机号不正确', 211);
            }else if(!$sms_code = $this->GP('yzm')){
                $this->msgbox->add('验证码不能为空', 212);
            }/*else if(K::M('system/session')->start()->get('code_'.$mobile) != $sms_code){
                $this->msgbox->add('验证码不正确', 213);
            }*/else if(K::M('cache/cache')->get('code_'.$mobile) != $sms_code){
                $this->msgbox->add('验证码不正确', 213);
            }else if($member = K::M('member/member')->member($mobile, 'mobile')){
                K::M('member/member')->update($member['uid'], array('wx_openid'=>WX_OPENID, 'wx_unionid'=>WX_UNIONID));
                $this->system->auth->manager($member['uid']);
                $this->msgbox->add('绑定帐号成功');
                $this->msgbox->set('forward', $rebackurl);
            }else{
                $data['mobile'] = $mobile;
                $data['wx_openid'] = WX_OPENID;
                $data['wx_unionid'] = WX_UNIONID;
                $data['paypasswd'] = $data['passwd'] = md5(uniqid());
                if($wx_info = $this->wechat_client()->getUserInfoById(WX_OPENID)){
                    $data['nickname'] = $wx_info['nickname'];
                }else{
                    $data['nickname'] = substr($mobile, 0,3).'****'.substr($mobile, -4);
                }
                if($uid = K::M('member/account')->create($data)){
                    if($wx_info['headimgurl']){
                        if($face = file_get_contents($wx_info['headimgurl'])){
                            K::M('member/face')->update_face($uid, '', $face);
                        }
                    }
                    if($this->system->auth->manager($uid)){
                        $this->msgbox->add('createdone');
                        $this->msgbox->set('forward', $rebackurl);
                    }
                }else{
                    $this->msgbox->add('绑定帐号失败,请确认后重新注册!',216);
                }
            }
        }else{
            $this->pagedata['rebakcurl'] = $rebakcurl;
            $this->tmpl = 'passport/wxbind.html';
        }
    }

    //注册
    public function register()
    {
        $rebackurl = $this->getrebackurl();
        if($this->checksubmit()){
            //$session =K::M('system/session')->start();
            $session =K::M('cache/cache');
            if(!$mobile = $this->GP('mobile')){
                $this->msgbox->add('手机号没有填写', 211);
            }else if(!K::M('verify/check')->mobile($mobile)){
                $this->msgbox->add('手机号有误', 211);
            }else if(!$yzm_code = $this->GP('yzm')){
                $this->msgbox->add('手机验证码错误', 212);
            }else if($yzm_code != $session->get('code_'.$mobile)){
                $this->msgbox->add('手机验证码错误或已过期',212);
            }else if(!$passwd = $this->GP('passwd')){
                $this->msgbox->add('密码没有填写', 213);
            }else if($passwd !== $this->GP('repasswd')){
                $this->msgbox->add('两次输入的密码不一致', 215);
            }else if(K::M('member/member')->check_mobile($mobile)){
                $data = array('mobile'=>$mobile);
                $data['paypasswd'] = $data['passwd'] = $passwd;
                $data['nickname'] = substr($mobile, 0,3).'****'.substr($mobile, -4);
                $data['register_from'] = 'web';
                if(defined('IN_WEIXIN') && defined('WX_OPENID')){
                    $data['wx_openid'] = WX_OPENID;
                    $data['wx_unionid'] = WX_UNIONID;
                    if($wx_info = $this->wechat_client()->getUserInfoById(WX_OPENID)){
                        $data['nickname'] = $wx_info['nickname'];
                    }
                }
                if($uid = K::M('member/account')->create($data)){
                    if($wx_info['headimgurl']){
                        if($face = file_get_contents($wx_info['headimgurl'])){
                            K::M('member/face')->update_face($uid, '', $face);
                        }
                    }
                    $this->msgbox->add('恭喜您，注册会员成功!');
                    $this->msgbox->set_data('forward', $rebackurl);
                }else{
                    $this->msgbox->add('注册失败,请确认后重新注册!',216);
                }
            }
        }else{
            $this->pagedata['rebackurl'] = $rebackurl;
            $this->tmpl = 'passport/register.html';
        }

    }

    /*  发送短信*/
    public function sendsms()
    {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            $is_register = $this->GP('is_register');

            if(!$mobile = $this->GP('mobile')){
                $this->msgbox->add('手机号码不正确', 211);
            }elseif(!$mobile = K::M('verify/check')->mobile($mobile)){
                $this->msgbox->add('手机号码不正确', 999);
            }elseif($is_register && K::M('member/member')->member($mobile, 'mobile')){
                $this->msgbox->add('手机号码已存在', 212);
            }else{
                $code = rand(100000,999999);
                //$session = K::M('system/session')->start();
                $session = K::M('cache/cache');
                $session->set('code_'.$mobile, $code,180); //15分钟缓存换成3分钟
                $smsdata =  array('code'=>$code);

                $_sendCode = defined("__DEBUG") && __DEBUG ? $code:'******';  //---remark---发送的验证码
                $cfg = $this->system->config->get('sms');//新增图形验证码
                if($img_code = $this->GP('img_code')){
                    if(!K::M('magic/verify')->check($img_code)){
                        $this->msgbox->add('图形验证码错误', 213);
                    }else if(K::M('sms/sms')->send($mobile, 'login', $smsdata)){
                        $this->msgbox->add('短信发送成功');
                        $this->msgbox->set_data('data', array('code' => $_sendCode, 'sms_code' => 0));
                    }
                }else if($cfg['verify_open'] && ($log_count = K::M('sms/log')->count(array('dateline' => '>:' . (__TIME - 600)))) >= $cfg['sms_num']){
                    $this->msgbox->set_data('data', array('code' => $_sendCode, 'sms_code' => 1));
                }else if(K::M('sms/sms')->send($mobile, 'login', $smsdata)){
                    $this->msgbox->add('短信发送成功');
                    $this->msgbox->set_data('data', array('code' => $_sendCode, 'sms_code' => 0));
                }
            }
        }
    }


    //退出登录
    public function loginout()
    {
        //=====20181127不再使用登出功能
        header("location:".$this->getrebackurl());  //直接跳转，不支持登出功能！
        return;
        //============================

        // $this->auth->loginout();
        // header("location:".$this->getrebackurl());
    }

    //找回密码
    public function forget()
    {
        $rebackurl = $this->getrebackurl();
        if($data=$this->checksubmit('data')){
            if(!$mobile = K::M('verify/check')->mobile($data['mobile'])){
                $this->msgbox->add("手机号码不正确!",211);
            }elseif(!$member = K::M('member/member')->member($mobile, 'mobile')){
                $this->msgbox->add("手机号码不存在!",212);
            }/*elseif(K::M('system/session')->start()->get('code_'.$mobile) != $data['sms_code']){
                $this->msgbox->add("短信验证码不正确!",213);
            }*/elseif(K::M('cache/cache')->get('code_'.$mobile) != $data['sms_code']){
                $this->msgbox->add("短信验证码不正确!",213);
            }elseif(!$passwd = $data['passwd']){
                $this->msgbox->add("新密码不能为空!",214);
            }elseif(!K::M('member/account')->update_passwd($member['uid'], $passwd)){
                $this->msgbox->add("未知错误,重置密码失败!",215);
            }else{
                $this->msgbox->add("重置密码成功!");
                $this->msgbox->set_data('forward', $rebackurl);
            }
        }else{
            $this->pagedata['rebackurl'] = $rebackurl;
            $this->tmpl = "passport/forget.html";
        }
    }

    //微信自动登录的实现接口
    public function getwxopenid()
    {
        $rebackurl = $this->getrebackurl();
        if (($code = $this->GP('code')) && ($code != 'wxpay')) {//这里是获得授权code后进行openid及unionid的获取工作
            $client = $this->wechat_client();
            $ret = $client->getAccessTokenByCode($code);

            //=====20181126新增，检测unionid或openid是否存在，如果不存在，则自动为用户创建新的用户并将unionid及openid绑定上。
            $member = NULL;
            if((isset($ret['unionid']) && ($ret['unionid'] = trim($ret['unionid']))!=="") || (isset($ret['openid']) && ($ret['openid'] = trim($ret['openid']))!==""))
            {
                $findField = isset($ret['unionid']) && $ret['unionid']!==""?"wx_unionid":"wx_openid";
                $findValue = $findField !== "wx_openid"?$ret['unionid']:$ret['openid'];
                $member = K::M('member/member')->member($findValue, $findField);
                if(!$member && $findField === "wx_unionid")
                {
                    $findField = "wx_openid";
                    $findValue = $ret['openid'];
                    $member = K::M('member/member')->member($findValue, $findField);
                }
                if(!$member && isset($ret['access_token']) && $ret['access_token']!=="")
                {
                    $userInfo = $client->getUserInfoByAuth($ret['access_token'],$ret['openid']);//通过access_token换取用户资料
                    if($userInfo)
                    {
                        $data['mobile'] = "";
                        $data['paypasswd'] = $data['passwd'] = md5(rand(111,99999999)."");  //随机密码
                        $data['nickname'] = rawurlencode($userInfo['nickname']);   //=====20181128为了避免特殊字符如Emoji表情无法入库，所以先进行编码
                        $data['register_from'] = "wxapp";
                        $data['wx_openid'] = $userInfo['openid'];
                        $data['wx_unionid'] = (isset($userInfo['unionid']) && $userInfo['unionid']!=="") ? $userInfo['unionid']:"";
                        $uid = K::M('member/account')->create($data, true);
                        if($uid && K::M('member/member')->update($uid, array('mobile'=>$uid), true))    //=====20181128入库成功后，将手机号改为用户的ID
                        {
                            $userInfo['headimgurl'] = trim($userInfo['headimgurl']);
                            if($userInfo['headimgurl'] !== "")
                            {
                                $face = file_get_contents($userInfo['headimgurl']);
                                if($face)
                                    K::M('member/face')->update_face($uid, '', $face);
                            }
                            $member = K::M('member/member')->member($uid, 'uid');
                        }
                    }
                }
            }
            //===========================================================================================


            //-----以下为原来的代码，不做过多修改，由于$member已经确定一定存在（除非新创建用户不存在），所以不需要使用模型再获取一次
            if($wx_unionid = $ret['unionid']){
                // if($member = K::M('member/member')->member($wx_unionid, 'wx_unionid')){
                if($member){
                    $this->auth->manager($member['uid']);
                }
                $wx_openid = $ret['openid'] ? $ret['openid'] : $wx_unionid;
                $this->cookie->set('wx_unionid', $wx_unionid,7200);
                $this->cookie->set('wx_openid', $wx_openid,7200);
            }else if ($wx_openid = $ret['openid']) {
                // if($member = K::M('member/member')->member($wx_openid, 'wx_openid')){
                if($member){
                    $this->auth->manager($member['uid']);
                }
                $this->cookie->set('wx_openid', $wx_openid,7200);
            } else {
                $this->msgbox->add('获取授权失败！');
                $this->msgbox->set_data('forward', $this->mklink('index',null,array(),'waimai'));
            }
        } else {// -----这里是负责跳转到微信OAuth验证URL上以获得code的，通常不需要修改这里
            if (!$wx_openid = $this->cookie->get('wx_openid')) {
                $url = $this->mklink('passport:getwxopenid', null, array('rebackurl'=>$rebackurl), 'home');
                ///{{{独立公众号授权
                $client = $this->wechat_client(); //$client
                $state = md5(uniqid());
                $authurl = $client->getOAuthConnectUri($url, $state, 'snsapi_userinfo');    //-----这里的第三个参数必须为“snsapi_userinfo”，否则会导致微信自动注册时无法拉取用户数据
                ///}}}
                ///{{{与其它应用使用一个公众号授权
                //$wxcfg = K::M('system/config')->get('wechat');
                //$authurl = 'http://api.wxzhu.com/tools/wechat/code.html?appid='.$wxcfg['appid'].'&wx_code_rebackurl='.urlencode($url);
                ///}}}
                header('Location:' . $authurl);
                exit();
            }
        }
        if (empty($wx_openid)) {//-----用户不授权获取资料
            $this->msgbox->add('获取授权失败！');
            $this->msgbox->set_data('forward', $this->mklink('index'));
        }else{//-----这里就是获得openid后进行跳转到登录之前的页面的
            $subscribe_open = 0;
            if($wx_info = $this->wechat_client()->getUserInfoById($wx_openid)){                
                if(!$wx_info['subscribe']){
                    $_wxcfg = $this->system->config->get('wechat');
                    $_cfg = $this->system->config->get('site');
                    $subscribe_qr = $_wxcfg['subscribe_qr'] ? $_wxcfg['subscribe_qr'] : $_cfg['weixinqr'];
                    if($subscribe_qr){
                        $subscribe_open = $_wxcfg['subscribe_open'] ? $_wxcfg['subscribe_open'] : 0;
                    }
                }
            }            
            if($subscribe_open){
                //header("Location:".$this->mklink('passport/wxsubscribe', null, array('rebackurl'=>$rebackurl)));
                //exit();
                $this->pagedata['subscribe_qr'] = $subscribe_qr;
                $this->pagedata['rebackurl'] = $this->getrebackurl();
                $this->tmpl = "passport/subscribe.html";
            }else{
                header("Location:".$this->getrebackurl());
            }
            //header("Location:".$this->getrebackurl());
        }
    }

    protected function getrebackurl($rebackurl=null)
    {
        if(empty($rebackurl)){
            if(!$rebackurl = $this->GP('rebackurl')){
                $rebackurl = $this->system->forward();
            }
        }
        if(empty($rebackurl) || strstr($rebackurl, 'passport')){
            $rebackurl = $this->mklink('index', null, null, 'www');
        }else if(strstr($rebackurl, 'update_passwd') || strstr($rebackurl, 'ucenter/info')){
            $rebackurl = $this->mklink('ucenter/member', null, null, 'waimai');
        }
        return $rebackurl;
    }


    public function verify()
    {
        K::M('magic/verify')->output();
    }

}
