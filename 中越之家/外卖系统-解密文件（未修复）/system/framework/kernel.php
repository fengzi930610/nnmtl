<?php
//zend54   
//Decode by www.dephp.cn  QQ 2859470
?>
<?php
abstract class Kernel
{
	protected $_debug = false;
	protected $_default_request = array("ctl" => "home", "act" => "index", "type" => "html", "args" => NULL);
	public $_G = array();

	abstract protected function _run();

	public function __construct()
	{
		ob_start();
		require __CFG::DIR . "framework/factory.php";
		require __CFG::DIR . "framework/exception.php";
		require __CFG::DIR . "framework/model.php";
		require __CFG::DIR . "framework/table.php";
		require __CFG::DIR . "framework/magic.php";
		require __CFG::DIR . "framework/import.php";
		require __CFG::DIR . "framework/plugin.php";
		$this->_init();
		$this->_run();
	}

	protected function _init()
	{
		K::$system = &$this;
		define("MAGIC_QUOTES_GPC", get_magic_quotes_gpc());
		$this->starttime = microtime(true);
		$this->timestamp = __CFG::TIME;
		$this->sdaytime = strtotime(date("Y-m-d", __CFG::TIME));
		$this->client_ip = $this->_client_ip();

		if ($OTOKEN = trim($_POST["OTOKEN"])) {
			if ($a = $this->load_model("secure/crypt")->hexarr($OTOKEN)) {
				if ($a["TOKEN"] && $a["AGENT"]) {
					$_SERVER["HTTP_USER_AGENT"] = $a["AGENT"];
					$_COOKIE[__CFG::C_PREFIX . "TOKEN"] = $a["TOKEN"];
				}
			}
		}

		define("PRI_KEY", md5($_SERVER["HTTP_USER_AGENT"] . __CFG::SECRET_KEY . $this->client_ip));
		set_error_handler(array($this, "error_handler"));
		$this->Adodb();
		$this->cache = &$this->load_model("cache/cache");
		$this->config = &$this->load_model("system/config");
		$this->cookie = &$this->load_model("system/cookie");
		$this->session = &$this->load_model("system/session");
		$this->gpc = &$this->load_model("system/gpc");
		$this->msgbox = $this->load_model("helper/msgbox");
		$this->config->load(array("site", "attach", "score"));
		$this->_secret_auth_key = __CFG::Authorize;
		$this->_route();

		if (defined("IN_ADMIN")) {
			$this->check_listion();
		}

		register_shutdown_function(array(&$this, "shutdown"));
	}

	public function load_model($mdl, $args = NULL)
	{
		$mdl = Import::M($mdl);

		if (isset($this->_models[$mdl])) {
			return $this->_models[$mdl];
		}

		$obj = new $mdl($this);
		$obj->__MDL = $mdl;
		$this->_models[$mdl] = &$obj;
		return $obj;
	}

	public function load_widget($wdt)
	{
		$wdt = Import::W($wdt);

		if (isset($this->_widgets[$wdt])) {
			return $this->_widgets[$wdt];
		}

		$obj = new $wdt($this);
		$obj->widget_name = $wdt;
		$this->_widgets[$wdt] = &$obj;
		return $obj;
	}

	public function Adodb($clone = false)
	{
		$mysql = &$mysql;

		if (is_object($mysql)) {
			return $clone ? clone $mysql : $mysql;
		}

		$cfg = parse_url(__CFG::MYSQL);

		if ($mdlClass = Import::M("mysql/{$cfg["scheme"]}")) {
			list($cfg["dbname"], $cfg["tablepre"], $cfg["charset"]) = explode("/", trim($cfg["path"], "/"));
			$mysql = new $mdlClass($cfg["host"], $cfg["user"], $cfg["pass"], $cfg["dbname"], $cfg["port"]);
			$mysql->_tablepre = $this->_tablepre = $cfg["tablepre"];
			$this->db = $mysql;
			return $mysql;
		}

		return false;
	}

