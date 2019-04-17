<?php

if(!empty($_GET)){
	$onearr = selectAll('category','*','fId=0');//一级菜单
	$arr = [];//定义一个数组为空去接受下面的数组遍历
	foreach($onearr as $k=>$v){//遍历一级菜单即fId为0的所有数据
		$arr[] = $v;
		$fId = $v['Id']; 
		$twoarr = selectAll('category','*','fId='.$fId);//查找所有二级菜单即fId不等于0的菜单
		foreach($twoarr as $kk=>$vv){
			$vv['name'] = '|-'.$vv['name'];
			$arr[] = $vv;//若为多级分类则继续往下遍历
		}
	}
	foreach($arr as $k=>$v){
		
	}

	$Id = $_GET['Id'];
	$re = selectOne('category','*', "Id='$Id'");
//	print_r($re);die;
	if(!empty($_POST)){
		if(empty($_POST['name'])){
			echo "<script> alert('分类名称不能为空');history.go(-1);</script>";die;
		}
		$fId = $_POST['fId'];
		if($fId!=0){
			$one = selectOne('category', '*', "Id='$fId'");
//			print_r($re);die;
			if($one['fId']!=0){
				echo "<script> alert('分类不能大于二级');history.go(-1);</script>";die;
			}
			
		}
//		print_r($re);die;
		if($re['name']==$_POST['name'] && $re['fId']==$fId){
			echo "<script> alert('分类数据未进行改动');location='index.php?m=admin&c=category&a=list'</script>";
		}
		$re = update('category',$Id, $_POST);
		if($re){
			echo "<script> alert('修改成功');location='index.php?m=admin&c=category&a=list'</script>";
			
		}else{
			echo "<script> history.go(-1);</script>";
		}
		
	}
	
	
	
}


include('app/admin/view/category/update.html');
?>