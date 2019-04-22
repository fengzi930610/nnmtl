<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: account.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Jifen_Jifen extends Model
{

    public function get_modules()
    {
        $modules = array(
            'waimai'=>'外卖',
            'paotui'=>'跑腿',
            );
        return $modules;
    }

    public function update_jifen($order_id=null, $order=null)
    {
        $order_id = (int)$order_id;
        if(!$order = K::M('order/order')->detail($order_id)){
            return false;
        }else if(!$cfg = $order['jifen_cfg']){
            return false;
        }else if(!in_array($order['from'],$cfg['jifen_module'])){
            return false;
        }else if($order['jifen_status'] == 1 || $order['order_status'] != 8 || $order['online_pay'] != 1){
            return false;
        }else{
            $jifen_total = (int)(($order['amount']+$order['money']) * $cfg['jifen_ratio']);
            $modules = $this->get_modules();
            $from_label = $modules[$order['from']] ? $modules[$order['from']] : '';
            switch ($cfg['jifen_type']) {
                case 1:  //评价后得积分
                    if($order['comment_status'] == 1){
                        $intro = sprintf($from_label."订单(单号:%s)评价完成，获得%s积分", $order['order_id'], $jifen_total);
                    }else{
                        return false;
                    }                    
                    break;
                case 2:  //确认订单的积分
                    if($order['comment_status'] == 0){
                        $intro = sprintf($from_label."订单(单号:%s)确认完成，获得%s积分", $order['order_id'], $jifen_total);
                    }else{
                        return false;
                    }                    
                    break;
                default:
                    # code...
                    break;
            }

            if(K::M('order/order')->update($order['order_id'],array('jifen_status'=>1))){
                K::M('member/member')->update_jifen($order['uid'], $jifen_total, $intro);
                return true;
            }
            return false;        
        }
    }
    
}
