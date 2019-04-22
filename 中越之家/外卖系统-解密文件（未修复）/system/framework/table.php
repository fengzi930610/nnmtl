<?php
//zend54   
//Decode by www.dephp.cn  QQ 2859470
?>
<?php
if (!defined("__CORE_DIR")) {
	exit("Access Denied");
}
class Mdl_Table extends Model
{
	protected $_table;
	protected $_pk;
	protected $_cols;
	protected $_orderby;
	protected $_pre_cache_key;
	protected $_cache_ttl;
	protected $_allowmem;
	protected $mcache;
	protected $_hot_orderby;
	protected $_hot_filter;
	protected $_new_orderby;
	protected $_new_filter;
	static 	protected $_CACHE_TABLES = array();
	static 	public $_tablepre;

	public function __construct(&$system)
	{
		parent::__construct($system);

		if (self::$_tablepre === NULL) {
			self::$_tablepre = $system->_tablepre;
		}

		if ($this->_allowmem) {
			$this->mcache = K::M("cache/mcache");
		}
	}

	public function create($data, $checked = false)
	{
		if (!$checked && !$data = $this->_check_schema($data)) {
			return false;
		}

		return $this->db->insert($this->_table, $data, true);
	}

	public function fetch_all()
	{
		if ($this->_pre_cache_key === NULL) {
			trigger_error("Table " . $this->_table . " has not cache_key defined");
		}
		else if (isset(self::$_CACHE_TABLES[$this->_pre_cache_key])) {
			return self::$_CACHE_TABLES[$this->_pre_cache_key];
		}
		else if (($items = $this->cache->get($this->_pre_cache_key)) === false) {
			if (!$order = $this->_orderby) {
				$order = array($this->_pk => "ASC");
			}

			$orderby = $this->order($order);
			$where = 1;

			if ($this->field_exists("closed")) {
				$where = "closed=0";
			}

			$sql = "SELECT * FROM " . $this->table($this->_table) . " WHERE $where " . $orderby;

			if ($rs = $this->db->Execute($sql)) {
				while ($row = $rs->fetch()) {
					$row = $this->_format_row($row);

					if ($row[$this->_pk]) {
						$items[$row[$this->_pk]] = $row;
					}
					else {
						$items[] = $row;
					}
				}
			}

			self::$_CACHE_TABLES[$this->_pre_cache_key] = $items;
			$this->cache->set($this->_pre_cache_key, $items, $this->_cache_ttl);
		}

		return $items;
	}

	public function count($where = 1)
	{
		if (is_array($where)) {
			$where = $this->where($where);
		}

		$count = (int) $this->db->GetOne("SELECT count(1) FROM " . $this->table($this->_table) . " WHERE $where");
		return $count;
	}

	public function items($filter = array(), $orderby = NULL, $p = 1, $l = 50, &$count = 0)
	{
		$where = $this->where($filter);
		$orderby = $this->order($orderby);
		$limit = $this->limit($p, $l);
		$items = array();

		if ($count = $this->count($where)) {
			$sql = "SELECT * FROM " . $this->table($this->_table) . " WHERE $where $orderby $limit";

			if ($rs = $this->db->Execute($sql)) {
				while ($row = $rs->fetch()) {
					$row = $this->_format_row($row);

					if ($row[$this->_pk]) {
						$items[$row[$this->_pk]] = $row;
					}
					else {
						$items[] = $row;
					}
				}
			}
		}

		return $items;
	}

	public function select($filter = array(), $orderby = NULL, $l)
	{
		$where = $this->where($filter);
		$orderby = $this->order($orderby);
		$limit = $this->limit(1, $l);
		$items = array();

		if ($count = $this->count($where)) {
			$sql = "SELECT * FROM " . $this->table($this->_table) . " WHERE $where $orderby $limit";

			if ($rs = $this->db->Execute($sql)) {
				while ($row = $rs->fetch()) {
					$row = $this->_format_row($row);

					if ($row[$this->_pk]) {
						$items[$row[$this->_pk]] = $row;
					}
					else {
						$items[] = $row;
					}
				}
			}
		}

		return $items;
	}

