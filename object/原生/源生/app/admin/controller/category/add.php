<?php
	if(!empty($_POST)){
//		print_r($_POST);die;
		if(empty($_POST['name'])){
			echo 1;die;//分类名不能为空
		}
		
		$name = $_POST['name'];
		$re = selectOne('category', 'name', "name='$name'");
		if($re){
			echo 2;die;//分类名已存在
		}
		
		$fId = $_POST['fId'];
		if($fId!=0){
			$re = selectOne('category', '*', "Id='$fId'");
//			print_r($re);die;
			if($re['fId']!=0){
				echo 5;die;
			}
			
		}
		
		$re = add('category',$_POST);
		
		if($re==1){
			echo 3;die;
		}else{
			echo 4;die;
		}
		
		
		
		
	}

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


	include('app/admin/view/category/add.html');
?>