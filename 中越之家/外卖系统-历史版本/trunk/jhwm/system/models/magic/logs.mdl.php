<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: logs.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Magic_Logs extends Mdl_Table
{
    
    protected $_table = 'system_logs';
    public function write($admin_id,$admin_name,$action,$level,$title,$data=array())
    {
        /*$a['admin_id'] = $admin_id;
        $a['admin_name'] = $admin_name;
        $a['title'] = addslashes($title);
        $a['action'] = addslashes($action);
        $a['level'] = $level;
        $data = is_array($data) ? var_export($data,true) : $data;
        $a['data'] = addslashes($data);
        $a['ip'] = __IP;
        $a['dateline'] = __CFG::TIME;*/
        
        $a['admin'] = $admin_name."(ID:".$admin_id.")";
        $a['title'] = addslashes($title);
        $a['action'] = addslashes($action);       
        $data = is_array($data) ? var_export($data,true) : $data;
        $a['content'] = addslashes($data);
        $a['dateline'] = __CFG::TIME;
        return $this->db->insert($this->_table,$a,true);
    }
    public function read($ID)
    {
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE ID='$ID' LIMIT 1";
        return $this->db->GetRow($sql);
    }
    public function fetch($admin='',$p=1, $l=50, &$count=0)
    {
        $where = $admin ? "admin='$admin'" : 1;
        $limit = $this->limit($p,$l);
        $items = array();
        if($count = $this->db->GetOne("SELECT COUNT(1) FROM ".$this->table($this->_table)." WHERE $where")){
            $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE $where ORDER BY ID DESC LIMIT $limit[0],$limit[1]";
            $rs = $this->db->Execute($sql);
            while($row = $rs->fetch()){
                $items[$row['ID']] = $row;
            }
        }
        return $items;
    }
}