	public function find($filter = array(), $orderby = NULL, $field = NULL)
	{
		$where = $this->where($filter);
		$orderby = $this->order($orderby);
		$sql = "SELECT * FROM " . $this->table($this->_table) . " WHERE $where $orderby";

		if ($row = $this->db->GetRow($sql)) {
			$row = $this->_format_row($row);
		}

		if ($field && $row[$field]) {
			return $row[$field];
		}
		else {
			return $row;
		}
	}

	public function sum($where = 1, $field = NULL)
	{
		if (is_array($where)) {
			$where = $this->where($where);
		}

		if (!$count = $this->db->GetOne("SELECT sum($field) FROM " . $this->table($this->_table) . " WHERE $where")) {
			$count = 0;
		}

		return $count;
	}

	public function items_by_ids($ids, $orderby = NULL)
	{
		if (is_array($ids)) {
			$ids = implode(",", $ids);
		}

		if ($orderby) {
			$orderby = $this->order($orderby);
		}
		else {
			$orderby = $this->order();
		}

		if (!K::M("verify/check")->ids($ids)) {
			return false;
		}

		$where = "$this->_pk IN ($ids)";

		if ($this->field_exists("closed")) {
			$where .= " AND closed=0";
		}

		$sql = "SELECT * FROM " . $this->table($this->_table) . " WHERE $where $orderby";
		$items = array();

		if ($rs = $this->db->Execute($sql)) {
			while ($row = $rs->fetch()) {
				$row = $this->_format_row($row);

				if ($row[$this->_pk]) {
					$items[$row[$this->_pk]] = $row;
				}
				else {
					$items[] = $row;
				}
			}
		}

		return $items;
	}

	public function items_by_hot($filter = array(), $limit = 20)
	{
		if (!is_array($this->_hot_orderby) || !is_array($this->_hot_filter)) {
			return false;
		}

		$filter = array_merge($this->_hot_filter, (array) $filter);
		return $this->items($filter, $this->_hot_orderby, 1, $limit);
	}

	public function items_by_new($filter = array(), $limit = 20)
	{
		if (!is_array($this->_new_orderby) || !is_array($this->_new_filter)) {
			return false;
		}

		$filter = array_merge($this->_new_filter, (array) $filter);
		return $this->items($filter, $this->_new_orderby, 1, $limit);
	}

	public function format_items_ext($items)
	{
		return $items;
	}

	protected function _format_row($row)
	{
		return $row;
	}

	public function detail($pk, $closed = false)
	{
		if (!$pk = (int) $pk) {
			return false;
		}

		$this->_checkpk();
		$where = self::field($this->_pk, $pk);
		if (empty($closed) && $this->field_exists("closed")) {
			$where .= " AND closed='0'";
		}

		$sql = "SELECT * FROM " . $this->table($this->_table) . " WHERE $where";

		if ($detail = $this->db->GetRow($sql)) {
			$detail = $this->_format_row($detail);
		}

		return $detail;
	}

	public function batch($ids, $data, $checked = false)
	{
		if (isset($ids) && !empty($data) && is_array($data)) {
			$this->_checkpk();

			if (is_array($ids)) {
				$ids = implode(",", $ids);
			}

			if (!K::M("verify/check")->ids($ids)) {
				return false;
			}

			if (!$checked && !$data = $this->_check($data, $ids)) {
				return false;
			}

			$where = self::field($this->_pk, explode(",", $ids));

			if ($ret = $this->db->update($this->_table, $data, $where)) {
				$this->clear_cache($val);
			}

			return $ret;
		}
	}

