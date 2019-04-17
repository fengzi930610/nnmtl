<?php
$re = selectOne('system','*',1);
//print_r($re);die;
if(!empty($_POST)){
	
	
//	print_r($_POST);die;
	$_POST['logo'] = upload('img');
//	print_r($_POST);die;
	if(empty($re)){
	
		$res = add('system', $_POST);
//		print_r(add('system', $_POST));die;
		
	}else{
//		echo !empty(upload('img'));die;
		if(empty(upload('img'))){
//			print_r($_POST);die;
			unset($_POST['logo']);
		}
		$res = update('system',$re['Id'],$_POST);
//		print_r($res);die;
	}
	if($res>=1){
		echo "<script> alert('操作成功');</script>";
		header("refresh:0;url=index.php?m=admin&c=system&a=index");
	}
	if($re==$_POST){
		echo "<script> alert('数据未修改');</script>";
		header("refresh:0;url=index.php?m=admin&c=system&a=index");
	}
	
}

	


include("app/admin/view/system/index.html");

?>