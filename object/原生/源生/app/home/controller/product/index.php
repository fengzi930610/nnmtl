<?php
$list = selectAll('category', '*', "fId=9");
$banner =selectOne('banner', 'imgsrc', '`group`=4 order by Id limit 1');

$page = empty($_GET['page'])?1:$_GET['page'];
$num = 9;//定义每页条数；
$star = ($page-1)*$num;//偏移量

$type = empty($_GET['type'])?2:$_GET['type'];
$categoryname = selectOne('category', 'name', 'Id='."'".$type."'");
//print_r($categoryname);
$arr = selectAll('product','*','state=1 and type='.$type.' order by Id desc limit '.$star.','.$num);
//	print_r($arr);die;

//	print_r($arr);die;
$pageNum = nppage('product',$page,$num,$type,$arr);



//$product =selectAll('product,category', 'product.*,category.name as categoryname', 'product.type=category.Id and type=2 order by Id desc limit 9');
//print_r($baoan);die;
include('app/home/view/product/index.html');
?>