<?php

if(!empty($_GET)){
	
	$Id = $_GET['Id'];
	$re = selectOne('manager','*', "Id='$Id'");
	if(!empty($_POST)){
		
//		$_POST['username'] = $re['username'];
		if(!empty($_POST['password']) || !empty($_POST['password_2'])){
			if($_POST['password']==$_POST['password_2']){
				$_POST['randstr'] = randstr(5);
				$_POST['password'] = md5($_POST['password'].$_POST['randstr']);
				unset($_POST['password_2']);
			}else{
				echo "<script> alert('两次密码输入不一致'); history.go(-1)</script>";die;
			}
		}else{
			unset($_POST['password_2']);
			unset($_POST['password']);
		}
		
//		$has = selectAll('manager','1');
//		$_POST['tname'];
//		print_r($has);die;//
		$tname = $_POST['tname'];
		if($tname != $re['tname']){
			$has = selectOne('manager','tname',"tname='$tname'");
//			print_r($has);die;
			if(!empty($has)){
				echo "<script> alert('用户昵称已存在');history.go(-1)</script>";
			}
		}
//		print_r($_POST);die;
		$result = update('manager',$Id, $_POST);
//		print_r($result);die;
		if($result){
			echo "<script> alert('修改成功');location='index.php?m=admin&c=manager&a=list'</script>";
			
		}else{
			echo "<script> history.go(-1);</script>";die;
		}
		
	}
	
	
	
}
$rotearr = selectAll('rote','*',1);

include('app/admin/view/manager/update.html');
?>