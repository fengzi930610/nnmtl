<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6
 * Time: 18:44
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Webview_Order extends Ctl {

    public function logstatus($order_id){

        if(!$order_id){
           $this->msgbox->add('订单不存在',211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
           $this->msgbox->add('订单不存在',212);
        }else if($order['shop_id']!=$this->shop_id){
           $this->msgbox->add('没有权限查看该订单',213);
        }else{
            $detail = K::M('order/time')->detail($order_id);
            $this->pagedata['detail'] = $detail;       
            $this->pagedata['order'] = $order;
            $this->tmpl = 'webview/order/logstatus.html';
        }    
    }

}