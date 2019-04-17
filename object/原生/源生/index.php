<?php
header("Content-type:text/html; charset=utf-8");

date_default_timezone_set("PRC");
session_start();

include('function/function.php');
include('function/sql.php');
include('function/code.php');
//include('public/img/imgages');

$module = empty($_GET['m'])?'admin':$_GET['m'];
$con = empty($_GET['c'])?'admin':$_GET['c'];
$action = empty($_GET['a'])?'login':$_GET['a'];

define('MODULE', $module);
define('CON', $con);
define('ACTION', $action);

//验证码无法在没有登录之前使用，那么在登陆之前引用即可
if($action=='code')
{
	include('app/admin/controller/admin/code.php');	die;
}

//判断get[m]是否存在，判断是否是访问后台admin则需要先登录，如果是访问home无所谓
if($module=='admin') 
{
	//判断session是否不存在,如果session不存在说明没有登陆，跳转到登陆界面
	if(empty($_SESSION['userinfo']))
	{
		include('app/admin/controller/admin/login.php');die;
	}
}
//页面标题
if(!empty($_GET['m']) && $_GET['m'] == "admin"){
	$c = $_GET['c'];
	$a = $_GET['a'];
//	print_r($a);die;
	$one = selectOne('level', 'name,fId', "controller='$c' and action='$a'");
//	print_r($one);die;
	$Id =  $one['fId'];
	$two = selectOne('level', 'name', "Id='$Id'");
	$title = $one['name'].' - '.$two['name'];
//	print_r($title);die;
//	print_r($two);die;
//	$result = 
}

if($module=="home"){
	$re = selectOne('system','*',1);
	$navarr = selectAll('nav','*',1);
}
if($con == 'banner'){
	$navarr = selectAll('nav','*',1);
}

if($action!='login' && $action!='logout' && $action!='code' && ($con!='index' && $action!='index') && $module !='home'){
	if($_SESSION['userinfo']['username']!='admin'){
		
		$levelone = selectOne('level', '*', 'module="'.MODULE.'" and controller="'.CON.'" and action="'.ACTION.'"');
//print_r($levelone);die;
		if(!in_array($levelone['name'], $_SESSION['level'])){
			echo "<script>alert('无权访问');history.go(-1);</script>";die;
		}
	}
	
}

$addlist = selectAll('level','*','action="'.'add"'); 
//print_r($addlist);die;
include('app/'.$module.'/controller/'.$con.'/'.$action.'.php');

?>