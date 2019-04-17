<?php
if(!empty($_GET['Id'])){
	
	$Id = $_GET['Id'];
//	print_r($Id);
	$one = selectOne('category', '*', "Id=$Id");
//	print_r($one);die;
	if($one['fId']!=0){
		$re = delete('category',$Id);
		if($re){
			echo "<script> alert('删除成功');</script>";
			header("refresh:0;url=index.php?m=admin&c=category&a=list");
			
		}
	}else{
		echo "<script> alert('请先删除子分类');history.go(-1);</script>";
	}
	

}


?>