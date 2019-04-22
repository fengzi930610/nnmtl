<?php
/**
 * Copy	Right Anhuike.com
 * $Id filter.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_Content_Filter
{
	public static function addslashes($str, $force=false)
	{
		if(!defined('MAGIC_QUOTES_GPC')){
			define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
		}
		if (!MAGIC_QUOTES_GPC || $force) {
			if (is_array($str)) {
				foreach($str as $key => $val) {
					$str[$key] = self::addslashes($val, $force);
				}
			} else {
				$str = addslashes(trim($str));
			}
		}
		return $str;	
	}
	public static function stripslashes($str, $force=false)
	{
		if(!defined('MAGIC_QUOTES_GPC')){
			define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
		}
		if (MAGIC_QUOTES_GPC || $force) {
			if (is_array($str)) {
				foreach($str as $key => $val) {
					$str[$key] = self::stripslashes($val, $force);
				}
			} else {
				$str = stripslashes($str);
			}
		}
		return $str;	
	}	
}