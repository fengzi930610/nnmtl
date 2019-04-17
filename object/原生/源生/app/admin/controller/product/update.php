<?php
if(!empty($_POST)){
		
//		if($_POST['state']==1){
//			$_POST['atime'] = time();
//		}else if($_POST['state']==0){
//			$_POST['rtime'] = null;
//		}

		unset($_POST['file']);
//		print_r($_POST);die;
		
		if($_POST['type']==0){
			echo 1;die;
		}
		if(empty($_POST['name'])){
			echo 2;die;
		}
		if(empty($_POST['summary'])){
			echo 3;die;
		}
		if(empty($_POST['content'])){
			echo 4;die;
		}
		$id = $_POST['Id'];
//		print_r(!empty($_GET['Id']));die;
		$result = selectOne('product','*', "Id='$id'");
		if($result['state']==0 && $_POST['state']==1){
			$_POST['rtime'] = time();
		}
		if($result['state']==1 && $_POST['state']==0){
			$_POST['rtime'] = null;
//			update('product',$Id, array('rtime'=>));
		}
		if(!empty($_FILES)){
			$_POST['imgsrc'] = upload('file');
	//		print_r($_POST);die;
			//缩略图函数调用
			thumb($_POST['imgsrc'],250);
			//水印图
			water($_POST['imgsrc'],'水多','1','logo2.png','0','16');
		}else{
			unset($_POST['img'])
		}
//		print_r($_POST);die;
		$update = update('product',$_POST['Id'], $_POST);
		
		if($update==1){
//			$_POST['utime'] = time();
			echo 5;die;
			
		}else if($update==0){
			echo 7;die;
			
		}else{
			echo 6;die;
		}
		
	}

if(!empty($_GET['Id'])){
	$Id = $_GET['Id'];
//		print_r(!empty($_GET['Id']));die;
	$re = selectOne('product','*', "Id='$Id'");
	
	$list = selectAll('category', '*', "fId=9");
	
	include('app/admin/view/product/update.html');
}
?>