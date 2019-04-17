<?php

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
$page = empty($_GET['page'])?1:$_GET['page'];
$num = 10;//定义每页条数；
$star = ($page-1)*$num;//偏移量
$newarr = array();
foreach($arr as $key=>$value){
	if($key>=$star && $key<$page*$num){
		$newarr[] = $value;
	}
}

$pageNum = page('level',$page,$num);
	

	include('app/admin/view/level/list.html');



//print_r($arr);die;

?>