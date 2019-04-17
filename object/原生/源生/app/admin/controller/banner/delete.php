<?php
if(!empty($_GET['Id'])){
	
	$Id = $_GET['Id'];
//	$one = selectOne('banner', 'name', 'Id='.$Id);
	
	$re = delete('banner',$Id);
	if($re){
		echo "<script> alert('删除成功');</script>";
		header("refresh:0;url=index.php?m=admin&c=banner&a=list");
		
	}

}


?>