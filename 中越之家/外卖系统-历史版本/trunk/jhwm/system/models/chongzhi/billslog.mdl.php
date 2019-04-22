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
class Mdl_Chongzhi_Billslog extends Mdl_Table {
    protected $_table = 'chong_bills_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,uid,amount,bills_id,reason,dateline';
}