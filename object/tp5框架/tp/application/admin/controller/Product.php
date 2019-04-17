<?php
namespace app\admin\controller;

use app\admin\controller\Commond;
use think\Loader;
use think\Db;
use app\admin\model\Mproduct;
use app\admin\model\ProUpdate;

class Product extends Commond{
	public function productList(){
		$list = Db::name('product')
			//关联查询
			->field('tp_product.*,tp_brand.name as brandName,tp_category.name as categoryName')
			->join('tp_brand','tp_product.brandId=tp_brand.Id','left')
			->join('tp_category','tp_product.categoryId=tp_category.Id','left')
			->order('Id desc')
			->paginate(8);
		
//		print_r($list);die;
		// 把分页数据赋值给模板变量list
		
//		$page = $list->render();
//		echo($page);die;
//		echo "string";
		
		$this->assign('list',$list);
		$this->assign('title','商品列表');
		return $this->fetch();
	}
	//商品添加
	public function productAdd(){
		
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
					$filePath = './public/static/admin/upload/image/'.date('Y-m-d');
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
				if($dataImg){
				
				//缩略图
				$thumbArr = [];
				foreach($dataImg as $k=>$v){
					$thumb = $this->thumb($v);
					$thumbArr[] = $thumb;
//					水印图
					$this->water($v,0);
					
					
				}
//				print_r($thumbArr);die;
				
				$data['thumb'] = implode(',', $thumbArr);
				$data['picture'] = implode(',', $dataImg);
			}
			}
			$product = new Mproduct($data);
			$result = $product->allowField(true)->save();
//			print_r($dataImg);die;
//			$data = input('post.');
//			print_r($data);die;
//			$result = Db::name('product')->insert($data);
//			print_r($result);die;
			if($result){
				
				$info = ['code'=>'1','error'=>true,'info'=>'商品添加成功'];
				return $info;
			}else{
				$info['code'] = '400';
				$info['info'] = '商品添加失败';
				return $info;
			}
			
			
		}else{
	//		echo "string";
			$brandList = Db::name('brand')->select();
	//		print_r($brandList);die;
			$categorylist = $this->cate();
			$this->assign('brandList', $brandList);
			$this->assign('categorylist', $categorylist);
			$this->assign('title','商品添加');
			return $this->fetch();
		}
	}

	public function productUpdate(){
//		
//		print_r($Id);die;
		
		if(request()->isAjax()){
//			$Id = input('Id');
//			print_r($Id);die;
			$info = ['code'=>'0','error'=>false,'info'=>''];
			$data = input('post.');
			
//			print_r($data);die;
			$files = request()->file('ufile');
//			print_r(!empty($files));die;
			if(!empty($files)){
				$dataImg = [];//定义空数组来存储上传图片的地址
				foreach($files as $k=>$v){
		//				echo "string";die;
					$filePath = './public/static/admin/upload/image/'.date('Y-m-d');
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
				if($dataImg){
				
				//缩略图
					$thumbArr = [];
					foreach($dataImg as $k=>$v){
						$thumb = $this->thumb($v);
						$thumbArr[] = $thumb;
	//					水印图
						$this->water($v,0);
						
						
					}
	//				print_r($thumbArr);die;
					
					$data['thumb'] = implode(',', $thumbArr);
					$data['picture'] = implode(',', $dataImg);
				}
			}
			$Id = $data['Id'];
			unset($data['Id']);
			
			$product = new ProUpdate();
			
			$result = $product->allowField(true)->save($data, ['Id' => $Id]);
//			print_r($result);die;
//			print_r($dataImg);die;
			if($result){
				
				$info = ['code'=>'1','error'=>true,'info'=>'商品修改成功'];
				return $info;
			}else if($result==0){
				$info = ['code'=>'1','error'=>true,'info'=>'商品数据未修改'];
				return $info;
			}else{
				$info['code'] = '400';
				$info['info'] = '商品修改失败';
				return $info;
			}
		}else{
			$Id = input('Id');
	//		print_r($Id);die;
			$one = Db::name('product')->where("Id=$Id")->find();
//			print_r($one);die;
			$this->assign('one',$one);
			$this->assign('title','商品编辑');
	//		echo "string";
			$brandList = Db::name('brand')->select();
	//		print_r($brandList);die;
			$categorylist = $this->cate();
			$this->assign('brandList', $brandList);
			$this->assign('categorylist', $categorylist);
			$this->assign('title','商品编辑');
			return $this->fetch();
		}
			
	}

	public function productDelete(){
		if(request()->isPost()){
			$arr = input('post.');
//			print_r($arr);die;
			if($arr){
				$result = Db::name('product')->delete($arr['Id']);
				if($result){
					$this->success('删除成功','product/productList');die;
				}else{
					$this->error('删除失败');die;
				}
			}else{
				$this->error('请选择要删除的商品');die;
			}
			
//			print_r($arr['Id']);die;
		}else{
		
			$Id = input('Id');
	//		print_r($Id);die;
			$result = Db::name('product')->where("Id=$Id")->delete();
			if($result){
				$this->success('删除成功','product/productList');die;
			}else{
				$this->error('删除失败');die;
			}
		}
	}
}
?>