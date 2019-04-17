<?php

if(!empty($_GET)){
	$onearr = selectAll('level','*','fId=0');//一级菜单
	$arr = [];//定义一个数组为空去接受下面的数组遍历
	foreach($onearr as $k=>$v){//遍历一级菜单即fId为0的所有数据
		$arr[] = $v;
		$fId = $v['Id']; 
		$twoarr = selectAll('level','*','fId='.$fId);//查找所有二级菜单即fId不等于0的菜单
		foreach($twoarr as $kk=>$vv){
			$vv['name'] = '|-'.$vv['name'];
			$arr[] = $vv;//若为多级分类则继续往下遍历
		}
	}
	foreach($arr as $k=>$v){
		
	}
}
	$Id = $_GET['Id'];
	$re = selectOne('level','*', "Id='$Id'");
	
	if(!empty($_POST)){
//		print_r($_POST);die;
		
		if(empty($_POST['name'])){
			echo "<script> alert('分类名称不能为空');history.go(-1);</script>";die;
		}
		$fId = $_POST['fId'];
		if($fId!=0){
			$one = selectOne('level', '*', "Id='$fId'");
//			print_r($one);die;
			if($one['fId']!=0){
				echo "<script> alert('分类不能大于二级');history.go(-1);</script>";die;
			}
			
		}
//		print_r($_POST);die;
		if($re==$_POST){
			echo "<script> alert('分类数据未进行改动');location='index.php?m=admin&c=level&a=list'</script>";
		}
//		print_r($Id);die;
		$cre = update('level',$Id,$_POST);
//		print_r($cre);die;
		if($cre){
			echo "<script> alert('修改成功');location='index.php?m=admin&c=level&a=list'</script>";
			
		}else{
			echo "<script> history.go(-1);</script>";
		}
		
	}
	
	
	



include('app/admin/view/level/update.html');
?>