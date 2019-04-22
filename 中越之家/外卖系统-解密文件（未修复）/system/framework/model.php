<?php
//zend54   
//Decode by www.dephp.cn  QQ 2859470
?>
<?php
class Model
{
	public $__MDL = "Model";
	public $succeed = false;
	public $error = 0;
	public $message = "";
	static 	public $system;
	private $_reback_params = array();

	public function __construct(&$system)
	{
		if (self::$system === NULL) {
			self::$system = &$system;
		}

		$this->_G = &$system->_G;
		$this->db = &$system->db;
		$this->msgbox = &$system->msgbox;
		$this->cookie = &$system->cookie;
		$this->cache = &$system->cache;
	}

	public function set_reback($k, $v)
	{
		$this->_reback_params[$k] = $v;
	}

	public function get_reback($k)
	{
		return $this->_reback_params[$k];
	}

	public function __call($name, $args)
	{
		trigger_error(get_class($this) . "->$name Not Found!!");
	}
}

if (!defined("__CORE_DIR")) {
	exit("Access Denied");
}

