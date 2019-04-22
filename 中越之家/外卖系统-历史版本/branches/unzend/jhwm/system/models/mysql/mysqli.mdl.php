<?php
/**
 * Copy Right IJH.CC
 * $Id mysqli.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_Mysql_Mysqli extends Mysqli
{
    
    public $_QSQL = array();
    public $_ERR = array();
    public function __construct($host, $user, $passwd, $dbname=null, $port=3306, $socket=null)
    {
        $this->cfg['host'] = $host;
        $this->cfg['user'] = $user;
        $this->cfg['passwd'] = $passwd;
        $this->cfg['dbname'] = $dbname;
        $this->cfg['port'] = $port;
        $this->cfg['socket'] = $socket;
    }
    public function connect()
    {
        if($this->link === null){
            if($this->cfg['socket'] !== null){
                $host = $this->cfg['socket'];
            } else {
                $host = "{$this->cfg['host']}:{$this->cfg['port']}";
            }
            if(!$this->link = parent::__construct($this->cfg['host'], $this->cfg['user'], $this->cfg['passwd'], $this->cfg['dbname'], $this->cfg['port'], $this->cfg['socket'])){
                if(__CFG::DEBUG){
                    trigger_error('Mysql Connect:['.mysql_errno().']'.mysql_error());
                }
                trigger_error('Mysql Connect Error');
            } else {
                parent::query('SET NAMES UTF8');
                if($this->version() > '4.1'){
                    parent::query("SET SQL_MODE=''");
                }
            }
        }
        return $this->link;
    }
    public function version()
    {
        return $this->server_info;
    }
    public function safe_query($sql, $mode=MYSQLI_STORE_RESULT)
    {
        Mdl_Mysql_Safecheck::checkquery($sql);
        return $this->query($sql, $mode);
    }
    public function query($sql, $mode=MYSQLI_STORE_RESULT)
    {
        $this->_QSQL[] = $sql;
        Mdl_Mysql_Safecheck::checkquery($sql);
        if($rs = $this->query($sql, $mode)){
            return new Mdl_Mysqli_Result($rs);
        }
        $this->_ERR[] = $this->errno.':'.$this->error;
        return false;
        $this->connect();
        if(__CFG::DEBUG) {
            $starttime = microtime(true);
        }
        $rs = parent::query($sql, $mode);
        if(__CFG::DEBUG){
            $errno = $this->errno;
            $exec_time = number_format((microtime(true) - $starttime), 6);
            $this->total_exec_time += $exec_time;
            $this->_QSQL[] = array($sql, $exec_time, $this->link, $errno);
            if($errno>0){    
                $this->_ESQL[] = array($sql, $this->mysql_debug_backtrace(), $errno, $this->error);
            }
        }
        if(preg_match("/(SELECT|SHOW|DESCRIBE|EXPLAIN)\s+/i", $sql, $match)){
            return new Mdl_Mysqli_Result($rs);
        }
        return $rs;
    }
    /**
     * 同Adodb:Execute,兼容用
     */
    public function Execute($sql, $mode=MYSQLI_STORE_RESULT)
    {
        return $this->safe_query($sql, $mode);
    }
    public function affected_rows()
    {
        return $this->affected_rows;
    }
    public function insert_id()
    {
        return $this->insert_id;
    }
    /**
     * 同Adodb:GetRow,兼容用
     */
    public function GetRow($sql)
    {
        if(!preg_match("/LIMIT/i",$sql)){
            $sql = "$sql LIMIT 1";
        }
        if($rs = $this->Execute($sql)){
            return $rs->fetch_row();
        }
        return false;
    }
    /**
     * 同Adodb:GetOne,兼容用
     */
    public function GetOne($sql)
    {
        if(!preg_match("/LIMIT/i",$sql)){
            $sql = "$sql LIMIT 1";
        }
        if($rs = $this->Execute($sql)){
            $row = $rs->fetch_array(MYSQLI_NUM);
            return $row[0];
        }
        return false;
    }
    public function GetAll($sql,$pk=null)
    {
        $rows = array();
       if($rs = $this->Execute($sql)){
            while($row = $rs->fetch()){
                if($pk !== null && $row[$pk]){
                    $rows[$pk] = $row;
                }else{
                   $rows[] = $row;
                }
            }
       }
       return $rows;
    }
    public function table($table)
    {
        return $this->_tablepre.$table;
    }    
    public function GetFields($table, $full=false)
    {
        $data = false;
        $FULL = $full ? 'FULL' : '';
        if($rs = $this->query("SHOW {$FULL} FIELDS FROM {$table}")){
            $data = array();
            while($value = $rs->fetch()) {
                $data[$value['Field']] = $value;
            }
        }
        return $data;        
    }
    
    /**
     * 同Adodb:AutoExecute,兼容用
     */
    public function AutoExecute($table, $data, $mode, $where=null)
    {
        switch(strtoupper($mode)){
            case '1':
            case 'INSERT':
                return $this->Insert($table,$data);
            case '2':
            case 'UPDATE' :
                return $this->update($talbe,$data,$where);
        }
        return false;
    }
    public function insert($table, $data,$return_insert_id=false, $replace=false)
    {
        $sql = $this->_insert_sql($data);
        $cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
		if($rs = $this->Execute("$cmd $table $sql")){
		    return $return_insert_id ? $this->insert_id() : $rs;
        }
        return false;
    }
    
    public function update($table, $data, $condition, $low_priority=false)
    {
		$sql = $this->_update_sql($data);
		$cmd = "UPDATE ".($low_priority ? 'LOW_PRIORITY' : '');
		$where = '';
		if(empty($condition)) {
			$where = '1';
		}else if(is_array($condition)) {
			$where = implode(' AND ', $condition);
		}else{
			$where = $condition;
		}
        return $this->Execute("$cmd $table SET $sql WHERE $where");
	}
    
    protected function _insert_sql($data)
    {
        ksort($data);
        return "(`".implode("`,`",array_keys($data))."`) VALUES('".implode("','",$data)."')";
    }
    protected function _update_sql($data, $glue = ',')
    {
  		$sql = $comma = '';
		foreach ((array)$data as $k => $v) {
            $k = "`{$k}`";
            if(substr($v,0,strlen($k)) == $k){
			    $sql .= $comma."$k=$v";
            }else{
                $sql .= $comma."$k='$v'";
            }
			$comma = $glue;
		}
		return $sql;      
    }
    public function quote($str, $noarray = false)
    {
        if (is_string($str))
            return '\'' . addcslashes($str, "\n\r\\'\"\032") . '\'';
        if (is_int($str) or is_float($str))
            return '\'' . $str . '\'';
        if (is_array($str)) {
            if($noarray === false) {
                foreach ($str as &$v) {
                    $v = $this->quote($v, true);
                }
                return $str;
            } else {
                return '\'\'';
            }
        }
        if (is_bool($str))
            return $str ? '1' : '0';
        return '\'\'';
    }
    
    public function SQLLOG()
    {
        return array('Time'=>$this->total_exec_time,'QSQL'=>$this->_QSQL, 'ESQL'=>$this->_ESQL);
    }
    public function mysql_debug_backtrace()
    {
        $info = array();        
        if($data = debug_backtrace()){
            foreach($data as $k=>$v){
                if($k > 0 && ($v['file'])){
                    $info[] = sprintf('[%04d]%s', $v['line'], $v['file']);
                }
            }
        }
        return $info;
    }

    public function begin(){
        $this->tranform_errno = 0;
        $this->open_transaction = true;
       mysql_query("BEGIN", $this->curlink);
    }
    public function rollback(){
         mysql_query("ROLLBACK", $this->curlink);
        $this->open_transaction = false;
    }
    public function commit(){
        mysql_query("COMMIT", $this->curlink);
        $this->open_transaction = false;

    }
}
class Mdl_Mysqli_Result
{
    private $rs = null;
    
