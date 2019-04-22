<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/3/24
 * Time: 13:42
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Ele_Order extends Model  {


    //创建订单
    public function ordercreate($data)
    {
        if(!$ext_order_id = $data['order_id']){
            return false;
        }else if(!$ext_shop_id = $data['shop_id']){
            return false;
        }else if(!$bind = K::M('waimai/accesstoken')->find(array('ext_shop_id'=>$ext_shop_id))){
            return false;
        }else if(!$waimai = K::M('waimai/waimai')->detail($bind['shop_id'])){
            return false;
        }else if(!$data['lng']||!$data['lat']){
            return false;
        }else if(($count = K::M('other/order')->count(array('ext_shop_id'=>$ext_shop_id,'ext_order_id'=>$ext_order_id)))>0){
            return false;
        }else{
            defined('CITY_ID') or define('CITY_ID',$waimai['city_id']);

            $parmas_pei_time = $data['pei_time']==null?date('H:i',__TIME):date('H:i',strtotime($data['pei_time']));
            $insert_order = array();
            //$pei_amount = K::M('other/order')->get_paotui_amount(array('type' => 'song', 'now_time' => $parmas_pei_time, 'lng' => $data['lng'], 'lat' => $data['lat'], 'o_lng' => $waimai['lng'], 'o_lat' => $waimai['lat'], 'freight' => 1));//这边暂时不考虑重量的问题
            $pei_amount = 0;
            $insert_order['amount'] = $pei_amount;
            $insert_order['city_id'] = $waimai['city_id'];
            $insert_order['staff_id'] =0;
            $insert_order['uid'] = 0;
            $insert_order['shop_id'] = $bind['shop_id'];
            $insert_order['from'] = 'other';
            $insert_order['order_status'] = 0;//未申请配送的订单状态为0
            $insert_order['online_pay'] = 1;
            $insert_order['pay_status'] = 1;
            $insert_order['total_price'] = $pei_amount;
            $insert_order['hongbao_id'] = 0;
            $insert_order['hongbao'] = 0;
            $insert_order['o_lng'] = $waimai['lng'];
            $insert_order['o_lat'] = $waimai['lat'];
            $insert_order['lng'] = $data['lng'];
            $insert_order['lat'] = $data['lat'];
            $insert_order['contact'] = $data['consignee'];
            $insert_order['mobile'] = $data['phoneList'][0];
            $insert_order['addr'] = $data['deliveryPoiAddress'];
            $insert_order['house'] = "";
            $insert_order['day'] = date('Ymd');
            $insert_order['clientip'] = __IP;
            $insert_order['pei_type'] = 0;
            $insert_order['intro'] = $data['description'];
            $insert_order['order_from'] = "wap";
            $insert_order['pei_time'] = $data['pei_time'] == null ? 0 : strtotime($data['pei_time']);
            $insert_order['dateline'] = __TIME;
            $insert_order['group_id'] = 0;
            $insert_order['day_num'] = $data['daySn'];
            $insert_order['pay_time'] = 0;
            $insert_order['pei_amount'] = $pei_amount + 0;
            $total_price = 0;
            foreach($data['groups'] as $k=>$v){
                foreach($v['items'] as $kk=>$vv){
                    $total_price += $vv['total'];
                }
            }
            $insert_order['total_price'] = $total_price;

            if($order_id = K::M('order/order')->create($insert_order)){
                $products = $extend = array();
                $price = 0;
                foreach($data['groups'] as $k=>$v){
                    foreach($v['items'] as $kk=>$vv){
                        $extend[] = $vv;
                        $price += $vv['total']; 
                        $products[] = array(
                            'product_name'=>$vv['name'],
                            'product_price'=>$vv['price'],
                            'product_number'=>$vv['quantity'],
                            'amount'=>$vv['total'],
                        );
                   }
                }
                $other_order = array();
                $other_order['order_id'] = $order_id;
                $other_order['shop_id'] = $bind['shop_id'];
                $other_order['type'] = 'ele';
                $other_order['price'] = $price;
                $other_order['product'] = serialize($products);
                $other_order['lng'] =$data['lng'];
                $other_order['lat'] =$data['lat'];
                $other_order['addr'] =$data['deliveryPoiAddress'];                                
                $other_order['contact'] =$data['consignee'];
                $other_order['mobile'] = $data['phoneList'][0];
                $other_order['ext_shop_id'] = $ext_shop_id;
                $other_order['ext_order_id'] = $ext_order_id;
                $other_order['extend'] = serialize($extend);
                $other_order['dateline'] = __TIME;

                if (K::M('other/order')->create($other_order)) {
                    if($print_list = K::M('shop/print')->items(array('shop_id'=>$waimai['shop_id']))){
                        foreach($print_list as $k=>$v){
                            K::M('order/order')->yunprint($order_id,1,$v['plat_id']);
                        }
                    }
                    K::M('order/log')->create(array('from'=>'member', 'log'=>'三方单(饿了么)，订单创建成功', 'order_id'=>$order_id));
                    return true;
                }else{
                    return false;
                }
            }
            return false;
        }
    }



    public function recievecancel($order_id)
    {
        if(!$order_id){
            return false;
        }else if(!$other_order = K::M('other/order')->find(array('ext_order_id'=>$order_id))){
            return false;
        }else if(!$order = K::M('order/order')->detail($other_order['order_id'])){
            return false;
        }else if($order['order_status'] == -1 || $order['order_status'] == 8){
            return false;
        }else if($other_order['refund_status']==-1){
            return false;
        }else{
            if(in_array($order['order_status'],array(3,4))){
                K::M('order/log')->create(array('from'=>'member', 'log'=>'三方单(饿了么)，订单取消', 'order_id'=>$order['order_id']));
                K::M('order/order')->update($order['order_id'],array('order_status'=>-1));
                return  K::M('paotui/order')->confirm($other_order['p_order_id'],null,'member');
            }else{
                if($other_order['p_order_id']){
                    K::M('order/order')->update($other_order['p_order_id'],array('order_status'=>-1));
                    K::M('order/log')->create(array('from'=>'member', 'log'=>'订单取消', 'order_id'=>$other_order['p_order_id']));
                    if($p_order = K::M('order/order')->detail($other_order['p_order_id'])){
                        K::M('waimai/waimai')->update_money($other_order['shop-id'],$p_order['pei_amount'],'取消订单，配送费退回配送费余额￥:'.$p_order['pei_amount']);
                    }
                }

                }


                K::M('order/log')->create(array('from'=>'member', 'log'=>'三方单(饿了么)，订单取消', 'order_id'=>$order['order_id']));
                return  K::M('order/order')->update($order['order_id'],array('order_status'=>-1));


        }
    }


    public function recievejiedan($order_id)
    {
        if(!$order_id){
            return false;
        }else if(!$other_order = K::M('other/order')->find(array('ext_order_id'=>$order_id))){
            return false;
        }else if(!$order = K::M('order/order')->detail($other_order['order_id'])){
            return false;
        }else if($order['order_status'] != 0){
            return false;
        }else if($order['order_status'] == -1 || $order['order_status'] == 8){
            return false;
        }else{
            if(K::M('order/order')->update($order['order_id'],array('order_status'=>2))){
                K::M('order/log')->create(array('from'=>'member', 'log'=>'三方单(饿了吗),商户接单', 'order_id'=>$order['order_id']));
                K::M('order/time')->update($order_id,array('shop_jiedan_time'=>__TIME));
                return true;
            }
            return false;

        }

    }


    public function recievepayback($order_id,$reason)
    {
        if(!$order_id){
            return false;
        }else if(!$other_order = K::M('other/order')->find(array('ext_order_id'=>$order_id))){
            return false;
        }else if(!$order = K::M('order/order')->detail($other_order['order_id'])){
            return false;
        }else if($order['order_status'] ==-1 ||$order['order_status'] == 8){
            return false;
        }else{
            if(K::M('order/order')->update($order['order_id'],array('refund_status'=>1))){
                $refund_log = array(
                    'order_id' => $order['order_id'],
                    'from' => 'member',
                    'uid' => 0,
                    'shop_id' => $order['shop_id'],
                    'reflect' => $reason,
                    'refund_price' => $order['amount'],
                );
                if(K::M('waimai/order/refund')->create($refund_log)){
                    K::M('order/log')->create(array('from'=>'member', 'log'=>'三方单(饿了吗),用户申请退款:'.$reason, 'order_id'=>$order['order_id']));
                    K::M('shop/shop')->send($order['shop_id'], '用户申请退款','用户('.$order['contact'].')申请订单(ID:'.$order['order_id'].')'."退款",array('type'=>'tuiOrder','order_id'=>$order['order_id']));
                }
                return true;
            }
            return false;
        }
    }


    public function recievecanelpayback($order_id)
    {
        if(!$order_id){
            return false;
        }else if(!$other_order = K::M('other/order')->find(array('ext_order_id'=>$order_id))){
            return false;
        }else if(!$order = K::M('order/order')->detail($other_order['order_id'])){
            return false;
        }else if($order['order_status'] == -1 || $order['order_status'] == 8){
            return false;
        }else{
            K::M('order/log')->create(array('from'=>'member', 'log'=>'三方单(饿了吗),用户取消退款', 'order_id'=>$order['order_id']));
            return  K::M('order/order')->update($order['order_id'],array('refund_status'=>0));
        }
    }


    public function recievecomplete($order_id)
    {
        if(!$order_id){
            return false;
        }else if(!$other_order = K::M('other/order')->find(array('ext_order_id'=>$order_id))){
            return false;
        }else if(!$order = K::M('order/order')->detail($other_order['order_id'])){
            return false;
        }else if($order['order_status'] == -1 || $order['order_status'] == 8){
            return false;
        }else{
            return  K::M('other/order')->confirm($order['order_id'],null,'member');
        }
    }

    public function recieveagree($order_id)
    {
        if(!$order_id){
            return false;
        }else if(!$other_order = K::M('other/order')->find(array('ext_order_id'=>$order_id))){
            return false;
        }else if(!$order = K::M('order/order')->detail($other_order['order_id'])){
            return false;
        }else if($order['order_status'] == -1 || $order['order_status'] == 8){
            return false;
        }else{

            if(in_array($order['order_status'],array(3,4))){
                K::M('order/log')->create(array('from'=>'member', 'log'=>'三方单(饿了吗),商家同意退款', 'order_id'=>$order['order_id']));
                K::M('order/order')->update($order['order_id'],array('order_status'=>-1,'refund_status'=>2));
                return  K::M('paotui/order')->confirm($other_order['p_order_id'],null,'member');
            }else{
                if($other_order['p_order_id']){
                    K::M('order/order')->update($other_order['p_order_id'],array('order_status'=>-1));
                }

                K::M('order/log')->create(array('from'=>'member', 'log'=>'三方单(饿了吗),商家同意退款', 'order_id'=>$order['order_id']));
                return  K::M('order/order')->update($order['order_id'],array('order_status'=>-1,'refund_status'=>2));

            }
        }

    }


    public function recieverefuse($order_id)
    {
        if(!$order_id){
            return false;
        }else if(!$other_order = K::M('other/order')->find(array('ext_order_id'=>$order_id))){
            return false;
        }else if(!$order = K::M('order/order')->detail($other_order['order_id'])){
            return false;
        }else if($order['order_status'] == -1 || $order['order_status'] == 8){
            return false;
        }else{

            return  K::M('order/order')->update($order['order_id'],array('refund_status'=>-1));
        }

    }
















}