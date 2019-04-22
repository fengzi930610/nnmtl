<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Oauth_Order extends Ctl
{
    public function detail($order_id)
    {
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
            $this->tmpl = 'oauth/order/detail.html';
        }
    }

    public function index($page=1, $status=1)
    {
        $page = max((int)$page,1);
        $limit = 10;
        //status  1:待结单  2:待配送  3:配送中  4:已送达  5:已完成  6:异常订单  7:已取消
        $filter = $orderby = array();
        $filter['from'] = 'other';
        $filter['shop_id'] = $this->shop_id;
        if($status == 1){
            $filter['order_status'] = 0;
        }else if($status == 2){
            $filter['order_status'] = 2;
        }else if($status == 3){
            $filter['order_status'] = 3;
        }else if($status == 4){
            $filter['order_status'] = 4;
        }else if($status == 5){
            $filter['order_status'] = 8;
        }else if($status == 6){
            $filter['refund_status'] = "<>:0";
        }else if($status == 7){
            $filter['order_status'] = -1;
        }
        if($SO = $this->GP('SO')){
            if($SO['stime'] && $SO['ltime']){
                $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }
            if(!$SO['stime'] && $SO['ltime']){
                $filter['dateline'] = "<:".(strtotime($SO['ltime'])+86399);
            }
            if($SO['stime'] && !$SO['ltime']){
                $filter['dateline'] = ">:".strtotime($SO['stime']);
            }
            if($SO['type'] && in_array($SO['type'], array('ele','meituan','own'))){
                $filter[':SQL'] = " w.`type`= '".$SO['type']."'";
            }            
        }

        $orderby['order_id'] = 'DESC';
        if($items = K::M('other/order')->items_join_by($filter,$orderby,$page,$limit,$count)){
            $p_order_ids = $staff_ids = array();
            foreach($items as $k=>$v){
                $items[$k] = K::M('other/order')->format_data($v);
                if($v['p_order_id']){
                    $p_order_ids[$v['p_order_id']] = $v['p_order_id'];
                }
                if($v['staff_id']){
                    $staff_ids[$v['staff_id']] = $v['staff_id'];
                }
            }
            $p_orders = K::M('paotui/order')->items_by_ids($p_order_ids);
            $staffs = K::M('staff/staff')->items_by_ids($staff_ids);
            
            foreach ($items as $k => $v) {
                if($p_order = $p_orders[$v['p_order_id']]){
                    $items[$k]['p_order'] = $p_order;
                    $items[$k]['total_price'] = $p_order['amount'] + $p_order['tip'];
                }
                $items[$k]['staff'] = $staffs[$v['staff_id']] ? $staffs[$v['staff_id']] : array();
                if($v['type']=='own'){
                    $items[$k]['total_price'] = ($p_order['amount'] + $p_order['tip'])>0?($p_order['amount'] + $p_order['tip']):0;
                }
            }


            
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('oauth/order:index', array('{page}',$status)), array('SO'=>$SO));
        }

        // K::M("system/logs")->log("sqllog",$this->system->db->SQLLOG());

        $params =  '';
        if(!empty($SO) && is_array($SO)){
            $params = http_build_query(array("SO"=>$SO));
            $params = '?'.$params;
        }
        $this->pagedata['SO'] = $SO;
        $this->pagedata['query'] = $params;
        $this->pagedata['status'] = $status;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'oauth/order/index.html';
    }

    //打印三方订单
    public function yprint($order_id)
    {
        if(!(int)$order_id){
            $this->msgbox->add('未指定需要打印的订单',201);
        }else if(!$order = K::M('other/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if($order['from'] != 'other'){
            $this->msgbox->add('订单不是三方订单',203);
        }else if(!($count = K::M('shop/print')->count(array('shop_id'=>$this->shop_id)))){
            $this->msgbox->add('商家未设置打印机',204);
        }else{
            if($print_list = K::M('shop/print')->items(array('shop_id'=>$this->shop_id))){
                foreach($print_list as $k=>$v){
                    K::M('order/order')->yunprint($order_id,1,$v['plat_id']);
                }
            }
        }
    }

    public function jiedan($order_id)
    {
       K::M('other/order')->jiedan($order_id,$this->shop_id);
    }

    //取消订单
    public function cancel($order_id)
    {
        K::M('other/order')->cancel($order_id,$this->shop_id);
    }

    public function setconfirm($order_id=0)
    {
        K::M('other/order')->setconfirm($order_id,$this->shop_id);    
    }

    public function get_minpei($order_id=0)
    {
        if(!$group_id = (int)$this->waimai_shop['group_id']){
            $this->msgbox->add('您还没有绑定配送站',211);
        }else if(!$group = K::M('pei/group')->detail($group_id)){
            $this->msgbox->add('绑定的配送站不存在或已关闭',212);
        }else{
            $this->msgbox->add('success');
            $this->msgbox->set_data('data',array('min_pei'=>$group['min_pei']));
        }
    }

    public function setpei($order_id = 0){
        if($data = $this->checksubmit('data')){
            if($data['tip']<0){
                $this->msgbox->add('非法的小费',299);
            }else{
                K::M('other/order')->setpei($order_id,$data['tip'],$this->shop_id);
            }
        }else{
            $this->msgbox->add('非法数据提交',201);
        }
    }

    //撤回配送
    public function cancelpei($order_id)
    {
       K::M('other/order')->cancelpei($order_id,$this->shop_id);

    }

    public function  agree($order_id){
        K::M('other/order')->agree_order_lite($order_id,$this->shop_id);
    }

    public function refuse($order_id){
        K::M('other/order')->disagree_refund_lite($order_id,$this->shop_id);


    }

    public function addtip($order_id){
        if($data = $this->checksubmit('data')){
            if($data['tip']<0){
                $this->msgbox->add('非法的小费',299);
            }else{
                K::M('other/order')->addtip($order_id,$this->shop_id,$data['tip']);
            }
        }else{
            $this->msgbox->add('非法数据提交',201);
        }
    }

    public function fadan()
    {
        if(!$data = $this->checksubmit('data')){
            $this->msgbox->add('数据有误！',211);
        }else if(!$contact = $data['contact']){
            $this->msgbox->add('收货人不能为空！',212);
        }else if(!$mobile = $data['mobile']){
            $this->msgbox->add('联系方式不能为空！',213);
        }else if(!$mobile = K::M('verify/check')->mobile($mobile)){
            $this->msgbox->add('联系方式格式有误！',213);
        }else if(!$addr = $data['addr']){
            $this->msgbox->add('收货地址不能为空！',214);
        }else if(!$group_id = (int)$this->waimai_shop['group_id']){
            $this->msgbox->add('您还没有绑定配送站',215);
        }else if(!$group = K::M('pei/group')->detail($group_id)){
            $this->msgbox->add('绑定的配送站不存在或已关闭',216);
        }else{
            $tip = (float)$data['tip'] ? (float)$data['tip'] : 0;
            $min_pei = (float)$group['min_pei'];
            $pei_amount = $min_pei + $tip;
            if($tip < 0){
                $this->msgbox->add('小费设置错误',217);
            }else if($this->waimai_shop['deliver'] < $pei_amount){
                $this->msgbox->add('配送费余额不足',218);
            }else{
                $lng = $lat = 0;
                $o_lng = $this->waimai_shop['lng'] ? $this->waimai_shop['lng'] : 0;
                $o_lat = $this->waimai_shop['lat'] ? $this->waimai_shop['lat'] : 0;
                $o_contact = $this->waimai_shop['title'];
                $o_mobile = $this->waimai_shop['phone'];
                $o_addr = $this->waimai_shop['addr'];
                $city_id = $this->waimai_shop['city_id'];

                $city = K::M('data/city')->detail($city_id);
                if($addrs = K::M('magic/baidu')->geocode_by_addr($addr, $city['city_name'])){
                    $lng = $addrs[0]['lng'];
                    $lat = $addrs[0]['lat'];
                }

                $insert_order = array();
                $insert_order['amount'] = 0;
                $insert_order['city_id'] = $city_id;
                $insert_order['staff_id'] =0;
                $insert_order['uid'] = 0;
                $insert_order['shop_id'] = $this->shop_id;
                $insert_order['from'] = 'other';
                $insert_order['order_status'] = 0;//未申请配送的订单状态为0
                $insert_order['online_pay'] = 1;
                $insert_order['pay_status'] = 1;
                $insert_order['total_price'] = 0;
                $insert_order['hongbao_id'] = 0;
                $insert_order['hongbao'] = 0;
                $insert_order['o_lng'] = $o_lng;
                $insert_order['o_lat'] = $o_lat;
                $insert_order['lng'] = $lng;
                $insert_order['lat'] = $lat;
                $insert_order['contact'] = $contact;
                $insert_order['mobile'] = $mobile;
                $insert_order['addr'] = $addr;
                $insert_order['house'] = "";
                $insert_order['day'] = date('Ymd');
                $insert_order['clientip'] = __IP;
                $insert_order['pei_type'] = 1;
                $insert_order['intro'] = $data['intro'];
                $insert_order['order_from'] = "wap";
                $insert_order['pei_time'] = 0;
                $insert_order['dateline'] = __TIME;
                $insert_order['group_id'] = $this->waimai_shop['group_id'];
                $insert_order['day_num'] = 0;
                $insert_order['pay_time'] = 0;
                $insert_order['pei_amount'] = $pei_amount;
                $insert_order['total_price'] = $pei_amount+$tip;
                if($order_id = K::M('order/order')->create($insert_order)){
                    $products = $extend = array();
                    $price = 0;
                    if($data['products'] && is_array($data['products'])){
                        foreach($data['products'] as $kk=>$vv){
                            $extend[] = $vv;
                            $products[] = array(
                                'product_name'=>$vv,
                                'product_price'=>0,
                                'product_number'=>0,
                                'amount'=>0,
                            );
                        }
                    }
                    
                    $other_order = array();
                    $other_order['order_id'] = $order_id;
                    $other_order['shop_id'] = $this->shop_id;
                    $other_order['type'] = 'own';
                    $other_order['price'] = $price;
                    $other_order['product'] = serialize($products);
                    $other_order['lng'] = $lng;
                    $other_order['lat'] = $lat;
                    $other_order['addr'] = $addr;                                
                    $other_order['contact'] = $contact;
                    $other_order['mobile'] = $mobile;
                    $other_order['ext_shop_id'] = 0;
                    $other_order['ext_order_id'] = 0;
                    $other_order['extend'] = serialize($extend);
                    $other_order['dateline'] = __TIME;

                    if (K::M('other/order')->create($other_order)) {
                        $insert_order['from'] = 'paotui';
                        $insert_order['total_price'] = $pei_amount;
                        $insert_order['amount'] = $pei_amount;
                        $insert_order['pei_amount'] = $pei_amount;
                        $insert_order['pay_status'] = 0;
                        $insert_order['day_num'] = 0;
                        if($p_order_id = K::M('order/order')->create($insert_order)){
                            $paotui_order = array(
                                'order_id'=>$p_order_id,
                                'from'=>'song',
                                'lng'=>$lng,
                                'lat'=>$lat,
                                'addr'=>$addr,
                                'contact'=>$contact,
                                'mobile'=>$mobile,
                                'o_lng'=>$o_lng,
                                'o_lat'=>$o_lat,
                                'o_addr'=>$o_addr,
                                'o_contact'=>$o_contact,
                                'o_mobile'=>$o_mobile,
                                'amount'=>$min_pei,
                                'tip'=>$tip,
                                'product'=>serialize($extend),
                                'type'=>0,
                                'weight'=>1,
                                'price'=>0,
                                );
                            if(K::M('paotui/order')->create($paotui_order)){
                                $log = sprintf("订单：(%s)发单，扣除配送费余额￥%s", $p_order_id, $pei_amount);
                                K::M('waimai/waimai')->update_money($this->shop_id, -$pei_amount, $log);

                                K::M('order/log')->create(array('from'=>'member', 'log'=>'三方单(自发单)，订单创建成功', 'order_id'=>$order_id));
                                K::M('order/log')->create(array('from'=>'member', 'log'=>'订单创建成功', 'order_id'=>$p_order_id));
                                K::M('paotui/order')->set_payed(array('order_id'=>$p_order_id,'amount'=>$pei_amount),array('code'=>'money'));
                                K::M('other/order')->set_order_day_num($order_id, $this->shop_id);
                                K::M('paotui/order')->set_order_day_num($p_order_id);
                                K::M('other/order')->update($order_id, array('p_order_id'=>$p_order_id));
                                K::M('order/order')->update($order_id, array('order_status'=>2));

                                K::M('order/time')->update($order_id,array('shop_jiedan_time'=>__TIME));
                                K::M('order/time')->update($p_order_id,array('shop_jiedan_time'=>__TIME));
                                
                                $this->msgbox->add('订单创建成功！')->response();
                            }
                        }
                    }
                }
                if($order_id){
                    K::M('order/order')->delete($order_id);
                    K::M('other/order')->delete($order_id);
                }
                if($p_order_id){
                    K::M('order/order')->delete($p_order_id);
                }
                $this->msgbox->add('订单创建失败！',219);
            }
        }
    }
    
}