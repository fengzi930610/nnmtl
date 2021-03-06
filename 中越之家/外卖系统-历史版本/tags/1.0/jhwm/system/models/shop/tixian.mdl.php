<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Shop_Tixian extends Mdl_Table
{   
  
    protected $_table = 'shop_tixian';
    protected $_pk = 'tixian_id';
    protected $_cols = 'tixian_id,shop_id,money,intro,account_info,status,reason,updatetime,clientip,dateline,end_money,from,trade_no,payee_account,pay_result,pay_status,city_id';
    protected $_orderby = array('tixian_id'=>'DESC');

    protected function _format_row($row)
    {
        $row['updatetime_lable'] = $row['updatetime'] ? date('Y-m-d H:i:s',$row['updatetime']) : "--------";
        return $row;
    }

    public function create_trade_no()
    {
        $i = rand(0, 999999);
        do{
            if (999999 == $i) {
                $i = 0;
            }
            ++$i;
            $no = '1'.date("ymd") . str_pad($i, 6, "0", STR_PAD_LEFT);
            $order_no = $this->db->GetRow("SELECT trade_no FROM ".$this->table($this->_table)." WHERE trade_no='{$no}'");
        } while ($order_no);
        return $no;
    }

    protected function _check($data, $tixian_id=null)
    {
        if(empty($tixian_id) && empty($data['trade_no'])){
            $data['trade_no'] = $this->create_trade_no();
        }
        return parent::_check($data, $log_id);
    }

    public function items_join_shop($filter, $order_by=array(), $page=1, $limit = 50, &$count = 0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('shop')." w ON o.shop_id = w.shop_id WHERE {$where}";
        if($res_count = $this->db->Execute($count_sql)){
            if($count_row = $res_count->fetch()){
                $count = $count_row['count'];
            }
            $sql =  "SELECT  o.*, w.`mobile` as 'mobile', w.`title` as 'title'  FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('shop')." w ON o.shop_id = w.shop_id WHERE {$where} {$order_by} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[$row[$this->_pk]] = $this->_format_row($row);
                }
            }
        }
        return $items;
    }
}