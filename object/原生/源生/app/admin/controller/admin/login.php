<?php

if(!empty($_POST)){
	//先判断验证码
//	print_r($_POST);die;
	if(strtolower($_SESSION['code'])!=strtolower($_POST['code']))
	{
		echo 1; die;
	}
	//判断验证码结束
//	验证数据表是否有用户名
	$username = $_POST['username'];//获取提交的用户名
//	$password = $_POST['password'];//获取提交的用户名
	
	$re = selectOne('manager','*',"username='$username'");
//	print_r($re['randstr']);die;
	if(empty($re)){
		echo 2;die;
	}else{
		if($re['state']==0){
			$password = md5($_POST['password'].$re['randstr']);
			if($password==$re['password']){
				if($_POST['check']==1){
				
					setcookie('username',$username,time()+3600);
					setcookie('password',$_POST['password'],time()+3600);
				}else{
				
					setcookie('username',$username,time()-3600);
					setcookie('password',$_POST['password'],time()-3600);
				}
				$_SESSION['userinfo']  = $re;
				$roteId = $re['roteId'];
				$rotearr = selectOne('rote', '*', 'Id='.$roteId);//查询角色；表中对应的角色
				$_SESSION['level'] = explode(',', $rotearr['rote']);//把rote字段里面的权限转化为数组以便到别的地方使用，所以要存到$_SESSION数组里面
//				print_r($_SESSION['level']);die;
				echo 3;die;
				//登录成功将数据库查到的数据写入session
				
		
				
			}else{
				echo 4;die;
			}
		}else{
			echo 5;die;
		}
	}
	//判断账号冻结状态
//	
		
		
	

}
	
include('app/admin/view/admin/login.html');




?>