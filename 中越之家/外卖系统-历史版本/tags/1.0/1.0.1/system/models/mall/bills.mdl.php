<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/26
 * Time: 13:34
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Mall_Bills extends Mdl_Table {

    protected $_table = 'jifen_bills';
    protected $_pk = 'bills_id';
    protected $_cols = 'bills_id,amount,jifen,bills_sn,dateline,fee';

    public function create($data)
    {
        $data['dateline'] = __TIME;
        $data['bills_sn'] = date('Ymd');
        $format_sql = $this->_insert_sql($data);
        $sql = 'INSERT INTO '.$this->table($this->_table).$format_sql.' ON DUPLICATE KEY UPDATE '.'jifen=jifen+'.$data['jifen'].','.'fee=fee+'.$data['fee'];
        return  $this->db->Execute($sql);
    }
    protected function _insert_sql($data)
    {
        ksort($data);
        return "(`".implode("`,`",array_keys($data))."`) VALUES('".implode("','",$data)."')";
    }






}