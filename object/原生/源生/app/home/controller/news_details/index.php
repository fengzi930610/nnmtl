<?php
if(!empty($_GET['Id'])){
	$Id = $_GET['Id'];
	$re =selectOne('news', '*', "Id='$Id'");
//	print_r($re);die;
	$type = empty($_GET['type'])?3:$_GET['type'];
	$prev = selectOne('news', '*', "type='$type' and Id<'$Id' and state='1' order by Id desc");
	$next = selectOne('news', '*', "type='$type' and Id>'$Id'");
//	print_r($next);die;
//	select min('id') from news where id>$Id
//	$prev = selectOne('news', '*', "Id='$Id'");
}
$banner = selectOne('banner', '*', '`group`=3 order by Id desc');
$list = selectAll('category', '*', "fId=1");
include('app/home/view/news_details/index.html');
?>