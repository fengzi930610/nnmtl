<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: log.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Sms_Log extends Mdl_Table
{   
  
    protected $_table = 'sms_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,mobile,content,sms,status,clientip,dateline';

    
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        return $this->db->insert($this->_table, $data, true);
    }

    public function update($pk, $data, $checked=false)
    {
        $this->_checkpk();
        if(!$checked && !$data = $this->_check_schema($data,  $pk)){
            return false;
        }
        return $this->db->update($this->_table, $data, $this->field($this->_pk, $pk));
    }
	public function lasttime_by_ip($ip)
    {
        $sql = "SELECT dateline FROM ".$this->table($this->_table)." WHERE clientip='$ip' ORDER BY log_id DESC LIMIT 1";
        return (int)$this->db->GetOne($sql);
    }
}