<?php

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
$page = empty($_GET['page'])?1:$_GET['page'];
$num = 8;//定义每页条数；
$star = ($page-1)*$num;//偏移量
$newarr = array();
foreach($arr as $key=>$value){
	if($key>=$star && $key<$page*$num){
		$result = selectOne('category', 'name', 'Id="'.$value['fId'].'"');
		$value[] = $result['name'];
		$newarr[] = $value;
		
//		$newarr[] =$result['name'];
	}
}

$pageNum = page('category',$page,$num);
//	print_r($newarr);die;

	include('app/admin/view/category/list.html');



//print_r($arr);die;

?>