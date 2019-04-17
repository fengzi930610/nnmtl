<?php
	$page = empty($_GET['page'])?1:$_GET['page'];
	$num = 8;//定义每页条数；
	$star = ($page-1)*$num;//偏移量
	$arr = selectAll('manager left join rote on manager.roteId=rote.Id','manager.*,rote.name','1 order by manager.Id desc limit '.$star.','.$num);
	$pageNum = page('manager',$page,$num);

//分页模块代码结束	

foreach($arr as $k=>$v){
//	
	if($v['state']==0){
		$arr[$k]['state']='启用';
	}else if($v['state']==1){
		$arr[$k]['state']='<b>冻结</b>';
	}
	if($v['sex']==0){
		$arr[$k]['sex']='女';
	}else if($v['sex']==1){
		$arr[$k]['sex']='男';
	}
	$arr[$k]['regtime']= date('Y-m-d H:i:s',$v['regtime']);
	
}
//print_r($_SERVER);die;

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])){
	echo json_encode(array('arr'=>$arr,'pageNum'=>$pageNum));
}else{
	include('app/admin/view/manager/list.html');
}


//print_r($arr);die;

?>