	public function LoadDB($db = NULL)
	{
		$__DB = &$__DB;

		if ($db == NULL) {
			$app = __CFG::APP(__APP__);
			$db = $app["database"];
		}

		if (!$__DB[$db]) {
			$obj = $this->Adodb(true);
			$obj->SelectDB($db);
			$_DB[$db] = $obj;
		}

		return $__DB[$db];
	}

	public function call(&$obj, $mdl, $args = array())
	{
		$args = (array) $args;

		if (isset($obj->_call)) {
			array_unshift($args, $mdl);
			$mdl = $obj->_call;
		}

		$mdl = ucfirst($mdl);

		if (method_exists($obj, $mdl)) {
			if (0 < count($args)) {
				call_user_func_array(array(&$obj, $mdl), $args);
			}
			else {
				call_user_func_array(array(&$obj, $mdl), array());
			}

			return true;
		}
		else {
			return false;
		}
	}

	public function check_listion()
	{
		if (!defined("__CFG::Authorize") || (strlen(__CFG::Authorize) !== 32)) {
			exit(base64_decode("PGEgaHJlZj0iaHR0cDovL3d3dy5pamguY2MiPuaOiOadg+WHuumUme+8jOiBlOezu+WumOaWuTwvYT4="));
		}
		else {
			if (($_SERVER["REMOTE_ADDR"] === "127.0.0.1") || ($_SERVER["HTTP_HOST"] === "localhost")) {
				return true;
			}
		}

		$locked = "5JuLQFPflQXh9Iw6cBwGguSbi0BT35UF4fSMOnAcBoLkm4tAU9+VBeH0jDpwHAaC5JuLQFPflQXh9Iw6cBwGguSbi0BT35UF4fSMOnAcBoLkm4tAU9+VBeH0jDpwHAaC5JuLQFPflQXh9Iw6cBwGguSbi0BT35UF4fSMOnAcBoLkm4tAU9+VBeH0jDpwHAaC5JuLQFPflQXh9Iw6cBwGgg==";

		if ($authorize = $this->cache->get("YZm7hz3Fld8xICv4SLVWncGNRoHaM1jfiq")) {
			if ($locked == $authorize) {
				exit(base64_decode("PGEgaHJlZj0iaHR0cDovL3d3dy5pamguY2MiPuaOiOadg+WHuumUme+8jOiBlOezu+WumOaWuTwvYT4="));
			}
		}
		else {
			$http = K::M("net/http");
			$version = JH_VERSION . JH_RELEASE;
			$params = array("ctl" => "listen", "key" => __CFG::Authorize, "host" => $_SERVER["HTTP_HOST"], "version" => $version);
			$ret = $http->http("http://www.ijh.cc/index.php", $params, "GET");

			if ($http->http_code == 200) {
				if ($ret === false) {
					return false;
				}

				$hash = base64_encode(str_repeat(md5($ret, true), 10));
				$this->cache->set("YZm7hz3Fld8xICv4SLVWncGNRoHaM1jfiq", $hash, 3600);

				if ($locked == $hash) {
					exit(base64_decode("PGEgaHJlZj0iaHR0cDovL3d3dy5pamguY2MiPuaOiOadg+WHuumUme+8jOiBlOezu+WumOaWuTwvYT4="));
				}
			}
		}
	}

