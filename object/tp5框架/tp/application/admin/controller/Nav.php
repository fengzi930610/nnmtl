<?php
namespace app\admin\controller;

use app\admin\controller\Commond;
use think\Loader;
use think\Db;
use app\admin\model\Navupdate;
use app\admin\model\Navadd;
class Nav extends Commond{
	public function navList(){
		// 查询nav数据表的所有数据  并且每页显示10条数据
		$list = Db::name('nav')->order('Id desc')->paginate(4);
//		print_r($list);die;
		// 把分页数据赋值给模板变量list
//		$page = $list->render();
//		echo($page);die;
//		echo "string";
		$this->assign('list', $list);
		$this->assign('title','导航列表');
		return $this->fetch();
	}
	public function navAdd(){
		if(request()->isAjax()){
			$info = ['code'=>'0','error'=>false,'info'=>''];
			$data = input('post.');
//			print_r($data);die;		
			$nav = new Navadd($data);
			$result = $nav->allowField(true)->save();
	//			print_r($dataImg);die;
	//			$data = input('post.');
	//			print_r($data);die;
	//			$result = Db::name('product')->insert($data);
	//			print_r($result);die;
			if($result){
				$info = ['code'=>'1','error'=>true,'info'=>'导航添加成功'];
				return $info;
			}else{
				$info['code'] = '400';
				$info['info'] = '导航添加失败';
				return $info;
			}
		}else{
	//		echo "string";
			$this->assign('title','导航添加');
			return $this->fetch();
		}
	}
	public function navUpdate(){
		
		if(request()->isAjax()){
			$info = ['code'=>'0','error'=>false,'info'=>''];
			$data = input('post.');
//			print_r($data);die;
			$Id = $data['Id'];
			unset($data['Id']);
			
			$nav = new Bnav();
			
			$result = $nav->allowField(true)->save($data, ['Id' => $Id]);
			if($result){
				
				$info = ['code'=>'1','error'=>true,'info'=>'导航修改成功'];
				return $info;
			}else if($result==0){
				$info = ['code'=>'1','error'=>true,'info'=>'导航数据未修改'];
				return $info;
			}else{
				$info['code'] = '400';
				$info['info'] = '导航修改失败';
				return $info;
			}
		}else{
			$Id = input('Id');
	//		print_r($Id);die;
			$one = Db::name('nav')->where("Id=$Id")->find();
//			print_r($one);die;
			$this->assign('one',$one);
			$this->assign('title','导航编辑');
			return $this->fetch();
		}
		
	}
	public function navDelete(){
		$Id = input('Id');
//		print_r($Id);die;
		$result = Db::name('nav')->where("Id=$Id")->delete();
		if($result){
			$this->success('删除成功','nav/navList');die;
		}else{
			$this->error('删除失败');die;
		}
	}
}
?>