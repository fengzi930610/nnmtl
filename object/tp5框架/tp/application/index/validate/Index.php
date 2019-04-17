<?php
namespace app\index\validate;
use think\Validate;

class Index extends Validate{
	protected $rule=[
		'username' => 'require|max:18',
		'password'=>'require|confirm',
		'phone' => 'require|number',
		'email' => 'require|email',
	];
	protected $message  =   [
	    'username.require' => '会员名称不能为空',
	    'name.max'     => '会员名称最多不能超过18个字符',	
	    'password.require' => '请输入密码',
	    'password.confirm' => '两次密码输入不一致',
	    'phone.require' => '手机号码不能为空',
	    'phone.number' => '手机号码格式不正确',
	    'email.require' => '请输入您的注册邮箱',
	    'email.email'        => '邮箱格式错误',  
    ];   
	
}
?>