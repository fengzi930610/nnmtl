<?php
	if(!empty($_POST)){
//		print_r($_POST);die;
		//判断验证码
		if(strtolower($_SESSION['code'])!=strtolower($_POST['code'])){
			echo 1; die;
		}
		
		unset($_POST['code']);
		
		    
		//调用函数     
		$str = checkMobile($_POST['phone']);
		if(!$str){
			echo 4;die;
			
		}
		
		
		$_POST['time'] = time();
		$re = add('suggestion',$_POST);
//		print_r($re);
		if($re==1){
			echo 2;die;
		}else{
			echo 3;die;
		}
		
		
		
		
	}
	
	//手机号码验证函数
		 function checkMobile($str){
		 	$pattern = "/^1[34578]{1}\d{9}$/";
		 	if (preg_match($pattern,$str)){
		 		Return true;
			}else{
				Return false;
			}
		}

?>