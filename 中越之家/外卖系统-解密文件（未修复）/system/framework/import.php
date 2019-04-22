<?php
//zend54   
//Decode by www.dephp.cn  QQ 2859470
?>
<?php
class Import
{
	static 	public $_GFILES = array();

	static public function I($i)
	{
		$i = str_replace(".", "/", $i);
		$path = "";

		if ($pos = strpos($i, ":")) {
			$path = substr($i, 0, $pos);
		}

		return self::_import(__CFG::DIR . "{$path}models/$i/interface.php");
	}

	static public function E($e)
	{
		$e = str_replace(".", "/", $e);
		$path = "";

		if ($pos = strpos($e, ":")) {
			$path = substr($e, 0, $pos);
			$e = substr($e, $pos + 1);
		}

		if (!self::_import(__CFG::DIR . "{$path}models/$e/exception.php")) {
			self::_import(__CFG::DIR . "models/$e/exception.php");
		}
	}

	static public function M($mdl)
	{
		$mdl = str_replace(".", "/", $mdl);
		$path = "";

		if ($pos = strpos($mdl, ":")) {
			$path = substr($mdl, 0, $pos) . "/";
			$mdl = substr($mdl, $pos + 1);
		}

		if ($path == "plugin/") {
			$file = __CFG::DIR . preg_replace("/^([\w\/]+)\/(\w+)$/i", "plugins/\1/models/\2.mdl.php", $mdl);
			self::_import($file);
			$mdl = "plugin/$mdl";
		}
		else {
			$file = __CFG::DIR . "{$path}models/$mdl.mdl.php";
			if (!self::_import($file) && !$path) {
				self::_import(__APP_DIR . "models/$mdl.mdl.php");
			}
		}

		$mdl = "Mdl_" . str_replace(" ", "_", ucwords(str_replace("/", " ", $mdl)));
		return $mdl;
	}

	static public function C($ctl)
	{
		$ctl = str_replace(".", "/", $ctl);

		if ($pos = strpos($ctl, ":")) {
			$path = substr($ctl, 0, $pos);
			$ctl = substr($ctl, $pos + 1);
		}

		if (!self::_import(__APP_DIR . "controllers/$ctl.ctl.php")) {
			return false;
		}

		return "Ctl_" . str_replace(" ", "_", ucwords(str_replace("/", " ", $ctl)));
	}

	static public function W($w)
	{
		self::_import(__CFG::DIR . "plugins/widgets/" . $w . "/widget.php");
		return "Widget_" . ucfirst($w);
	}

	static public function P($plugin)
	{
		self::_import(__CFG::DIR . "plugins/$plugin.php");
		return "Plugin_" . str_replace(" ", "_", ucfirst(str_replace("/", " ", $plugin)));
	}

	static public function L($lib)
	{
		self::_import(__CFG::DIR . "libs/" . $lib);
	}

	static private function _import($file)
	{
		if (!file_exists($file)) {
			return false;
		}

		$hash = md5($file);

		if (!isset(self::$_GFILES[$hash])) {
			self::$_GFILES[$hash] = $file;
			require $file;
		}

		return true;
	}
}

class Using extends Import
{

	static public function I($i)
	{
		$i = str_replace(".", "/", $i);
		$path = "";

		if ($pos = strpos($i, ":")) {
			$path = substr($i, 0, $pos);
		}

		return self::_import(__CFG::DIR . "{$path}models/$i/interface.php");
	}

	static public function E($e)
	{
		$e = str_replace(".", "/", $e);
		$path = "";

		if ($pos = strpos($e, ":")) {
			$path = substr($e, 0, $pos);
			$e = substr($e, $pos + 1);
		}

		if (!self::_import(__CFG::DIR . "{$path}models/$e/exception.php")) {
			self::_import(__CFG::DIR . "models/$e/exception.php");
		}
	}

	static public function M($mdl)
	{
		$mdl = str_replace(".", "/", $mdl);
		$path = "";

		if ($pos = strpos($mdl, ":")) {
			$path = substr($mdl, 0, $pos) . "/";
			$mdl = substr($mdl, $pos + 1);
		}

		if ($path == "plugin/") {
			$file = __CFG::DIR . preg_replace("/^([\w\/]+)\/(\w+)$/i", "plugins/\1/models/\2.mdl.php", $mdl);
			self::_import($file);
			$mdl = "plugin/$mdl";
		}
		else {
			$file = __CFG::DIR . "{$path}models/$mdl.mdl.php";
			if (!self::_import($file) && !$path) {
				self::_import(__APP_DIR . "models/$mdl.mdl.php");
			}
		}

		$mdl = "Mdl_" . str_replace(" ", "_", ucwords(str_replace("/", " ", $mdl)));
		return $mdl;
	}

	static public function C($ctl)
	{
		$ctl = str_replace(".", "/", $ctl);

		if ($pos = strpos($ctl, ":")) {
			$path = substr($ctl, 0, $pos);
			$ctl = substr($ctl, $pos + 1);
		}

		if (!self::_import(__APP_DIR . "controllers/$ctl.ctl.php")) {
			return false;
		}

		return "Ctl_" . str_replace(" ", "_", ucwords(str_replace("/", " ", $ctl)));
	}

	static public function W($w)
	{
		self::_import(__CFG::DIR . "plugins/widgets/" . $w . "/widget.php");
		return "Widget_" . ucfirst($w);
	}

	static public function P($plugin)
	{
		self::_import(__CFG::DIR . "plugins/$plugin.php");
		return "Plugin_" . str_replace(" ", "_", ucfirst(str_replace("/", " ", $plugin)));
	}

	static public function L($lib)
	{
		self::_import(__CFG::DIR . "libs/" . $lib);
	}

	static private function _import($file)
	{
		if (!file_exists($file)) {
			return false;
		}

		$hash = md5($file);

		if (!isset(self::$_GFILES[$hash])) {
			self::$_GFILES[$hash] = $file;
			require $file;
		}

		return true;
	}
}

if (!defined("__CORE_DIR")) {
	exit("Access Denied");
}

