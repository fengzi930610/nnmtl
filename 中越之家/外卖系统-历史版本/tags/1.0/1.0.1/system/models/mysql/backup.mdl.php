<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: backup.mdl.php 10028 2015-05-05 12:15:23Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Mysql_Backup extends Model 
{    
    
    public function structtable($table)
    {
        $sqldump = '';
        $rs = $this->db->query("SHOW CREATE TABLE $table");
        if(!$this->db->error()) {
            $sqldump = "DROP TABLE IF EXISTS $table;\n";
        } else {
            return '';
        }
        $create = $rs->fetch_row($createtable);
        $sqldump .= $create[1];
        $rs = $this->db->query("SHOW TABLE STATUS LIKE '$table'");
        $tablestatus = $rs->fetch();
        $sqldump .= ($tablestatus['Auto_increment'] ? " AUTO_INCREMENT=$tablestatus[Auto_increment]" : '').";\n\n";
        if($this->db->version() >= '4.1' && $this->db->version() < '5.1') {
            if($tablestatus['Auto_increment'] <> '') {
                $temppos = strpos($sqldump, ',');
                $sqldump = substr($sqldump, 0, $temppos).' auto_increment'.substr($sqldump, $temppos);
            }
            if($tablestatus['Engine'] == 'MEMORY') {
                $sqldump = str_replace('TYPE=MEMORY', 'TYPE=HEAP', $sqldump);
            }
        }
        echo $sqldump;
        return $sqldump;
    }
    public function dumptable($table, &$startfrom = 0, $currsize = 0, $sizelimit=1024)
    {
        $offset = 300;
        //$sizelimit = 2048; //KB
        $sqldump = '';
        $tablefields = array();
        if(!$tablefields = $this->db->GetFields($table)){
            return '';
        }
        $fields = array_keys($tablefields);
        $firstfield = $fields[0];
        $rs = $this->db->query("SHOW FULL COLUMNS FROM $table");
        if(!$rs || $this->db->errno() == 1146) {
            return;
        }else {
            while($fieldrow = $rs->fetch()) {
                $tablefields[] = $fieldrow;
            }
        }
        $numrows = $offset;
        while($currsize + strlen($sqldump) + 500 < $sizelimit * 1000 && $numrows == $offset) {
            $sql = "SELECT * FROM $table LIMIT $startfrom, $offset";
            $rs = $this->db->Execute($sql);
            $numrows = $rs->num_rows(); 
            while($row = $rs->fetch_row()){
                $comma = $t = '';
                foreach($row as $v){
                    $t .= $comma."'".$this->db->escape_string($v)."'";
                    $comma = ',';
                }
                $sqldump .= "INSERT INTO $table VALUES ($t);\n";
                $startfrom ++;
            }
            if($numrows < $offset){
                $startfrom = -1;
            }
        }
        //$sqldump .= "\n";
        return $sqldump;
    }
    public function bakinfo($hash)
    {
        $info = array();
        $bakdir = __CORE_DIR.'/data/backup/'.$hash;
        if(!preg_match('/^\d{8}_\w{8}$/i', $hash)){
            return false;
        }else if(!is_dir($bakdir)){
            return false;
        }else{
            $info['name'] = $name;
            $info['time'] = filectime($bakdir);
            $fp = dir($bakdir);
            $total_size = $count = 0;
            $items = array();
            while (false !== ($name = $fp->read())) {
                if(preg_match("/^{$hash}_(\d+)\.php$/i", $name, $m)){                    
                    $file = $bakdir.'/'.$name;
                    $size = filesize($file);
                    $total_size += $size;
                    $count ++;
                    $row = array('volumn'=>$m[1], 'file'=>$file, 'size'=>$size);
                    $items[$m[1]] = $row;
                }
            }
            $fp->close();
            $info['total_size'] = $total_size;
            $info['count'] = $count;
            $info['volumns'] = $items;
        }
        return $info;
    }
    public function restore($hash, $volumn=1)
    {
        if(!$bakinfo = K::M('mysql/backup')->bakinfo($hash)){
            $this->msgbox->add('你要还原的备份不存在', 211);
            return false;
        }else if(!($item = $bakinfo['volumns'][$volumn]) || $volumn > $bakinfo['count']){
            //$this->msgbox->add('还原数据成功');
            return true;
        }else{
            $content = file_get_contents($item['file']);
            $this->db->runquery($content);
            $volumn++;
            if($volumn >= $bakinfo['count']){
                return true;
            }
            return $volumn;
        }
    }
    public function optimize($tables)
    {
        if(is_array($tables)){
            $tables = implode(',', $tables);
        }
        return $this->db->query("OPTIMIZE TABLE $tables");
    }
}