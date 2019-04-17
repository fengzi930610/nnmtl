<?php


$page = empty($_GET['page'])?1:$_GET['page'];
$num = 8;//定义每页条数；
$star = ($page-1)*$num;//偏移量
$bannerarr = selectAll('banner','*','1 order by Id desc limit '.$star.','.$num);

foreach($bannerarr as $k=>$v){
	$bannerarr[$k]['group']=group($v['group']);
	$path = $v['imgsrc'];
	$dirname = dirname($path);
	$basename = basename($path);
	$bannerarr[$k]['imgsrc']=$dirname.'/thumb_'.$basename;
}
function group($group){
	switch ($group) {
		case '1':
			return "翔隆首页";
			break;
		case '2':
			return "公司介绍";
			break;
		case '3':
			return "新闻中心";
			break;
		case '4':
			return "产品中心";
			break;
		case '5':
			return "联系我们";
			break;
		default:
			
			break;
	};
}
$pageNum = page('banner',$page,$num);


include('app/admin/view/banner/list.html');



?>