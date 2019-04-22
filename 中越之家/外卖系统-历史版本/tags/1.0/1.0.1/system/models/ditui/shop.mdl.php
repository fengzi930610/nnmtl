<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Ditui_Shop extends Mdl_Table
{   
  
    protected $_table = 'ditui_shop';
    protected $_pk = 'sid';
    protected $_cols = 'sid,ditui_id,shop_id,signup_amount,dateline';
    // 近一月推荐商户曲线
    public function ditui_shop($filter=null, $page=1,$limit=31) 
    {
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT FROM_UNIXTIME(dateline,'%Y-%m-%d') as date,COUNT(shop_id) as shopids FROM {$this->table($this->_table)} WHERE {$where} GROUP BY date ORDER BY date DESC $limit";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['date']] = $row;
            }
        }
        return $items;
    }
   
    // 近一月收入曲线
    public function ditui_income($filter=null, $page=1,$limit=31)
    {
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT FROM_UNIXTIME(dateline,'%Y-%m-%d') as date,SUM(`signup_amount`) as money FROM {$this->table($this->_table)} WHERE {$where} GROUP BY date ORDER BY date DESC $limit";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['date']] = $row;
            }
        }
        return $items;
    }
    
    public function count_income($filter)
    {
        $where = $this->where($filter);
        $sql = "SELECT SUM(`signup_amount`) as money FROM {$this->table($this->_table)} WHERE {$where}";
        if($row = $this->db->GetRow($sql)) {
            return $row['money'];
        }
    }
}