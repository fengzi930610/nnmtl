<?php
//zend54   
//Decode by www.dephp.cn  QQ 2859470
?>
<?php
class DB
{
	static 	public $db;
	static 	public $tablepre = "";

	static public function update($table, $data, $condition, $low_priority = false)
	{
	}

	static public function insert($table, $data, $return_insert_id = false, $replace = false)
	{
		return self::$db->insert($this->table);
	}

	static public function table($table)
	{
		return self::$tablepre . $table;
	}
}

if (!defined("__CORE_DIR")) {
	exit("Access Denied");
}