    public function __construct($rs)
    {
        $this->rs = &$rs;
    }
    public function __set($var,$val)
    {
        $this->rs->$var = $val;
        return $val;
    }
    public function __get($var)
    {
        return $this->rs->$var;
    }
    public function __isset($var)
    {
        return isset($this->rs->$var);
    }
    public function __unset($var)
    {
        unset($this->rs->$var);
    }
    public function __call($func,$args)
    {
       return call_user_func_array(array($this->rs,$func),$args);
    }
    public function fetch_row()
    {
        return $this->rs->fetch_assoc();
    }
	public function fetch()
    {
        return $this->rs->fetch_assoc();
    }
}

//code copy from by discuz by shzhrui
class Mdl_Mysql_Safecheck
{
    protected static $checkcmd = array('SELECT', 'UPDATE', 'INSERT', 'REPLACE', 'DELETE');
    protected static $config = array(
            'function'  => array('load_file','hex','substring','if','ord','char'),
            'action'    => array('intooutfile','intodumpfile','unionselect','(select', 'unionall', 'uniondistinct'),
            'notice'    => true,
            'fullnotice'=> false
        );
    public static function checkquery($sql)
    {
        $cmd = trim(strtoupper(substr($sql, 0, strpos($sql, ' '))));
        if (in_array($cmd, self::$checkcmd)) {
            $test = self::_do_query_safe($sql);
            if ($test < 1) {
                exit('It is not safe to do this query');
            }
        }
        return true;
    }
    private static function _do_query_safe($sql)
    {
        $sql = str_replace(array('\\\\', '\\\'', '\\"', '\'\''), '', $sql);
        $mark = $clean = '';
        if (strpos($sql, '/') === false && strpos($sql, '#') === false && strpos($sql, '-- ') === false) {
            $clean = preg_replace("/'(.+?)'/s", '', $sql);
        } else {
            $len = strlen($sql);
            $mark = $clean = '';
            for ($i = 0; $i < $len; $i++) {
                $str = $sql[$i];
                switch ($str) {
                    case '\'':
                        if (!$mark) {
                            $mark = '\'';
                            $clean .= $str;
                        } elseif ($mark == '\'') {
                            $mark = '';
                        }
                        break;
                    case '/':
                        if (empty($mark) && $sql[$i + 1] == '*') {
                            $mark = '/*';
                            $clean .= $mark;
                            $i++;
                        } elseif ($mark == '/*' && $sql[$i - 1] == '*') {
                            $mark = '';
                            $clean .= '*';
                        }
                        break;
                    case '#':
                        if (empty($mark)) {
                            $mark = $str;
                            $clean .= $str;
                        }
                        break;
                    case "\n":
                        if ($mark == '#' || $mark == '--') {
                            $mark = '';
                        }
                        break;
                    case '-':
                        if (empty($mark) && substr($sql, $i, 3) == '-- ') {
                            $mark = '-- ';
                            $clean .= $mark;
                        }
                        break;
                    default:
                        break;
                }
                $clean .= $mark ? '' : $str;
            }
        }
        $clean = preg_replace("/[^a-z0-9_\-\(\)#\*\/\"]+/is", "", strtolower($clean));
        if (self::$config['fullnotice']) {
            $clean = str_replace('/**/', '', $clean);
        }
        if (is_array(self::$config['function'])) {
            foreach (self::$config['function'] as $fun) {
                if (strpos($clean, $fun . '(') !== false)
                    return '-1';
            }
        }
        if (is_array(self::$config['action'])) {
            foreach (self::$config['action'] as $action) {
                if (strpos($clean, $action) !== false)
                    return '-3';
            }
        }
        if (self::$config['likehex'] && strpos($clean, 'like0x')) {
            return '-2';
        }
        if (is_array(self::$config['notice'])) {
            foreach (self::$config['notice'] as $note) {
                if (strpos($clean, $note) !== false)
                    return '-4';
            }
        }
        return 1;
    }
}