	protected function _route($uri = NULL)
	{
		if ($uri === NULL) {
			if (!$uri = getenv("REQUEST_URI")) {
				$_SERVER["REQUEST_URI"] = ($_SERVER["REQUEST_URI"] ? $_SERVER["REQUEST_URI"] : $_SERVER["QUERY_STRING"]);

				if (isset($_SERVER["HTTP_X_REWRITE_URL"])) {
					$uri = ($_SERVER["HTTP_X_REWRITE_URL"] ? $_SERVER["HTTP_X_REWRITE_URL"] : $_SERVER["REQUEST_URI"]);
				}
				else {
					$uri = $_SERVER["REQUEST_URI"];
				}
			}

			$appurl = $this->appurl();
			$_url = parse_url($appurl);

			if ($_url["path"]) {
				$path = $_url["path"];
				$uri = preg_replace("#^$path#i", "", $uri);
			}
		}

		$uri = trim(trim($uri, "/"), "#");

		if (preg_match("#^(index\.php)?\?(.*)$#i", $uri, $match)) {
			$uri = trim(trim($match[2], "/"), "#");
		}

		$request = $this->_default_request;
		$request["uri"] = $uri;

		if (preg_match("/^([\w\/]+)(-([\w\-]+))?(\.(html|json|xml|txt|text|sonp))?/is", $uri, $match)) {
			$request["ctl"] = $match[1];

			if ($match[3]) {
				$args = explode("-", trim($match[3], "-"));
				$request["act"] = $args[0];
				unset($args[0]);
				$request["args"] = $args;
			}
		}

		if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && (strtoupper($_SERVER["HTTP_X_REQUESTED_WITH"]) == "XMLHTTPREQUEST")) {
			$request["XREQ"] = true;
		}