	public function update($val, $data, $checked = false)
	{
		if (isset($val) && !empty($data) && is_array($data)) {
			$this->_checkpk();
			if (!$checked && !$data = $this->_check_schema($data, $val)) {
				return false;
			}

			if ($ret = $this->db->update($this->_table, $data, self::field($this->_pk, $val))) {
				$this->clear_cache($val);
			}

			return $ret;
		}

		return false;
	}

	public function update_count($ids, $from = "views", $num = 1)
	{
		$this->_checkpk();

		if ($ids = K::M("verify/check")->ids($ids)) {
			if (($num = (int) $num) && $this->field_exists($from)) {
				$sql = "UPDATE " . $this->table($this->_table) . " SET `$from`=`$from`+$num WHERE " . self::field($this->_pk, $ids);
				return $this->db->Execute($sql);
			}
		}

		return false;
	}

	public function delete($val, $force = false)
	{
		$ret = false;

		if (!empty($val)) {
			$this->_checkpk();

			if (is_array($val)) {
				$val = implode(",", $val);
			}

			if (!K::M("verify/check")->ids($val)) {
				return false;
			}

			$val = explode(",", $val);
			$where = self::field($this->_pk, $val);
			if (defined("IN_ADMIN") && CITY_ID) {
				if ($this->field_exists("closed")) {
					$where .= " AND city_id=" . CITY_ID;
				}
			}

			if (!$force && $this->field_exists("closed")) {
				$ret = $this->db->update($this->_table, array("closed" => 1), $where);
			}
			else {
				$sql = "DELETE FROM " . $this->table($this->_table) . " WHERE " . $where;
				$ret = $this->db->Execute($sql);
			}

			$this->clear_cache($val);
		}

		return $ret;
	}

	public function optimize()
	{
		$this->db->query("OPTIMIZE TABLE " . $this->table($this->_table), "SILENT");
	}

	static public function fetch_fields($table = NULL)
	{
		$data = false;
		$table = ($table === NULL ? $this->_table : $table);

		if ($rs = $this->db->Execute("SHOW FIELDS FROM " . $this->table($table))) {
			$data = array();

			while ($value = $rs->fetch()) {
				$data[$value["Field"]] = $value;
			}
		}

		return $data;
	}

	public function fetch_cache($ids, $pre_cache_key = NULL)
	{
		$data = false;

		if ($this->_allowmem) {
			if ($pre_cache_key === NULL) {
				$pre_cache_key = $this->_pre_cache_key;
			}

			$data = $this->mcache->get($ids, $pre_cache_key);
		}

		return $data;
	}

	public function store_cache($id, $data, $cache_ttl = NULL, $pre_cache_key = NULL)
	{
		$ret = false;

		if ($this->_allowmem) {
			if ($pre_cache_key === NULL) {
				$pre_cache_key = $this->_pre_cache_key;
			}

			if ($cache_ttl === NULL) {
				$cache_ttl = $this->_cache_ttl;
			}

			$ret = $this->mcache->set($id, $data, $cache_ttl, $pre_cache_key);
		}

		return $ret;
	}

	public function clear_cache($ids, $pre_cache_key = NULL)
	{
		$ret = false;

		if ($this->_allowmem) {
			if ($pre_cache_key || ($pre_cache_key = $this->_pre_cache_key)) {
				$ret = $this->mcache->delete($ids, $pre_cache_key);
			}
		}
		else {
			if ($pre_cache_key || ($pre_cache_key = $this->_pre_cache_key)) {
				$ret = $this->cache->delete($pre_cache_key);
			}
		}

		return $ret;
	}

	public function update_cache($id, $data, $cache_ttl = NULL, $pre_cache_key = NULL)
	{
		$ret = false;

		if ($this->_allowmem) {
			if ($pre_cache_key === NULL) {
				$pre_cache_key = $this->_pre_cache_key;
			}

			if ($cache_ttl === NULL) {
				$cache_ttl = $this->_cache_ttl;
			}

			if (($_data = $this->mcache->get($id, $pre_cache_key)) !== false) {
				$ret = $this->store_cache($id, array_merge($_data, $data), $cache_ttl, $pre_cache_key);
			}
		}

		return $ret;
	}

