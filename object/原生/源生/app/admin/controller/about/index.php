<?php
//公司介绍内容查询
$list = selectAll('category', '*', "fId=15");
$re = selectOne('about','*',1);
//print_r($re['cId']);die;
if(!empty($_POST)){
	if($_POST['cId']==0){
		echo "<script> alert('请选择分类');history.back(-1)</script>";
		die;
	}
//	print_r($_POST);die;
	if(empty($re)){
//		print_r($_POST);die;
		$res = add('about', $_POST);
//		print_r(add('system', $_POST));die;
	}else{
		
		$res = update('about',$re['Id'],$_POST);
//		print_r($res);die;
	}
	if($res>=1){
		echo "<script> alert('操作成功');</script>";
		header("refresh:0;url=index.php?m=admin&c=about&a=index");
	}
	if($re==$_POST){
		echo "<script> alert('数据未修改');</script>";
		header("refresh:0;url=index.php?m=admin&c=about&a=index");
	}
	
}

	


include("app/admin/view/about/index.html");

?>