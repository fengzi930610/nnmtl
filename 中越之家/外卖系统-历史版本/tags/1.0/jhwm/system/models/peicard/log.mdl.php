<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/27
 * Time: 13:41
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Peicard_log extends Mdl_Table {

    protected $_table = 'peicard_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,uid,cid,order_id,money,day,dateline';

    public function delete_by_orderId($order_id)
    {
    	if(!$order_id){
    		return false;
    	}else{
    		$sql = "DELETE FROM ".$this->table($this->_table)." WHERE `order_id`={$order_id}";
            $ret = $this->db->Execute($sql);
            return $ret;
    	}
    }

    public function counts_group_by($filter=array(), $groupby='cid')
    {
        $where = $this->where($filter);
        $sql = "SELECT cid, uid, COUNT(1) as count FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY {$groupby}";
        $items = array();
        if($res = $this->db->Execute($sql)){
            while($row = $res->fetch()){
                $items[$row[$groupby]] =$row;
            }
        }
        return $items;
    }
}