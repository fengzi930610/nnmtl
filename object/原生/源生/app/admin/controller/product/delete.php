<?php
if(!empty($_GET['Id'])){
	
	$Id = $_GET['Id'];
	$re = delete('product',$Id);
	if($re){
		echo "<script> alert('删除成功');</script>";
		header("refresh:0;url=index.php?m=admin&c=product&a=list");
		
	}

}


?>