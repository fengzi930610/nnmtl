<?php
//zend54   
//Decode by www.dephp.cn  QQ 2859470
?>
<?php
if (!defined("__CORE_DIR")) {
	exit("Access Denied");
}
class CTL extends Factory
{
	public $MOD = array();

	public function __construct(&$system)
	{
		parent::__construct($system);
		$this->cookie = $system->cookie;
		$this->InitializeApp();
		register_shutdown_function(array(&$this, "shutdown"));
		register_shutdown_function(array(&$this, "logs"));
	}

	protected function InitializeApp()
	{
		$this->msgbox->template("admin:page/notice.html");
		$this->system->objctl = &$this;
		$this->pagedata["module_config"] = __CFG::$MODULE;
		$cates = K::M("waimai/cate")->items(array("parent_id" => 0));
		$cate_ids = array();

		foreach ($cates as $k => $v ) {
			$cate_ids[$v["cate_id"]] = $v["cate_id"];
		}

		$cates2 = K::M("waimai/cate")->items(array("parent_id" => $cate_ids));

		foreach ($cates as $k => $v ) {
			foreach ($cates2 as $k1 => $v1 ) {
				if ($v["cate_id"] == $v1["parent_id"]) {
					$cates[$k]["son"][] = $v1;
				}
			}
		}

		$this->pagedata["cates"] = $cates;
		$this->pagedata["order_voice"] = K::M("waimai/config")->getordervoice();
		$this->admin = &$this->system->admin;

		if (!$this->check_priv()) {
			$this->msgbox->add("您没有权限操作", -1);
			$this->msgbox->response();
		}
	}

	protected function _init_pagedata()
	{
		parent::_init_pagedata();
		$this->pagedata["MOD"] = $this->MOD;
		$this->pagedata["ADMIN"] = $this->admin->admin;
		$this->pagedata["OATOKEN"] = $this->system->OATOKEN;
		$this->pagedata["pager"]["url"] = __APP_URL;
		$this->pagedata["pager"]["res"] = __CFG::RES_URL;
		$this->pagedata["jh_map_key"] = MAP_KEY;
		$this->pagedata["jh_server_key"] = MAP_SERVER_KEY;
		$this->pagedata["request"] = $this->request;
		$output = K::M("system/frontend");
		$this->pagedata["have_wxapp"] = (defined("HAVE_WX_APP") ? HAVE_WX_APP : false);
		$this->pagedata["have_paotui"] = (defined("HAVE_PAOTUI") ? HAVE_PAOTUI : false);
		$this->pagedata["have_pei"] = (defined("HAVE_PEI") ? HAVE_PEI : false);
		$this->pagedata["have_tongcheng"] = (defined("HAVE_TONGCHENG") ? HAVE_TONGCHENG : false);
		$this->pagedata["have_qiang"] = (defined("HAVE_QIANG") ? HAVE_QIANG : false);
		$this->pagedata["have_jifen"] = (defined("HAVE_JIFEN") ? HAVE_JIFEN : false);
		$this->pagedata["have_ditui"] = (defined("HAVE_DITUI") ? HAVE_DITUI : false);
		$this->pagedata["have_pwxapp"] = (defined("HAVE_PWXAPP") ? HAVE_PWXAPP : false);
		$output->setCompileDir(__CFG::DIR . "data/tpladmin");
	}

	protected function check_priv($ctl = NULL, $act = NULL)
	{
		$ctl = ($ctl ? $ctl : $this->request["ctl"]);
		$act = ($act ? $act : $this->request["act"]);

		if ($ctl == "index") {
			$this->MOD = array("mod_id" => 0, "module" => "module", "ctl" => $ctl, "act" => "act", "title" => "通用控制器");
			return true;
		}
		else if ($this->MOD = K::M("module/view")->ctlmap($ctl, $act)) {
			if ($this->admin->check_priv($this->MOD["mod_id"])) {
				return true;
			}
		}

		return false;
	}

	protected function logs($title = "", $data = array())
	{
		if (defined("__SYSTEM_LOGS") && __SYSTEM_LOGS) {
			$admin_id = $this->admin->admin_id;
			$admin_name = $this->admin->admin_name;
			$action = $this->request["ctl"] . ":" . $this->request["act"];
			$title = ($title ? $title : $this->MOD["title"]);
			$data = ($data ? $data : $this->request["uri"]);
			$admin = $this->admin;
			return K::M("magic/logs")->write($admin_id, $admin_name, $action, "管理日志", $title, $data);
		}
		else {
			return false;
		}
	}

	public function city_id($city_id)
	{
		if (CITY_ID && (CITY_ID != $city_id)) {
			$city_id = CITY_ID;
		}

		return $city_id;
	}

	public function check_city($city_id)
	{
		return true;
	}

	public function verify_city($city_id)
	{
		if (CITY_ID && (CITY_ID != $city_id)) {
			$this->msgbox->add("不可越权操作", 403)->response();
		}

		return true;
	}

	protected function filter_fields($fields, $row)
	{
		if (!is_array($fields)) {
			$fields = explode(",", $fields);
		}

		foreach ((array) $row as $k => $v ) {
			if (!in_array($k, $fields)) {
				unset($row[$k]);
			}
		}

		return $row;
	}

	public function shutdown()
	{
		return false;

		if ($this->MOD["syslog"]) {
			$admin_id = $this->admin->admin_id;
			$admin_name = $this->admin->admin_name;
			$action = $this->MOD["ctl"] . ":" . $this->MOD["act"];
			$title = $this->MOD["title"];
			$data = array();
			$data["reqest"] = $this->request;
			K::M("magic/logs")->write($admin_id, $admin_name, $action, "系统日志", $title, $data);
		}
	}
}


