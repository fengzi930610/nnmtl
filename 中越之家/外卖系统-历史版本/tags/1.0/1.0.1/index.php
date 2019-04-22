<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: index.php 9343 2015-03-24 07:07:00Z youyi $
 */
$proc = "http://";
define("_GATE_URL_",$proc.$_SERVER['HTTP_HOST'].preg_replace("/\/[^\/]*$/", "", $_SERVER['SCRIPT_NAME']));
define('IN_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);
$host = $_SERVER['HTTP_HOST'];
$explode_arr = explode('.',$host);
$request_url = trim($_SERVER['REQUEST_URI'], '/');
$url_expload = explode('/',$request_url);
if(preg_match("/^(wd|fx)(\d+)\.([\w\-\.]+)$/i", $host, $m)){
	if($m[1] == 'wd'){
		require(IN_DIR.'system/weidian/index.php');
	}else if($m[1] == 'fx'){
		require(IN_DIR.'system/fenxiao/index.php');
	}
}else if($url_expload[0] == 'waimai'){
    require(IN_DIR.'system/waimai/index.php');
}else if($url_expload[0] == 'wmbiz'){
    require(IN_DIR.'system/wmbiz/index.php');
}else if($url_expload[0]=='staff'){
	require(IN_DIR.'system/staff/index.php');
}else if($url_expload[0]=='jifen'){
	require(IN_DIR.'system/jifen/index.php');
}else if($url_expload[0]=='ditui'){
	require(IN_DIR.'system/ditui/index.php');
}else if($url_expload[0]=='pei'){
	require(IN_DIR.'system/pei/index.php');
}else if($url_expload[0]=='dispatch'){
    require(IN_DIR.'system/dispatch/index.php');
}else if($url_expload[0]=='paotui'){
	require(IN_DIR.'system/paotui/index.php');
}else if($url_expload[0]=='qiang'){
	require(IN_DIR.'system/qiang/index.php');
}else{
	require(IN_DIR.'system/home/index.php');
}

new Index();

