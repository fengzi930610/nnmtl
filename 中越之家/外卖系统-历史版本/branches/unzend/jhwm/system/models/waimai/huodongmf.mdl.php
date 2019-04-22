<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_HuodongMf extends Mdl_Table
{
    protected $_table = 'waimai_huodong_mf';
    protected $_pk = 'huodong_id';
    protected $_cols = 'huodong_id,shop_id,config,num,limit,group,stime,ltime,audit,closed,dateline,clientip';
    //protected $_orderby = array('coupon_id' => 'DESC', 'order_amount' => 'DESC');
    
    public function _format_row($row)
    {
        $row['config'] = !empty($row['config']) ? unserialize($row['config']) : array();// 活动配置
        return $row;
    }
    public function coupon_mf($order){
        if(!is_array($order)||!$order){
            return false;
        }else if($order['from']!='waimai'){
            return false;
        }else{
            $filter = array();
            $filter['shop_id'] = $order['shop_id'];
            $filter['stime'] = '<:'.__TIME;
            $filter['ltime'] = '>:'.__TIME;
            $filter['closed'] = 0;
            $filter['aduit'] = 1;

            $cfg_huodong = K::M('waimai/config')->gethuodongconfig();
            $cfg_true_huodong = $cfg_huodong?$cfg_huodong:array(
                'hongbao'=>0,
                'first'=>0,
                'manjian'=>0,
                'youhui'=>0,
                'manfan'=>0
            );

            if($mf=$this->find($filter)){

                if($order['pei_type']==1&&$order['online_pay']==0){
                    if($cfg_true_huodong['manfan']==0){
                        return false;
                    }else{
                        $juli =$order['total_price']-$order['first_youhui'] - $order['coupon'] - $order['order_youhui'] - $order['hongbao'];
                    }
                }else {
                    $juli = $order['amount']+$order['money'];
                }
                $arr = array();
                foreach ($mf['config'] as $k=>$v){
                    $arr[$v['paid_amount']][] = $v;
                }
                $shop = K::M('waimai/waimai')->detail($order['shop_id']);
                ksort($arr);
                $create_data = array();
                foreach ($arr as $v){
                    if($juli>=$v[0]['paid_amount']&&($v[0]['order_amount']>0)&&($v[0]['coupon_amount']>0)){
                        $create_data['shop_id'] = $shop['shop_id'];
                        $create_data['order_id'] = 0;
                        $create_data['type'] = 1;
                        $create_data['uid'] = $order['uid'];
                        $create_data['huodong_id'] =$mf['huodong_id'];
                        $create_data['order_amount'] = $v[0]['order_amount'];
                        $create_data['coupon_amount'] = $v[0]['coupon_amount'];
                        $create_data['stime'] = $mf['stime'];
                        $create_data['ltime'] = __TIME+(86400*$v[0]['day']);
                        $create_data['title']=$shop['title'].'满返优惠券';
                    }
                }
                if($create_data&&$create_data['order_amount']>0&&$create_data['coupon_amount']>0){
                    K::M('waimai/coupon')->create($create_data);
                }
            }
        }
    }
}
