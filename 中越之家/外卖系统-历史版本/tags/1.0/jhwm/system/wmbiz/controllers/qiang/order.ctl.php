<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Qiang_Order extends Ctl
{

    protected function get_shop_fee($order)
    {
        $fee = $shop_amount = 0;
        if($order['order_status'] == 8){
            $qiang_bills_log = K::M('qiang/billslog')->find(array('bills_number'=>$order['order_id']));
            $fee = $qiang_bills_log['fee'];
            $shop_amount = $qiang_bills_log['amount'] + $fee;
        }else{
            $shop_amount = $order['amount'] + $order['money'];
            $fee =  number_format(($shop_amount * $order['bl'])/100, 2, '.', '');
            if($shop_amount<=0){
                $fee = 0;
            }
        }
        return array('fee'=>$fee,'shop_amount'=>$shop_amount);
    }

    public function index($page=1)
    {
        $filter = $pager = $items = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 20;
        if ($SO = $this->GP('SO')) {
            $pager['SO'] = $SO;
            if (is_array($SO['dateline'])) {
                if ($SO['dateline'][0] && $SO['dateline'][1]) {
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1]) + 86400;
                    $filter['dateline'] = $a . "~" . $b;
                }
            }
            if (isset($SO['order_so'])) {
                switch ($SO['order_so']) {
                    case '-2': // 已退款
                        $filter['order_status'] = -2; 
                        break;
                    case '-1': // 已取消
                        $filter['order_status'] = -1; 
                        break;
                    case '1': // 待发货
                        $filter['order_status'] = 0; 
                        $filter['pay_status'] = 1;
                        $filter['pei_type'] = 0;
                        break;
                    case '2': // 待收货
                        $filter['order_status'] = 6; 
                        $filter['pay_status'] = 1;
                        $filter['pei_type'] = 0; 
                        break;
                    case '3': // 待消费
                        $filter['order_status'] = 5; 
                        $filter['pay_status'] = 1;
                        $filter['pei_type'] = 3;
                        break;
                    case '4': // 已完成
                        $filter['order_status'] = 8; 
                        $filter['pay_status'] = 1; 
                        break;
                    case '5': // 已过期
                        $filter['order_status'] = -3; 
                        $filter['pay_status'] = 1; 
                        $filter['pei_type'] = 3;
                        break;
                }
            }
            if (isset($SO['pei_so'])) {
                switch ($SO['pei_so']) {
                    case '1': // 物流发货
                        $filter['pei_type'] = 0; 
                        break;
                    case '2': // 到店消费
                        $filter['pei_type'] = 3; 
                        break;
                }
            }
            if ($SO['number']) {
                if($ticket = K::M('qiang/order')->detail_by_number($SO['number'])){
                    $filter['order_id'] = $ticket['order_id'];
                }else{
                    $filter['order_id'] = '';
                }
            }
        }
        $filter['from'] = 'qiang';
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 0;
        if($items = K::M('order/order')->items($filter, array('order_id'=>'DESC'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('qiang/order/index', array('{page}')), array('SO'=>$SO));
            $order_ids = $uids = array();
            foreach($items as $k=>$val){
                $order_ids[$val['order_id']] = $val['order_id'];
                $uids[$val['uid']] = $val['uid'];
            }
            if($qiang_order_list = K::M('qiang/order')->items_by_ids($order_ids)){
                foreach ($items as $k => $v) {
                    foreach($qiang_order_list as $kk => $vv){
                        if($v['order_id'] == $vv['order_id']){
                            $items[$k]['notes'] = $vv['notes'];
                            $items[$k]['ticket_status'] = $vv['ticket_status'];
                            $items[$k]['use_ltime'] = $vv['use_ltime'];
                            $items[$k]['qiang_order'] = $vv;
                        }
                    }
                }
            }
            foreach ($items as $k => $v) {
                $items[$k] = K::M('qiang/order')->_format_data($v);
            }
        }
        $this->pagedata['member_list'] = K::M('member/member')->items_by_ids($uids);
        $this->pagedata['type'] = K::M('qiang/qiang')->getType();
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'qiang/order/index.html';
    }

    public function detail($order_id = null)
    {
        if (!$order_id = (int) $order_id) {
            $this->msgbox->add('未指定要查看内容的ID', 211);
        } else if (!$detail = K::M('qiang/order')->detail($order_id)) {
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        } else if($detail['shop_id'] != $this->shop_id){
            $this->msgbox->add(L('非法操作'), 213);
        }else {
            if(!$logs = K::M('order/log')->items(array('order_id'=>$detail['order_id']),array('order_id'=>'DESC'))){
                $logs = array();
            }
            $detail['logs'] = $logs;

            //备注信息
            $note_label = array();
            $notes = $detail['notes'] ? unserialize($detail['notes']) : array();
            if($notes_label = K::M('qiang/qiang')->get_notes_label($notes['limit_sku'])){
                foreach ($notes as $k => $v) {
                    if($notes_label[$k]){
                        if($k == 'pei_type' && $v == 2){
                            $note_label[] = $notes_label[$k][$v][0];
                            $note_label[] = $notes_label[$k][$v][1];
                        }else{
                            $note_label[] = $notes_label[$k][$v];
                        }
                    }
                }
            }
            $detail['note_label'] = $note_label;

            //评论信息
            $comment = array();
            if($comment = K::M('qiang/comment')->find(array('order_id'=>$order_id))){
                if($comment['have_photo'] == 1){
                    $photos = K::M('qiang/commentphoto')->items(array('comment_id'=>$comment['comment_id']));
                    $comment['photos'] = $photos;
                }
                $comment['score_wight'] = $comment['score']*20;
            }
            $detail['comment'] = $comment;

            //物流信息
            if(!$wuliu = K::M('net/http')->callapi('tools/kuaidi/auto',array('code'=>$detail['express']))){
                $wuliu = array();
            }
            $this->pagedata['json_wuliu'] = json_encode($wuliu);
            
            $detail['fee_arr'] = $this->get_shop_fee($detail);
            $this->pagedata['detail'] = K::M('qiang/order')->_format_data($detail);
            $this->pagedata['users'] = K::M('member/member')->detail($detail['uid']);
            $this->pagedata['express_config'] =  array('顺丰速运','EMS','申通快递','圆通快递','中通快递','韵达快递','百世汇通','宅急送','天天快递','德邦物流','其他');
            $this->tmpl = 'qiang/order/detail.html';
        }
    }

    //取消订单
    public function cancel($order_id)
    {
        if(!$order_id = (int)$order_id){
            $this->msgbox->add(L('订单不存在'), 211);
        }else if(!$order = K::M('qiang/order')->detail($order_id)) {
            $this->msgbox->add("该订单不存在",212);
        }else if($order['shop_id'] != $this->shop_id || $order['from'] != 'qiang'){
            $this->msgbox->add(L('非法操作'), 213);
        }else if($order['order_status'] < 0){
            $this->msgbox->add('订单已经取消，无需重复取消',214);
        }else if($order['order_status'] != 0){
            $this->msgbox->add('当前订单是不可取消的状态',215);
        }else if($order['pay_status'] != 0){
            $this->msgbox->add('已支付的订单不可取消',215);
        }else if(K::M('order/order')->cancel($order_id, $order, 'shop')){
            $this->msgbox->add('取消订单成功');
        }else {
            $this->msgbox->add('取消订单失败',216);
        }
    }


    //取消退款
    public function payback($order_id)
    {
        if(!$order_id = (int)$order_id){
            $this->msgbox->add(L('订单不存在'), 211);
        }else if(!$order = K::M('qiang/order')->detail($order_id)){
            $this->msgbox->add('该订单不存在',212);
        }else if($order['shop_id'] != $this->shop_id || $order['from'] != 'qiang'){
            $this->msgbox->add(L('非法操作'), 213);
        }else if(!in_array($order['order_status'], array(0,5))){
            $this->msgbox->add('当前订单状态无法退款',214);
        }else if($order['pay_status'] != 1){
            $this->msgbox->add('尚未支付，不可申请退款',214);
        }else if(!$notes = unserialize($order['notes'])){
            $this->msgbox->add('该抢购不支持退款',216);
        }else if($notes['is_tui'] != 1){
            $this->msgbox->add('该抢购不支持退款',217);
        }else if(K::M('order/order')->cancel($order_id, $order, 'shop')){
            $this->msgbox->add('退款申请成功');
        }else{
            $this->msgbox->add('退款申请失败',218);
        }
    }


    // ajax 验证密码
    public function check()
    {
        if(!$number = $this->GP('number')){
            $this->msgbox->add('请输入券码核销',211);
        }else if(!$order = K::M('qiang/order')->detail_by_number($number)) {
            $this->msgbox->add('券码核销不正确',211);
        }else if($order['number'] != $number) {
            $this->msgbox->add('券码核销不正确',213);
        }else if(!empty($order['use_time'])) {
            $this->msgbox->add('该券已使用',215);
        }else if($order['use_ltime'] < __TIME) {
            $this->msgbox->add('该券已过期',216);
        }else if($order['shop_id'] != $this->shop_id) {
            $this->msgbox->add('核销的券码不属于你的店铺',214);
        }else if($order['order_status'] != 5){
            $this->msgbox->add('订单状态不可核销',216);
        }else {
            $order['youxiao_time'] = date('Y-m-d',$order['use_ltime']);
            $this->msgbox->add('success');
            $this->msgbox->set_data('data',array('ticket'=>$order));
        }
    }

    //核销券码
    public function setspend()
    {   
        if($this->checksubmit()){
            if(!$order_id = (int)$this->GP('order_id')) {
                $this->msgbox->add('参数错误',210);
            }else if(!$number = $this->GP('number')) {
                $this->msgbox->add('请输入有效的核销券码',211);
            }else if(!$order = K::M('qiang/order')->detail_by_number($number)) {
                $this->msgbox->add('券码核销不正确',211);
            }else if($order['order_id'] != $order_id) {
                $this->msgbox->add('券码核销不正确',213);
            }else if(!empty($order['use_time'])) {
                $this->msgbox->add('该券已使用',215);
            }else if(!empty($order['use_ltime']) && $order['use_ltime'] < __TIME){
                $this->msgbox->add('该电子券已过期',214);
            }else if($order['shop_id'] != $this->shop_id) {
                $this->msgbox->add('核销的券码不属于你的店铺',214);
            }else if($order['order_status'] != 5){
                $this->msgbox->add('订单状态不可核销',216);
            }else if(K::M('order/order')->confirm($order['order_id'], $order, 'shop')){
                $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
                $title = sprintf("您在[%s]的抢购订单完成", $waimai['title'], $order['order_id']);
                $content = sprintf("您在[%s]的订单(单号：%s)电子券码(%s)已使用", $waimai['title'], $order['order_id'], $number);
                K::M('member/member')->send($order['uid'], $title, $content,  array('type'=>'order', 'order_id'=>$order['order_id']));
                $this->msgbox->add('核销成功');
            }else{
               $this->msgbox->add('核销失败',217);
            }
        }else{
            $this->tmpl = 'qiang/order/ticket.html';
        }
    }

    public function delivery()
    {
        if(!$order_id = (int)$this->GP('order_id')) {
            $this->msgbox->add('未指定要发货的订单', 401);
        }else if (!$order = K::M('qiang/order')->detail($order_id)) {
            $this->msgbox->add('等待发货的订单不存在', 211);
        }else if($order['order_status'] < 0){
            $this->msgbox->add('订单已取消不能发货', 212);
        }else if(empty($order['pay_status'])){
            $this->msgbox->add('订单未支付不能发货', 213);
        }else if($order['order_status'] == 8){
            $this->msgbox->add('订单已完成无需发货', 214);
        }else if($order['order_status'] != 0 || $order['pei_type'] != 0){
            $this->msgbox->add('订单状态不可发货', 215);
        }else if(!$express_name = $this->GP('express_name')){
            $this->msgbox->add('请填写完整的物流信息！',216);
        }else if(!$express = $this->GP('express')){
            $this->msgbox->add('请填写物流单号！',216);
        }else{
            $up_data = array();
            $up_data['express_name'] = $express_name;
            $up_data['express'] = $express;
            $up_data['order_status'] = 6;
            $up_data['lasttime'] = __TIME;
            if (K::M('order/order')->update($order_id, $up_data)) {
                $logmsg = '发货成功';
                K::M('order/log')->create(array('order_id'=>$order_id,'from'=>'admin','log'=>$logmsg,'status'=>4));
                K::M('member/member')->send($order['uid'], '您的抢购订单已经发货', "您的抢购订单(".$order_id.")已经开始发货");
                $this->msgbox->add('订单发货成功');                    
            }           
        }
    }

    
}