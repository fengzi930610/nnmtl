<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27
 * Time: 15:56
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Cash_Billslog extends Mdl_Table {
    protected $_table = 'cash_bills_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,bills_id,bills_sn,staff_id,order_id,amount,dateline,fee,pei_amount';


}