<?php
header("Content-type:text/html; charset=utf-8");


$link = @mysqli_connect('localhost', 'root', 'root', '0528project', '3306');//链接数据库，链接成功返回对象
if(!$link){
	die('连接失败'.mysqli_connect_error());
}
mysqli_set_charset($link,'utf8');//设置编码
//删除
function delete($table,$id){
	global $link;
	
	$sql = 'delete from '.$table.' where Id='.$id;//数据库执行语句
	
	mysqli_query($link, $sql);
	
	$re = mysqli_affected_rows($link);
	
	return $re;
	
}



//新增内容
function add($table,$arr){
	global $link;
	
	$kstr = '';
	$vstr = '';
	foreach($arr as $k=>$v){
		$kstr .=",".$k;
		$vstr .=",'".$v."'";
	}
	$kstr = substr($kstr, 1);
	$vstr = substr($vstr, 1);
	
	$sql = "insert into $table($kstr) values ($vstr)";
//	print_r($sql);die;
	mysqli_query($link, $sql);
	
	$re = mysqli_affected_rows($link);
	
	return $re;
}
//$arr = array('username'=>'admin5','password'=>'admin5');
//$re = add('manager',$arr);
//print_r($re);
//更改
function update($table,$id,$arr){
	global $link;
	
	$setstr = "";
	
	foreach($arr as $k=>$v){
		
		$setstr .= ",".$k."='".$v."'";
		
	}
	
	$setstr = substr($setstr, 1);
	
	$sql = "update $table set $setstr where Id=".$id;
	
	mysqli_query($link, $sql);
	$re = mysqli_affected_rows($link);
	
	return $re;
	
}

//$arr = array('username'=>'admin100');
//update('manager',9,$arr);

//查询一条

function selectOne($table,$field,$where){
	global $link;
		
	$sql = "select $field from $table where $where";
	
	$result = mysqli_query($link, $sql);
	
	
	$re = mysqli_fetch_assoc($result);
	
	return $re;
	
	
	
}

//selectOne('manager','*','username="admin" and password="admin"');


//查询所有

function selectAll($table,$field,$where){
	global $link;
	
	$sql = "select $field from $table where $where";
	
	$result = mysqli_query($link, $sql);
	
	$arr = array();//定义一个空数组去接收结果集
	
	while($re=mysqli_fetch_assoc($result)){
		
		$arr[]=$re;
		
	}
	return $arr;
	
}





?>