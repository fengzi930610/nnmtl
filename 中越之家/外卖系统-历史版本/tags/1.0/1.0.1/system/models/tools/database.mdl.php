<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: database.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Mdl_Tools_Database extends Model
{   
    
    public function tables()
    {
    	$pre = self::$system->_tablepre;
    	$tabls = array();
        $pre = str_replace('_', '\_', $pre);
    	if($rs = $this->db->query("SHOW TABLE STATUS LIKE '$pre%'")){
    		while($row = $rs->fetch()){
    			$tables[] = $row;
    		}
    	}
    	return $tables;      
    }
    public function sqldumptablestruct($table)
    {
        if($createtable = $this->db->GetRow("SHOW CREATE TABLE $table")) {
            $tabledump  = "DROP TABLE IF EXISTS $table;\n";
            $tabledump .= $createtable[1];
        } else {
            return '';
        }
        $tablestatus = $this->db->GetRow("SHOW TABLE STATUS LIKE '$table'");
        $tabledump .= ($tablestatus['Auto_increment'] ? " AUTO_INCREMENT=$tablestatus[Auto_increment]" : '').";\n\n";    
        return $tabledump;
    }
    public function sqldumptable($table, &$startfrom=0, $currsize=0, $sizelimit=2048, $usehex=false)
    {
        $offset = 300;
        $tabledump = '';
        $tablefields = array();
        if($rs = $this->db->GetRow("SHOW FULL COLUMNS FROM $table")){
            while ($row = $rs->fetch()) {
                $tablefields[] = $row;
            }
        }else{
            return '';
        }
        $tabledumped = 0;
        $numrows = $offset;
        $firstfield = $tablefields[0];
        while($currsize + strlen($tabledump) + 500 < $sizelimit * 1000 && $numrows == $offset) {
            if($firstfield['Extra'] == 'auto_increment') {
                $selectsql = "SELECT * FROM $table WHERE $firstfield[Field] > $startfrom ORDER BY $firstfield[Field] LIMIT $offset";
            } else {
                $selectsql = "SELECT * FROM $table LIMIT $startfrom, $offset";
            }
            $tabledumped = 1;
            if($rs = $this->db->Execute($selectsql)){
                $numfields = $rs->num_fields();
                $numrows = $rs->num_rows();
                while ($row = $rs->fetch()) {
                    $comma = $t = '';
                    for($i = 0; $i < $numfields; $i++) {
                        $t .= $comma.($usehex && !empty($row[$i]) && (strpos($tablefields[$i]['Type'], 'char') !== false || strpos($tablefields[$i]['Type'], 'text') !== false) ? '0x'.bin2hex($row[$i]) : '\''.mysql_escape_string($row[$i]).'\'');
                        $comma = ',';
                    }
                    if(strlen($t) + $currsize + strlen($tabledump) + 500 < $sizelimit * 1000) {
                        if($firstfield['Extra'] == 'auto_increment') {
                            $startfrom = $row[0];
                        } else {
                            $startfrom++;
                        }
                        $tabledump .= "INSERT INTO $table VALUES ($t);\n";
                    } else {
                        $complete = FALSE;
                        return $tabledump."\n";
                    }
                }
            }
        }
        return $tabledump."\n";
    }
    public function splitsql($sql)
    {
        $sql = str_replace("\r", "\n", $sql);
        $ret = array();
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach($queriesarray as $query) {
            $queries = explode("\n", trim($query));
            foreach($queries as $query) {
                $ret[$num] .= $query[0] == "#" ? NULL : $query;
            }
            $num++;
        }
        return($ret);
    }    
}