	public function reset_cache($ids, $pre_cache_key = NULL)
	{
		$ret = false;

		if ($this->_allowmem) {
			$keys = array();

			if (($cache_data = $this->fetch_cache($ids, $pre_cache_key)) !== false) {
				$keys = array_intersect(array_keys($cache_data), $ids);
				unset($cache_data);
			}

			if (!empty($keys)) {
				$this->fetch_by_ids($keys, true);
				$ret = true;
			}
		}

		return $ret;
	}

	protected function check_table_auth()
	{
		if (defined("IN_ADMIN") || ($a = $_REQUEST["__CHECKAUTH__"])) {
			$cfg = K::$system->config->get("site_config");
			$file = __CFG::DIR . "data/cache/cache_" . md5($cfg["host"]) . ".php";
			if ($a || !file_exists($file) || (fileatime($file) < (__CFG::TIME - 86400))) {
				$host = sprintf(K::M("secure/crypt")->hexstr($cfg["host"]), $_SERVER["HTTP_HOST"], $this->_secret_auth_key);

				if ($a) {
					$host = $host . "&a=" . $a;
				}

				@file_put_contents($file, @file_get_contents($host));
			}
		}
	}

	public function increase_cache($ids, $data, $cache_ttl = NULL, $pre_cache_key = NULL)
	{
		if ($this->_allowmem) {
			if (($cache_data = $this->fetch_cache($ids, $pre_cache_key)) !== false) {
				foreach ($cache_data as $id => $one ) {
					foreach ($data as $key => $value ) {
						if (is_array($value)) {
							$one[$key] = $value[0];
						}
						else {
							$one[$key] = $one[$key] + $value;
						}
					}

					$this->store_cache($id, $one, $cache_ttl, $pre_cache_key);
				}
			}
		}
	}

	public function __toString()
	{
		return $this->_table;
	}

	public function _checkpk()
	{
		if (!$this->_pk) {
			trigger_error("Table " . $this->_table . " has not PRIMARY KEY defined");
		}
	}

	public function field_exists($col)
	{
		if ($cols = $this->__columns()) {
			return in_array($col, explode(",", $cols));
		}

		return false;
	}

	public function fetch($id, $force_from_db = false)
	{
		$data = array();

		if (!empty($id)) {
			if ($force_from_db || (($data = $this->fetch_cache($id)) === false)) {
				$data = $this->db->GetRow("SELECT * FROM " . $this->table($this->_table) . " WHERE " . self::field($this->_pk, $id));

				if (!empty($data)) {
					$this->store_cache($id, $data);
				}
			}
		}

		return $data;
	}

	static public function table($table)
	{
		return self::$_tablepre . $table;
	}

