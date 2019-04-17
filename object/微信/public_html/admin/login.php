<?php
	//获取用户提交的账号密码
	$user = $_POST["user"];
	$pass = $_POST["pass"];

	//打开JSON文件
	$file = file_get_contents("data/user.json");
	
	//将文件的数据转换出来
	$userinfo = json_decode($file)->userinfo;
	//遍历数据 获取登录的账号和密码
	foreach($userinfo as $value){
		if($value->user == $user){//账号比较
			//对服务器的数据进行加密
			if(md5($value->pass) == $pass){//密码比较
				echo '{"type":"success","code":"1"}';
				exit;
			}else{
				echo '{"type":"error","code":"2"}';
				exit;
			}
		}
	}
	//上面没有返回，代表找到不该用户
	echo '{"type":"error","code":"1"}';//用户不存在
?>