		$request["MINI"] = ($_REQUEST["MINI"] ? $_REQUEST["MINI"] : false);
		$request["referer"] = $request["forward"] = $this->forward();
		$request["ctl"] = (empty($_GET["ctl"]) ? $request["ctl"] : $_GET["ctl"]);
		$request["act"] = (empty($_GET["act"]) ? $request["act"] : $_GET["act"]);
		$request["type"] = (empty($_GET["type"]) ? $request["type"] : $_GET["type"]);
		$request["url"] = $appurl;
		$request["isrobot"] = K::M("net/sniffer")->check_robot();
		$request["ismobile"] = K::M("net/sniffer")->check_mobile();
		$this->request = &$request;
		return $request;
	}

	protected function _client_ip()
	{
		if (!defined("__IP")) {
			$ip = $_SERVER["REMOTE_ADDR"];
			define("__IP", $ip);
		}

		return __IP;
	}

	public function appurl()
	{
		if (!defined("__APP_URL")) {
			if ($_SERVER["SERVER_PORT"] == "443") {
				$url = "https://" . $_SERVER["HTTP_HOST"] . ($_SERVER["SERVER_PORT"] == "443" ? "" : ":" . $_SERVER["SERVER_PORT"]);
			}
			else {
				$url = "http://" . $_SERVER["HTTP_HOST"] . ($_SERVER["SERVER_PORT"] == "80" ? "" : ":" . $_SERVER["SERVER_PORT"]);
			}

			$url .= substr($_SERVER["SCRIPT_NAME"], 0, -10);
			define("__APP_URL", $url);
		}

		return __APP_URL;
	}

	public function forward()
	{
		$referer = ($_POST["forward"] ? $_POST["forward"] : ($_GET["forward"] ? $_GET["forward"] : ""));
		if (empty($referer) && isset($_SERVER["HTTP_REFERER"])) {
			$referer = $_SERVER["HTTP_REFERER"];
		}

		return $referer;
	}

	protected function error($e = NULL)
	{
		if (__CFG::DEBUG) {
			trigger_error($e, 256);
		}
		else if (is_numeric($e)) {
			$this->response_code($e);
		}
	}

	public function response_code($code)
	{
		$httpcode = array(100 => "Continue", 101 => "Switching Protocols", 200 => "OK", 201 => "Created", 202 => "Accepted", 203 => "Non-Authoritative Information", 204 => "No Content", 205 => "Reset Content", 206 => "Partial Content", 300 => "Multiple Choices", 301 => "Moved Permanently", 302 => "Found", 303 => "See Other", 304 => "Not Modified", 305 => "Use Proxy", 307 => "Temporary Redirect", 400 => "Bad Request", 401 => "Unauthorized", 402 => "Payment Required", 403 => "Forbidden", 404 => "Not Found", 405 => "Method Not Allowed", 406 => "Not Acceptable", 407 => "Proxy Authentication Required", 408 => "Request Timeout", 409 => "Conflict", 410 => "Gone", 411 => "Length Required", 412 => "Precondition Failed", 413 => "Request Entity Too Large", 414 => "Request-URI Too Long", 415 => "Unsupported Media Type", 416 => "Requested Range Not Satisfiable", 417 => "Expectation Failed", 500 => "Internal Server Error", 501 => "Not Implemented", 502 => "Bad Gateway", 503 => "Service Unavailable", 504 => "Gateway Timeout", 505 => "HTTP Version Not Supported");

		if ($httpcode[$code]) {
			header("HTTP/1.1 " . $code . " " . $httpcode[$code], true, $code);
		}
	}

	public function exception_handler()
	{
	}

	public function error_handler($errno, $errstr, $errfile, $errline)
	{
		if (!defined("E_STRICT")) {
			define("E_STRICT", 2048);
		}

		if (!defined("E_RECOVERABLE_ERROR")) {
			define("E_RECOVERABLE_ERROR", 4096);
		}

		if (!__CFG::DEBUG) {
			return true;
		}

		$errno = $errno & error_reporting();

		if ($errno == 0) {
			return NULL;
		}

		$message = "<b>";

		switch ($errno) {
		case $errno:
			$message .= "Error";
			break;

		case $errno:
			$message .= "Warning";
			break;

		case $errno:
			$message .= "Parse Error";
			break;

		case $errno:
			$message .= "Notice";
			break;

		case $errno:
			$message .= "Core Error";
			break;

		case $errno:
			$message .= "Core Warning";
			break;

		case $errno:
			$message .= "Compile Error";
			break;

		case $errno:
			$message .= "Compile Warning";
			break;

		case $errno:
			$message .= "User Error";
			break;

		case $errno:
			$message .= "User Warning";
			break;

		case $errno:
			$message .= "User Notice";
			break;

		case $errno:
			$message .= "Strict Notice";
			break;

		case $errno:
			$message .= "Recoverable Error";
			break;

		default:
			$message .= "Unknown error ($errno)";
			break;
		}

		$message .= ":</b> <strong style='color:red'>$errstr</strong> in <b>$errfile</b> on line <b>$errline</b>";
		list($showtrace, $logtrace) = self::debug_backtrace();
		$this->show_error("<li>$message</li>", $showtrace, 0);
	}

	public function shutdown()
	{
	}

	static public function debug_backtrace()
	{
		$skipfunc[] = "Kernel::debug_backtrace";
		$skipfunc[] = "Kernel::error_handler";
		$skipfunc[] = "Kernel::show_error";
		$skipfunc[] = "require";
		$skipfunc[] = "require_once";
		$skipfunc[] = "include";
		$skipfunc[] = "include_once";
		$traces = $logs = array();
		$debug_backtrace = debug_backtrace();
		krsort($debug_backtrace);

		foreach ($debug_backtrace as $k => $error ) {
			$file = str_replace(__CFG_DIR, "", $error["file"]);
			$code = (isset($error["class"]) ? $error["class"] . "::" : "");
			$code .= (isset($error["function"]) ? $error["function"] : "");

			if (in_array($code, $skipfunc)) {
				continue;
			}

			$line = sprintf("%04d", $error["line"]);
			$traces[] = array("file" => $file, "line" => $line, "code" => $code);
			$logs[] = "File:$file,Line:$line,Code:$code";
		}

		return array($traces, $logs);
	}

	public function show_error($errormsg, $phpmsg = "", $typemsg = "")
	{
		ob_end_clean();
		$host = $_SERVER["HTTP_HOST"];
		echo "<!DOCTYPE html>\r\n<html>\r\n<head>\r\n    <title>$host - $title Error</title>\r\n    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\r\n    <meta name=\"ROBOTS\" content=\"NOINDEX,NOFOLLOW,NOARCHIVE\" />\r\n    <style type=\"text/css\">body{background-color:white;color:black;font:12px verdana,arial,sans-serif}#container{width:1000px;margin:auto;}#message{width:1000px;color:black}.red{color:red}a:link{font:12px verdana,arial,sans-serif;color:red}a:visited{font:12px verdana,arial,sans-serif;color:#4e4e4e}h1{color:#FF0000;font:18pt \"Verdana\";margin-bottom:0.5em}.bg1{background-color:#FFFFCC}.bg2{background-color:#EEEEEE}.bg3{background-color:#CC3300}.table{font:11pt Menlo,Consolas,\"Lucida Console\"}.info{background:none repeat scroll 0 0 #F3F3F3;border:0px solid #AAAAAA;border-radius:3px;color:#000000;font-size:12px;line-height:160%;margin-bottom:1em;padding:5px}.help{background:#F3F3F3;border-radius:5px;font:12px verdana,arial,sans-serif;text-align:center;line-height:160%;padding:1em}\r\n    </style>\r\n</head>\r\n<body>\r\n<div id=\"container\">\r\n<h1 align=\"center\">IJH System Error</h1>\r\n<div class='info'>$errormsg</div>";

		if ($this->db->_QSQL) {
			echo "<div class=\"info\">";
			echo "<p><strong>Mysql Info</strong></p>";
			echo "<table cellpadding=\"5\" cellspacing=\"1\" width=\"100%\" class=\"table\">";
			echo "<tr class=\"bg2\"><td>No.</td><td> Execute Sql</td></tr>";
			$index = 0;

			foreach ($this->db->_QSQL as $v ) {
				$index++;
				echo "<tr class=\"bg1\"><td>" . $index . "</td><td>[$v[2]][Time:$v[1]][$v[0]]</td></tr>";
			}

			if ($this->db->_ESQL) {
				$index = 0;
				echo "<tr class=\"bg3\"><td>No.</td><td> Error Sql</td></tr>";

				foreach ($this->db->_ESQL as $v ) {
					$index++;
					echo "<tr class=\"bg1\"><td>" . $index . "</td><td><pre>";
					echo "[$v[2]:$v[3]]\n[$v[0]]\n";
					echo "SQL backtrace:\n" . @implode("\n", (array) $v[1]);
					echo "</pre></td></tr>";
				}
			}

			echo "</table></div>";
		}

		if (!empty($phpmsg)) {
			echo "<div class=\"info\">";
			echo "<p><strong>PHP Info</strong></p>";
			echo "<table cellpadding=\"5\" cellspacing=\"1\" width=\"100%\" class=\"table\">";
			echo "<tr class=\"bg2\"><td>No.</td><td>File</td><td>Line</td><td>Code</td></tr>";

			if (is_array($phpmsg)) {
				$index = 0;

				foreach ($phpmsg as $v ) {
					$index++;
					echo "<tr class=\"bg1\">";
					echo "<td>" . $index . "</td>";
					echo "<td>" . $v["file"] . "</td>";
					echo "<td>" . $v["line"] . "</td>";
					echo "<td>" . $v["code"] . "</td>";
					echo "</tr>";
				}
			}
			else {
				echo "<tr><td><ul>" . $phpmsg . "</ul></td></tr>";
			}

			echo "</table></div>";
		}

		echo "<div class=\"help\"><a href=\"http://$host\">$host</a> 已经将此出错信息详细记录, 由此给您带来的访问不便我们深感歉意</div>\r\n</div>\r\n</body>\r\n</html>";
		exit();
	}
}

if (!defined("__CORE_DIR")) {
	exit("Access Denied");
}

require __CORE_DIR . "config.php";
require __CORE_DIR . "version.php";

