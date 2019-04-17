<?php

if(!empty($_GET['Id'])){
	
	$Id = $_GET['Id'];
//	print_r($Id);die;
	$re = selectOne('suggestion','*', "Id='$Id'");
//	print_r($re);die;
	if(!empty($_POST)){
//		print_r($_POST);die;
//		echo 520;die;
		//手机号码验证函数
		 function checkMobile($str){
		 	$pattern = "/^1[34578]{1}\d{9}$/";
		 	if (preg_match($pattern,$str)){
		 		Return true;
			}else{
				Return false;
			}
		}    
		//调用函数     
		$str = checkMobile($_POST['phone']);
//		print_r($str);die;
		if(!$str){
			echo "<script>alert('手机号码格式错误');history.back(-1)</script>";die;
			
		}
		
		$result = update('suggestion',$_POST['Id'],$_POST);
//		print_r($re);
		if($result==1){
			echo "<script>alert('修改成功');location='index.php?m=admin&c=suggestion&a=list'</script>";die;
		}else if($result==0){
			echo "<script>alert('数据未修改');location='index.php?m=home&c=suggestion&a=list'</script>";die;
		}else{
			echo "<script>alert('修改失败');history.back(-1)</script>";die;
		}
		
		
		
		
	}
	
	
	
}

include('app/admin/view/suggestion/update.html');
?>