<?php
if(!empty($_GET['Id'])){
	$Id = $_GET['Id'];
	$re =selectOne('product', '*', "Id='$Id'");
}
$type = empty($_GET['type'])?2:$_GET['type'];
$banner =selectOne('banner', 'imgsrc', '`group`=4 order by Id limit 1');
$list = selectAll('category', '*', "fId=9");

include('app/home/view/pro_details/index.html');
?>