<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Order_Cuilog extends Mdl_Table
{
    protected $_table = 'order_cuilog';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,uid,shop_id,staff_id,order_id,reply,reply_time,dateline';
    public function create($data)
    {
    	$data['dateline'] = $data['dateline'] ? $data['dateline'] :  __CFG::TIME;
        if ($log_id = $this->db->insert($this->_table, $data, true)) {
            return $log_id;
        }
    }

    public function count_by_order($filter)
    {
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT `order_id`,`reply_time`, COUNT(1) as cui_count FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `order_id`";      
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['order_id']] = $row;
            }
        }
        return $items;
    }

    public function items_group_by_order_id($ids)
    {
        if(is_array($ids)){
            $ids = implode(',', $ids);
        }
        if(!K::M('verify/check')->ids($ids)){
            return false;
        }
        $where = "`order_id` IN ($ids)";
        $orderby = $this->order(array('dateline'=>'DESC'));
        //$sql = "SELECT * FROM (SELECT * FROM ".$this->table($this->_table)."{$orderby}) a WHERE {$where} GROUP BY order_id"; // 框架不支持子查询；
        $sql = "SELECT order_id, MAX(concat_ws('-',dateline,log_id,uid,shop_id,staff_id,reply,reply_time)) as concat_ws FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY order_id";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $field_list = array();
                if ($row['concat_ws']) {
                    $field_list = explode('-', $row['concat_ws']);
                    $row['log_id'] = $field_list[1];
                    $row['uid'] = $field_list[2];
                    $row['shop_id'] = $field_list[3];
                    $row['staff_id'] = $field_list[4];
                    $row['reply'] = $field_list[5];
                    $row['reply_time'] = $field_list[6];
                    $row['dateline'] = $field_list[0];
                    unset($row['concat_ws']);
                    $items[$row['order_id']] = $row;
                }
            }
        }
        return $items;
    }
  
}
