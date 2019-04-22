<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/26
 * Time: 13:36
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Mall_Billslog extends Mdl_Table {
    protected $_table = 'jifen_bills_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,bills_id,fee,jifen,uid,bills_sn,dateline,order_id';

}