<?php
/**
 * Copy Right IJH.CC$
 * $Id index.php by @shzhrui$
 */
$proc = "http://";
if((!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') ||
	(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
	(!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
) $proc = "https://";
define("_GATE_URL_",$proc.$_SERVER['HTTP_HOST'].preg_replace("/\/[^\/]*$/", "", $_SERVER['SCRIPT_NAME']));
require(dirname(dirname(__FILE__))."/system/admin/index.php");
new Index();
