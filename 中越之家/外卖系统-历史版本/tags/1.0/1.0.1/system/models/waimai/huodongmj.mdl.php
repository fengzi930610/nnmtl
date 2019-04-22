<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_HuodongMj extends Mdl_Table
{
    protected $_table = 'waimai_huodong_mj';
    protected $_pk = 'huodong_id';
    protected $_cols = 'huodong_id,shop_id,title,config,stime,ltime,audit,closed,dateline,clientip';
    //protected $_orderby = array('coupon_id' => 'DESC', 'order_amount' => 'DESC');
    
    public function _format_row($row)
    {
        $row['config'] = !empty($row['config']) ? unserialize($row['config']) : array();// 活动配置
        return $row;
    }
    public function order_youhui($shop_id,$amount){
        if(!$shop_id){
            return false;
        }
        if(!$amount||$amount<=0){
            return false;
        }
        $filter = array();
        $filter['shop_id']=$shop_id;
        $filter['stime']='<:'.__TIME;
        $filter['ltime']='>:'.__TIME;
        $filter['audit'] = 1;
        $filter['closed'] = 0;
        if($detail = $this->find($filter)){
            $youhui = array();
            $foreach = array();
            foreach ($detail['config'] as $v){
                $foreach[$v['order_amount']] = $v;
            }
            ksort($foreach);
            foreach ($foreach as $v){
               if($amount>=$v['order_amount']){
                   $youhui['order_amount'] = $v['order_amount'];
                   $youhui['youhui_amount'] = $v['coupon_amount'];
                   $youhui['shop_amount'] = $v['shop_amount'];
                   $youhui['roof_amount'] = $v['roof_amount'];

               }
            }
            return $youhui;
        }else{
            return false;
        }



    }
}
