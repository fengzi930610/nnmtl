<?php
namespace app\admin\controller;

use app\admin\controller\Commond;
use think\Loader;
use think\Db;

class Level extends Commond{
	public function levelList(){
		// 查询level数据表的所有数据  并且每页显示10条数据
		$list = $this->level();
//		print_r($list);die;
		// 把分页数据赋值给模板变量list
		
//		$page = $list->render();
//		echo($page);die;
//		echo "string";
		$this->assign('list', $list);
		$this->assign('title','权限列表');
		return $this->fetch();
	}
	public function levelAdd(){
		if(request()->isPost()){
			$data = input('post.');
//			print_r($data);die;
			$validate = Loader::validate('level');
			if(!$validate->check($data)){
				$this->error($validate->getError());die;
			}
			$result = Db::name('level')->insert($data);
//			print_r($result);die;
			if($result){
				$this->success('添加成功','level/levelList');die;
			}else{
				$this->error('添加失败');die;
			}
			
		}
		$levelList = $this->level();
//		print_r($levelList);die;
//		echo "string";
		$this->assign('levelList',$levelList);
		$this->assign('title','权限添加');
		return $this->fetch();
	}
	public function levelUpdate(){
		$Id = input('Id');
//		print_r($Id);die;
		$one = Db::name('level')->where("Id=$Id")->find();
//		print_r($one);die;
		$this->assign('one',$one);
		$this->assign('title','权限编辑');
		$list = $this->level();
		$this->assign('list',$list);
		if(request()->isPost()){
			
			$data = input('post.');
//			print_r($data);die;
			$validate = Loader::validate('level');
			if(!$validate->check($data)){
				$this->error($validate->getError());die;
			}
			$result = Db::name('level')->update($data);
//			print_r($result);die;
			if($result==1){
				$this->success('修改成功','level/levelList');die;
			}else if($result==0){
				$this->success('数据未修改','level/levelList');die;
			}else{
				$this->error('修改失败');die;
			}
			
		}
//		echo "string";
		
		return $this->fetch();
	}
	public function levelDelete(){
		$Id = input('Id');
//		print_r($Id);die;
		$result = Db::name('level')->where("Id=$Id")->delete();
		if($result){
			$this->success('删除成功','level/levelList');die;
		}else{
			$this->error('删除失败');die;
		}
	}
}
?>