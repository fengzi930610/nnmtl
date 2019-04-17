<?php
	$page = empty($_GET['page'])?1:$_GET['page'];
	$num = 3;//定义每页条数；
	$star = ($page-1)*$num;//偏移量
	$arr = selectAll('news','*','1 order by Id desc limit '.$star.','.$num);
	$pageNum = page('news',$page,$num);

//分页模块代码结束	

foreach($arr as $k=>$v){
	$arr[$k]['type'] = type($v['type']);
	
	if($v['state']==0){
		$arr[$k]['state'] = '<b>审核中</b>';
		update('news',$k, array('rtime'=>null));
	} 
	if($v['state']==1){
		$arr[$k]['state'] = '已发布';
		$arr[$k]['rtime'] = empty($arr[$k]['rtime'])?'':date('Y-m-d H:i:s',$arr[$k]['rtime']);
		
	}
	$arr[$k]['ctime'] = empty($arr[$k]['ctime'])?'':date('Y-m-d H:i:s',$arr[$k]['ctime']);
	$arr[$k]['utime'] = empty($arr[$k]['utime'])?'':date('Y-m-d H:i:s',$arr[$k]['utime']); 
	
}
function type($type){
	switch ($type) {
		case '1':
			return "服装行业资讯";
			break;
		case '2':
			return "中国服饰新闻";
			break;
		case '3':
			return "服装行业新闻";
			break;
		default:
			
			break;
	};
}

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])){
	echo json_encode(array('arr'=>$arr,'pageNum'=>$pageNum));
}else{
	include('app/admin/view/news/list.html');
}
?>