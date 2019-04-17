<?php
//	$page = empty($_GET['page'])?1:$_GET['page'];
//	$num = 2;//定义每页条数；
//	$star = ($page-1)*$num;//偏移量、
	$arr = selectAll('rote','*','1 order by Id desc');
//	$pageNum = page('manager',$page,$num);

//分页模块代码结束	




include('app/admin/view/rote/list.html');


//print_r($arr);die;

?>