<?php


//header('Content-type: application/json');
//print_r($_FILES);die;
	if(!empty($_POST)){
		
		if($_POST['state']==1){
			$_POST['rtime'] = time();
		}else if($_POST['state']==0){
			$_POST['rtime'] = null;
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
//		print_r($_POST);die;
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
		$_POST['access'] = 0;
		$_POST['author'] = $_SESSION['userinfo']['username'];
		$_POST['ctime'] = time();
		$re = add('news',$_POST);
		
			if($re==1){
				echo 3;die;
			}else{
				echo 4;die;
			}

	}


	$list = selectAll('category', '*', "fId=1");
	include('app/admin/view/news/add.html');
?>