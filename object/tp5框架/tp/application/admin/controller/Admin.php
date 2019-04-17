<?php
namespace app\admin\controller;

//use think\Config;
use think\Controller;
use think\Request;
use think\Db;

class Admin extends Controller{
	
	public function login(){
//		print_r(Config('template'));die;
//		return view('index',['name'=>'abv']);
//		$this->assign('name','123456');
		if(Request::instance()->isAjax()){
			$info = ['error'=>false,'msg'=>''];
//			print_r($_POST);
			if(!captcha_check(input('post.code'))){
				$info['msg'] = '验证码有误';
				return $info;
//				echo "<script>alert('验证码有误');history.go(-1);</script>";die;//验证码判断
			}
			$username = input('post.username');
			$password = input('post.password');
//			print_r($username);die;
			$result = Db::name('manager')->where("username='$username'")->find();
//			print_r($result);
			if(!$result){
				$info['msg'] = '用户名不存在';
				return $info;
//				echo "<script>alert('用户名不存在');history.go(-1);</script>";die;
			}
			if($result['password'] != $password){
				$info['msg'] = '账号或密码错误';
				return $info;
//				echo "<script>alert('账号或密码错误');history.go(-1);</script>";die;
			}
			//数据处理
			session('username',$username);
			$info = ['error'=>true,'msg'=>'登陆成功'];
			return $info;
//			echo "登陆成功";
		}
		return $this->fetch();
		
//		echo Config();
	}
	
	public function logout(){
		session(null);
		$this->success('退出成功','admin/login');
	}
	
	
	
}





?>