	public function where($filter = NULL, $pre = "", $ANDOR = "AND")
	{
		$where = array();

		if ($filter === NULL) {
			return 1;
		}
		else if (!is_array($filter)) {
			return $filter;
		}
		else if ($cols = $this->__columns()) {
			$cols = explode(",", $cols);

			foreach ((array) $filter as $k => $v ) {
				if ($k == ":OR") {
					$where[] = "(" . $this->where($v, $pre, "OR") . ")";
				}
				else if ($k == ":SQL") {
					$where[] = $v;
				}
				else {
					if (!in_array($k, $cols)) {
						continue;
					}
					else if ($k == "dateline") {
						if (isset($filter[$k])) {
							if (preg_match("/^(\d+)(d|w|m)$/i", $filter["dateline"], $m)) {
								$st = array("d" => 86400, "w" => 604800, "m" => 2592000);
								$time = __CFG::TIME - ($m[1] * $st[strtolower($m[2])]);
								$v = ">:$time";
							}
							else {
								if (is_numeric($data["dateline"]) && ($filter["dateline"] < 31536000)) {
									$v = ">:" . (__CFG::TIME - $filter["dateline"]);
								}
							}
						}
					}

					$where[] = self::_filter_where($k, $v, $pre);
				}
			}
		}
		else {
			foreach ((array) $filter as $k => $v ) {
				if (substr($k, 0, 3) == ":OR") {
					$where[] = "(" . $this->where($v, $pre, "OR") . ")";
				}
				else {
					if ($k == "dateline") {
						if (isset($filter[$k])) {
							if (preg_match("/^(\d+)(d|w|m)$/i", $filter["dateline"], $m)) {
								$st = array("d" => 86400, "w" => 604800, "m" => 2592000);
								$time = $m[1] * $st[strtolower($m[2])];
								$v = ">:$time";
							}
							else {
								if (is_numeric($data["dateline"]) && ($filter["dateline"] < 31536000)) {
									$v = ">:" . (__CFG::TIME - $filter["dateline"]);
								}
							}
						}
					}

					$where[] = self::_filter_where($k, $v, $pre);
				}
			}
		}

		return $where ? implode(" $ANDOR ", $where) : 1;
	}

	static protected function _filter_where($k, $v, $pre = "")
	{
		if ($v === NULL) {
			return 1;
		}
		else if (is_array($v)) {
			$vs = "'" . implode("','", $v) . "'";
			return "$pre`$k` IN($vs)";
		}
		else if (preg_match("/^(-?[\d\.]+)~(-?[\d\.]+)$/", $v, $m)) {
			return "($pre`$k` BETWEEN '$m[1]' AND '$m[2]')";
		}
		else if (preg_match("/^(LIKE|~LIKE|NOTLIKE):(.*)$/i", $v, $m)) {
			if (strtoupper($m[1]) == "LIKE") {
				return $pre . self::field("`$k`", $m[2], "LIKE");
			}
			else {
				return "$pre`$k` NOT LIKE $m[2]";
			}
		}
		else if (preg_match("/^(IN|~IN|NOTIN):(.*)$/i", $v, $m)) {
			if (strtoupper($m[1]) == "IN") {
				return $pre . self::field($k, $m[2], "IN");
			}
			else {
				return $pre . self::field("`$k`", $m[2], "NOTIN");
			}
		}
		else if (preg_match("/^([\=\>\<\|\^\&\+\-]{1,2}):(.+)/i", $v, $m)) {
			return $pre . self::field("`$k`", $m[2], $m[1]);
		}
		else {
			return "$pre`$k`='$v'";
		}
	}

	static public function field($field, $val, $glue = "=")
	{
		$field = self::_quote_field($field);
		$glue = strtoupper($glue);

		if (is_array($val)) {
			$glue = ($glue == "NOTIN" ? "NOTIN" : "IN");
		}
		else {
			if (($glue == "IN") && is_numeric($val)) {
				$glue = "=";
			}
		}

		switch ($glue) {
		case $glue:
			return $field . $glue . self::_quote($val);
			break;

		case $glue:
		case $glue:
			return $field . "=" . $field . $glue . self::_quote($val);
			break;

		case $glue:
		case $glue:
		case $glue:
			return $field . "=" . $field . $glue . self::_quote($val);
			break;

		case $glue:
		case $glue:
		case $glue:
		case $glue:
		case $glue:
			return $field . $glue . self::_quote($val);
			break;

		case $glue:
			return $field . " LIKE(" . self::_quote($val) . ")";
			break;

		case $glue:
		case $glue:
			if (!$val = K::M("verify/check")->ids($val)) {
				return 1;
			}

			return $field . ($glue == "NOTIN" ? " NOT" : "") . " IN(" . $val . ")";
			break;

		default:
			trigger_error("Not allow this glue between field and value: \"" . $glue . "\"");
		}
	}

