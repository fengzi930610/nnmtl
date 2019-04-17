<?php
	if(!empty($_POST)){
//		print_r($_POST);die;
		if(empty($_POST['name'])){
			echo "<script>alert('菜单名不能为空');history.go(-1)</script>";die;//菜单名不能为空
		}
		
		$name = $_POST['name'];
		$re = selectOne('level', 'name', "name='$name'");
		if($re){
			echo "<script>alert('菜单名已存在');history.go(-1)</script>";die;//菜单名已存在
		}
		
		$fId = $_POST['fId'];
		if($fId!=0){
			$re = selectOne('level', '*', "Id='$fId'");
//			print_r($re);die;
			if($re['fId']!=0){
				echo "<script>alert('菜单不能大于二级');history.go(-1)</script>";die;
			}
			
		}
		
		$re = add('level',$_POST);
		
		if($re==1){
			echo "<script>alert('菜单添加成功');location='index.php?m=admin&c=level&a=list'</script>";die;
		}else{
			echo "<script>alert('菜单添加失败');history.go(-1)</script>";die;
		}
		
		
		
		
	}

$onearr = selectAll('level','*','fId=0');//一级菜单
$arr = [];//定义一个数组为空去接受下面的数组遍历
foreach($onearr as $k=>$v){//遍历一级菜单即fId为0的所有数据
	$arr[] = $v;
	$fId = $v['Id']; 
	$twoarr = selectAll('level','*','fId='.$fId);//查找所有二级菜单即fId不等于0的菜单
	foreach($twoarr as $kk=>$vv){
		$vv['name'] = '|-'.$vv['name'];
		$arr[] = $vv;//若为多级分类则继续往下遍历
	}
}


	include('app/admin/view/level/add.html');
?>