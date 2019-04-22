<?php
/**
 * Copy Right IJH.CC$
 * $Id index.php by @shzhrui$
 */
$proc = "http://";
define("_GATE_URL_",$proc.$_SERVER['HTTP_HOST'].preg_replace("/\/[^\/]*$/", "", $_SERVER['SCRIPT_NAME']));
require(dirname(dirname(__FILE__))."/system/admin/index.php");
new Index();
