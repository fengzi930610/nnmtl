<?php
namespace app\admin\controller;

use app\admin\controller\Commond;
use think\Loader;
use think\Db;
use app\admin\model\Bbrand;
class Brand extends Commond{
	public function brandList(){
		// 查询brand数据表的所有数据  并且每页显示10条数据
		$list = Db::name('brand')->order('Id desc')->paginate(1);
//		print_r($list);die;
		// 把分页数据赋值给模板变量list
		
//		$page = $list->render();
//		echo($page);die;
//		echo "string";
		$this->assign('list', $list);
		$this->assign('title','品牌列表');
		return $this->fetch();
	}
	public function brandAdd(){
		if(request()->isAjax()){
			$info = ['code'=>'0','error'=>false,'info'=>''];
			$data = input('post.');
//			print_r($data);die;
			$files = request()->file('ufile');
//			print_r(!empty($files));die;
			if(!empty($files)){
				$dataImg = [];//定义空数组来存储上传图片的地址
				foreach($files as $k=>$v){
		//				echo "string";die;
					$filePath = './public/static/admin/upload/image/brand/'.date('Y-m-d');
		//				print_r(!file_exists($filePath));die;
					if(!file_exists($filePath)){
						mkdir($filePath,0777,true);
		//					print_r(file_exists($filePath));die;
					}
					$info = $v->validate(['ext'=>'jpg,jpeg,bmp,png,gif'])->rule('uniqid')->move($filePath);
					if(!empty($info)){
						$one = $info->getSaveName();//获取一张图片的名称
		//				print_r($one);die;
						$dataImg[] = rtrim($filePath,'/').'/'.$info->getSaveName();//图片路径
					}
					
				}
				$data['logo'] = implode(',', $dataImg);
			}
		
			$brand = new Bbrand($data);
			$result = $brand->allowField(true)->save();
	//			print_r($dataImg);die;
	//			$data = input('post.');
	//			print_r($data);die;
	//			$result = Db::name('product')->insert($data);
	//			print_r($result);die;
			if($result){
				
				$info = ['code'=>'1','error'=>true,'info'=>'品牌添加成功'];
				return $info;
			}else{
				$info['code'] = '400';
				$info['info'] = '品牌添加失败';
				return $info;
			}
			
			
		}else{
			
	//		echo "string";
			$this->assign('title','品牌添加');
			return $this->fetch();
		}
	}
	public function brandUpdate(){
		
		if(request()->isAjax()){
			$info = ['code'=>'0','error'=>false,'info'=>''];
			$data = input('post.');
//			print_r($data);die;
			$files = request()->file('ufile');
//			print_r(!empty($files));die;
			if(!empty($files)){
				$dataImg = [];//定义空数组来存储上传图片的地址
				foreach($files as $k=>$v){
		//				echo "string";die;
					$filePath = './public/static/admin/upload/image/brand/'.date('Y-m-d');
		//				print_r(!file_exists($filePath));die;
					if(!file_exists($filePath)){
						mkdir($filePath,0777,true);
		//					print_r(file_exists($filePath));die;
					}
					$info = $v->validate(['ext'=>'jpg,jpeg,bmp,png,gif'])->rule('uniqid')->move($filePath);
					if(!empty($info)){
						$one = $info->getSaveName();//获取一张图片的名称
		//				print_r($one);die;
						$dataImg[] = rtrim($filePath,'/').'/'.$info->getSaveName();//图片路径
					}
					
				}
				$data['logo'] = implode(',', $dataImg);
			}
			$Id = $data['Id'];
			unset($data['Id']);
			
			$brand = new Bbrand();
			
			$result = $brand->allowField(true)->save($data, ['Id' => $Id]);
			if($result){
				
				$info = ['code'=>'1','error'=>true,'info'=>'品牌修改成功'];
				return $info;
			}else if($result==0){
				$info = ['code'=>'1','error'=>true,'info'=>'品牌数据未修改'];
				return $info;
			}else{
				$info['code'] = '400';
				$info['info'] = '品牌修改失败';
				return $info;
			}
		}else{
			$Id = input('Id');
	//		print_r($Id);die;
			$one = Db::name('brand')->where("Id=$Id")->find();
//			print_r($one);die;
			$this->assign('one',$one);
			$this->assign('one',$one);
			$this->assign('title','品牌编辑');
			return $this->fetch();
		}
		
	}
	public function brandDelete(){
		$Id = input('Id');
//		print_r($Id);die;
		$result = Db::name('brand')->where("Id=$Id")->delete();
		if($result){
			$this->success('删除成功','Brand/brandList');die;
		}else{
			$this->error('删除失败');die;
		}
	}
}
?>