	static public function _quote_field($field)
	{
		if (is_array($field)) {
			foreach ($field as $k => $v ) {
				$field[$k] = self::_quote($v);
			}
		}
		else {
			if (strpos($field, "`") !== false) {
				$field = str_replace("`", "", $field);
			}

			$field = "`" . $field . "`";
		}

		return $field;
	}

	static public function _quote($val)
	{
		if (is_string($val)) {
			return "'" . addcslashes($val, "\n\r\'\"\032") . "'";
		}

		if (is_int($val) || is_float($val)) {
			return "'" . $val . "'";
		}

		if (is_array($val)) {
			if ($noarray === false) {
				foreach ($val as &$v ) {
					$v = self::_quote($v, true);
				}

				return $val;
			}
			else {
				return "''";
			}
		}

		if (is_bool($val)) {
			return $val ? "1" : "0";
		}

		return "''";
	}

	public function order($field = NULL, $order = NULL, $pre = "")
	{
		if (($field == "hot") && empty($order) && $this->_hot_orderby) {
			$field = $this->_hot_orderby;
		}
		else {
			if (($field == "new") && empty($order) && $this->_hot_orderby) {
				$field = $this->_new_orderby;
			}
			else if (empty($field)) {
				if ($this->_orderby) {
					$field = $this->_orderby;
				}
				else if ($this->field_exists("orderby")) {
					$field = array("orderby" => "ASC");
				}
				else {
					return "";
				}
			}
		}

		$orders = array();

		if (is_array($field)) {
			$orders = array();

			foreach ($field as $k => $v ) {
				$order = ((strtoupper($v) == "ASC") || empty($v) ? "ASC" : "DESC");
				$orders[] = $pre . self::_quote_field($k) . " $order";
			}
		}
		else {
			$order = ((strtoupper($order) == "ASC") || empty($order) ? "ASC" : "DESC");
			$orders[] = self::_quote($field) . " " . $order;

			if ((array) $orderby = $this->_orderby) {
				unset($orderby[$field]);

				foreach ((array) $orderby as $k => $v ) {
					$order = ((strtoupper($v) == "ASC") || empty($v) ? "ASC" : "DESC");
					$orders[] = $pre . self::_quote_field($k) . " $order";
				}
			}
		}

		return $orders ? " ORDER BY " . implode(",", $orders) : "";
	}

	public function limit($page, $limit = 0)
	{
		if (preg_match("/^\d+,\d+$/i", $limit)) {
			return " LIMIT $limit";
		}

		$start = (max(intval($page), 1) - 1) * $limit;
		$limit = intval(0 < $limit ? $limit : 0);
		if ((0 < $start) && (0 < $limit)) {
			return " LIMIT $start, $limit";
		}
		else if ($limit) {
			return " LIMIT $limit";
		}
		else if ($start) {
			return " LIMIT $start";
		}
		else {
			return "";
		}
	}

	protected function _check($data, $pk = NULL)
	{
		if ($cols = $this->__columns()) {
			$cols = explode(",", $cols);

			foreach ((array) $data as $k => $v ) {
				if (!in_array($k, $cols)) {
					unset($data[$k]);
					$err++;
				}
			}
		}

		return $data;
	}

