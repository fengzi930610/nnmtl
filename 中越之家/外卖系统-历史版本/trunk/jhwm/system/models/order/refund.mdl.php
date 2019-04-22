<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/25
 * Time: 14:45
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Order_Refund extends Mdl_Table {

    protected $_table = 'order_refund';
    protected $_pk = 'id';
    protected $_cols = 'id,order_id,create_time,amount,remark,type';

    protected function _format_row($row)
    {
        $row['id'] = (int)$row['id'];
        $row['order_id'] = (int)$row['order_id'];
        $row['create_time'] = (int)$row['create_time'];
        $row['amount'] = (int)$row['amount'];
        $row['remark'] = trim($row['remark']);
        $row['type'] = (int)$row['type'];
        return $row;
    }
}