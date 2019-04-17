<?php
	$page = empty($_GET['page'])?1:$_GET['page'];
	$num = 8;//定义每页条数；
	$star = ($page-1)*$num;//偏移量
	$arr = selectAll('suggestion','*','1 order by Id desc limit '.$star.','.$num);
	$pageNum = page('suggestion',$page,$num);

//分页模块代码结束	

//print_r($_SERVER);die;

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])){
	echo json_encode(array('arr'=>$arr,'pageNum'=>$pageNum));
}else{
	include('app/admin/view/suggestion/list.html');
}


//print_r($arr);die;

?>