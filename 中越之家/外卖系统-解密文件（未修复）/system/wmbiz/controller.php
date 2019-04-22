<?php
//zend54   
//Decode by www.dephp.cn  QQ 2859470
?>
<?php
if (!defined("__CORE_DIR")) {
	exit("Access Denied");
}
class Ctl extends Factory
{
	protected $_allow_fields = "";

	public function __construct(&$system)
	{
		parent::__construct($system);
		$this->cookie = $system->cookie;
		$this->system->objctl = &$this;
		$this->msgbox->template("page/notice.html");
		$this->system->auth = K::M("shop/auth");
		$this->auth = $this->system->auth;

		if ($this->auth->token()) {
			if (!$shop = K::M("shop/shop")->detail($this->auth->shop_id)) {
			}
			else {
				$this->shop_id = $this->auth->shop_id;
				$this->shop = $this->auth->shop;
				$this->waimai_shop = K::M("waimai/waimai")->detail($this->shop_id);
			}
		}
		else if (!in_array($this->request["ctl"], array("login", "page", "webview/index:index"))) {
			header("Location:" . $this->mklink("login"));
			exit();
		}
	}

	protected function InitializeApp()
	{
	}

	protected function _init_pagedata()
	{
		$CONFIG = $this->system->config->load(array("site"));
		$site = $CONFIG["site"];
		parent::_init_pagedata();
		$theme = $this->default_theme();
		$this->pagedata["shop"] = $this->shop;
		$this->pagedata["waimai_shop"] = $this->waimai_shop;
		$this->pagedata["pager"]["url"] = $site["url"];
		$this->pagedata["pager"]["res"] = __CFG::RES_URL;
		$this->pagedata["pager"]["theme"] = $site["siteurl"] . "/themes";
		$this->pagedata["nowtime"] = $this->pagedata["pager"]["dateline"] = __TIME;
		$this->pagedata["VER"] = JH_RELEASE;
		$this->pagedata["site"] = $site;
		$this->pagedata["jh_map_key"] = MAP_KEY;
		$this->pagedata["jh_server_key"] = MAP_SERVER_KEY;
		if (defined("IN_APP_CLIENT") && (IN_APP_CLIENT == "WINDOW")) {
			$this->pagedata["in_window"] = 1;
		}
		else {
			$this->pagedata["in_window"] = 0;
		}

		$this->pagedata["have_qiang"] = (defined("HAVE_QIANG") ? HAVE_QIANG : false);
		$output = K::M("system/frontend");
		$output->setCompileDir(__CFG::DIR . "data/tplwmbiz");
	}

	public function check_fields($data, $fields = NULL)
	{
		if ($fields === NULL) {
			$fields = $this->_allow_fields;
		}

		if (!is_array($fields)) {
			$fields = explode(",", $fields);
		}

		foreach ((array) $data as $k => $v ) {
			if (!in_array($k, $fields)) {
				unset($data[$k]);
			}
		}

		return $data;
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

	public function check_login()
	{
		if (!$this->shop_id) {
			if ($this->request["XREQ"] || $this->request["MINI"]) {
				$this->msgbox->add("很抱歉，你还没有登录不能访问", 101);
			}
			else {
				$this->tmpl = "login/index.html";
			}

			$this->msgbox->response();
			exit();
		}

		return true;
	}

	protected function set_resource_view(&$output)
	{
		$theme = $this->default_theme();
		$output->setTemplateDir(__CFG::TMPL_DIR . "wmbiz");
		$output->registerFilter("pre", array($this, "smarty_pre_filter"));
		$output->registerFilter("post", array($this, "smarty_post_filter"));
	}

	public function smarty_pre_filter($source, $smarty)
	{
		$s = array("/(<\{KT[^\}]*\}>)/", "/(<\{\/KT\}>)/", "/(<\{AD[^\}]*\}>)/", "/(<\{\/AD\}>)/", "/(<\{calldata[^\}]*\}>)/", "/(<\{\/calldata\}>)/");
		$r = array("\1<{literal}>", "<{/literal}>\1", "\1<{literal}>", "<{/literal}>\1", "\1<{literal}>", "<{/literal}>\1");
		return preg_replace($s, $r, $source);
	}

	public function smarty_post_filter($source, $smarty)
	{
		if ($file_dependency = $smarty->properties["file_dependency"]) {
			list($hash, $info) = each($file_dependency);
			$tmpl = $smarty->template_resource;

			if ($info[2] == "file") {
				$theme = substr($info[0], strlen(__CFG::TMPL_DIR), -strlen($tmpl));
				$theme = str_replace("\\", "/", $theme);
				$theme = str_replace("/", "", $theme);
				$site = $this->system->config->get("site");
				$theme_url = trim($site["url"], "/") . "/themes/" . $theme;
				return preg_replace("/%THEME%/", $theme_url, $source);
			}
		}

		return $source;
	}

	public function theme_default_handler($type, $name, &$content, &$modified, Smarty $smarty)
	{
		if ($type == "file") {
			$file = __CFG::TMPL_DIR . "wmbiz" . DIRECTORY_SEPARATOR . $name;
			return $file;
		}

		return false;
	}

	public function error($error)
	{
		if (is_numeric($error)) {
			$this->system->response_code($error);
		}

		$this->tmpl = "page/$error.html";
		$this->output();
	}

	public function shutdown()
	{
	}

	protected function default_theme()
	{
		$theme = &$theme;

		if ($theme === NULL) {
			if (empty($theme)) {
				$theme = K::M("system/theme")->default_theme();
			}
		}

		return $theme;
	}

	protected function wechat_client()
	{
		$client = &$client;

		if ($client === NULL) {
			$client = K::M("weixin/wechat")->admin_wechat_client();
		}

		return $client;
	}

	public function mklink($ctl, $args = array(), $extname = ".html", $params = array())
	{
		return K::M("helper/link")->mklink("$ctl", $args, $params, "wmbiz", true, $extname);
	}
}