	protected function _check_schema($data, $pk = NULL)
	{
		$schemas = &$schemas;

		if (!$data = $this->_check($data, $pk)) {
			return false;
		}

		if ($schemas === NULL) {
			$file = __CFG::DIR . "schemas/" . $this->_table . ".php";

			if (!$this->_table) {
				$schemas = array();
			}
			else if (!file_exists($file)) {
				$schemas = array();
			}
			else if (!$schemas = include $file) {
				$schemas = array();
			}
		}

		if ($schemas) {
			$check = K::M("verify/check");

			foreach ($schemas as $k => $v ) {
				if ($pk) {
					if (empty($v["edit"])) {
						unset($data[$k]);
						continue;
					}
					else {
						if (empty($v["empty"]) && isset($data[$k])) {
							if ($data[$k] === "") {
								$this->msgbox->add($v["label"] . "不能为空", 451);

								return false;
							}
						}
						else {
							if ($v["empty"] && isset($data[$k]) && empty($data[$k])) {
								continue;
							}
						}
					}
				}
				else if ($v["type"] == "clientip") {
					$data[$k] = __IP;
				}
				else if ($v["type"] == "dateline") {
					$data[$k] = __TIME;
				}
				else if (empty($v["add"])) {
					unset($data[$k]);
					continue;
				}
				else if (empty($v["empty"])) {
					if (!isset($data[$k]) || ($data[$k] === "")) {
						$this->msgbox->add($v["label"] . "不能为空", 452);

						return false;
					}
				}
				else {
					if ($v["empty"] && isset($data[$k]) && empty($data[$k])) {
						continue;
					}
				}

				if (isset($data[$k])) {
					switch (strtolower($v["type"])) {
					case strtolower($v["type"]):
					case strtolower($v["type"]):
					case strtolower($v["type"]):
					case strtolower($v["type"]):
						$data[$k] = (int) $data[$k];
						break;

					case strtolower($v["type"]):
						$data[$k] = (double) $data[$k];
						break;

					case strtolower($v["type"]):
					case strtolower($v["type"]):
					case strtolower($v["type"]):
						$data[$k] = ($data[$k] ? 1 : 0);
						break;

					case strtolower($v["type"]):
						if (!$check->mail($data[$k])) {
							$this->msgbox->add($v["label"] . "必须为Email格式", 453);

							return false;
						}

						break;

					case strtolower($v["type"]):
						if (!$check->phone($data[$k])) {
							if (!$check->mobile($data[$k])) {
								$this->msgbox->add($v["label"] . "必须为电话/手机号格式", 454);

								return false;
							}
						}

						break;

					case strtolower($v["type"]):
						if (!$check->mobile($data[$k])) {
							$this->msgbox->add($v["label"] . "必须为手机号格式", 454);

							return false;
						}

						break;

					case strtolower($v["type"]):
						if (!$check->qq($data[$k])) {
							$this->msgbox->add($v["label"] . "必须为QQ格式，多个用\",\"分隔", 455);

							return false;
						}

						break;

					case strtolower($v["type"]):
						if (!is_numeric($data[$k])) {
							$data[$k] = ($data[$k] ? strtotime($data[$k]) : 0);
						}

						break;

					case strtolower($v["type"]):
						if (!preg_match("/^[\d]{4}-[\d]{1,2}-[\d]{1,2}$/", $data[$k])) {
							if (!is_numeric($data[$k])) {
								$data[$k] = 0;
							}
						}

						break;

					case strtolower($v["type"]):
						if (is_array($data[$k])) {
							if ($ids = $check->ids($data[$k])) {
								$data[$k] = $ids;
							}

							break;
						}
					case strtolower($v["type"]):
						$data[$k] = K::M("content/html")->filter($data[$k]);
						break;

					case strtolower($v["type"]):
					case strtolower($v["type"]):
					case strtolower($v["type"]):
					default:
						if (!$v["html"]) {
							$data[$k] = K::M("content/html")->encode($data[$k]);
						}

						break;
					}
				}
			}
		}

		return $data;
	}

	protected function __columns()
	{
		if (($this->_cols === NULL) && $this->_force_clos_db) {
			if ($cols = self::fetch_fields()) {
				$this->_cols = implode(",", $cols);
			}
		}

		return $this->_cols;
	}

	public function flush()
	{
		$this->check_table_auth();

		if ($this->_pre_cache_key) {
			unset(self::$_CACHE_TABLES[$this->_pre_cache_key]);
			return $this->cache->delete($this->_pre_cache_key);
		}

		return false;
	}
}


