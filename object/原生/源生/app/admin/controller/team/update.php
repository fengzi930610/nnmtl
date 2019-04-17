<?php

if(!empty($_GET)){

	$Id = $_GET['Id'];
	$re = selectOne('team','*', "Id='$Id'");
}
//print_r($re);die;	
	if(!empty($_POST)){
		
		unset($_POST['file']);
//		print_r($_POST);die;
		
		if(empty($_POST['name'])){
			echo 1;die;
		}
		if(empty($_POST['phone'])){
			echo 2;die;
		}
		if(empty($_POST['position'])){
			echo 3;die;
		}
		
		if(!empty($_FILES)){
//			print_r($_FILES);die;
//			echo "string";die;
			$_POST['imgsrc'] = upload('file');
//			print_r($_POST['imgsrc']);die;
			//缩略图函数调用
			thumb($_POST['imgsrc'],250);
			//水印图
			water($_POST['imgsrc'],'水多','1','logo2.png','0','16');
		
//			$_POST['imgsrc']=upload('pic');
//			//缩略图函数调用
//			thumb($_POST['imgsrc'],100);
//			//水印图
//			water($_POST['imgsrc'],'水多');
		}
//		die;
//		print_r($_POST);die;
		$re = update('team','Id='.$_POST['Id'],$_POST);
		
			if($re==1){
				echo 4;die;
				
			}else if($re==0){
				echo 6;die;
			}else{
				echo 5;die;
			}

	}
	
	
	



include('app/admin/view/team/update.html');
?>