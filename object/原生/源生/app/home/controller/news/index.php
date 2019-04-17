<?php
	$banner = selectOne('banner', '*', '`group`=3 order by Id desc');
	$list = selectAll('category', '*', "fId=1");
	$page = empty($_GET['page'])?1:$_GET['page'];
	$num = 4;//定义每页条数；
	$star = ($page-1)*$num;//偏移量
	
	$type = empty($_GET['type'])?3:$_GET['type'];
	$arr = selectAll('news','*','state=1 and type='.$type.' order by Id desc limit '.$star.','.$num);
//	print_r($arr);die;
	
//	print_r($arr);die;
	$pageNum = nppage('news',$page,$num,$type,$arr);
	

	include('app/home/view/news/index.html');
	
	

?>