<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Log extends Mdl_Table
{   
  
    protected $_table = 'staff_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,staff_id,money,intro,admin,day,clientip,dateline,balance,extend';
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        $data['clientip'] = $data['clientip'] ? $data['clientip'] : __IP;
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __CFG::TIME;
        $data['day'] = date('Ymd', $data['dateline']);
        return $this->db->insert($this->_table, $data, true);
    }
    public function log($staff_id, $money, $intro,$balance,$extend)
    {
        $a = array();
        if(!$staff_id = (int)$staff_id){
            return false;
        }
        $a = array('staff_id'=>$staff_id, 'money'=>$money, 'intro'=>$intro,'balance'=>$balance);
        if(defined('IN_ADMIN')){
            $admin = K::$system->admin->admin;
            $a['admin'] = "{$admin['admin_id']}:{$admin['admin_name']}";
        }
        $a['clientip'] = __IP;
        $a['dateline'] = __TIME;
        $a['day'] = date('Ymd', $a['dateline']);
        $a['extend'] = $extend;
        return $this->db->insert($this->_table, $a, true);
    }
    /**
     * 根据天统计金额
     * param $filter 查询条件
     * param $limit 开始 
     */
    public function count_by_day($filter=null, $page=1, $limit=30)
    {
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT `day`, SUM(`money`) as day_money FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `day` ORDER BY day ASC $limit";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['day']] = $row;
            }
        }
        return $items;
    }
    public function total_by_staff($filter)
    {
        $where = $this->where($filter);
        $sql = "SELECT COUNT(1) as total_count, SUM(`money`) as total_money FROM ".$this->table($this->_table)." WHERE {$where}";
        return $this->db->GetRow($sql);
    }

    protected function _format_row($row){
        $row['extend'] =  $row['extend']?unserialize($row['extend']):array();
        return $row;
    }

}