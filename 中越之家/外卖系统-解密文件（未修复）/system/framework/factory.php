<?php
//zend54   
//Decode by www.dephp.cn  QQ 2859470
?>
<?php
class Factory
{
	public $tmpl;

	public function __construct(&$system)
	{
		$this->system = &$system;
		$this->gpc = &$system->gpc;
		$this->msgbox = &$system->msgbox;
		$this->auth = &$system->auth;
		$this->uid = &$system->auth->uid;
		$this->uname = &$system->auth->uname;
		$this->MEMBER = &$system->auth->member;
		$this->request = &$system->request;
		$this->cache = &$system->cache;
	}

	public function output($o = false)
	{
		ob_clean();
		$actType = strtolower($this->action["type"]);

		switch ($actType) {
		case $actType:
			$this->contentType = "text/plain";
			echo K::M("utility/json")->encode($this->pagedata);
			break;

		case $actType:
			$this->contentType = "text/xml";
			$mdlXml = K::M("utility/xml");
			echo $mdlXml->ToXml($this->pagedata);
			break;

		case $actType:
		case $actType:
			$this->contentType = "text/plain";
			break;

		case $actType:
		default:
			$this->contentType = "text/html";
			$this->_init_pagedata();
			$this->system->smarty_start_time = microtime(true);
			$output = K::M("system/frontend");

			if (isset($this->default_resource_type)) {
				$output->default_resource_type = $this->default_resource_type;
			}

			$this->set_resource_view($output);

			foreach ($this->pagedata as $key => $val ) {
				$output->assign($key, $val);
			}

			if (!$tmpl = $this->tmpl) {
				$tmpl = $this->pagedata["_OO_"];
			}

			if ($o) {
				$content = $output->fetch($tmpl);
				$content = preg_replace("/<!--.+?-->/is", "", $content);
				return preg_replace("/\s+/s", " ", $content);
			}

			$output->display($tmpl);
			$this->system->smarty_execute_time = microtime(true) - $this->system->smarty_start_time;
			break;
		}

		exit();
	}

	protected function set_resource_view(&$output)
	{
		return true;
	}

	protected function _init_pagedata()
	{
		if (isset($this->pagedata["header"])) {
			foreach ($this->pagedata["header"] as $key => $val ) {
				$this->_header[$key] = $val;
			}
		}

		unset($this->pagedata["header"]);
		$this->system->config->load(array("site", "bulletin", "attach"));
		$this->pagedata["CONFIG"] = Mdl_System_Config::$_CFG;
		$this->pagedata["COOKIE"] = $this->system->cookie->fetch_all();
		$this->pagedata["request"] = $this->system->request;
		$this->pagedata["pager"]["res"] = __CFG::RES_URL;
		$this->pagedata["pager"]["img"] = Mdl_System_Config::$_CFG["attach"]["attachurl"];
		$this->pagedata["pager"]["dateline"] = __CFG::TIME;
		$this->pagedata["OTOKEN"] = $this->system->OTOKEN;
		$this->pagedata["TMPLVER"] = K::M("cache/cache")->version();
	}

	protected function G($k)
	{
		return $this->gpc->get($k, "g");
	}

	protected function P($k)
	{
		return $this->gpc->get($k, "p");
	}

	protected function GP($k)
	{
		return $this->gpc->get($k, "gp");
	}

	protected function response_code($code)
	{
		$this->system->response_code($code);
	}

	public function error($code)
	{
		if (is_numeric($code)) {
			$this->response_code($code);
			exit();
		}
	}

	public function checksubmit($submit = NULL)
	{
		if (strtoupper($_SERVER["REQUEST_METHOD"]) === "POST") {
			if ($submit === NULL) {
				return true;
			}

			return $this->GP($submit);
		}

		return false;
	}

	public function response()
	{
		$request = $this->request;

		if (!$tmpl = $this->tmpl) {
			$tmpl = $this->pagedata["_OO_"];
		}

		if ($request["XREQ"]) {
			if ($tmpl) {
				$this->msgbox->set_data("html", $this->output(false));
			}

			$this->msgbox->show("", "JSON");
		}
		else if ($tmpl) {
			$this->output();
		}
		else if ($url) {
			$this->msgbox->show($url, "HTML");
		}
		else {
			$this->msgbox->show($request["referer"], "HTML");
		}
	}

	public function mklink($ctl = NULL, $args = array(), $params = array(), $http = NULL, $rewrite = true)
	{
		if (empty($ctl)) {
			$ctl = $this->request["ctl"] . ":" . $this->request["act"];
		}

		if ($http === NULL) {
			if (defined("IN_ADMIN") || defined("IN_FENZHAN")) {
				$http = false;
			}
			else {
				$http = true;
			}
		}

		return K::M("helper/link")->mklink($ctl, $args, $params, $http, $rewrite);
	}

	public function mkpage($count, $limit, $page, $link = NULL, $params = array())
	{
		if (empty($link)) {
			$link = $this->mklink(NULL, array("{page}"));
		}

		return K::M("helper/page")->mkpage($count, $limit, $page, $link, $params);
	}
}

if (!defined("__CORE_DIR")) {
	exit("Access Denied");
}

