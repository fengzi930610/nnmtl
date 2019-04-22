<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/19
 * Time: 17:43
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Chongzhi_Bills extends Mdl_Table {
    protected $_table = 'chong_bills';
    protected $_pk = 'bills_id';
    protected $_cols = 'bills_id,amount,bills_sn,dateline';

    public function create($data)
    {
        $data['dateline'] = __TIME;
        $data['bills_sn'] = date('Ymd');
        $format_sql = $this->_insert_sql($data);
        $sql = 'INSERT INTO '.$this->table($this->_table).$format_sql.' ON DUPLICATE KEY UPDATE '.'amount=amount+'.$data['amount'];
        return  $this->db->Execute($sql);
    }
    protected function _insert_sql($data)
    {
        ksort($data);
        return "(`".implode("`,`",array_keys($data))."`) VALUES('".implode("','",$data)."')";
    }

}