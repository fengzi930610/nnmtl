<?php


$page = empty($_GET['page'])?1:$_GET['page'];
$num = 8;//定义每页条数；
$star = ($page-1)*$num;//偏移量
$teamrarr = selectAll('team','*','1 order by Id desc limit '.$star.','.$num);
//print_r($teamrarr);die;
foreach($teamrarr as $k=>$v){
	$teamrarr[$k]['sex']=group($v['sex']);
	$path = $v['imgsrc'];
	$dirname = dirname($path);
	$basename = basename($path);
	$teamrarr[$k]['imgsrc']=$dirname.'/thumb_'.$basename;
}
function group($group){
	switch ($group) {
		case '1':
			return "男";
			break;
		case '0':
			return "女";
			break;
		
		default:
			
			break;
	};
}
$pageNum = page('team',$page,$num);


include('app/admin/view/team/list.html');



?>