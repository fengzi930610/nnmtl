<?php
	if(!empty($_POST)){
		$_POST['rote'] = implode(',', $_POST['rote']);//由于数据库不能存储，转化为字符串
//		print_r($_POST);die;
		$name = $_POST['name'];
		if(empty($name)){
			echo "<script>alert('角色名不能为空');history.go(-1)</script>";die;
		}
		
		
		$re = selectOne('rote', '*', "name='$name'");
		if($re){
			echo "<script>alert('角色名已存在');history.go(-1)</script>";die;
		}
		
		$re = add('rote',$_POST);	
		if($re==1){
			echo "<script>alert('角色添加成功');location='index.php?m=admin&c=rote&a=list';</script>";die;
			
		}else{
			echo "<script>alert('角色添加失败');history.go(-1)</script>";die;
		}
		
		
		
		
	}



	include('app/admin/view/rote/add.html');
?>