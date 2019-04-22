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
		$this->system->config->get("mobile");
		$this->system->objctl = &$this;
		$this->msgbox->template("page/notice.html");
		$this->InitializeApp();
		if (defined("IN_WEIXIN") && IN_WEIXIN) {
			$this->wx_openid = $this->check_wxopenid();
			$wxcfg = $this->system->config->get("wechat");
			Import::L("weixin/jssdk.php");
			$wxjssdk = new WeixinJSSDK($wxcfg["appid"], $wxcfg["appsecret"]);
			$this->pagedata["wxjs_config"] = $wxjssdk->getSignPackage();
		}
	}

	protected function InitializeApp()
	{
	}

	protected function _init_pagedata()
	{
		if (defined("IN_WEIXIN")) {
			$CONFIG = $this->system->config->load(array("site", "wechat"));
			$this->pagedata["wechat"] = $CONFIG["wechat"];
		}
		else {
			$CONFIG = $this->system->config->load(array("site"));
		}

		$site = $CONFIG["site"];
		parent::_init_pagedata();
		$theme = $this->default_theme();
		$this->pagedata["MEMBER"] = $this->MEMBER;
		$this->pagedata["city_id"] = $this->system->cookie->get("UxCityId");
		$this->pagedata["pager"]["url"] = $site["url"];
		$this->pagedata["pager"]["res"] = __CFG::RES_URL;
		$this->pagedata["pager"]["C_DOMAIN"] = __CFG::C_DOMAIN;
		$this->pagedata["pager"]["theme"] = $site["siteurl"] . "/themes";
		$this->pagedata["nowtime"] = $this->pagedata["pager"]["dateline"] = __TIME;
		$this->pagedata["VER"] = JH_RELEASE;
		$this->pagedata["site"] = $site;
		$this->pagedata["jh_map_key"] = MAP_KEY;
		$this->pagedata["jh_server_key"] = MAP_SERVER_KEY;
		$output = K::M("system/frontend");
		$output->setCompileDir(__CFG::DIR . "data/tplcache");

		if (defined("IN_WEIXIN")) {
			$wxcfg = $this->system->config->get("wechat");
			Import::L("weixin/jssdk.php");
			$wxjssdk = new WeixinJSSDK($wxcfg["appid"], $wxcfg["appsecret"]);
			$this->pagedata["wxjs_config"] = $wxjssdk->getSignPackage();
			$this->pagedata["wxjs_config_json"] = json_encode($wxjssdk->getSignPackage());
		}

		$this->pagedata["footerNav"] = K::M("adv/themes")->getFooterNav();
		$appdown_cfg = $this->system->config->get("app_download");
		if (($this->request["ctl"] == "index") && ($this->request["act"] == "appdown")) {
			$appdown_cfg["client_download"] = 0;
		}

		$this->pagedata["app_download"] = $appdown_cfg;
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
		if (!$this->uid) {
			if ($this->request["XREQ"] || $this->request["MINI"]) {
				$this->msgbox->add("很抱歉，你还没有登录不能访问", 101);
			}
			else {
				header("Location:" . $this->mklink("passport:login", NULL, NULL, "www"));
				exit();
			}

			$this->msgbox->response();
			exit();
		}

		return true;
	}

	public function check_wxopenid()
	{
		if (!defined("WX_OPENID")) {
			if (!$wx_openid = $this->cookie->get("wx_openid")) {
				$rebackurl = $this->getpageurl();
				header("Location:" . $this->mklink("passport:getwxopenid", NULL, array("rebackurl" => $rebackurl), "www"));
				exit();
			}

			define("WX_OPENID", $wx_openid);
			$wx_unionid = $this->cookie->get("wx_unionid");
			define("WX_UNIONID", $wx_unionid);
		}

		return WX_OPENID;
	}

	protected function set_resource_view(&$output)
	{
		$theme = $this->default_theme();
		$output->setTemplateDir(__CFG::TMPL_DIR);
		$output->registerFilter("pre", array($this, "smarty_pre_filter"));
		$output->registerFilter("post", array($this, "smarty_post_filter"));
		$output->default_template_handler_func = array($this, "theme_default_handler");
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
				$theme_url = trim($site["url"], "/") . "/themes/" . trim($theme, "/");
				return preg_replace("/%THEME%/", "/themes/waimai", $source);
			}
		}

		return $source;
	}

	public function theme_default_handler($type, $name, &$content, &$modified, Smarty $smarty)
	{
		if ($type == "file") {
			$file = __CFG::TMPL_DIR . "waimai" . DIRECTORY_SEPARATOR . $name;
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
			$client = K::M("weixin/wechat")->wechat_client();
		}

		return $client;
	}

	protected function getrebackurl($rebackurl = NULL)
	{
		if (empty($rebackurl)) {
			if (!$rebackurl = $this->GP("rebackurl")) {
				$rebackurl = $this->system->forward();
			}
		}

		if (empty($rebackurl)) {
			$rebackurl = $this->mklink("index", NULL, NULL, "waimai");
		}

		return $rebackurl;
	}

	protected function getpageurl($pageurl = NULL)
	{
		return $this->mklink(NULL, $this->request["args"], NULL, "waimai");
	}
}


