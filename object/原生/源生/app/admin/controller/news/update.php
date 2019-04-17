<?php

if(!empty($_GET)){
	if(!empty($_GET['Id'])){
		$Id = $_GET['Id'];
		$re = selectOne('news','*', "Id='$Id'");
	}
//	$Id = $_GET['Id'];
//	$re = selectOne('news','*', "Id='$Id'");
	if(!empty($_POST)){
		$re = selectOne('news','*', "Id='".$_POST['Id']."'");
//		$data = json_encode($_POST);
//		print_r($_POST);die;
//		echo "string";die;
//		$data = $_POST;
//		print_r($data);die;
//		print_r($_FILES);die;
		if($re['state']==0 && $_POST['state']==1){
			$_POST['rtime'] = time();
		}
		if($re['state']==1 && $_POST['state']==0){
			$_POST['rtime'] = null;
//			update('news',$Id, array('rtime'=>null));
		}
		if($_POST['type']==0){
			echo 1;die;
		}
		if($_POST['content']=="undefined"){
			echo 5;die;
		}
		if(empty($_POST['title'])){
			echo 2;die;
		}
		unset($_POST['file']);
//		print_r($_FILES);die;
//		$path = 
		if(!empty($_FILES)){
			$path = upload('file');
//			print_r($_FILES);die;
//			echo json_encode(array('path'=>$path));
			$_POST['img'] = $path;
			//缩略图函数调用
//			thumb($_POST['img'],200);
//			//水印图
//			water($_POST['img'],'水多');
		}
//	
		$result = update('news',$_POST['Id'],$_POST);
		
			if($result==1){
				echo 3;die;
			}else if($result==0){
				echo 6;die;
			}else{
				echo 4;die;
			}

	}
	
	
	
}

$list = selectAll('category', '*', "fId=1");
include('app/admin/view/news/update.html');
?>