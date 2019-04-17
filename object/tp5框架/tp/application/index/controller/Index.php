<?php
namespace app\index\controller;//命名空间

use app\index\controller\Common;//引用
use think\Loader;
use think\Request;
use think\Db;

class Index extends Common{//继承
//首页
	public function index(){
		if(!empty(input('type'))){
			$type = input('type');
//			print_r($type);die;
			$this->assign('type',$type);
		}
		if(!empty(input('c'))){
			$c = input('c');
//			print_r($type);die;
			$this->assign('c',$c);
		}
		if(!empty(input('type')) && !empty(input('c'))){
			//新品上市列表
			if(input('type')==1){								
				$c = input('c');
//				查询所有fId=$c的分类
				$clast = db('category')->field('Id')->where('fId',$c)->select();
//				print_r($clast);die;
				$arr = [];
				foreach($clast as $k=>$v){
					$arr[] = $v['Id'];
				}
				//查询所有对应的商品
				$newArrivalOne = Db::name('product')->where('newArrival',1)->where('Id','in',$arr)->order('Id desc')->find();//查询最新一个属于新品的产品
				$newArrivalTwo = Db::name('product')->where('newArrival',1)->where('Id','in',$arr)->order('Id desc')->limit(1,11)->select();
//				print_r($arr);die;
			}else{
				//查询所有fId=$c的分类
				$clast = db('category')->field('Id')->where('fId',1)->select();
//				print_r($clast);die;
				$arr = [];
				foreach($clast as $k=>$v){
					$arr[] = $v['Id'];
				}
				$newArrivalOne = Db::name('product')->where('newArrival',1)->where('Id','in',$arr)->order('Id desc')->find();//查询最新一个属于新品的产品
				$newArrivalTwo = Db::name('product')->where('newArrival',1)->where('Id','in',$arr)->order('Id desc')->limit(1,11)->select();
			}
			
			//热门推荐列表
			if(input('type')==2){								
				$c = input('c');
//				查询所有fId=$c的分类
				$clast = db('category')->field('Id')->where('fId',$c)->select();
//				print_r($clast);die;
				$arr = [];
				foreach($clast as $k=>$v){
					$arr[] = $v['Id'];
				}
				//查询所有对应的商品
				$hotOne = Db::name('product')->where('hotProduct',1)->where('Id','in',$arr)->order('Id desc')->find();//查询最新一个属于新品的产品
				$hotTwo = Db::name('product')->where('hotProduct',1)->where('Id','in',$arr)->order('Id desc')->limit(1,11)->select();
//				print_r($arr);die;
			}else{
				//查询所有fId=$c的分类
				$clast = db('category')->field('Id')->where('fId',1)->select();
//				print_r($clast);die;
				$arr = [];
				foreach($clast as $k=>$v){
					$arr[] = $v['Id'];
				}
				$hotOne = Db::name('product')->where('hotProduct',1)->where('Id','in',$arr)->order('Id desc')->find();//查询最新一个属于新品的产品
				$hotTwo = Db::name('product')->where('hotProduct',1)->where('Id','in',$arr)->order('Id desc')->limit(5)->select();
			}
		}else{
			$clast = db('category')->field('Id')->where('fId',$c)->select();
//				print_r($clast);die;
			$arr = [];
			foreach($clast as $k=>$v){
				$arr[] = $v['Id'];
			}
			
			$newArrivalOne = Db::name('product')->where('newArrival',1)->where('Id','in',$arr)->order('Id desc')->find();//查询最新一个属于新品的产品
			$newArrivalTwo = Db::name('product')->where('newArrival',1)->where('Id','in',$arr)->order('Id desc')->limit(1,11)->select();
			
			$hotOne = Db::name('product')->where('hotProduct',1)->where('Id','in',$arr)->order('Id desc')->find();//查询最新一个属于热门的产品
			$hotTwo = Db::name('product')->where('hotProduct',1)->where('Id','in',$arr)->order('Id desc')->limit(5)->select();
		}
		
//		print_r($newArrivalTwo);die;
		
		$this->assign([
			'title'=>'首页',
			'newArrivalOne'=>$newArrivalOne,
			'newArrivalTwo'=>$newArrivalTwo,
			'hotOne'=>$hotOne,
			'hotTwo'=>$hotTwo,
		]);
		return $this->fetch();
//		echo 21313;
	}
	
//	用户注册
	public function register(){
		if(request()->isPost()){
			$data = input('post.');
//			print_r($data);die;
			$validate = Loader::validate('Index');
			
			if(!$validate->check($data)){
				$this->error($validate->getError());die;
			}
		
			$username = Db::name('member')->where('username',$data['username'])->find();
//			print_r($username);die;
			if($username){
				$this->error('会员名称已存在');die;
			}
			
			$phone = Db::name('member')->where('phone',$data['phone'])->find();
			if($phone){
				$this->error('手机号码已注册');die;
			}
			
			$email = Db::name('member')->where('email',$data['email'])->find();
			if(!$phone){
				$this->error('此邮箱已注册');die;
			}
//			echo "string";;die;
			unset($data['password_confirm']);
//			print_r($data);die;
			$result = Db::name('member')->insert($data);
//			print_r($result);die;
			if($result){
				$this->success('注册成功','Index/login');die;
			}else{
				$this->error('注册失败');die;
			}
			
		}
		$navlist = $this->nav();
		$this->assign('title','用户注册');
		return $this->fetch();
	}
	
	//会员中心
	public function user(){
		$this->assign('title','会员中心');
		
		return $this->fetch();
	}
	
	//登陆
	public function login(){
		
		if(Request::instance()->isAjax()){
			$info = ['error'=>false,'msg'=>''];
//			print_r($_POST);die;
			
			$username = input('post.username');
			$password = input('post.password');
			$result = Db::name('member')->where("username='$username'")->find();
//			print_r($result['Id']);
//			die;
			if(!$result){
				$info['msg'] = '用户名不存在';
				return $info;
			}
			if($result['password'] != $password){
				$info['msg'] = '账号或密码错误';
				return $info;
			}
			//数据处理
			if($result){
				session('username',$username);
				$Id = $result['Id'];
				session('Id',$result['Id']);
				$info = ['error'=>true,'msg'=>'登陆成功'];
				return $info;
			}
			
//			echo "登陆成功";
		}else{
			
			
			$this->assign('title','会员登录');
			return $this->fetch();
		}
		
		
	}
	//会员退出
	public function logout(){
		
		//清除session
		session(null);
		$this->success('退出成功','index/login');
	}
	//地址管理
    public function address(){
		
		$this->assign('title','地址管理');
		return $this->fetch();
	}
	
	//商品详情
	public function productDetail(){
		$Id = input('Id');
		$detail = Db::name('product')->where("Id='$Id'")->find();
//		print_r($Id);
		
		$this->assign([
			'detail' => $detail,
			'Id' => $Id,
			'title'=>'商品详情'
		]);
		return $this->fetch();
	}
	
	//商品列表
	public function shoplist(){
		$Id = input('Id');
		$detail = Db::name('product')->where("Id='$Id'")->find();
//		print_r($Id);
		$this->assign([
			'detail' => $detail,
			'Id' => $Id,
			'title'=>'商品详情'
		]);
		return $this->fetch();
	}
}
