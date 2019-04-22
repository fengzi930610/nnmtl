<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2015-07-22 01:25:14Z wanglei $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Signup extends Ctl
{
    
    public function shop()
    {  
    	if($data = $this->checksubmit('data')){
            $agree = (int)$this->GP("agree");
            if($agree !== 1)
                $this->msgbox->add("未同意《商家入驻协议》")->response();
    		//$session =K::M('system/session')->start();
            $session =K::M('cache/cache');
            if(!$data = $this->check_fields($data, 'mobile')){//,passwd,code
                $this->msgbox->add('非法的数据提交', 210);
            }elseif (!$mobile = K::M('verify/check')->vietnamMobile($data['mobile'])) {
                $this->msgbox->add('手机号不能为空', 212);
            // }else if(empty($data['code'])){
            //     $this->msgbox->add('验证码不能为空', 213);
            // }else if($data['code'] != $session->get('code_'.$data['mobile'])){
            //     $this->msgbox->add('验证码不正确', 214);
            // }else if(!$passwd = $data['passwd']){
            //     $this->msgbox->add('登录密码不正确', 215);
            }else if($shop = K::M('shop/shop')->find(array('mobile'=>$mobile))){
                $this->msgbox->add('该手机号码已存在', 216);
            }else{
                $passwd = date("YmdHis").rand(100,9999999);//随机密码
                $datas = array('mobile'=>$mobile,'passwd'=>md5($passwd));
                if($shop_id = K::M('shop/shop')->create($datas)){
                	$waimai = array('shop_id'=>$shop_id, 'phone'=>$mobile, 'last_time'=>__TIME);
                	K::M('waimai/waimai')->create($waimai);
                   	$this->msgbox->add('申请入驻成功');
                }else{
                    $this->msgbox->add('申请入驻失败，系统错误',217);
                }
            }
        }else{
        	$this->tmpl = 'signup/shop.html';
        }        
    }

    public function staff()
    {   
        if($data = $this->checksubmit('data')){
    		// $session =K::M('system/session')->start();
            $session =K::M('cache/cache');
            if(!$data = $this->check_fields($data, 'name,mobile,passwd,code,city_id')){
                $this->msgbox->add('非法的数据提交', 210);
            }else if(!$name = $data['name']){
            	$this->msgbox->add('用户名不能为空', 211);
            }elseif (!$mobile = K::M('verify/check')->vietnamMobile($data['mobile'])) {
                $this->msgbox->add('手机号不能为空', 212);
            }else if(empty($data['code'])){
                $this->msgbox->add('验证码不能为空', 213);
            }else if($data['code'] != $session->get('code_'.$mobile)){
                $this->msgbox->add('验证码不正确', 214);
            }else if(!$passwd = $data['passwd']){
                $this->msgbox->add('登录密码不正确', 215);
            }else if(K::M('staff/staff')->mobile($mobile)){
                $this->msgbox->add('该手机号码已存在', 216);
            }else if(!$city_id = $data['city_id']){
            	$this->msgbox->add('请选择所在城市', 217);
            }else if(!$city = K::M('data/city')->detail($city_id)){
            	$this->msgbox->add('所选城市不存在或已删除', 218);
            }else{
                $datas = array(
	                'from' => 'paotui',
	                'city_id' => $city_id,
	                'name'=>$name,
	                'verify_name' => 3, //0:待审,1:通过,2:拒绝,3:未提交
	                'mobile' => $mobile,
	                'passwd' => md5($passwd),
	            );
                if($staff_id = K::M('staff/staff')->create($datas)){
                	$verify = array('staff_id'=>$staff_id);
                	K::M('staff/verify')->create($verify);
                   	$this->msgbox->add('申请入驻成功');
                }else{
                    $this->msgbox->add('申请入驻失败，系统错误',219);
                }
            }
        }else{
        	$this->pagedata['citys'] = K::M('data/city')->fetch_all();
        	$this->tmpl = 'signup/staff.html';
        }        
    }
}