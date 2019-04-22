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
class Ctl_Webview_Qiang_Order extends Ctl {

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

    /*查看电子券*/
    public function ticket($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',201);
        }else if(!$order = K::M('qiang/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if($order['shop_id'] != $this->shop_id){
            $this->msgbox->add('没有权限查看该订单',203);
        }else if($order['order_status'] != 5){
            $this->msgbox->add('订单状态错误',204);
        }else if($order['pei_type'] != 3){
            $this->msgbox->add('消费状态错误',204);
        }else{
            //备注信息
            $note_label = array();
            $notes = $order['notes'] ? unserialize($order['notes']) : array();
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
            $order['note_label'] = $note_label;

            $order = $this->filter_fields('order_id,number,dateline,contact,mobile,qiang_title,qiang_discount_price,qiang_number,qiang_freight,note_label,uid', $order);
            $this->pagedata['member'] = K::M('member/member')->detail($order['uid']);
            $this->pagedata['order'] = $order;
            $this->tmpl = 'webview/qiang/order/ticket.html';
        }
    }


    /**订单列表**/
    public function index($ret=1)
    {
        $this->pagedata['ret'] = $ret;
        $this->tmpl = 'webview/qiang/order/index.html';
    }

    /**订单加载**/
    public function loadorder($page=1){
        $page = max((int)$page,1);
        $limit = 10;
        $ret = (int)$this->GP('ret');
        switch ($ret) {
            case '1': // 待消费
                $filter['order_status'] = 5; 
                $filter['pay_status'] = 1;
                $filter['pei_type'] = 3;
                break;
            case '2': // 待发货
                $filter['order_status'] = 0; 
                $filter['pay_status'] = 1;
                $filter['pei_type'] = 0;
                break;
            case '3': // 待收货
                $filter['order_status'] = 6; 
                $filter['pay_status'] = 1;
                $filter['pei_type'] = 0; 
                break;
            case '4': //  无效/退款
                $filter[':SQL'] = "`order_status` < 0"; 
                break;
            case '5': // 已完成
                $filter['order_status'] = 8; 
                $filter['pay_status'] = 1; 
                break;
        }
        $filter['from'] = 'qiang';
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 0;
        if($items = K::M('order/order')->items($filter, array('order_id'=>'DESC'), $page, $limit, $count)){
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

            $members = K::M('member/member')->items_by_ids($uids);
            foreach ($items as $k => $v) {
                $items[$k] = K::M('qiang/order')->_format_data($v);
                $items[$k]['member'] = $members[$v['uid']];
            }
        }else{
            $items  = array();
            $count = 0;
        }
        if($count <= 10){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['items'] = $items;
        $this->pagedata['page'] = $page;
        $this->pagedata['express_config'] =  array('顺丰速运','EMS','申通快递','圆通快递','中通快递','韵达快递','百世汇通','宅急送','天天快递','德邦物流','其他');
        $this->tmpl = 'webview/qiang/order/loadorder.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html',$html);
        $this->msgbox->json();
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
            $detail['fee_arr'] = $this->get_shop_fee($detail);
            $this->pagedata['detail'] = K::M('qiang/order')->_format_data($detail);
            $this->pagedata['member'] = K::M('member/member')->detail($detail['uid']);
            $this->pagedata['express_config'] =  array('顺丰速运','EMS','申通快递','圆通快递','中通快递','韵达快递','百世汇通','宅急送','天天快递','德邦物流','其他');
            $this->tmpl = 'webview/qiang/order/detail.html';
        }
    }

    // ajax 验证密码
    public function order_check()
    {
        if(!$order_id = $this->GP('order_id')){
            $this->msgbox->add('参数错误',211);
        }else if(!$number = $this->GP('number')){
            $this->msgbox->add('请输入券码核销',211);
        }else if(!$order = K::M('qiang/order')->detail_by_number($number)) {
            $this->msgbox->add('券码核销不正确',211);
        }else if($order['number'] != $number) {
            $this->msgbox->add('券码核销不正确',213);
        }else if($order_id != $order['order_id']){
            $this->msgbox->add('核销券码不属于该订单',213);
        }else if(!empty($order['use_time'])) {
            $this->msgbox->add('该券已使用',215);
        }else if($order['use_ltime'] < __TIME) {
            $this->msgbox->add('该券已过期',216);
        }else if($order['shop_id'] != $this->shop_id) {
            $this->msgbox->add('核销的券码不属于你的店铺',214);
        }else if($order['order_status'] != 5){
            $this->msgbox->add('订单状态不可核销',216);
        }else {
            $this->msgbox->add('券码验证成功');
        }
    }

    public function wuliu($order_id = null)
    {
        if(!$order_id = (int)$order_id){
            $this->msgbox->add('参数错误',201);
        }else if(!$order = K::M('qiang/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已被删除',201);
        }else if($order['order_status'] < 6){
            $this->msgbox->add('订单状态错误',201);
        }else if(empty($order['express'])){
            $this->msgbox->add('错误的物流单号',201);
        }else{
            if(!$wuliu = K::M('net/http')->callapi('tools/kuaidi/auto',array('code'=>$order['express']))){
                $wuliu = array();
            }

            $wuliu_label = ($order['order_status'] == 8) ? '已签收' : '待签收'; 
            $items = array(
                'wuliu' => array_values($wuliu),
                'photo' => $order['photo'],
                'express' => $order['express'],
                'express_name' => $order['express_name'],
                'wuliu_label' => $wuliu_label,
            );
            $this->pagedata['items'] = $items;
            $this->tmpl = 'webview/qiang/order/wuliu.html';
        }
    }


}