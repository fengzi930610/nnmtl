<?php
	if(!empty($_POST)){
		
		if(empty($_POST['username']) || empty($_POST['password'])){
			echo 1;die;
		}
		if($_POST['password']!=$_POST['password_2']){
			echo 2;die;
		}
		$username = $_POST['username'];
		$result = selectOne('manager', 'username', "username='$username'");
		if($result){
			echo 3;die;
		}
		$tname =$_POST['tname'];
//		
		$result1 = selectOne('manager','tname',"tname='$tname'");
		if($result1){
			echo 6;die;
		}
		
		$_POST['randstr'] = randstr();
		$_POST['regtime'] = time();
		$_POST['password'] = md5($_POST['password'].$_POST['randstr']);
		unset($_POST['password_2']);
//		$password = $_POST['password'];
//		$Email = $_POST['Email'];
//		$phone = $_POST['phone'];
//		$sex = $_POST['sex'];
		
//		print_r($_POST);die;
		
//		$arr =array('username'=>'$username','password'=>'$password','Email'=>'$Email','phone'=>'$phone','sex'=>'$sex');
		
		$re = add('manager',$_POST);
//		print_r($re);
		if($re==1){
			echo 4;die;
		}else{
			echo 5;die;
		}
		
		
		
		
	}
//查询数据库的所有角色
$rotearr = selectAll('rote','*',1);
//print_r($rotearr);

include('app/admin/view/manager/add.html');
?>