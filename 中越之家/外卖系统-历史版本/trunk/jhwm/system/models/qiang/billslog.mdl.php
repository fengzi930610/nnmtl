<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Qiang_Billslog extends Mdl_Table
{
    protected $_table = 'qiang_bills_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,bills_id,bills_sn,shop_id,bills_number,status,amount,fee,dateline,count,type,user_amount,bl,freight';

    public function create($data)
    {
        $data['dateline'] = __TIME;
        return $this->db->insert($this->_table, $data, true);
    }

    public function update_status($bills_id){
        if(!$bills_id){
            return false;
        }
        $where = $this->where(array('bills_id'=>$bills_id));
        $SQL = "UPDATE ".$this->table($this->_table)." SET status = '1'  WHERE {$where}";
        return $this->db->Execute($SQL);
    }
}
