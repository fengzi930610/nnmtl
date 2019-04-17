<?php
namespace app\admin\controller;

use think\Controller;
use think\Session;
use think\Db;

class Commond extends Controller{
	public function __construct(){
		parent::__construct();
		$this->isLogin();
//		$list = $this->cate();
//		print_r($list);die;
	}
	private function isLogin(){
//		echo "判断是否登陆";die;
		if(!Session::has('username')){
			$this->error('请先登录','Admin/login');
		}
	}
	//递归
	public function cate($Id=0,$list=[],$spac=0){
		if($Id>0) $spac += 2;
		$arr = Db::name('category')->where("fId=$Id")->select();
//		print_r($arr);die;
		if($arr){
			foreach($arr as $k=>$v){
				$v['name'] = str_repeat('|', $spac).$v['name'];
				$list[] = $v;
				$list = $this->cate($v['Id'],$list,$spac);
			}
		}
		return $list;
	}
	//递归遍历
	public function level($Id=0,$list=[],$spac=0){
		$spac += 1;
		$arr = Db::name('level')->where("fId=$Id")->select();
//		print_r($arr);die;
		if($arr){
			foreach($arr as $k=>$v){
				$v['name'] = str_repeat('|', $spac).'-'.$v['name'];
				$list[] = $v;
				$list = $this->level($v['Id'],$list,$spac);
			}
		}
		return $list;
	}
	
	public function thumb($imagePath,$width=150,$height=150){
		$image = \think\Image::open($imagePath);
		$path = dirname($imagePath).'/thumb';
		if(!file_exists($path)){
				mkdir($path,0777,true);
//					print_r(file_exists($filePath));die;
		}
//			print_r($image);die;
		$thumbPath = $path.'/thumb_'.basename($imagePath);
//			print_r($thumbPath);die;
		$image->thumb($width,$height)->save($thumbPath);
		return $thumbPath;
	}
	
	public function water($imagePath,$type=1){
		$image = \think\Image::open($imagePath);
//		print_r($image);die;
		$path = dirname($imagePath).'/water';
		if(!file_exists($path)){
			mkdir($path,0777,true);
//					print_r(file_exists($filePath));die;
		}
		$waterPath = $path.'/water_'.basename($imagePath);
		
		// 给原图左上角添加水印并保存water_image.png
		if($type==1){
			$image->water('./logo.png',\think\Image::WATER_NORTHWEST)->save($waterPath);
		}else{
			$image->text('十年磨一剑','./public/static/admin/foots/helbablackdbnormal.ttf',20,'#ffffff')->save($waterPath);
		}
		
//		print_r($water);die;
//		return $waterPath;
	}
}
?>