<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/2
 * Time: 15:41
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Reward_Order extends Model {

   public function set_payed($log, $trade=array()){
       $order_id = $log['order_id'];
       if(!$order = K::M('order/order')->detail($order_id)){
           return false;
       }else if($order['pay_status']==1){
           return false;
       }else if($order['order_status']==8){
           return false;
       }else if(!$staff_id =$order['staff_id']){
           return false;
       }else if(!$staff = K::M('staff/staff')->detail($staff_id)){
           return false;
       }else{
           if($res =  K::M('order/order')->update($order_id, array('pay_status' => 1,'order_status'=>8),true)){

              if(K::M('staff/staff')->update_money($staff_id,$order['amount'],'用户打赏金额'.$order['amount'])){
                  return true;
              }else{
                  return false;
              }
           }

       }
   }

   public function cancel($order_id){
       if(!$order_id){
           return false;
       }else if(!$order = K::M('order/order')->detail($order_id)){
           return false;
       }else if($order['order_status']==8){
           return false;
       }else if($order['money']==0){
           return false;
       }else{
           if($this->db->update('order', array('order_status'=>-1), "order_id='{$order_id}'", true)){
               K::M('order/order')->update($order_id, array('lasttime'=>__TIME), true);// 更新时间
               if($order['online_pay']){
                   if($order['money'] > 0){
                       K::M('member/member')->update_money($order['uid'], $order['money'], '订单(ID:'.$order['order_id'].')打赏未完成，返还余额抵扣部分');
                   }
                   // if($money = $this->get_return_amount($order_id, $order)){
                   //     K::M('member/member')->update_money($order['uid'], $money, '订单(ID:'.$order['order_id'].')取消退回到余额');
                   // }
               }


               }
       }


   }


}