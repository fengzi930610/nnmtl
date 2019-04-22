<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/29
 * Time: 9:32
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Intracity_Billslog extends Mdl_Table {

    protected $_table = 'intracity_bills_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,bills_id,shop_id,order_id,amount,dateline,group_id,staff_id,extend';

    protected function _format_row($row)
    {
        $row['extend'] = $row['extend']?unserialize($row['extend']):array();
        return $row;
    }


}
