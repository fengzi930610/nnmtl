<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/14
 * Time: 9:59
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Paidan extends Mdl_Table {
    protected $_table = 'staff_paidan';
    protected $_pk = 'paidan_id';
    protected $_cols = 'paidan_id,staff_id,pai,accept,refuse,day,dateline';

    public function create($data)
    {
        $data['dateline'] = __TIME;
        $data['day'] = date('Ymd');
        $format_sql = $this->_insert_sql($data);
        $sql = 'INSERT INTO '.$this->table($this->_table).$format_sql.' ON DUPLICATE KEY UPDATE '.'pai=pai+'.$data['pai'].','.'accept=accept+'.$data['accept'].',refuse=refuse+'.$data['refuse'];
        return  $this->db->Execute($sql);
    }
    protected function _insert_sql($data)
    {
        ksort($data);
        return "(`".implode("`,`",array_keys($data))."`) VALUES('".implode("','",$data)."')";
    }


}