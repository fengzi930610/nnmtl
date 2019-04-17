<?php

if(!empty($_GET)){

	$Id = $_GET['Id'];
	$re = selectOne('banner','*', "Id='$Id'");
}
//print_r($re);die;	
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
		
//		print_r($_POST);die;
		if($re==$_POST){
			echo "<script> alert('分类数据未进行改动');location='index.php?m=admin&c=banner&a=list'</script>";
		}
//		print_r($Id);die;
		$cre = update('banner',$Id,$_POST);
//		print_r($cre);die;
		if($cre){
			echo "<script> alert('修改成功');location='index.php?m=admin&c=banner&a=list'</script>";
			
		}else{
			echo "<script> history.go(-1);</script>";
		}
		
	}
	
	
	



include('app/admin/view/banner/update.html');
?>