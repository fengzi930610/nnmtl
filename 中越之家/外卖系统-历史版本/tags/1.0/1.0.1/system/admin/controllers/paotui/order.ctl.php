<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/30
 * Time: 15:27
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Paotui_Order extends Ctl {

    public function index($page=1,$type=0){

        $filter = $pager = array();
        $pager['page'] = $page =  max(intval($page), 1);
        $pager['limit'] = $limit = 10;
        $order_by = array('order_id'=>"desc");
        $filter['from'] = 'paotui';
        $filter['closed'] = 0;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['order_id']){
                $filter['order_id'] = $SO['order_id'];
            }
            if($SO['uid']){
                $filter['uid'] = $SO['uid'];
            }
            if($SO['stime']&&!$SO['ltime']){
                $filter['dateline'] = ">:".strtotime($SO['stime']);
            }
            if(!$SO['stime']&&$SO['ltime']){
                $filter['dateline'] = "<:".(strtotime($SO['ltime'])+86399);
            }
            if($SO['stime']&&$SO['ltime']){
                $filter['dateline']  = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (o.order_id = '".$SO['keywords']."' OR w.nickname LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%' OR ext.title LIKE '%".$SO['keywords']."%' OR ext.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
    
        switch ($type) {
            case '1': // 待支付
                $filter['order_status'] = 0;
                $filter['online_pay'] = 1;
                $filter['pay_status'] = 0;
                break;
            case '2': // 待接单
                $filter['order_status'] = 0;
                $filter['online_pay'] = 1;
                $filter['pay_status'] = 1;
                break;
            case '3': // 取货中
                $filter['order_status'] = 1;
                $filter['online_pay'] = 1;
                $filter['pay_status'] = 1;
                $filter['staff_id'] = ">:0";
                break;
            case '4': // 送货中
                $filter['order_status'] = 3;
                $filter['online_pay'] = 1;
                $filter['pay_status'] = 1;
                $filter['staff_id'] = ">:0";
                break;
            case '5': // 已送达
                $filter['order_status'] = 4;
                $filter['online_pay'] = 1;
                $filter['pay_status'] = 1;
                $filter['staff_id'] = ">:0";
                break;
            case '6': // 已完成
                $filter['order_status'] = 8;
                $filter['online_pay'] = 1;
                $filter['pay_status'] = 1;
                $filter['staff_id'] = ">:0";
                break;
            case '7': // 已完成
                $filter['order_status'] = -1;// 催单
                break;
            default:
                break;
        }
        if($items = K::M('order/order')->items_join_member_shop($filter,$order_by,$page,$limit,$count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}', $type)), array('SO'=>$SO));
            $order_ids = $staff_ids = $uids = $shop_ids = array();
            foreach($items as $v){
                $order_ids[] = $v['order_id'];
                if($v['uid']){
                    $uids[] = $v['uid'];
                }
                if($v['staff_id']){
                    $staff_ids[] =$v['staff_id'];
                }
                if($v['shop_id']){ //三方单获取商户信息
                    $shop_ids[$v['shop_id']] = $v['shop_id'];
                }
            }
            $staff_list = K::M('staff/staff')->items_by_ids($staff_ids);
            //$member_list = K::M('member/member')->items_by_ids($uids);
            $paotui_order_list = K::M('paotui/order')->items_by_ids($order_ids);

            //$shops = K::M('waimai/waimai')->items_by_ids($shop_ids);
            
            foreach($items as $k=>$v){
                $v['paotui_order'] =  $paotui_order_list[$v['order_id']];
                //$v['member_info']  =  $member_list[$v['uid']];
                $v['member_info']  =  array('nickname'=>$v['member_nickname'], 'mobile'=>$v['member_mobile']);
                $v['staff_info']   =  $staff_list[$v['staff_id']];

                /*if($v['shop_id'] && !$v['uid'] && ($shop = $shops[$v['shop_id']])){
                    $v['member_info']  =  array('nickname'=>$shop['title'],'mobile'=>$shop['phone']);
                }*/
                if($v['shop_id'] && !$v['uid']){
                    $v['member_info']  =  array('nickname'=>$v['shop_title'],'mobile'=>$v['shop_mobile']);
                }

                $pay_type = $v['online_pay'] == 1 ? "在线支付" : "";
                $v['detail_json'] = json_encode(array(
                    'products'=>$paotui_order_list[$v['order_id']]['product'],
                    'detail'=>array(
                        'order_id'=>$v['order_id'],
                        'member'=>$v['member_info']['nickname'].'('.$v['member_info']['mobile'].')',
                        'dateline'=>date('Y-m-d H:i:s', $v['dateline']),
                        'pay_type'=>$pay_type,
                        'intro'=>$v['intro'] ? $v['intro'] : '--',
                    ),
                    'detail_2'=>array(
                        array('title'=>'配送费：','val'=>'￥'.$v['pei_amount']),
                        array('title'=>'小费：','val'=>'￥'.$paotui_order_list[$v['order_id']]['tip']),
                        array('title'=>'红包抵扣：','val'=>'-￥'.$v['hongbao']),
                        array('title'=>'总计：','val'=>'￥'.$v['amount']),
                        )
                ));
                $v['type_label'] = '';
                $items[$k] = K::M('paotui/order')->get_msg($v);
            }

            $types = K::M('other/order')->getType();
            if($other_orders = K::M('other/order')->items(array('p_order_id'=>$order_ids))){
                foreach ($other_orders as $k => $v) {
                    if($items[$v['p_order_id']]){
                        $items[$v['p_order_id']]['type_label'] = $types[$v['type']] ? $types[$v['type']] : '';
                    }
                }
            }           
        }

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['st'] = $type;
        $this->pagedata['groups'] = K::M('pei/group')->fetch_all();
        $this->tmpl = 'admin:paotui/order/items.html';
    }

    public function so(){
        $this->tmpl = 'admin:paotui/order/so.html';
    }

     public function detail($order_id = null)
    {
        if(!$order_id = (int)$order_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if((!$detail = K::M('order/order')->detail($order_id)) || (!$p_order = K::M('paotui/order')->detail($order_id))){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $detail = K::M('paotui/order')->get_msg($detail);            
            $detail['user'] = K::M('member/member')->detail($detail['uid']);
            $detail['logs'] = K::M('order/log')->items(array('order_id'=>$order_id),array('log_id'=>'DESC'));
            $payments = K::M('payment/payment')->find(array('payment'=>$detail['pay_code']));
            $pays = array();
            foreach($payments as $k=>$val){
                $pays[$val['payment']] = $val['title'];
            }
            $detail['payments'] = $pays;
            $detail['staff'] = K::M('staff/staff')->detail($detail['staff_id']);
            $detail['types'] = K::M('order/log')->get_log_types();
            $payments = K::M('order/order')->get_payments();
            $order_from = array('weixin'=>'微信','ios'=>'苹果APP','android'=>'安卓APP','wap'=>'wap端','www'=>'网页端');

            $detail['type_label'] = '';
            $types = K::M('other/order')->getType();
            if($other_order = K::M('other/order')->find(array('p_order_id'=>$order_id))){
                $detail['type_label'] = $types[$other_order['type']] ? $types[$other_order['type']] : 0;
                if(!$detail['uid'] && ($shop = K::M('waimai/waimai')->detail($other_order['shop_id']))){
                    $detail['user'] = array('nickname'=>$shop['title'], 'mobile'=>$shop['phone']);
                }                
            }

            $this->pagedata['froms'] = $order_from[$detail['order_from']];
            $this->pagedata['pay_method'] = $payments[$detail['pay_code']];
            $this->pagedata['detail'] = $detail;
            $this->pagedata['p_order'] = $p_order;
            $this->tmpl = 'admin:paotui/order/detail.html';
        }
    }

    // 取消订单
    public function cancel($order_id=null)
    {
        if($order_id = (int)$order_id){
            if(!$order_id) {
                $this->msgbox->add('订单号不存在',210);
            }else if(!$order = K::M('order/order')->detail($order_id)) {
                $this->msgbox->add('该订单不存在',211);
            }else if($order['order_status'] ==-1 || $order['order_status']==8){
                $this->msgbox->add('该订单不可取消',213);
            }/*else if(K::M('paotui/order')->cancel($order_id,$order,'admin')){
                $this->msgbox->add('取消订单成功');
            }*/else{
                //$this->msgbox->add('取消订单失败',215);
                if(!$order['uid'] && $other_order = K::M('other/order')->find(array('p_order_id'=>$order_id))){
                    K::M('other/order')->cancel($other_order['order_id'], $other_order['shop_id'], 'admin');
                }else{
                    if(K::M('paotui/order')->cancel($order_id,$order,'admin')){
                        $this->msgbox->add('取消订单成功');
                    }else{
                        $this->msgbox->add('取消订单失败',215);
                    }
                }
            }
        }else{
            $this->msgbox->add('参数有误！',216);
        }
    }

    // 订单完成
    public function confirm($order_id=null) 
    { 
        if($order_id = (int)$order_id){
            if(!$order_id) {
                $this->msgbox->add('订单号不存在',210);
            }else if(!$order = K::M('order/order')->detail($order_id)) {
                $this->msgbox->add('该订单不存在',211);
            }else if($order['order_status'] <= 1 || $order['order_status']==8){
                $this->msgbox->add('该订单不可确认完成',213);
            }/*else if(K::M('paotui/order')->confirm($order_id,$order,'admin')){
                $this->msgbox->add('订单确认成功');
            }*/else{
                //$this->msgbox->add('订单确认失败',216);
                if(!$order['uid'] && $other_order = K::M('other/order')->find(array('p_order_id'=>$order_id))){
                    if(K::M('other/order')->received($other_order['order_id'], $other_order['shop_id'])){
                        $this->msgbox->add('订单确认成功');
                    }else{
                        $this->msgbox->add('订单确认失败',215);
                    }
                }else{
                    if(K::M('paotui/order')->confirm($order_id,$order,'admin')){
                        $this->msgbox->add('订单确认成功');
                    }else{
                        $this->msgbox->add('订单确认失败',216);
                    }
                }
            } 
        }
    }



}