<?php
//zend54   
//Decode by www.dephp.cn  QQ 2859470
?>
<?php
class Plugin
{
	public $_ctl;
	public $request;

	public function __construct(&$system)
	{
		$this->system = &$system;
		$this->gpc = &$system->gpc;
	}

	public function output()
	{
		$this->pagedata["_PLUGIN_"] = $this->request["app"] . "/view/" . $this->pagedata["_OO_"];

		if ($this->request["app"]) {
			$this->pagedata["_PLUGIN_"] = "apps/" . $this->pagedata["_PLUGIN_"];
		}

		unset($this->pagedata["_OO_"]);
		$this->_ctl->pagedata = array_merge($this->_ctl->pagedata, $this->pagedata);
		$this->_ctl->Output();
	}
}

if (!defined("__CORE_DIR")) {
	exit("Access Denied");
}

