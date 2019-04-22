<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/24
 * Time: 17:06
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Order_Time extends Mdl_Table
{
    protected $_table = 'order_time';
    protected $_pk = 'order_id';
    protected $_cols = 'order_id,create_time,pay_time,shop_jiedan_time,staff_jiedan_time,staff_start_time,staff_compltet_time,order_compltet_time';




}