<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/19
 * Time: 15:42
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::C('staff');
class Ctl_Order extends Ctl_Staff {

    public function detail($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',201);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if($order['staff_id']!=$this->staff_id){
            $this->msgbox->add('没有权限查看该订单',203);
        }else{
            $product = array();
            if($order['from'] == 'paotui'){
                $p_order = K::M('paotui/order')->detail($order_id);
                $product[] = array(
                    'product_name'=>implode(' ',$p_order['product']),
                    'product_num'=>1,
                    'yuji_price'=>$p_order['yuji_price'],
                    'price'=>$p_order['price'],
                    'weight'=>$p_order['weight']
                    );
                $log = K::M('staff/billslog')->find(array('staff_id'=>$this->staff_id,'order_id'=>$order_id));
                $this->pagedata['p_order'] = $p_order;
            }else{
                $filter_product = array();
                $filter_product['order_id'] = $order_id;
                $product = K::M('waimai/orderproduct')->items($filter_product,array('pid'=>'DESC'),1,9999,$count_product);
                $log = K::M('waimai/billslog')->find(array('shop_id'=>$order['shop_id'],'bills_number'=>$order_id));
                $this->pagedata['waimai_order'] = K::M('waimai/order')->detail($order_id);
            }

            $this->pagedata['product'] = $product;
            $this->pagedata['log'] = $log;
            $this->pagedata['order'] = $order;
            $this->tmpl = 'order/detail.html';
        }
    }

    public function logstatus($order_id)
    {
        if(!$order_id){
           $this->msgbox->add('订单不存在',211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
           $this->msgbox->add('订单不存在',212);
        }/*else if($order['staff_id']!=$this->staff_id){
           $this->msgbox->add('没有权限查看该订单',213);
        }*/else{
           $detail = K::M('order/time')->detail($order_id);
           $this->pagedata['detail'] = $detail;
           $this->pagedata['order'] = $order;
           $this->tmpl = 'order/logstatus.html';
        }
    }

}