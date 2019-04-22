<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 */
if(strtolower(php_sapi_name()) != 'cli'){
    exit('only run cli');
}
@ini_set("display_errors","On");
@error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT ^ E_WARNING);;
@set_time_limit(0);
@ini_set('memory_limit','128M');
@ini_set('allow_url_fopen', 'On');
@date_default_timezone_set('Asia/Shanghai');
require(dirname(dirname(__FILE__)).'/system/home/index.php');
$system = new Index('magic-shell');

$attachCfg = K::M('system/config')->get('attach');


$count = 0;
function ossync($dir)
{
	global $count;
	$count ++;
	$dir = rtrim($dir, '/');
	static $_uploader = null;
	if($_uploader === null){
		$_uploader = K::M('storage/aliyun');
	}

	$handler = dir($dir); 
	while($fname = $handler->read()){
		$file = $dir.'/'.$fname;
		if($fname == '.' || $fname == '..'){

		}elseif(is_dir($file)){
			return ossync($file);
		}elseif(is_file($file)){
			$_uploader->upload($file, null, false);
			echo "COUNT:{$count}\t{$file}\n";
		}
	}
	$handler->close();
}
