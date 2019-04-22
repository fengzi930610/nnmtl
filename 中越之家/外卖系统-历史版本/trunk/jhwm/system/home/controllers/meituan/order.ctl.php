<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/4/3
 * Time: 16:15
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Meituan_Order extends Ctl {


    //创建订单接口
    public function notify(){
        if($data = $_REQUEST){
            $insert_data = array();
            $order = json_decode($data['order'],true);
            $insert_data['shop_id'] = $data['ePoiId'];
            $insert_data['order_id'] = $order['orderId'];
            $insert_data['lng'] = $order['longitude'];
            $insert_data['lat'] = $order['latitude'];
            $insert_data['pei_time'] = $order['deliveryTime'];
            $insert_data['contact']  = $order['recipientName'];
            $insert_data['mobile']   = $order['recipientPhone'];
            $insert_data['addr'] = $order['recipientAddress'];
            $insert_data['daySeq'] = $order['daySeq'];
            $insert_data['product'] = json_decode($order['detail'],true);
            $insert_data['intro'] = $order['caution'];
            $insert_data['payType'] = $order['payType'];
            $insert_data['total'] = $order['total'];
            K::M('meituan/order')->ordercreate($insert_data);
        }
        echo json_encode(array('data'=>"ok"));exit;
    }

    public function recreve_order(){
        if($data = $_REQUEST){
            $order = json_decode($data['order'],true);
            K::M('meituan/order')->recievejiedan( $order['orderId']);
        }
        echo json_encode(array('data'=>"ok"));exit;
    }

    public function quxiao(){
        if($data = $_REQUEST){
            $order = json_decode($data['orderCancel'],true);
            K::M('meituan/order')->recievecancel($order['orderId']);
        }
        echo json_encode(array('data'=>"ok"));exit;
    }

    public function refund(){
        if($data = $_REQUEST){
            $order = json_decode($data['orderRefund'],true);
            $order_id = $order['orderId'];
            $reason = $order['reason'];
            if($order['notifyType']=='apply'){
                K::M('meituan/order')->recievepayback($order_id,$reason);
            }else if($order['notifyType']=='agree'){
                K::M('meituan/order')->recieveagree($order_id);
            }else if($order['notifyType']=='reject'){
                K::M('meituan/order')->recieverefuse($order_id);
            }else if($order['notifyType']=='cancelRefund'||$order['orderRefund']['notifyType']=='cancelRefundComplaint') {
                K::M('meituan/order')->recievecanelpayback($order_id);
            }
        }
        echo json_encode(array('data'=>"ok"));exit;
    }



    public  function refund_part(){
        if($data = $_REQUEST){
            $order = json_decode($data['order'],true);
            $order_id =$order['orderId'];
            $reason   =  $order['reason'];
            if($order['notifyType']=="part"){
                K::M('meituan/order')->recievepayback($order_id,$reason);
            }else if($order['notifyType']=='agree'){
                K::M('meituan/order')->recieveagree($order_id);
            }else if($order['notifyType']=="reject "){
                K::M('meituan/order')->recieverefuse($order_id);
            }
        }
        echo json_encode(array('data'=>"ok"));exit;
    }

    public function complete(){
        if($data = $_REQUEST){
            $order = json_decode($data['order'],true);
            K::M('system/logs')->log('complete',array($data));
            $order_id = $order['orderId'];
            K::M('meituan/order')->recievecomplete($order_id);
        }
        echo json_encode(array('data'=>"ok"));exit;
    }









}