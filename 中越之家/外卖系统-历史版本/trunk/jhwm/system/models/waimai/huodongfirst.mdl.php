<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 17:06
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Huodongfirst extends Mdl_Table {
    protected $_table = 'waimai_huodong_first';
    protected $_pk = 'huodong_id';
    protected $_cols = 'huodong_id,shop_id,config,stime,ltime,audit,closed,dateline,clientip,type';
    //protected $_orderby = array('coupon_id' => 'DESC', 'order_amount' => 'DESC');



    public function _format_row($row)
    {
        $row['config'] = !empty($row['config']) ? unserialize($row['config']) : array();// 活动配置
        return $row;
    }
}