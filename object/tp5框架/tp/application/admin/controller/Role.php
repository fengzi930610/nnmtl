<?php
namespace app\admin\controller;

use app\admin\controller\Commond;
use think\Loader;
use think\Db;

class Role extends Commond{
	public function roleList(){
		// 查询role数据表的所有数据  并且每页显示10条数据
		$list = Db::name('role')->order('Id desc')->select();
//		print_r($list);die;
//		$result = Db::name('level')->field('name')->where('Id','in','1,2')->select();
//		print_r($result);die;
		
		foreach($list as $k=>$v){
			$str = $v['jurisdiction'];
//			print_r($str);die;
//			print_r("$v['jurisdiction']");die;
			$result = Db::name('level')->field('name')->where('Id','in',"$str")->select();
			
//			$levelList[] = $result;
//			$levelList = [];
			$levelList = [];
			foreach($result as $kk=>$vv){
				$name = $vv['name'];
//				print_r($kk['name']);
//				print_r($result[$kk]['name']);die;
				$levelList[] = $name;
				
			}
			$levelStr = implode(',', $levelList);
			$list[$k]['level'] = $levelStr;
			print_r($list);die;
////			print_r($levelLi;st);die;
		}
		
		
		print_r($levelList);die;
		$this->assign('list', $list);
		$this->assign('title','角色列表');
		return $this->fetch();
	}
	
	public function roleAdd(){
		
//		print_r($levelList);die;
		if(request()->isPost()){
			$data = input('post.');
//			print_r($data);die;
			$validate = Loader::validate('Role');
			if(!$validate->check($data)){
				$this->error($validate->getError());die;
			}
			$data['jurisdiction'] = implode(',', $data['Id']);
			unset($data['Id']);
//			print_r($data);die;
			$result = Db::name('role')->insert($data);
//			print_r($result);die;
			if($result){
				$this->success('添加成功','Role/roleList');die;
			}else{
				$this->error('添加失败');die;
			}
			
		}else{
			$levelList = Db::name('level')->select();
			$this->assign('levelList',$levelList);
	//		echo "string";
			$this->assign('title','角色添加');
			return $this->fetch();
		}
		
	}
	public function roleUpdate(){
		$Id = input('Id');
//		print_r($Id);die;
		$one = Db::name('role')->where("Id=$Id")->find();
//		print_r($one);die;
		$this->assign('one',$one);
		$this->assign('title','角色编辑');
		
		if(request()->isPost()){
			
			$data = input('post.');
//			print_r($data);die;
			$validate = Loader::validate('Brand');
			if(!$validate->check($data)){
				$this->error($validate->getError());die;
			}
			$result = Db::name('role')->update($data);
//			print_r($result);die;
			if($result==1){
				$this->success('修改成功','Brand/roleList');die;
			}else if($result==0){
				$this->success('数据未修改','Brand/roleList');die;
			}else{
				$this->error('修改失败');die;
			}
			
		}
//		echo "string";
		
		return $this->fetch();
	}
	public function roleDelete(){
		$Id = input('Id');
//		print_r($Id);die;
		$result = Db::name('role')->where("Id=$Id")->delete();
		if($result){
			$this->success('删除成功','Brand/roleList');die;
		}else{
			$this->error('删除失败');die;
		}
	}
}
?>