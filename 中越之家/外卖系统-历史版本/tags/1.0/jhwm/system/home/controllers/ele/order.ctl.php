<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/3/22
 * Time: 15:05
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Ele_Order extends Ctl {

    public function notify(){
        $content = file_get_contents("php://input");
        if($content){
            if(!$data = json_decode($content,true)){
                $this->msgbox->add('数据解析异常',201);
            }else{
                $message = json_decode($data['message'],true);
                K::M('system/logs')->log('a-eledata',$data);
                if($data['type']==10){
                    $create_data = array();
                    $create_data['order_id'] = $message['orderId'];
                    $create_data['shop_id'] =$data['shopId'];
                    $location = explode(',',$message['deliveryGeo']);
                    $create_data['lng'] = $location[0];
                    $create_data['lat'] = $location[1];
                    $create_data['pei_time'] = $message['deliverTime'];
                    $create_data['consignee'] = $message['consignee'];
                    $create_data['phoneList'] = $message['phoneList'];
                    $create_data['deliveryPoiAddress'] = $message['deliveryPoiAddress'];
                    $create_data['description'] =$message['description'];
                    $create_data['daySn'] = $message['daySn'];
                    $create_data['groups'] = $message['groups'];
                    K::M('ele/order')->ordercreate($create_data);
                }else if(in_array($data['type'],array(14,15,17,25,35))){
                    K::M('ele/order')->recievecancel($message['orderId']);
                }else if($data['type']==12){
                    K::M('ele/order')->recievejiedan($message['orderId']);
                }else if($data['type']==20){
                    K::M('ele/order')->recievepayback($message['orderId'],$message['reason']);
                }else if($data['type']==21){
                    K::M('ele/order')->recievecanelpayback($message['orderId']);
                }else if($data['type']==18){
                    K::M('ele/order')->recievecomplete($message['orderId']);
                }else if($data['type']==32||$data['type']==22){
                    K::M('ele/order')->recieverefuse($message['orderId']);
                }else if($data['type']==33||$data['type']==23){
                    K::M('ele/order')->recieveagree($message['orderId']);
                }else if($data['type']==100){
                    if($accesstoken = K::M('waimai/accesstoken')->find(array('ext_shop_id'=>$data['shopId']))){
                        K::M('waimai/accesstoken')->update_accesstoken($accesstoken['shop_id'],array('ext_shop_id'=>0,'access_token'=>'',"expires_in"=>0,'refresh_token'=>""));
                    }                    
                }
            }
            echo json_encode(array('message'=>'ok'));
            exit;
        }else{
            echo json_encode(array('message'=>'ok'));
            exit;
        }


    }


}