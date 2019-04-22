<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/15
 * Time: 15:06
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Webview_bills_Order extends Ctl {

    public function detail($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',201);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if($order['shop_id']!=$this->shop_id){
            $this->msgbox->add('没有权限查看该订单',203);
        }else{
            $filter_product = array();
            $filter_product['order_id'] = $order_id;
            $waimai_product = K::M('waimai/orderproduct')->items($filter_product,array('pid'=>'DESC'),1,9999,$count_product);
            $log = K::M('waimai/billslog')->find(array('shop_id'=>$this->shop_id,'bills_number'=>$order_id));
            $this->pagedata['product'] = $waimai_product;
            $this->pagedata['log'] = $log;
            $this->pagedata['order'] = $order;
            $this->pagedata['waimai_order'] = K::M('waimai/order')->detail($order_id);
            $this->tmpl = 'webview/bills/order/detail.html';
        }

    }





}