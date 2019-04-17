<?php
//print_r($_FILES);die;
	if(!empty($_POST)){
		unset($_POST['file']);
//		print_r($_POST);
		if($_POST['state']==1){
			$_POST['atime'] = time();
		}else if($_POST['state']==0){
			$_POST['rtime'] = null;
		}
		if($_POST['type']==0){
			echo 1;die;
		}
		if(empty($_POST['name'])){
			echo 2;die;
		}
		if(empty($_POST['summary'])){
			echo 3;die;
		}
		if($_POST['content'] == 'undefined'){
			echo 4;die;
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
		$_POST['additions'] = $_SESSION['userinfo']['username'];
		$_POST['ctime'] = time();
//		print_r($_POST);die;
		$re = add('product',$_POST);
		
			if($re==1){
				echo 5;die;
				
			}else{
				echo 6;die;
			}

	}else{
		$list = selectAll('category', '*', "fId=9");
//		print_r($list);die;
		include('app/admin/view/product/add.html');
	}



	
?>