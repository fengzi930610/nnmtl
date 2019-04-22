<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Other_Order extends Ctl
{    
    public function index($page=1, $st=0)
    {
    	$filter = $orderby = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 10;
        $filter['from'] = "other";
        $orderby['order_id'] = 'DESC';

        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['order_id']){
                $filter['order_id'] = $SO['order_id'];
            }
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1])+86400;
                    $filter['dateline'] = $a."~".$b;
                }
            }
            if($SO['type'] && in_array($SO['type'], array('ele','meituan','own'))){
                $filter[':SQL'] = " w.`type`= '".$SO['type']."'";
            }

            //4.0模糊查询
            if($SO['keywords']){
                $shop_filter[':OR'] = array(
                    'title'=>'LIKE "%'.$SO['keywords'].'"%',
                    'phone'=>'LIKE "%'.$SO['keywords'].'"%'
                    );

                $filter[':SQL'] = " (o.order_id LIKE '%".$SO['keywords']."%' OR ext.title LIKE '%".$SO['keywords']."%' OR ext.phone LIKE '%".$SO['keywords']."%')";
            }
        }
        
        switch ($st) {
            case '1': // 待接单
                $filter['order_status'] = 0;
                break;
            case '2': // 待配送
                $filter['order_status'] = 2;
                break;
            case '3': // 配送中
                $filter['order_status'] = 3;
                break;
            case '4': // 已送达
                $filter['order_status'] = 4;
                break;
            case '5': // 已完成
                $filter['order_status'] = 8;
                break;
            case '6': // 已取消
                $filter['order_status'] = -1;
                break;
            case '7': // 异常单
                $filter['refund_status'] = "<>:0";
                break;
            default:
                break;
        }
        
        if($items = K::M('other/order')->items_join_by_shop($filter,$orderby,$page,$limit,$count)){
            $p_order_ids = $staff_ids = $shop_ids = array();
            foreach($items as $k=>$v){
                $items[$k] = K::M('other/order')->format_data($v);
                $items[$k]['detail_json'] = "";
                if($v['p_order_id']){
                    $p_order_ids[$v['p_order_id']] = $v['p_order_id'];
                }
                if($v['staff_id']){
                    $staff_ids[$v['staff_id']] = $v['staff_id'];
                }
                if($v['shop_id']){
                    $shop_ids[$v['shop_id']] = $v['shop_id'];
                }
            }

            $p_orders = K::M('paotui/order')->items_by_ids($p_order_ids);
            $staffs = K::M('staff/staff')->items_by_ids($staff_ids);
            //$shops = K::M('waimai/waimai')->items_by_ids($shop_ids);
            
            foreach ($items as $k => $v) {
                if($p_order = $p_orders[$v['p_order_id']]){
                    $v['p_order'] = $p_order;
                    $v['total_price'] = $v['pei_amount'] = $p_order['amount'] + $p_order['tip'];
                }

                $v['staff'] = $staffs[$v['staff_id']] ? $staffs[$v['staff_id']] : array();
                //$v['shop'] = $shops[$v['shop_id']] ? $shops[$v['shop_id']] : array();

                $pay_type = $v['online_pay'] == 1 ? "在线支付" : "货到付款";
                $v['detail_json'] = json_encode(array(
                    'products'=>$v['product'],
                    'detail'=>array(
                        'order_id'=>$v['order_id'],
                        'dateline'=>date('Y-m-d H:i:s', $v['dateline']),
                        'pay_type'=>$pay_type,
                        'intro'=>$v['intro'],
                    ),
                    'detail_2'=>array(
                        array('title'=>'配送费：','val'=>'￥'.($v['p_order'] ? $v['p_order']['amount'] : 0)),
                        array('title'=>'小费：','val'=>'￥'.($v['p_order'] ? $v['p_order']['tip'] : 0)),
                        array('title'=>'总计：','val'=>'￥'.$v['pei_amount']),
                        )
                ));
                $items[$k] = $v;
            }
            
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}',$st)), array('SO'=>$SO));
        }

        $params =  '';
        if(!empty($SO) && is_array($SO)){
            $params = http_build_query(array("SO"=>$SO));
            $params = '?'.$params;
        }
                
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['st'] = $st;
        $this->pagedata['parmas'] = http_build_query($parmas);

        $this->tmpl = 'admin:other/order/items.html';
    }
       
    public function so()
    {
        $this->tmpl = 'admin:other/order/so.html';
    }
        
    public function detail($order_id = null)
    {
        if(!$order_id = (int)$order_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('other/order')->detail($order_id,true)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $detail = K::M('other/order')->format_data($detail);
            
            $detail['shop'] = $waimai = K::M('waimai/waimai')->detail($detail['shop_id']);

            $detail['logs'] = K::M('order/log')->items(array('order_id'=>$order_id),array('log_id'=>'DESC'));
            foreach ($detail['logs'] as $log_k=>$log_v){
                if(!$log_v['log']){
                    unset($detail['logs'][$log_k] );
                }
            }
            $detail['staff'] = K::M('staff/staff')->detail($detail['staff_id']);

            $payments = K::M('order/order')->get_payments();
            $order_from = array('weixin'=>'微信','ios'=>'苹果APP','android'=>'安卓APP','wap'=>'wap端','www'=>'网页端');

            $detail['p_order'] = array();
            if($p_order = K::M('paotui/order')->detail($detail['p_order_id'])){
                $detail['p_order'] = $p_order;
                $detail['pei_amount'] = $p_order['amount'] + $p_order['tip'];
            }

            $this->pagedata['detail'] = $detail;

            $this->tmpl = 'admin:other/order/detail.html';
        }
    }
   
    // 取消订单
    public function cancel($order_id=null,$is_check = true)
    {
        if (!$order_id = (int)$order_id) {
            $this->msgbox->add(L('错误的订单!'), 211);
        }elseif (!$order = K::M('other/order')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的订单!'), 212);
        }else{
            K::M('other/order')->cancel($order_id, $order['shop_id'], 'admin');
        }
    }

    public function jiedan($order_id)
    {
        if (!$order_id = (int)$order_id) {
            $this->msgbox->add(L('错误的订单!'), 211);
        }elseif (!$order = K::M('other/order')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的订单!'), 212);
        }else{
            K::M('other/order')->jiedan($order_id, $order['shop_id'], 'admin');
        }
    }

    public function setpei($order_id=null)
    {
        if(!$order_id || !($order = K::M('other/order')->detail((int)$order_id))){
            $this->msgbox->add('订单不存在！',211);
        }else if(!$waimai = K::M('waimai/waimai')->detail($order['shop_id'])){
            $this->msgbox->add('外卖商家不存在',212);
        }else if(!$group = K::M('pei/group')->detail($waimai['group_id'])){
            $this->msgbox->add('商家绑定的配送站不存在或者已关闭',213);
        }else if($data = $this->checksubmit('data')){
            if($data['tip'] < 0){
                $this->msgbox->add('非法的小费',211);
            }else{
                K::M('other/order')->setpei($order_id, $data['tip'], $order['shop_id'], 'admin');
            }
        }else{
            $this->pagedata['group'] = $group;
            $this->pagedata['order'] = $order;
            $this->tmpl = 'admin:other/order/setpei.html';
        }
    }

    public function setconfirm($order_id=0)
    {
        if (!$order_id = (int)$order_id) {
            $this->msgbox->add(L('错误的订单!'), 211);
        }elseif (!$order = K::M('other/order')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的订单!'), 212);
        }else{
            K::M('other/order')->setconfirm($order_id, $order['shop_id'], 'admin');
        }    
    }

    public function cancelpei($order_id=0)
    {
        if (!$order_id = (int)$order_id) {
            $this->msgbox->add(L('错误的订单!'), 211);
        }elseif (!$order = K::M('other/order')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的订单!'), 212);
        }else{
            K::M('other/order')->cancelpei($order_id, $order['shop_id'], 'admin');
        }    
    }

    public function addtip($order_id=0){
        if (!$order_id = (int)$order_id) {
            $this->msgbox->add(L('错误的订单!'), 211);
        }else if(!$order = K::M('other/order')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的订单!'), 212);
        }else if(!$p_order = K::M('paotui/order')->detail($order['p_order_id'])){
            $this->msgbox->add(L('配送记录不存在!'), 213);
        }else if($data = $this->checksubmit('data')){
            if($data['tip'] < 0){
                $this->msgbox->add('非法的小费',214);
            }else{
                K::M('other/order')->addtip($order_id, $order['shop_id'], $data['tip'], 'admin');
            }
        }else{
            $this->pagedata['p_order'] = $p_order;
            $this->pagedata['order'] = $order;
            $this->tmpl = 'admin:other/order/addtip.html';
        }
    }   
    
    //同意退款
    public function agree($order_id=0)
    {
        if (!$order_id = (int)$order_id) {
            $this->msgbox->add(L('错误的订单!'), 211);
        }elseif (!$order = K::M('other/order')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的订单!'), 212);
        }else{
            K::M('other/order')->agree_order_lite($order_id, $order['shop_id']);
        }
    }

    //拒绝退款
    public function refuse($order_id=0)
    {
        if (!$order_id = (int)$order_id) {
            $this->msgbox->add(L('错误的订单!'), 211);
        }elseif (!$order = K::M('other/order')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的订单!'), 213);
        }else{
            K::M('other/order')->disagree_refund_lite($order_id, $order['shop_id']);
        }
    }
   
}