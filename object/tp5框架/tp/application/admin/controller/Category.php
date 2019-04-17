<?php
namespace app\admin\controller;

use app\admin\controller\Commond;
use think\Loader;
use think\Db;

class Category extends Commond{
	public function categoryList(){
		
		$list = $this->cate();
//		print_r($list);die;
		// 把分页数据赋值给模板变量list
		
//		$page = $list->render();
//		echo($page);die;
//		echo "string";
		$this->assign('list', $list);
		$this->assign('title','分类列表');
		return $this->fetch();
	}
	public function categoryAdd(){
		
		if(request()->isPost()){
			$data = input('post.');
//			print_r($data);die;
			$validate = Loader::validate('category');
			if(!$validate->check($data)){
				$this->error($validate->getError());die;
			}
			$result = Db::name('category')->insert($data);
//			print_r($result);die;
			if($result){
				$this->success('添加成功','category/categoryList');die;
			}else{
				$this->error('添加失败');die;
			}
			
		}
//		echo "string";
		$list = $this->cate();
		$this->assign('list', $list);
		$this->assign('title','分类添加');
		return $this->fetch();
	}
	public function categoryUpdate(){
		$Id = input('Id');
//		print_r($Id);die;
		$one = Db::name('category')->where("Id=$Id")->find();
//		print_r($one);die;
		$this->assign('one',$one);
		$this->assign('title','分类编辑');
		
		if(request()->isPost()){
			
			$data = input('post.');
//			print_r($data);die;
			$validate = Loader::validate('category');
			if(!$validate->check($data)){
				$this->error($validate->getError());die;
			}
			$result = Db::name('category')->update($data);
//			print_r($result);die;
			if($result==1){
				$this->success('修改成功','category/categoryList');die;
			}else if($result==0){
				$this->success('数据未修改','category/categoryList');die;
			}else{
				$this->error('修改失败');die;
			}
			
		}
//		echo "string";
		$list = $this->cate();
		$this->assign('list', $list);
		return $this->fetch();
	}
	public function categoryDelete(){
		$Id = input('Id');
//		print_r($Id);die;
		$result = Db::name('category')->where("Id=$Id")->delete();
		if($result){
			$this->success('删除成功','category/categoryList');die;
		}else{
			$this->error('删除失败');die;
		}
	}
}
?>