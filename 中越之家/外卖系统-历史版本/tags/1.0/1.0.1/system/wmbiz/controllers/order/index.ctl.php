<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Order_Index extends Ctl
{

    public function get_shop_fee($order,$waimai_order,$shop){
        //修改外卖对账
         //1:仅商品价格
        //2:商品价格加上打包费
        //3:实际结算
        if($order['order_status']==8){
            $waimai_bills_log = K::M('waimai/billslog')->find(array('bills_number'=>$order['order_id']));
            $fee = $waimai_bills_log['fee'];
            $shop_amount = $waimai_bills_log['amount']+$fee;

        }else{
            if($order['online_pay']==1){
                //2017-12-25 外卖3.7 新增自提单单独结算比例 begin
                if($order['pei_type']==3&&$this->waimai_shop['is_ztsp']==1){
                    $this->waimai_shop['waimai_bl'] = $this->waimai_shop['zt_bl'];
                }
                //2017-12-25 外卖3.7 新增自提单单独结算比例 end
                $staff_amount = $order['pei_amount'];
                $shop_amount = $order['amount'] + $order['hongbao'] + $order['money']+$waimai_order['roof_amount']+$waimai_order['first_roof']-$staff_amount+$order['peicard_youhui'];
                if($this->waimai_shop['jiesuan_type']==1){
                    $fee =  number_format((($order['total_price']-$waimai_order['freight']-$waimai_order['package_price']) * $this->waimai_shop['waimai_bl'])/100, 2, '.', '');
                    if($fee<=0){
                        $fee = 0;
                    }
                }else if($this->waimai_shop['jiesuan_type']==2){
                    $fee =  number_format((($order['total_price']-$waimai_order['freight']) * $this->waimai_shop['waimai_bl'])/100, 2, '.', '');
                    if($fee<=0){
                        $fee = 0;
                    }
                }else{
                    $fee =  number_format(($shop_amount * $this->waimai_shop['waimai_bl'])/100, 2, '.', '');
                    if($shop_amount<=0){
                        $fee = 0;
                    }
                }
            }else if($order['online_pay']==0&&$order['pei_type']==1){
                $staff_amount = $order['pei_amount'];
                $shop_amount = $order['total_price']-$order['coupon']-$order['order_youhui']-$staff_amount-$order['first_youhui']+$waimai_order['roof_amount']+$waimai_order['first_roof']-$order['discount_youhui']-$order['huangou_youhui'];
                if($this->waimai_shop['jiesuan_type']==1){
                    $fee =  number_format((($order['total_price']-$waimai_order['freight']-$waimai_order['package_price']) * $this->waimai_shop['waimai_bl'])/100, 2, '.', '');
                    if($fee<=0){
                        $fee = 0;
                    }
                }else if($this->waimai_shop['jiesuan_type']==2){
                    $fee =  number_format((($order['total_price']-$waimai_order['freight']) * $this->waimai_shop['waimai_bl'])/100, 2, '.', '');
                    if($fee<=0){
                        $fee = 0;
                    }
                }else{
                    $fee =  number_format(($shop_amount * $this->waimai_shop['waimai_bl'])/100, 2, '.', '');
                    if($fee<=0){
                        $fee = 0;
                    }
                }
            }else{
                $shop_amount = $order['total_price']-$order['coupon']-$order['order_youhui']-$order['first_youhui']+$waimai_order['roof_amount']+$waimai_order['first_roof']-$order['discount_youhui']-$order['huangou_youhui'];
                $fee = 0;
            }
        }
        return array('fee'=>$fee,'shop_amount'=>$shop_amount);
    }

    public function index($page=1)
    {
        $this->reminder($page);
    }
    
    // 外卖订单(催单)
    public function reminder($page)
    {
        $filter = $pager = $items = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;

        $filter['order_status'] = array(1,2,3);// 催单
        $filter['pei_type'] = array(0, 1, 3);
        $filter['refund_status'] = 0;
        $filter['closed'] = 0;
        $filter['from'] = 'waimai';
        $filter['shop_id'] = $this->shop_id;
        if ($keyword = (int)$this->GP('keyword')) {// 头部搜索
            $filter[':OR'] = array('mobile' => 'LIKE:%'.$keyword.'%', 'order_id' => 'LIKE:%'.$keyword.'%');
        }
        $orderby = array('pei_time'=>'ASC', 'order_id'=>'ASC');// 期望送达时间 第一排序
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            if ($SO['orderby'] == 1) {
                $orderby = array('dateline'=>'ASC'); // 下单时间
            }
        }
        if($items = K::M('order/order')->items_join_cuilog($filter, $orderby, $page, $limit, $count)){
            $cui_order_ids = array();
            $shop_ids = array();
            $order_ids = array();
            $staff_ids = array();
            foreach ($items as $kk=>$vv){
                $shop_ids[] = $vv['shop_id'];
                $order_ids[] = $vv['order_id'];
                $items[$kk] = K::M('waimai/order')->get_label($vv);
                if($v['staff_id']){
                    $staff_ids[$v['staff_id']] = $v['staff_id'];
                }
            }
            foreach($items as $k => $v){
                $cui_order_ids[$v['order_id']] = $v['order_id'];
                if($v['pei_time'] > 0) {
                    $items[$k]['pei_time_label'] = date("Y-m-d H:i", $v['pei_time']);
                }else{
                    $items[$k]['pei_time_label'] = L('尽快送达');
                }
                $items[$k]['products'] = array();
            }
            if($product_list = K::M('waimai/orderproduct')->items(array('order_id'=>$cui_order_ids), null, 1, 500)){
                foreach($product_list as $k => $v){
                    $shuxin = "";
                    foreach($v['specification'] as $vvt){
                        $shuxin.="+".$vvt['val'];
                    }
                    $v['shuxin'] = $shuxin;
                    $items[$v['order_id']]['products'][] = $v;
                }
            }
            $waimai_order =K::M('waimai/order')->items_by_ids($order_ids);
            $waimai_shop =K::M('waimai/waimai')->items_by_ids($shop_ids);
            $staffs = K::M('staff/staff')->items_by_ids($staff_ids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['waimai_order'] = $waimai_order[$vv['order_id']];
                $items[$kk]['fee'] = $this->get_shop_fee($vv,$waimai_order[$vv['order_id']] ,$waimai_shop[$vv['shop_id']]);
                if($vv['pei_type']==1&&$vv['online_pay']==0){
                    $items[$kk]['amount'] = $vv['total_price']-$vv['first_youhui']-$vv['order_youhui']-$vv['coupon']+$waimai_order[$vv['order_id']]['first_roof']+$waimai_order[$vv['order_id']]['roof_amount']-$vv['pei_amount'];
                }
                $items[$kk]['staff'] = $staffs[$vv['staff_id']] ? $staffs[$vv['staff_id']] : array();
                $items[$kk]['products'] = K::M('waimai/orderproduct')->get_basketProducts($vv['products']);
            }
            $items = $this->group_by_uid($items);
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('order/index:index', array('{page}')), array('SO'=>$SO));
        }
        $filter_print = array();
        $filter_print['shop_id'] = $this->shop_id;
        $filter_print['status'] = 1;
        $count = K::M('shop/print')->count($filter_print);
        $this->pagedata['is_print'] = $count;

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'order/reminder.html';
    }

    //催单回复
    public function cui_reply()
    {
        if (!$order_id = (int)$this->GP('order_id')) {
            $this->msgbox->add('参数错误!', 211);
        }elseif (!$cuilog = K::M('order/cuilog')->find(array('order_id'=>$order_id),array('log_id'=>'desc'))) {
            $this->msgbox->add('没有要回复的催单!', 212);
        }elseif ($cuilog['reply_time'] > 0) {
            $this->msgbox->add('没有要回复的催单!', 213);
        }elseif ($cuilog['shop_id'] != $this->shop_id) {
            $this->msgbox->add('请勿操作他人订单', 214);
        }elseif (!$reply = trim(htmlspecialchars($this->GP('reply')))) {
            $this->msgbox->add('回复内容不能为空!', 215);
        }elseif (K::M('order/cuilog')->update($cuilog['log_id'], array('reply'=>$reply, 'reply_time'=>__TIME))) {
            $order = K::M('order/order')->detail($order_id);
            $log_data = array();
            $log_data['uid'] = $order['uid'];
            $log_data['title'] = '商家回复催单';
            $log_data['content'] = "商家回复您的订单ID(".$order_id.")催单:".$reply;
            $log_data['type'] = 2;
            $log_data['is_read'] = 0;
            $log_data['order_id'] = $order_id;
            $log_data['can_id'] = 0;
            K::M('member/message')->create($log_data);

            $this->msgbox->add('催单回复成功');
        }else{
            $this->msgbox->add('催单回复失败', 216);
        }
    }

    // 待接单
    public function receive($page=1)
    {
        $filter = $pager = $items = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;

        $filter['order_status'] = 0;// 待接单
        $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
        $filter['pei_type'] = array(0, 1, 3);
        $filter['refund_status'] = 0;
        $filter['closed'] = 0;
        $filter['from'] = 'waimai';
        $filter['shop_id'] = $this->shop_id;
        if ($keyword = (int)$this->GP('keyword')) {// 头部搜索
            $filter[':OR'] = array('mobile' => 'LIKE:%'.$keyword.'%', 'order_id' => 'LIKE:%'.$keyword.'%');
        }
        $orderby = array('pei_time'=>'ASC');// 期望送达时间 第一排序
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            if ($SO['orderby'] == 1) {
                $orderby = array('dateline'=>'ASC'); // 下单时间
            }

        }
        if($items = K::M('order/order')->items($filter, $orderby, $page, $limit, $count)){
            $order_ids = array();
            $shop_ids = array();
            foreach ($items as $kk=>$vv){
                $shop_ids[] = $vv['shop_id'];
                $items[$kk] = K::M('waimai/order')->get_label($vv);
            }
            foreach($items as $k=>$v){
                $order_ids[$v['order_id']] = $v['order_id'];
                $items[$k]['products'] = array();
            }

            if ($waimai_order_list = K::M('waimai/order')->items_by_ids($order_ids)) {
                foreach ($waimai_order_list as $k=>$v) {
                    $items[$k] = array_merge($items[$v['order_id']], $v);
                }
            }
            
            if($product_list = K::M('waimai/orderproduct')->items(array('order_id'=>$order_ids), null, 1, 500)){
                foreach($product_list as $k => $v){
                    $shuxin = "";
                    foreach($v['specification'] as $vvt){
                        $shuxin.="+".$vvt['val'];
                    }
                    $v['shuxin'] = $shuxin;
                    $items[$v['order_id']]['products'][] = $v;
                }
            }
            $items = $this->group_by_uid($items);
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('order/index:receive', array('{page}')), array('SO'=>$SO));
            $waimai_order =K::M('waimai/order')->items_by_ids($order_ids);
            $waimai_shop =K::M('waimai/waimai')->items_by_ids($shop_ids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['waimai_order'] = $waimai_order[$vv['order_id']];
                $items[$kk]['fee'] = $this->get_shop_fee($vv,$waimai_order[$vv['order_id']] ,$waimai_shop[$vv['shop_id']]);
                if($vv['pei_type']==1&&$vv['online_pay']==0){
                    $items[$kk]['amount'] = $vv['total_price']-$vv['first_youhui']-$vv['order_youhui']-$vv['coupon']+$waimai_order[$vv['order_id']]['first_roof']+$waimai_order[$vv['order_id']]['roof_amount']-$vv['pei_amount'];
                }
                $items[$kk]['products'] = K::M('waimai/orderproduct')->get_basketProducts($vv['products']);
            }
        }

        $filter_print = array();
        $filter_print['shop_id'] = $this->shop_id;
        $filter_print['status'] = 1;
        $count = K::M('shop/print')->count($filter_print);
        $this->pagedata['is_print'] = $count;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'order/receive.html';
    }

    public function jiedan($order_id,$type=2)
    {
        if(!$order_id = (int)$order_id){
            $this->msgbox->add(L('订单不存在'), 211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add(L('订单不存在或已被删除'), 212);
        }else if($order['shop_id'] != $this->shop_id){
            $this->msgbox->add(L('请勿操作他人订单'), 213);
        }else if($order['from'] != 'waimai'){
            $this->msgbox->add(L('非法操作'), 214);
        }else if($order['pei_type'] == 2){
            $this->msgbox->add(L('代购订单不可接单'), 216);
        }else if($order['online_pay'] && !$order['pay_status']){
            $this->msgbox->add(L('订单未支付不可接单'), 217);
        }else if((int)$order['order_status'] !== 0){
            $this->msgbox->add(L('订单状态不可接单'), 218);
        }else {
            if(K::M('order/order')->update($order_id, array('order_status'=>$type,'lasttime'=>__TIME))){
                K::M('order/time')->update($order_id,array('shop_jiedan_time'=>__TIME));
                //自动打印订单判断 todo...
                $log = '商家已接单(订单号:'.$order_id.')';
                K::M('order/log')->create(array('from'=>'shop', 'log'=>$log, 'order_id'=>$order_id));
                K::M('waimai/log')->create(array('from'=>'shop', 'log'=>$log, 'order_id'=>$order_id, 'type'=>3));//-1取消，0其他，1下单，2支付，3接单，4配送，5送达，6确认完成 7.申请退款 8.已退款 9.拒绝退款
                //通知用户,APP推送 weixin模板消息
                K::M('order/order')->send_member('商家已经接单', sprintf("您在[%s]下的订单(%s)，商家已接单", $this->waimai_shop['title'], $order_id), $order);
                $data = K::M('order/order')->localprint($order_id);
                $this->msgbox->set_data('data',$data);
                $this->msgbox->add('接单成功！');
                $this->msgbox->json();
            }else{
                $this->msgbox->add('接单失败',219);
            }
        }
    }
    
    public function auto_jiedan($order_id){
        if(!$order_id = (int)$order_id){
            $this->msgbox->add(L('订单不存在'), 211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add(L('订单不存在或已被删除'), 212);
        }else if($order['shop_id'] != $this->shop_id){
            $this->msgbox->add(L('请勿操作他人订单'), 213);
        }else if($order['from'] != 'waimai'){
            $this->msgbox->add(L('非法操作'), 214);
        }else if($order['pei_type'] == 2){
            $this->msgbox->add(L('代购订单不可接单'), 216);
        }else if($order['online_pay'] && !$order['pay_status']){
            $this->msgbox->add(L('订单未支付不可接单'), 217);
        }else if((int)$order['order_status'] !== 0){
            $this->msgbox->add(L('订单状态不可接单'), 218);
        }else {
            if($order['pei_time']>0){
                $status = 1;
            }else{
                $status = 2;
            }
            if(K::M('order/order')->update($order_id, array('order_status'=>$status,'lasttime'=>__TIME))){
                K::M('order/time')->update($order_id,array('shop_jiedan_time'=>__TIME));
                //自动打印订单判断 todo...
                $log = '商家已接单(订单号:'.$order_id.')';
                K::M('order/log')->create(array('from'=>'shop', 'log'=>$log, 'order_id'=>$order_id));
                K::M('waimai/log')->create(array('from'=>'shop', 'log'=>$log, 'order_id'=>$order_id, 'type'=>3));//-1取消，0其他，1下单，2支付，3接单，4配送，5送达，6确认完成 7.申请退款 8.已退款 9.拒绝退款
                //通知用户,APP推送 weixin模板消息
                K::M('order/order')->send_member('商家已经接单', sprintf("您在[%s]下的订单(%s)，商家已接单", $this->waimai_shop['title'], $order_id), $order);
                $data = K::M('order/order')->localprint($order_id);
                $this->msgbox->set_data('data',$data);
                $this->msgbox->json();
                $this->msgbox->add('接单成功！');
            }else{
                $this->msgbox->add('接单成功失败',219);
            }            
        }
    }

    //取消订单
    public function cancel()
    {
        if(!$order_id = (int)$this->GP('order_id')){
            $this->msgbox->add(L('订单不存在'), 211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add(L('订单不存在或已被删除'), 212);
        }else if($order['shop_id'] != $this->shop_id || $order['from'] != 'waimai' || $order['pei_type'] == 2){
            $this->msgbox->add(L('非法操作'), 213);
        }else if($order['order_status'] != 0 ){
            $this->msgbox->add(L('订单状态不可取消'), 214);
        }else if(K::M('order/order')->cancel($order_id, $order, 'shop')){
            $log = '商家取消订单(订单号:'.$order['order_id'].')';
            K::M('waimai/log')->create(array('from'=>'shop', 'log'=>$log, 'order_id'=>$order_id, 'type'=>-1));//-1取消，0其他，1下单，2支付，3接单，4配送，5送达，6确认完成 7.申请退款 8.已退款 9.拒绝退款 
            $this->msgbox->add('取消订单成功');
        }
    }

    // 待配送
    public function pei($page)
    {
        $filter = $pager = $items = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $time = strtotime(date('Y-m-d'))+86399;

        $filter['order_status'] = array(2);// 待配送
        $filter[':OR'] = array(
            'pay_status'=>1,
            'online_pay'=>0,
        );// 已付款 || 货到付款
        $filter['pei_type'] = array(0, 1);
      //  $filter[':SQL'] = "refund_status != '1' AND (pei_time = '0' OR (  pei_time < ".$time." AND pei_time>".strtotime(date('Y-m-d'))."))";
        $filter['refund_status'] = 0;
        $filter['closed'] = 0;
        $filter['from'] = 'waimai';
        $filter['shop_id'] = $this->shop_id;
        if ($keyword = (int)$this->GP('keyword')) {// 头部搜索
            $filter[':OR'] = array('mobile' => 'LIKE:%'.$keyword.'%', 'order_id' => 'LIKE:%'.$keyword.'%');
        }
        $orderby = array('pei_time'=>'ASC', 'order_id'=>'ASC');// 期望送达时间 第一排序
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            if ($SO['orderby'] == 1) {
                $orderby = array('dateline'=>'ASC'); // 下单时间
            }
        }
        if($items = K::M('order/order')->items($filter, $orderby, $page, $limit, $count)){
            foreach ($items as $kk=>$vv){
                $items[$kk] = K::M('waimai/order')->get_label($vv);
            }
            $order_ids = array();
            $shop_ids = array();
            $staff_ids = array();
            foreach($items as $k=>$v){
                $shop_ids[] = $v['shop_id'];
                $order_ids[$v['order_id']] = $v['order_id'];
                $items[$k]['products'] = array();
                if($v['staff_id']){
                    $staff_ids[$v['staff_id']] = $v['staff_id'];
                }
            }

            if ($waimai_order_list = K::M('waimai/order')->items_by_ids($order_ids)) {
                foreach ($waimai_order_list as $k=>$v) {
                    $items[$k] = array_merge($items[$v['order_id']], $v);
                }
            }
            
            if($product_list = K::M('waimai/orderproduct')->items(array('order_id'=>$order_ids), null, 1, 500)){
                foreach($product_list as $k => $v){
                    $shuxin = "";
                    foreach($v['specification'] as $vvt){
                        $shuxin.="+".$vvt['val'];
                    }
                    $v['shuxin'] = $shuxin;
                    $items[$v['order_id']]['products'][] = $v;
                }
            }
            $waimai_order =K::M('waimai/order')->items_by_ids($order_ids);
            $waimai_shop =K::M('waimai/waimai')->items_by_ids($shop_ids);
            $staffs = K::M('staff/staff')->items_by_ids($staff_ids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['waimai_order'] = $waimai_order[$vv['order_id']];
                $items[$kk]['fee'] = $this->get_shop_fee($vv,$waimai_order[$vv['order_id']] ,$waimai_shop[$vv['shop_id']]);
                if($vv['pei_type']==1&&$vv['online_pay']==0){
                    $items[$kk]['amount'] = $vv['total_price']-$vv['first_youhui']-$vv['order_youhui']-$vv['coupon']+$waimai_order[$vv['order_id']]['first_roof']+$waimai_order[$vv['order_id']]['roof_amount']-$vv['pei_amount'];
                }
                $items[$kk]['staff'] = $staffs[$vv['staff_id']] ? $staffs[$vv['staff_id']] : array();
                $items[$kk]['products'] = K::M('waimai/orderproduct')->get_basketProducts($vv['products']);
            }
            $items = $this->group_by_uid($items);
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('order/index:pei', array('{page}')), array('SO'=>$SO));
        }

        $filter_print = array();
        $filter_print['shop_id'] = $this->shop_id;
        $filter_print['status'] = 1;
        $count = K::M('shop/print')->count($filter_print);
        $this->pagedata['is_print'] = $count;
        $this->pagedata['pei_type'] = $this->waimai_shop['pei_type'];
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'order/pei.html';
    }

    //商家配送 或 骑手配送
    public function setpei($order_id, $pei_type)
    {
        $pei_type = $pei_type ? 1 : 0;
        if(!$order_id = (int)$order_id){
            $this->msgbox->add('参数不正确',210);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add('订单不存在或已被删除',211);
        }else if($order['shop_id'] != $this->shop_id){
            $this->msgbox->add('请勿操作他人订单',212);
        }else if($order['from'] != 'waimai'){
            $this->msgbox->add('非法操作',213);
        }else if($order['staff_id'] > 0){
            $this->msgbox->add('已有骑手接单不可操作',214);
        }else if(!in_array($order['pei_type'], array(0, 1))){
            $this->msgbox->add('该订单不可配送',215);
        }else if(!in_array($order['order_status'], array(1, 2))){
            $this->msgbox->add('该订单不可配送',216);
        }else if(K::M('order/order')->update($order_id, array('order_status' => 3, 'pei_type'=>$pei_type, 'lasttime'=>__TIME))){
            //订单日志 v-1取消，0其他，1下单，2支付，3接单，4配送，5送达，6确认完成
            K::M('order/log')->create(array('order_id' => $order_id, 'from' => 'shop', 'log' => L('商家开始配送'), 'type' => 4));
            K::M('waimai/log')->create(array('from'=>'shop', 'log' => L('商家开始配送'), 'order_id'=>$order_id, 'type'=>4));//-1取消，0其他，1下单，2支付，3接单，4配送，5送达，6确认完成 7.申请退款 8.已退款 9.拒绝退款 
            //通知用户,APP推送 weixin模板消息
            $title = $content = sprintf("您在[%s]下的订单(%s)，商家已经开始配送", $this->waimai_shop['title'], $order_id);
            K::M('order/order')->send_member($title, $content, $order);
            $this->msgbox->add('设置配送状态成功');

        }
    }

    // 配送中
    public function delivery($page)
    {
        $filter = $pager = $items = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;

        $filter['order_status'] = 3;// 配送中
        //$filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
        $filter['pei_type'] = array(0, 1);
        $filter[':SQL'] = "refund_status != '1'";
        $filter['closed'] = 0;
        $filter['from'] = 'waimai';
        $filter['shop_id'] = $this->shop_id;
        if ($keyword = (int)$this->GP('keyword')) {// 头部搜索
            $filter[':OR'] = array('mobile' => 'LIKE:%'.$keyword.'%', 'order_id' => 'LIKE:%'.$keyword.'%');
        }
        $orderby = array('pei_time'=>'ASC', 'order_id'=>'ASC');// 期望送达时间 第一排序
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            if ($SO['orderby'] == 1) {
                $orderby = array('dateline'=>'ASC'); // 下单时间
            }
        }
        if($items = K::M('order/order')->items($filter, $orderby, $page, $limit, $count)){
            foreach ($items as $kk=>$vv){
                $items[$kk] = K::M('waimai/order')->get_label($vv);
            }
            $order_ids = array();
            $shop_ids = array();
            $staff_ids = array();
            foreach($items as $k=>$v){
                $shop_ids[] = $v['shop_id'];
                $order_ids[$v['order_id']] = $v['order_id'];
                $items[$k]['products'] = array();
                if($v['staff_id']){
                    $staff_ids[$v['staff_id']] = $v['staff_id'];
                }
            }

            if ($waimai_order_list = K::M('waimai/order')->items_by_ids($order_ids)) {
                foreach ($waimai_order_list as $k=>$v) {
                    $items[$k] = array_merge($items[$v['order_id']], $v);
                }
            }
            
            if($product_list = K::M('waimai/orderproduct')->items(array('order_id'=>$order_ids), null, 1, 500)){
                foreach($product_list as $k => $v){
                    $shuxin = "";
                    foreach($v['specification'] as $vvt){
                        $shuxin.="+".$vvt['val'];
                    }
                    $v['shuxin'] = $shuxin;
                    $items[$v['order_id']]['products'][] = $v;
                }
            }

            $waimai_order =K::M('waimai/order')->items_by_ids($order_ids);
            $waimai_shop =K::M('waimai/waimai')->items_by_ids($shop_ids);
            $staffs = K::M('staff/staff')->items_by_ids($staff_ids);

            foreach ($items as $kk=>$vv){
                $items[$kk]['waimai_order'] = $waimai_order[$vv['order_id']];
                $items[$kk]['fee'] = $this->get_shop_fee($vv,$waimai_order[$vv['order_id']] ,$waimai_shop[$vv['shop_id']]);
                if($vv['pei_type']==1&&$vv['online_pay']==0){
                    $items[$kk]['amount'] = $vv['total_price']-$vv['first_youhui']-$vv['order_youhui']-$vv['coupon']+$waimai_order[$vv['order_id']]['first_roof']+$waimai_order[$vv['order_id']]['roof_amount']-$vv['pei_amount'];
                }
                $items[$kk]['staff'] = $staffs[$vv['staff_id']] ? $staffs[$vv['staff_id']] : array();
                $items[$kk]['products'] = K::M('waimai/orderproduct')->get_basketProducts($vv['products']);
            }
            $items = $this->group_by_uid($items);
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('order/index:delivery', array('{page}')), array('SO'=>$SO));
        }
        $filter_print = array();
        $filter_print['shop_id'] = $this->shop_id;
        $filter_print['status'] = 1;
        $count = K::M('shop/print')->count($filter_print);
        $this->pagedata['is_print'] = $count;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'order/delivery.html';
    }

    // 退单
    public function refund($page)
    {
        $filter = $pager = $items = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;

        //$filter['order_status'] = array(1,2,3,4);// 允许退款的订单状态
        //$filter['refund_status'] = 1;// 退款申请
        $filter['refund_status'] = "<>:0";
        $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
        $filter['pei_type'] = array(0, 1, 3);
        $filter['closed'] = 0;
        $filter['from'] = 'waimai';
        $filter['shop_id'] = $this->shop_id;
        if ($keyword = (int)$this->GP('keyword')) {// 头部搜索
            $filter[':OR'] = array('mobile' => 'LIKE:%'.$keyword.'%', 'order_id' => 'LIKE:%'.$keyword.'%');
        }
        $orderby = array( 'order_id'=>'DESC','pei_time'=>'ASC');// 期望送达时间 第一排序
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            if ($SO['orderby'] == 1) {
                $orderby = array('dateline'=>'ASC'); // 下单时间
            }
        }
        if($items = K::M('order/order')->items($filter, $orderby, $page, $limit, $count)){
            foreach ($items as $kk=>$vv){
                $items[$kk] = K::M('waimai/order')->get_label($vv);
            }
            $order_ids = array();
            $shop_ids = array();
            $staff_ids = array();
            foreach($items as $k=>$v){
                $order_ids[$v['order_id']] = $v['order_id'];
                $items[$k]['products'] = array();
                $items[$k]['refund_info'] = array('reflect'=>"",'dateline'=>0);
                $shop_ids[] = $v['shop_id'];
                if($v['staff_id']){
                    $staff_ids[$v['staff_id']] = $v['staff_id'];
                }
            }

            if ($waimai_order_list = K::M('waimai/order')->items_by_ids($order_ids)) {
                foreach ($waimai_order_list as $k=>$v) {
                    $items[$k] = array_merge($items[$v['order_id']], $v);
                }
            }

            // 获取异常（退款）订单信息
            if ($refund_list = K::M('waimai/order/refund')->items_by_ids($order_ids)) {
                foreach ($items as $k => $v) {
                    if ($v['refund_status'] == 1 && $refund_list[$k]) {
                        $items[$k]['refund_info'] = $this->filter_fields('reflect,dateline', $refund_list[$k]);
                    }
                }
            }
            if($product_list = K::M('waimai/orderproduct')->items(array('order_id'=>$order_ids), null, 1, 500)){
                foreach($product_list as $k => $v){
                    $shuxin = "";
                    foreach($v['specification'] as $vvt){
                        $shuxin.="+".$vvt['val'];
                    }
                    $v['shuxin'] = $shuxin;
                    $items[$v['order_id']]['products'][] = $v;
                }
            }

            $waimai_order =K::M('waimai/order')->items_by_ids($order_ids);
            $waimai_shop =K::M('waimai/waimai')->items_by_ids($shop_ids);
            $staffs = K::M('staff/staff')->items_by_ids($staff_ids);

            foreach ($items as $kk=>$vv){
                $items[$kk]['waimai_order'] = $waimai_order[$vv['order_id']];
                $items[$kk]['fee'] = $this->get_shop_fee($vv,$waimai_order[$vv['order_id']] ,$waimai_shop[$vv['shop_id']]);
                if($vv['pei_type']==1&&$vv['online_pay']==0){
                    $items[$kk]['amount'] = $vv['total_price']-$vv['first_youhui']-$vv['order_youhui']-$vv['coupon']+$waimai_order[$vv['order_id']]['first_roof']+$waimai_order[$vv['order_id']]['roof_amount']-$vv['pei_amount'];
                }
                $items[$kk]['staff'] = $staffs[$vv['staff_id']] ? $staffs[$vv['staff_id']] : array();
                $items[$kk]['products'] = K::M('waimai/orderproduct')->get_basketProducts($vv['products']);
            }
            $items = $this->group_by_uid($items);
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('order/index:refund', array('{page}')), array('SO'=>$SO));
        }
        $filter_print = array();
        $filter_print['shop_id'] = $this->shop_id;
        $filter_print['status'] = 1;
        $count = K::M('shop/print')->count($filter_print);
        $this->pagedata['is_print'] = $count;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'order/refund.html';
    }

    //商家退款
    public function refund_agree($order_id)
    {
        if (!$order_id = (int)$order_id) {
            $this->msgbox->add(L('错误的订单!'), 211);
        }elseif (!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的退款订单!'), 212);
        }elseif (!in_array($order['order_status'], array(1,2,3,4)) && $order['refund_status'] != 1 && $order['closed'] != 0 && $order['from'] != 'waimai') {
            $this->msgbox->add(L('没有要操作的退款订单!'), 213);
        }elseif (!$refund_order = K::M('waimai/order/refund')->detail($order_id)) {
            $this->msgbox->add(L('没有要退款的订单!'), 214);
        }elseif (K::M('order/order')->refund($order)) {
            $this->msgbox->add(L('退款成功！'));
            $log_data = array();
            $log_data['uid'] = $order['uid'];
            $log_data['title'] = '商家同意退款';
            $log_data['content'] = "商家同意您的订单ID(".$order_id.")退款申请";
            $log_data['type'] = 2;
            $log_data['is_read'] = 0;
            $log_data['order_id'] = $order_id;
            $log_data['can_id'] = 0;
            K::M('member/message')->create($log_data);
        }else{
            $this->msgbox->add(L('退款失败！'), 214);
        }
    }

    //商家拒绝退款
    public function refused()
    {
        if (!$order_id = (int)$this->GP('order_id')) {
            $this->msgbox->add(L('错误的订单!'), 211);
        }elseif (!$reply = trim(htmlspecialchars($this->GP('reply')))) {
            $this->msgbox->add(L('请填写拒绝原因!'), 212);
        }elseif (!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的退款订单!'), 213);
        }elseif (!in_array($order['order_status'], array(1,2,3,4)) && $order['refund_status'] != 1 && $order['closed'] != 0 && $order['from'] != 'waimai') {
            $this->msgbox->add(L('没有要操作的退款订单!'), 214);
        }elseif (!$refund_order = K::M('waimai/order/refund')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的退款订单!'), 215);
        }elseif (K::M('order/order')->refund_refused($order, $reply)) {
            $log_data = array();
            $log_data['uid'] = $order['uid'];
            $log_data['title'] = '商家拒绝退款,拒绝理由:'.$reply;
            $log_data['content'] = "商家拒绝您的订单ID(".$order_id.")退款申请,拒绝理由:".$reply;
            $log_data['type'] = 2;
            $log_data['is_read'] = 0;
            $log_data['order_id'] = $order_id;
            $log_data['can_id'] = 0;
            K::M('member/message')->create($log_data);

            $this->msgbox->add(L('拒绝退款成功！'));
        }else{
            $this->msgbox->add(L('拒绝退款失败！'), 215);
        }
    }

    // 自提单
    public function ziti($page)
    {
        $filter = $pager = $items = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;

        $filter['order_status'] = array(0, 1,2);// 待接单、已接单
        $filter['refund_status'] = 0;
        $filter[':OR'] = array(
            'pay_status'=>1,
            'online_pay'=>0
        );
        //$filter['pay_status'] = 1;// 已付款
        $filter['pei_type'] = 3;// 自提
        $filter['closed'] = 0;
        $filter['from'] = 'waimai';
        $filter['shop_id'] = $this->shop_id;
        if ($keyword = (int)$this->GP('keyword')) {// 头部搜索
            $filter[':OR'] = array('mobile' => 'LIKE:%'.$keyword.'%', 'order_id' => 'LIKE:%'.$keyword.'%');
        }
        $orderby = array('pei_time'=>'ASC', 'order_id'=>'ASC');// 期望送达时间 第一排序
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            if ($SO['orderby'] == 1) {
                $orderby = array('dateline'=>'ASC'); // 下单时间
            }
        }
        if($items = K::M('order/order')->items($filter, $orderby, $page, $limit, $count)){
            foreach ($items as $kk=>$vv){
                $items[$kk] = K::M('waimai/order')->get_label($vv);
            }
            $order_ids = array();
            $shop_ids = array();
            foreach($items as $k=>$v){
                $order_ids[$v['order_id']] = $v['order_id'];
                $items[$k]['products'] = array();
                $shop_ids[] = $v['shop_id'];
            }

            if ($waimai_order_list = K::M('waimai/order')->items_by_ids($order_ids)) {
                foreach ($waimai_order_list as $k=>$v) {
                    $items[$k] = array_merge($items[$v['order_id']], $v);
                }
            }

            if($product_list = K::M('waimai/orderproduct')->items(array('order_id'=>$order_ids), null, 1, 500)){
                foreach($product_list as $k => $v){
                    $shuxin = "";
                    foreach($v['specification'] as $vvt){
                        $shuxin.="+".$vvt['val'];
                    }
                    $v['shuxin'] = $shuxin;
                    $items[$v['order_id']]['products'][] = $v;
                }
            }
            $waimai_order =K::M('waimai/order')->items_by_ids($order_ids);
            $waimai_shop =K::M('waimai/waimai')->items_by_ids($shop_ids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['waimai_order'] = $waimai_order[$vv['order_id']];
                $items[$kk]['fee'] = $this->get_shop_fee($vv,$waimai_order[$vv['order_id']] ,$waimai_shop[$vv['shop_id']]);
                if($vv['pei_type']==1&&$vv['online_pay']==0){
                    $items[$kk]['amount'] = $vv['total_price']-$vv['first_youhui']-$vv['order_youhui']-$vv['coupon']+$waimai_order[$vv['order_id']]['first_roof']+$waimai_order[$vv['order_id']]['roof_amount']-$vv['pei_amount'];
                }
                $items[$kk]['products'] = K::M('waimai/orderproduct')->get_basketProducts($vv['products']);
            }
            $items = $this->group_by_uid($items);
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('order/index:ziti', array('{page}')), array('SO'=>$SO));
        }
        $filter_print = array();
        $filter_print['shop_id'] = $this->shop_id;
        $filter_print['status'] = 1;
        $count = K::M('shop/print')->count($filter_print);
        $this->pagedata['is_print'] = $count;
        //echo '<pre>';
      //  print_r($this->system->db->SQLLOG());exit;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'order/ziti.html';
    }

    //商家制作完成
    public function complete($order_id)
    {
        if(!$order_id = (int)$order_id){
            $this->msgbox->add('参数不正确',210);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add('订单不存在或已被删除',211);
        }else if($order['shop_id'] != $this->shop_id){
            $this->msgbox->add('请勿操作他人订单',212);
        }else if($order['from'] != 'waimai' || $order['staff_id'] > 0){
            $this->msgbox->add('非法操作',213);
        }else if($order['order_status'] != 1 || $order['pei_type'] != 3){
            $this->msgbox->add('非法操作',216);
        }else if(K::M('order/order')->update($order_id, array('order_status' => 2, 'lasttime'=>__TIME))){
            //通知用户,APP推送 weixin模板消息
            $title = $content = sprintf("您在[%s]下的订单(%s)，商家已经制作完成，请及时取餐", $this->waimai_shop['title'], $order_id);
            K::M('order/order')->send_member($title, $content, $order);
            $this->msgbox->add('制作已完成，等待用户取餐');
        }
    }

    // 历史订单
    public function history($page)
    {
        $filter = $pager = $items = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
      //  $filter['order_status'] = array(-2, -1, 0, 1, 2, 3, 4, 8);// -2:退款取消订单, -1:已取消, 0:未处理, 1:已接单（跑腿订单已接单）, 2:配货中  3：配送开始（跑腿服务中），4：配送完成（跑腿服务完成），8：订单完成
        $filter['pei_type'] = array(0, 1, 3);// 0:自己送，1:跑腿送,  2:代购(仅仅外卖), 3:用户自提单
        $filter['closed'] = 0;
        $filter['from'] = 'waimai';
        $filter['shop_id'] = $this->shop_id;
        $filter[':SQL'] =  "((`online_pay`=1 AND `pay_status`=1 ) OR (`online_pay`=0))";

        if ($keyword = (int)$this->GP('keyword')) {// 头部搜索
            $filter[':OR'] = array('mobile' => 'LIKE:%'.$keyword.'%', 'order_id' => 'LIKE:%'.$keyword.'%');
        }
        $orderby = array('order_id'=>'DESC');// 订单最后更新时间 第一排序
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            if (isset($SO['online_pay'])) {
                switch ($SO['online_pay']) {
                    case '1':
                        $filter['online_pay'] = 1; // 在线支付
                        break;
                    case '2':
                        $filter['online_pay'] = 0; // 线下支付
                        break;
                }
            }
            if (isset($SO['order_status'])) {
                switch ($SO['order_status']) {
                    case '1':
                        $filter['order_status'] = array(0, 1, 2, 3, 4, 8); // 有效订单
                        break;
                    case '2':
                        $filter['order_status'] = array(-1,-2); // 无效订单
                        break;
                }
            }
            if (isset($SO['pei_type'])) {
                switch ($SO['pei_type']) {
                    case '1':
                        $filter['pei_type'] = 0; // 商家配送
                        break;
                    case '2':
                        $filter['pei_type'] = 1; // 第三方配送
                        break;
                    case '3':
                        $filter['pei_type'] = 3; // 自提单
                        break;
                }
            }
        }
        if($items = K::M('order/order')->items($filter, $orderby, $page, $limit, $count)){
            
          /*  echo '<pre>';
            print_r($items);exit;*/
            $order_ids = $cui_order_ids = array();
            $shop_ids = $staff_ids = array();
            foreach($items as $k=>$v){
                $shop_ids[] = $v['shop_id'];
                $order_ids[$v['order_id']] = $v['order_id'];
                $items[$k]['products'] = $items[$k]['refund_info'] = array();
                if ($v['cui_time'] > 0) {// 催单
                    $cui_order_ids[$v['order_id']] = $v['order_id'];
                }
                if($v['staff_id']){
                    $staff_ids[$v['staff_id']] = $v['staff_id'];
                }
            }
            // 催单
            if (!$cui_logs = K::M('order/cuilog')->items_group_by_order_id($cui_order_ids)) {
                $cui_logs = array();
            }
            // 获取异常（退款）订单信息
            if (!$refund_list = K::M('waimai/order/refund')->items_by_ids($order_ids)) {
                $refund_list = array();
            }

            $coupon_id = array();
            foreach ($items as $k => $v) {
                $items[$k]= K::M("waimai/order")->get_label($v);
                if ($v['refund_status'] != 0 && $refund_list[$k]) { // 0:正常,1:退款中, 2:退款完成,-1:退款失败,3:平台处理纠纷
                    $items[$k]['refund_info'] = $this->filter_fields('reflect,reply,dateline', $refund_list[$k]);
                }
                $items[$k]['cui_logs'] = $cui_logs[$k] ? $cui_logs[$k] : array(); // 催单
            }

            if ($waimai_order_list = K::M('waimai/order')->items_by_ids($order_ids)) {
                foreach ($waimai_order_list as $k=>$v) {
                    $items[$k] = array_merge($items[$v['order_id']], $v);
                    $items[$k]['waimai_order'] = $v;
                }
            }
            if($product_list = K::M('waimai/orderproduct')->items(array('order_id'=>$order_ids), null, 1, 500)){
                foreach($product_list as $k => $v){
                    $shuxin = "";
                    foreach($v['specification'] as $vvt){
                        $shuxin.="+".$vvt['val'];
                    }
                    $v['shuxin'] = $shuxin;
                    $items[$v['order_id']]['products'][] = $v;
                }
            }

            $waimai_shop = K::M('waimai/waimai')->items_by_ids($shop_ids);
            $staffs = K::M('staff/staff')->items_by_ids($staff_ids);

            foreach ($items as $kk=>$vv){
                $items[$kk] = K::M('order/order')->hideContact($vv);
                $items[$kk]['fee'] = $this->get_shop_fee($vv,$waimai_order_list[$vv['order_id']] ,$waimai_shop[$vv['shop_id']]);
                if($vv['pei_type']==1&&$vv['online_pay']==0){
                    $items[$kk]['amount'] = $vv['total_price']-$vv['first_youhui']-$vv['order_youhui']-$vv['coupon']+$waimai_order[$vv['order_id']]['first_roof']+$waimai_order[$vv['order_id']]['roof_amount']-$vv['pei_amount'];
                }
                $items[$kk]['staff'] = $staffs[$vv['staff_id']] ? $staffs[$vv['staff_id']] : array();
                $items[$kk]['products'] = K::M('waimai/orderproduct')->get_basketProducts($vv['products']);
            }

            $items = $this->group_by_uid($items);
            
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('order/index:history', array('{page}')), array('SO'=>$SO));
        }
        $filter_print = array();
        $filter_print['shop_id'] = $this->shop_id;
        $filter_print['status'] = 1;
        $count = K::M('shop/print')->count($filter_print);
        $this->pagedata['is_print'] = $count;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'order/history.html';
    }
    //确认送达
    public function delivery_confirm($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',220);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',221);
        }else if($order['shop_id']!=$this->shop_id){
            $this->msgbox->add('您无权操作该订单',222);
        }else if($order['order_status']!=3){
            $this->msgbox->add('该订单不能确认送达',223);
        }else if($order['from']!='waimai'){
            $this->msgbox->add('该订单不是外卖订单',224);
        }else if($order['pei_type']!=0){
            $this->msgbox->add('该订单不是商家配送订单，您不能确认送达',225);
        }else{
            if(K::M('order/order')->update($order_id,array('order_status'=>4))){
                $this->msgbox->add('确认送达成功');
            }else{
                $this->msgbox->add('确认送达失败',225);
            }
        }
    }

    // 自提订单核销
    public function setspend()
    {
        if($order_id = $this->GP('order_id')) {
            if(!$order_id){
                $this->msgbox->add('要核销的订单不存在', 211);
            }else if(!$order = K::M('waimai/order')->detail($order_id)){
                $this->msgbox->add(L('无效的订单'), 218);
            }else if($order['order_status'] == 8){
                $this->msgbox->add(L('该订单已完成'), 221);
            }else if($order['order_status'] < 0){
                $this->msgbox->add(L('订单已取消'), 220);
            }else if(!$spend_number = $this->GP('spend_number')){
                $this->msgbox->add(L('请输入自提码'), 213);
            }else if($order['spend_number'] != $spend_number){
                $this->msgbox->add(L('输入的自提码不正确'), 214);
            }else if($order['spend_status']){
                $this->msgbox->add(L('该订单已核销'), 221);
            }else if($order['shop_id'] != $this->shop_id){
                 $this->msgbox->add(L('不可操作其他店铺订单'), 219);
            }else if(K::M('order/order')->confirm($order['order_id'], $order, 'shop')){
                if(K::M('waimai/order')->update($order['order_id'], array('spend_status'=>1))){
                    $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
                    $title = sprintf("您在[%s]的自提订单完成", $waimai['title'], $order['order_id']);
                    $content = sprintf("您在[%s]的订单(单号：%s)自提码(%s)已使用", $waimai['title'], $order['order_id'], $order['spend_number']);
                    K::M('member/member')->send($order['uid'], $title, $content,  array('type'=>'order', 'order_id'=>$order['order_id']));
                    $this->msgbox->add(L('核销码成功'));
                }else{
                   $this->msgbox->add(L('核销码失败'), 222); 
                }
            }else{
                $this->msgbox->add(L('核销码失败'), 223);
            }
        }
    }
    //商家直接取消订单
    public function canel($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',230);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',231);
        }else if($order['from']!='waimai'){
            $this->msgbox->add('订单来源不正确',232);
        }else if($order['order_status']==8||$order['order_status']==-1){
            $this->msgbox->add('当前订单不能取消',233);
        }else if($order['shop_id']!=$this->shop_id){
            $this->msgbox->add('不可操作其他店铺订单',234);
        }else{
            if(K::M('order/order')->cancel($order['order_id'],null,'shop')){
                $this->msgbox->add('取消订单成功');
            }else{
                $this->msgbox->add('取消订单失败',235);
            }
        }

    }

    public function yuding($page=1){
        $filter = $pager = $items = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['order_status'] = 1;
        $filter[':OR'] = array(
            'pay_status'=>1,
            'online_pay'=>0,
        );// 已付款 || 货到付款
       // -2:退款取消订单 , -1:已取消，0：未处理，1：已接单（跑腿订单已接单），2:配货中  3：配送开始（跑腿服务中），4：配送完成（跑腿服务完成），8：订单完成
        $filter[':SQL'] = "refund_status != '1' AND pei_time>0";
        $filter['closed'] = 0;
        $filter['from'] = 'waimai';
        $filter['shop_id'] = $this->shop_id;
        if ($keyword = (int)$this->GP('keyword')) {// 头部搜索
            $filter[':OR'] = array('mobile' => 'LIKE:%'.$keyword.'%', 'order_id' => 'LIKE:%'.$keyword.'%');
        }
        $orderby = array('pei_time'=>'ASC', 'order_id'=>'ASC');// 期望送达时间 第一排序
        if($SO = $this->GP('SO')){
            $this->pagedata['day'] = $SO['day'];
            $pager['SO'] = $SO;
            $start_time = strtotime(date('Y-m-d'));
            $end_time   = strtotime(date('Y-m-d'))+86399;
            if($SO['day']&&$SO['day']==1){
                $filter['pei_time'] = $start_time.'~'.$end_time;
            }else if($SO['day']&&$SO['day']!=1){
                $start_time =   $start_time+(($SO['day']-1)*86400);
                $end_time   =   $end_time+(($SO['day']-1)*86400);
                $filter['pei_time'] = $start_time.'~'.$end_time;              
            }
            
            $orderby = array(
                'pei_time'=>'ASC',
                'dateline'=>'ASC'
            ); // 下单时间
        }
        $day1 = strtotime(date('Y-m-d').'+3 day');
        $day2 = strtotime(date('Y-m-d').'+4 day');
        $day3 = strtotime(date('Y-m-d').'+5 day');
        $day_time = array(
            date('m-d',$day1), date('m-d',$day2), date('m-d',$day3),
        );
        $this->pagedata['day'] = $day_time;
        if($items = K::M('order/order')->items($filter, $orderby, $page, $limit, $count)){
            $order_ids = array();
            $shop_ids = array();
            foreach($items as $k=>$v){
                $order_ids[$v['order_id']] = $v['order_id'];
                $items[$k]['products'] = array();
                $shop_ids[] = $v['shop_id'];
            }
            foreach ($items as $kk=>$vv){
                $items[$kk] = K::M('waimai/order')->get_label($vv);
            }

            if ($waimai_order_list = K::M('waimai/order')->items_by_ids($order_ids)) {
                foreach ($waimai_order_list as $k=>$v) {
                    $items[$k] = array_merge($items[$v['order_id']], $v);
                }
            }

            if($product_list = K::M('waimai/orderproduct')->items(array('order_id'=>$order_ids), null, 1, 500)){
                foreach($product_list as $k => $v){
                    $shuxin = "";
                    foreach($v['specification'] as $vvt){
                        $shuxin.="+".$vvt['val'];
                    }
                    $v['shuxin'] = $shuxin;
                    $items[$v['order_id']]['products'][] = $v;
                }
            }
            $items = $this->group_by_uid($items);
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('order/index:pei', array('{page}')), array('SO'=>$SO));
            $waimai_shop =K::M('waimai/waimai')->items_by_ids($shop_ids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['waimai_order'] = $waimai_order_list[$vv['order_id']];
                $items[$kk]['fee'] = $this->get_shop_fee($vv,$waimai_order_list[$vv['order_id']] ,$waimai_shop[$vv['shop_id']]);
                if($vv['pei_type']==1&&$vv['online_pay']==0){
                    $items[$kk]['amount'] = $vv['total_price']-$vv['first_youhui']-$vv['order_youhui']-$vv['coupon']+$waimai_order[$vv['order_id']]['first_roof']+$waimai_order[$vv['order_id']]['roof_amount']-$vv['pei_amount'];
                }
                $items[$kk]['products'] = K::M('waimai/orderproduct')->get_basketProducts($vv['products']);
            }
        }

        $filter_print = array();
        $filter_print['shop_id'] = $this->shop_id;
        $filter_print['status'] = 1;
        $count = K::M('shop/print')->count($filter_print);
        $this->pagedata['is_print'] = $count;
        $this->pagedata['pei_type'] = $this->waimai_shop['pei_type'];
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'order/yuding.html';
    }

    //云打印机
    public function yunprint($order_id,$num=1,$plait_id=0){
        if(!$order_id){
            $this->msgbox->add('订单不存在',214);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',215);
        }else if($order['from']!='waimai'){
            $this->msgbox->add('当前只支持外卖订单打印',216);
        }else if($order['shop_id']!=$this->shop_id){
            $this->msgbox->add('不可操作其他店铺订单',217);
        }else if(!$print_r = K::M('shop/print')->detail($plait_id)){
            $this->msgbox->add('打印机不存在',218);
        }else if($print_r['shop_id']!=$this->shop_id){
            $this->msgbox->add('不可操作别的商铺的打印机');
        } else{
            K::M('order/order')->yunprint($order_id,$num,$plait_id);
        }
    }
    
    public function group_by_uid($items){
        /*foreach ($items as $key=>$val){
            $filter = array();
            $filter['shop_id'] = $this->shop_id;
            $filter['from'] = 'waimai';
            $filter['uid'] = $val['uid'];
            $filter['order_status'] = '>=:0';
            $items[$key]['count_order'] = K::M('order/order')->count($filter);
        }
        return $items;*/

        $uids = array();
        foreach ($items as $k => $v) {
            if($v['uid']){
                $uids[$v['uid']] = $v['uid'];
            }
            //$items[$k]['count_order'] = 0;
            $items[$k]['count_order'] = $v['member_orders'];
        }
        /*$filter = array('from'=>'waimai', 'shop_id'=>$this->shop_id, 'uid'=>$uids, 'order_status'=>'>=:0');
        if($datas = K::M('waimai/order')->orders_group_by($filter, 'uid')){
            foreach ($items as $k => $v) {
                if($a = $datas[$v['uid']]){
                    $items[$k]['count_order'] = $a['orders'];
                }
            }
        }*/
        return $items;
    }

    //获取用户历史订单
    public function get_uid_order($uid,$order_id){
        if(!$uid){
            $this->msgbox->add('用户不存在',218);
        }else if(!$user = K::M('member/member')->detail($uid)){
            $this->msgbox->add('用户不存在',219);
        }else{
            $filter = array();
            $filter['shop_id'] = $this->shop_id;
            $filter['uid'] = $uid;
            $filter['from'] = 'waimai';
            $filter[':SQL'] = "order_id <>".$order_id;
            $filter['order_status'] = '>=:0';

            if($items = K::M('order/order')->items($filter,array('order_id'=>'DESC'),1,10,$count)){
                $order_ids = array();
                foreach($items as $k=>$v){
                    $order_ids[$v['order_id']] = $v['order_id'];
                    $items[$k]['products'] = array();
                    $items[$k]['format_time'] = date('Y-m-d H:i',$v['dateline']);
                    $items[$k]['title'] = $this->waimai_shop['title'];
                }
                if ($waimai_order_list = K::M('waimai/order')->items_by_ids($order_ids)) {
                    foreach ($waimai_order_list as $k=>$v) {
                        $items[$k] = array_merge($items[$v['order_id']], $v);
                    }
                }
                if($product_list = K::M('waimai/orderproduct')->items(array('order_id'=>$order_ids), null, 1, 500)){
                    foreach($product_list as $k => $v){
                        $items[$v['order_id']]['products'][] = $v;
                    }
                }
                $data = array();
                $data['order'] = $items;
                $data['member'] = $user;
                $this->msgbox->set_data('data',$data);
                $this->msgbox->add('success');
                $this->msgbox->json();
                
            }else{
                $this->msgbox->add('没找到该用户的历史订单',220);
            }            
        }
    }

    //申请配送
    public function set_mpei($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',218);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',219);
        }else if($order['shop_id']!=$this->shop_id){
            $this->msgbox->add('不可操作其他店铺订单',220);
        }else if($order['from']!='waimai'){
            $this->msgbox->add('订单来源不正确',221);
        }else if($order['order_status']!=1){
            $this->msgbox->add('订单状态不可申请配送',221);
        }else{
            if(K::M('order/order')->update($order_id,array('order_status'=>2))){
                $this->msgbox->add('申请配送成功');
            }else{
                $this->msgbox->add('申请配送失败',222);
            }
        }
    }
    
    public function print_test($palit_id){
        $text = $this->GP('text');
        if(!$palit_id){
            $this->msgbox->add('未指定打印机',218);
        }else if(!$text){
            $this->msgbox->add('请输入需要打印的内容',219);
        }else if(!$print_r = K::M('shop/print')->detail($palit_id)){
            $this->msgbox->add('打印机不存在',218);
        }else if($print_r['shop_id']!=$this->shop_id){
            $this->msgbox->add('不可操作别的商铺的打印机');
        }else{
            K::M('order/order')->print_test($palit_id,$text);
        }
    }

    public function print_local($order_id){
        if(!$order_id){
            $this->msgbox->add('订单存在',238);
        }else if(!$order=K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',239);
        }else if($order['shop_id']!=$this->shop_id){
            $this->msgbox->add('不可打印别的商铺的订单',240);
        }else{
            $data = K::M('order/order')->localprint($order_id);
            $this->msgbox->set_data('data',$data);
            $this->msgbox->json();
        }
    }

    public function get_common_detail($order_id){
        if(!$order_id){
            $this->msgbox->add('未指定需要查看的评论',202);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('需要查看的订单不存在',203);
        }else if($order['order_status']!=8){
            $this->msgbox->add('该订单还未完成',204);
        }else if($order['comment_status']!=1){
            $this->msgbox->add('未找到该订单的评论信息',205);
        }else{
            $comment = K::M('waimai/comment')->find(array('order_id'=>$order_id));
            $comment['photo'] = array();
            $comment['member'] = array();
            if($comment){
                $comment_photo = K::M('waimai/commentphoto')->items(array('comment_id'=>$comment['comment_id']));
                $comment['photo'] = $comment_photo;
                $comment['score'] = $comment['score']*20;
                $comment['score_peisong'] = $comment['score_peisong']*20;
                $member = K::M('member/member')->detail($comment['uid']);
                $comment['member'] = array(
                    'nickname'=>$member['nickname'].'('.substr_replace($member['mobile'],'****',3,4).')'
                );
                $comment['time'] = date('Y-m-d H:i:s');
            }
            $this->msgbox->set_data('data',$comment);
        }

    }

    public function detail($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',201);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if(!$waimai_order = K::M('waimai/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',203);
        }else if($order['shop_id']!=$this->shop_id){
            $this->msgbox->add('该订单不属于您的店铺',204);
        }else{
            if($items = K::M('order/order')->items(array('order_id'=>$order_id), array('order_id'=>"desc"), 1, 1, $count)){
                $order_ids = $cui_order_ids = array();
                $member_ids = array();
                $shop_ids = array();
                foreach($items as $k=>$v){
                    $member_ids[$v['uid']] = $v['uid'];
                    $shop_ids[] = $v['shop_id'];
                    $order_ids[$v['order_id']] = $v['order_id'];
                    $items[$k]['products'] = $items[$k]['refund_info'] = array();
                    if ($v['cui_time'] > 0) {// 催单
                        $cui_order_ids[$v['order_id']] = $v['order_id'];
                    }
                }
                // 催单
                if (!$cui_logs = K::M('order/cuilog')->items_group_by_order_id($cui_order_ids)) {
                    $cui_logs = array();
                }
                // 获取异常（退款）订单信息
                if (!$refund_list = K::M('waimai/order/refund')->items_by_ids($order_ids)) {
                    $refund_list = array();
                }
                $member_list = K::M('member/member')->items_by_ids($member_ids);
                if($items[$order_id]['order_status']==8&&$items[$order_id]['comment_status']==1){
                    $waimai_comment = K::M('waimai/comment')->find(array('order_id'=>$order_id));
                    $waimai_cpmment_photo = K::M('waimai/commentphoto')->items(array('comment_id'=>$waimai_comment['comment_id']));
                    $this->pagedata['comment'] = $waimai_comment;
                    $this->pagedata['comment_photo'] = $waimai_cpmment_photo;
                }

                $coupon_id = array();
                foreach ($items as $k => $v) {
                    $items[$k]= K::M("waimai/order")->get_label($v);
                    if ($v['refund_status'] != 0 && $refund_list[$k]) { // 0:正常,1:退款中, 2:退款完成,-1:退款失败,3:平台处理纠纷
                        $items[$k]['refund_info'] = $this->filter_fields('reflect,reply,dateline', $refund_list[$k]);
                    }
                    $items[$k]['cui_logs'] = $cui_logs[$k] ? $cui_logs[$k] : array(); // 催单
                    $items[$k]['users'] = $member_list[$v['uid']];
                    if($items[$k]['order_status']==8&&$items[$k]['comment_status']==1){
                        $items[$k]['score'] = $waimai_comment['score']*20;
                        $items[$k]['score_peisong'] = $waimai_comment['score_peisong']*20;
                        $items[$k]['score_avg'] = ($waimai_comment['score']+$waimai_comment['score_peisong'])/2;
                        $items[$k]['comment_id'] = $waimai_comment['comment_id'];
                        $items[$k]['reply_time'] = $waimai_comment['reply_time'];
                        $items[$k]['reply'] = $waimai_comment['reply'];
                    }
                }

                if ($waimai_order_list = K::M('waimai/order')->items_by_ids($order_ids)) {
                    foreach ($waimai_order_list as $k=>$v) {
                        $items[$k] = array_merge($items[$v['order_id']], $v);
                        $items[$k]['waimai_order'] = $v;
                    }
                }
                if($product_list = K::M('waimai/orderproduct')->items(array('order_id'=>$order_ids), null, 1, 500)){
                    foreach($product_list as $k => $v){
                        $shuxin = "";
                        foreach($v['specification'] as $vvt){
                            $shuxin.="+".$vvt['val'];
                        }
                        $v['shuxin'] = $shuxin;
                        $items[$v['order_id']]['products'][] = $v;
                    }
                }
                $waimai_shop = K::M('waimai/waimai')->items_by_ids($shop_ids);
                foreach ($items as $kk=>$vv){
                    $items[$kk]['fee'] = $this->get_shop_fee($vv,$waimai_order_list[$vv['order_id']] ,$waimai_shop[$vv['shop_id']]);

                    if($vv['pei_type']==1&&$vv['online_pay']==0){
                        $items[$kk]['amount'] = $vv['total_price']-$vv['first_youhui']-$vv['order_youhui']-$vv['coupon']+$waimai_order[$vv['order_id']]['first_roof']+$waimai_order[$vv['order_id']]['roof_amount']-$vv['pei_amount'];
                    }
                    $items[$kk]['products'] = K::M('waimai/orderproduct')->get_basketProducts($vv['products']);
                }
                $items = $this->group_by_uid($items);
                $order_log  = K::M('order/log')->items(array('order_id'=>$order_id),array('log_id'=>'ASC'));
                foreach ($order_log as $log_key =>$log_val){
                    if(!$log_val['log']){
                        unset($order_log[$log_key]);
                    }
                }
            }

            $this->pagedata['log_items'] = $order_log;
            $filter_print = array();
            $filter_print['shop_id'] = $this->shop_id;
            $filter_print['status'] = 1;
            $count = K::M('shop/print')->count($filter_print);
            $this->pagedata['is_print'] = $count;
            $this->pagedata['items'] = $items;
            $this->tmpl = 'order/detail.html';
        }
    }   
}