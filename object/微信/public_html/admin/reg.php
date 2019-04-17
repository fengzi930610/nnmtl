<?php
	//print_r($_POST);
	
//	打开JSON文件
	$file = file_get_contents("data/user.json");
	
	//将文件的数据转换出来
	$obj = json_decode($file);
	
	//判断该用户是否存在
	foreach($obj->userinfo as $el){
		if($el->user === $_POST["user"]){
			echo '{"type":"error","code":"1"}';//代表用户名重复
			exit;
		}
		if($el->email === $_POST["email"]){
			echo '{"type":"error","code":"2"}';//代表邮箱已经被注册
			exit;
		}
		
	}
	
	
	//根据自己的需求添加新的数据
	$user = array("user"=>$_POST["user"],"pass"=>$_POST["pass"],"email"=>$_POST["email"]);
	//将新的数据添加到整体内容之中
	
	$obj ->userinfo[] = $user;
	
	//将数据保存回文件中
	file_put_contents("data/user.json", json_encode($obj));
	
	echo '{"type":"success","code":"1"}';
?>