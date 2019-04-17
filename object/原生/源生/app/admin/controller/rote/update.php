<?php

if(!empty($_GET)){
	
	$Id = $_GET['Id'];
	$re = selectOne('rote','*', "Id='$Id'");
	$re['rote'] = explode(',', $re['rote']);
	if(!empty($_POST)){
		if(empty($_POST['name'])){
			echo "<script>alert('角色名不能为空');history.go(-1)</script>";die;
		}
		$_POST['Id'] = $Id;
		if($_POST==$re){
			echo "<script> alert('修改成功');location='index.php?m=admin&c=rote&a=list'</script>";die;
		}
		$_POST['rote'] = implode(',', $_POST['rote']);
		
		$result = update('rote',$Id, $_POST);
		if($result){
			echo "<script> alert('修改成功');location='index.php?m=admin&c=rote&a=list'</script>";die;
			
		}else{
			echo "<script> history.go(-1);</script>";die;
		}
		
	}
	
	
	
}
//$rotearr = selectAll('rote','*',1);

include('app/admin/view/rote/update.html');
?>