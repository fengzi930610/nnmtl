<?php
//zend54   
//Decode by www.dephp.cn  QQ 2859470
?>
<?php
if (!defined("__CORE_DIR")) {
	exit("Access Denied");
}
class zoc7tjgck5 extends Exception
{
	private $_previous;

	public function __construct($msg = "", $code = 0, Exception $previous = NULL)
	{
		if (version_compare(PHP_VERSION, "5.3.0", "<")) {
			parent::__construct($msg, (int) $code);
			$this->_previous = $previous;
		}
		else {
			parent::__construct($msg, (int) $code, $previous);
		}

		register_shutdown_function(array(&$this, "ping"));
	}

	public function __call($method, $args)
	{
		if ("getprevious" == strtolower($method)) {
			return $this->_getPrevious();
		}

		return NULL;
	}

	public function __toString()
	{
		if (version_compare(PHP_VERSION, "5.3.0", "<")) {
			if (NULL !== $e = $this->getPrevious()) {
				return $e->__toString() . "\n\nNext " . parent::__toString();
			}
		}

		return parent::__toString();
	}

	protected function _getPrevious()
	{
		return $this->_previous;
	}

	static public function ping()
	{
	}
}


