<?php
	if(!empty($_POST)){
//		print_r($_POST);die;
		
		if($_POST['group']==0){
			echo "<script>alert('请选择分组类型');history.go(-1)</script>";die;
		}
		
		$_POST['`group`'] = $_POST['group'];
		unset($_POST['group']);
//		upload('imgsrc');
		$_POST['imgsrc'] = upload('img');
//		print_r($_POST);die;
		//缩略图函数调用
		thumb($_POST['imgsrc'],250);
		//水印图
		water($_POST['imgsrc'],'水多','1','logo2.png','0','16');
		
		
		$re = add('banner',$_POST);
		
			if($re==1){
				echo "<script>alert('增加成功')</script>";
				header("refresh:0;url=index.php?m=admin&c=banner&a=list");die;
			}else{
				echo "<script>alert('增加失败');history.go(-1)</script>";die;
			}

	}




	include('app/admin/view/banner/add.html');
?>