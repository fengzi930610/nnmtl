<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Order_Refund extends Mdl_Table
{   
    protected $_table = 'waimai_order_refund';
    protected $_pk = 'order_id';
    protected $_cols = 'order_id,from,uid,shop_id,reflect,reply,reply_time,refund_price,status,dateline';
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        return $this->db->insert($this->_table, $data, false);
    }


    
}