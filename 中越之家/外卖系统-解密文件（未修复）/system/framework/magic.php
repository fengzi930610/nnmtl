<?php
//zend54   
//Decode by www.dephp.cn  QQ 2859470
?>
<?php
class K
{
	static 	public $system;

	static public function M($mdl)
	{
		return K::$system->load_model($mdl);
	}

	static public function L($lang)
	{
	}

	static public function C($ctl)
	{
	}

	static public function W($wgt)
	{
		return K::$system->load_widget($wgt);
	}

	static public function DB($db = NULL)
	{
		return K::$system->LoadDB($db);
	}

	static public function IDS($ids)
	{
		if (is_array($ids)) {
			$ids = implode("','", $ids);
		}
		else if (strpos($ids, "'") === false) {
			$ids = str_replace(",", "','", trim($ids, ","));
		}
		else {
			return trim($ids, ",");
		}

		return "'$ids'";
	}

	static public function GUID($key = "")
	{
		return strtoupper(md5(uniqid(mt_rand(), true) . $key));
	}

	static public function IS_GUID($guid)
	{
		return preg_match("/[0-9A-F]{32}/", $guid);
	}
}

function GUID($key = "")
{
	$charid = strtoupper(md5(uniqid(mt_rand(), true) . $key));
	$hyphen = chr(45);
	$GUID = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
	return $GUID;
}

function L($key)
{
	$__LANG = &$GLOBALS["__LANG"];
	return $__LANG[$key] ? $__LANG[$key] : $key;
}

if (!defined("__CORE_DIR")) {
	exit("Access Denied");
}

