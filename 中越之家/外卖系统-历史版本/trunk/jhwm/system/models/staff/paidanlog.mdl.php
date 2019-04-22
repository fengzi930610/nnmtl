<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/10
 * Time: 17:08
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Paidanlog extends Mdl_Table {

    protected $_table = 'staff_paidan_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,order_id,intro';
}