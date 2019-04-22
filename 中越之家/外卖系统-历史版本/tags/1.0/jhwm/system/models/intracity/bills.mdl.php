<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/29
 * Time: 9:28
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Intracity_Bills extends Mdl_Table {

    protected $_table = 'intracity_bills';
    protected $_pk = 'bills_id';
    protected $_cols = 'bills_id,bills_sn,shop_id,amount,dateline,extend,group_id';

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

    protected function _format_row($row)
    {
        $row['extend'] = $row['extend']?unserialize($row['extend']):array();
        return $row;
    }






}