<?php
if(!empty($_GET['Id'])){
	
	$Id = $_GET['Id'];
	$one = selectOne('level', 'name', 'fId='.$Id);
	if(!empty($one)){
		echo "<script> alert('请先删除子级菜单');history.go(-1);</script>";die;
	}
	$re = delete('level',$Id);
	if($re){
		echo "<script> alert('删除成功');</script>";
		header("refresh:0;url=index.php?m=admin&c=level&a=list");
		
	}

}


?>