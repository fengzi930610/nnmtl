<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_HuodongCoupon extends Mdl_Table
{
    protected $_table = 'waimai_huodong_coupon';
    protected $_pk = 'huodong_id';
    protected $_cols = 'huodong_id,shop_id,config,num,limit,group,stime,ltime,audit,closed,dateline,clientip';
    //protected $_orderby = array('coupon_id' => 'DESC', 'order_amount' => 'DESC');
    
    public function _format_row($row)
    {
        $row['config'] = !empty($row['config']) ? unserialize($row['config']) : array();// 活动配置
        return $row;
    }
}
