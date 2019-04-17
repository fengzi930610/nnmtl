<?php
namespace app\index\controller;//命名空间

use app\index\controller\Common;//引用
use think\Loader;
use think\Request;
use think\Db;

class Cart extends Common{
	//购物车添加页面
	public function cartAdd(){
		$info = ['code'=>0,'info'=>false,'msg'=>''];
//		echo !session('?username');
		if(!session('?username')){
			$info = ['code'=>400,'info'=>false,'msg'=>'请先登录'];
			return $info;
		}
		//查询购物车表里对应的会员Id和商品Id是否已存在
//		print_r(session('Id'));die;
		$data['memberId'] = session('Id');
		$data['productId'] = input('productId');
//		print_r($data);
		$num = input('num');
		$ishas = db('cart')->where($data)->find();
		if($ishas){
			//存在，则改变数量
			$result = db('cart')->where('Id='.$ishas['Id'])->setInc('num',$num);
		}else{
			//不存在，则添加一条数据
			$data['num'] = $num;
			$data['createtime'] = time();
			$result = db('cart')->insert($data);
		}
		if($result>0){
			$info = ['code'=>500,'info'=>true,'msg'=>'已添加购物车'];
			return $info;
		}else{
			$info = ['code'=>401,'info'=>false,'msg'=>'添加购物车失败'];
			return $info;
		}
	}
	
	//购物车列表
	public function cartList(){
		if(session('?username')){
			$Id =session('Id');
			$cartList = db('cart')
			->field('tp_cart.*,tp_product.thumb,tp_product.name,tp_product.price')
			->join('tp_product','tp_cart.productId=tp_product.Id','left')
			->order('createtime','desc')
			->where("memberId=$Id")
			->select();
		}
		
//		print_r($cartList);die;
		
		
		
		$this->assign('cartList',$cartList);
		$this->assign('title','购物车列表');
		return $this->fetch();
	}
	//生成订单
	public function check(){
		
		//初始化购物车状态
		$Id = session('Id');
		db('cart')->where(['memberId'=>$Id])->update(['state'=>0]);
//		empty(session('Id'));
//		print_r(session('Id'));die;
		$data = input('post.');
//		print_r($array);die;
//		print_r($array);die;
		foreach($data['data'] as $k=>$v){
			$re = db('cart')->where(['Id'=>$v[0]])->update(['state'=>1,'num'=>$v[1]]);
		};
//		print_r($re);
		if($re){
			return array(['info'=>true]);
		}
//		
		
		
	}
	
	//核对订单
	public function orders(){
		if(session('?username')){
			$Id =session('Id');
			$cartList = db('cart')
			->field('tp_cart.*,tp_product.thumb,tp_product.name,tp_product.price')
			->join('tp_product','tp_cart.productId=tp_product.Id','left')
			->order('createtime','desc')
			->where("memberId=$Id and state=1")
			->select();
			
		}
		
		
		
		$this->assign('cartList',$cartList);
		$this->assign('title','核对订单');
		return $this->fetch();
	}
	
	
	
}

?>