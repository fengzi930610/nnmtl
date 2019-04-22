<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: logs.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_System_Logs
{
	public $logpath = "";
	public function __construct()
	{
		$this->logpath = __CFG::DIR.'data/logs/';
	}
	public function Log($fname,$log)
	{
		if(is_array($log) || is_object($log)){
			$log = var_export($log,true);
		}
		$log =	'<?php exit("Access denied");?>'."\n+-----------------------------------------------------+\n".
				"Time:".date("Y-m-d H:i:s")."\nLog:{$log}\n\n";
		$fp = @fopen($this->logpath."{$fname}.php","a");
		@fwrite($fp,$log);
		@fclose($fp);
	}
	public function syslog($fname,$log)
	{
		if(is_array($log) || is_object($log)){
			$log = var_export($log,true);
		}
		$log =	'<?php exit("Access denied");?>'."\n+-----------------------------------------------------+\n".
				"Time:".date("Y-m-d H:i:s")."\nLog:{$log}\n\n";	
		$fp = @fopen($this->logpath."{$fname}.php","a");
		@fwrite($fp,$log);
		@fwrite($fp,"SERVER:".var_export($_SERVER,true));
		@fwrite($fp,"\nCOOKIE:".var_export($_COOKIE,true));
		@fwrite($fp,"\nPOST:".var_export($_POST,true));
		@fwrite($fp,"\nGET:".var_export($_GET,true)."\n");		
		@fclose($fp);	
	}
	public function error($log)
	{
		if(is_array($log) || is_object($log)){
			$log = var_export($log, true);
		}
		$log =	'<?php exit("Access denied");?>'."\tTime:".date("Y-m-d H:i:s")."\tError:{$log}\n";
		$fp = @fopen($this->logpath.'system-error-'.date('Ym').'.php',"a");
		@fwrite($fp,$log);
		@fclose($fp);
	}
	public function AppLog($fname,$log)
	{
	}
}