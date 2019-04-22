<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: index.php 7284 2014-11-24 10:42:02Z maoge $
 */
/*if(strtolower(php_sapi_name()) != 'cli'){
    exit('only run cli');
}*/
@ini_set("display_errors", "On");
@error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT ^ E_WARNING);;
@set_time_limit(0);
@ini_set('memory_limit','128M');
@ini_set('allow_url_fopen', 'On');
@date_default_timezone_set('Asia/Shanghai');
require(dirname(__DIR__).'/system/home/index.php');
$system = new Index('magic-shell');
$config = K::M('system/config')->get('automatic');
$paotui_config =  K::M('system/config')->get('paotuimatic');
$unpay_cancel_time = (int)$config['unpay_cancel_time'] ? $config['unpay_cancel_time'] : 15; //默认15m
$auto_comfirm_time = (int)$config['auto_comfirm_time'] ? (int)$config['auto_comfirm_time']: 180; //默认3小时
$unjiedan_cancel_time = (int)$config['unjiedan_cancel_time'] ? (int)$config['unjiedan_cancel_time'] : 15; //默认15m
$untreated_agree_time = (int)$config['untreated_agree_time']?(int)$config['untreated_agree_time']:720;

//跑腿未支付自动取消
$paotui_unpay_cancel_time = (int)$paotui_config['unpay_cancel_time'] ? $paotui_config['unpay_cancel_time'] : 15; //默认15m
//跑腿完成自动结算
$paotui_auto_comfirm_time = (int)$paotui_config['auto_comfirm_time'] ? (int)$paotui_config['auto_comfirm_time']: 180; //默认3小时
//未接单自动取消
$paotui_unjiedan_cancel_time =(int)$paotui_config['unjiedan_cancel_time'] ? (int)$paotui_config['unjiedan_cancel_time'] : 30; //默认15m
$_LOGDATA = array('unpay_cancel'=>'', 'auto_confirm'=>'', 'unjiedan_cancel'=>'', 'shop_new_order'=>'', 'staff_new_order'=>'');
//15分钟未支付的自动取消
$filter = array('online_pay'=>1, 'order_status'=>0, 'pay_status'=>0,'from'=>'waimai', 'dateline'=>'<:'.(time()-($unpay_cancel_time*60)));
if($items = K::M('order/order')->items($filter, null, 1, 30, $unpay_cancel_count)){
    foreach($items as $k=>$v){
        K::M('waimai/order')->cancel($v['order_id'], $v, 'system');
        K::M('member/member')->send($v['uid'], "订单被取消", sprintf('您的订单(编号:%s)超时未支付，订单自动取消', $v['order_id']), array('type'=>'order', 'order_id'=>$v['order_id']));
        $_LOGDATA['unpay_cancel'] .= $v['order_id'] .  ',';
    }
}


//外卖3小时过期自动结算
$filter = array(':OR'=>array('pay_status'=>1,"online_pay"=>0), 'order_status'=>array(4,5), 'from'=>'waimai', 'lasttime'=>'<:'.(time()-($auto_comfirm_time*60)));
if($items = K::M('order/order')->items($filter, null, 1, 30, $waimai_confirm_count)){
    foreach($items as $k=>$v){
        K::M('waimai/order')->confirm($v['order_id'], $v, 'system');
        $_LOGDATA['auto_confirm'] .= $v['order_id'] .  ',';
    }
}

//外卖15分钟商户费接单自动取消
$filter = array('order_status'=>'0', 'from'=>'waimai');
$_unjiedan_lost_time = (time()-($unjiedan_cancel_time*60));
$filter[':SQL'] = "((`pay_status`=1 AND `pay_time`<".$_unjiedan_lost_time.") OR (`online_pay`=0 AND `dateline`<".$_unjiedan_lost_time."))";
if($items = K::M('order/order')->items($filter, null, 1, 30, $waimai_unjiedan_cancel_count)){
    foreach($items as $k=>$v){
        K::M('waimai/order')->cancel($v['order_id'], $v, 'system');
        K::M('shop/shop')->send($v['shop_id'], "未接单自动取消", sprintf('订单(编号:%s)超过%s分钟未接单自动取消', $v['order_id'],$unjiedan_cancel_time), array('type'=>'cancel', 'order_id'=>$v['order_id']));
        K::M('member/member')->send($v['uid'], "商家未接单自动取消", sprintf('您的外卖订单(编号:%s)超过%s分钟商家未接单自动取消', $v['order_id'],$unjiedan_cancel_time), array('type'=>'order', 'order_id'=>$v['order_id']));
        $_LOGDATA['unjiedan_cancel'] .= $v['order_id'] .  ',';
    }
}

//跑腿--begin
//跑腿未支付自动取消
$filter_paotui = array('online_pay'=>1, 'order_status'=>0, 'pay_status'=>0,'from'=>'paotui', 'dateline'=>'<:'.(time()-($paotui_unpay_cancel_time*60)));
if($items = K::M('order/order')->items($filter_paotui, null, 1, 30, $paotui_canel_count)){
    foreach($items as $k=>$v){
        K::M('paotui/order')->cancel($v['order_id'], $v, 'system');
        K::M('member/member')->send($v['uid'], "订单被取消", sprintf('您的订单(编号:%s)超时未支付，订单自动取消', $v['order_id']), array('type'=>'order', 'order_id'=>$v['order_id']));
       // $_LOGDATA['unpay_cancel'] .= $v['order_id'] .  ',';
    }
}
//跑腿服务完成自动结算
$filter_paotui = array('pay_status'=>1, 'order_status'=>array(4,5), 'from'=>'paotui', 'lasttime'=>'<:'.(time()-($paotui_auto_comfirm_time*60)));
if($items = K::M('order/order')->items($filter_paotui, null, 1, 30, $paotui_confirm_count)){
    foreach($items as $k=>$v){
        K::M('paotui/order')->confirm($v['order_id'], $v, 'system');
       // $_LOGDATA['auto_confirm'] .= $v['order_id'] .  ',';
    }
}
//跑腿超时未接单
$filter_paotui = array('order_status'=>'0','pay_status'=>1,'from'=>'paotui','pay_time'=>'<:'.(time()-($paotui_unjiedan_cancel_time*60)));
if($items = K::M('order/order')->items($filter_paotui, null, 1, 30, $paotui_unjiedan_cancel_count)){
    foreach($items as $k=>$v){
        K::M('paotui/order')->cancel($v['order_id'], $v, 'system');
        K::M('member/member')->send($v['uid'], "骑手未接单自动取消", sprintf('您的跑腿订单(编号:%s)超过%s分钟骑手未接单自动取消', $v['order_id'],$unjiedan_cancel_time), array('type'=>'order', 'order_id'=>$v['order_id']));
        //$_LOGDATA['unjiedan_cancel'] .= $v['order_id'] .  ',';
    }
}

//跑腿--end

//新订单消息推送给商家

//新订单消息推送给商家
$filter_neworder['order_status'] = 0;
$filter_neworder['from'] = 'waimai';
//$filter_neworder['pei_type'] = array(0, 1, 2, 3);
$filter_neworder[':OR'] = array('pay_status' => 1, 'online_pay' => 0);
if($new_order_items = K::M('order/order')->neworders_by_shopid($filter_neworder, 1, 1000)){
    $shop_ids = array();
    foreach($new_order_items as $k=>$v){
        $shop_ids[$v['shop_id']] = $v['shop_id'];
        K::M('shop/shop')->send($v['shop_id'], '您的店铺有新订单', sprintf('您有%s个新订单待处理', $v['neworders']) ,  array('type'=>'newOrder', 'order_id'=>0,'tag_and'=>array('login_on')));
    }

    /*if($shop_ids){
        K::M('shop/shop')->send(array_values($shop_ids), '您的店铺有新订单', '您有'.$v['neworders'].'个新订单待处理' ,  array('type'=>'newOrder', 'order_id'=>0,'tag_and'=>array('login_on')));
    }*/
    $_LOGDATA['shop_new_order'] = implode(',', $shop_ids);
}

//抢购开始==================================================================
//15分钟未支付的自动取消
$filter = array('online_pay'=>1, 'order_status'=>0, 'pay_status'=>0,'from'=>'qiang', 'dateline'=>'<:'.(time()-900));
if($items = K::M('order/order')->items($filter, null, 1, 30, $qiang_cancel_count)){
    foreach($items as $k=>$v){
        K::M('qiang/order')->cancel($v['order_id'], $v, 'system');
        K::M('member/member')->send($v['uid'], "订单被取消", sprintf('您的抢购订单(编号:%s)超时未支付，订单自动取消', $v['order_id']), array('type'=>'order', 'order_id'=>$v['order_id']));
    }
}

//抢购订单处理 八天未確認收貨，自动確認收貨
$filter = array('pay_status'=>1, 'order_status'=>6, 'from'=>'qiang', 'lasttime'=>'<:'.(time()-691200));
if($items = K::M('order/order')->items($filter, null, 1, 30, $qiang_confirm_count)){
    foreach($items as $k=>$v){
        K::M('order/order')->confirm($v['order_id'], $v, 'system');
    }
}

//抢购订单处理 已过期电子券 可退款
$filter = array('from' => 'qiang', 'closed' => 0, 'pay_status'=>1,'pei_type'=>3,'order_status'=>5);
if($order_list = K::M('order/order')->items($filter, null, 1, 300, $qiang_ticket_count)){
    $order_ids = $qiang_order_ids = $expired_order_ids = array();
    foreach($order_list as $k=>$v){
        $order_ids[$v['order_id']] = $v['order_id'];
    }
    if($qiang_order = K::M('qiang/order')->items_by_ids($order_ids)){
        foreach ($qiang_order as $k => $v) {
            if(!empty($v['use_ltime']) && empty($v['use_time']) && $v['use_ltime'] < time()){
                if($v['notes']){
                    $notes = unserialize($v['notes']);
                    if($notes['is_tui'] == 1){
                        $qiang_order_ids[$v['order_id']] = $v['order_id'];
                    }else{
                        $expired_order_ids[$v['order_id']] = $v['order_id'];
                    }
                }
            }
        }
    }
    if($items = K::M('order/order')->items_by_ids($order_ids)){
        foreach($items as $k=>$v){
            if(in_array($v['order_id'], $qiang_order_ids)){
                K::M('qiang/order')->cancel($v['order_id'], $v, 'system',',抢购订单过期');
                K::M('member/member')->send($v['uid'], "订单被取消", sprintf('您的抢购订单(编号:%s)已过期，订单自动取消', $v['order_id']), array('type'=>'order', 'order_id'=>$v['order_id']));
            }else if(in_array($v['order_id'], $expired_order_ids)){
                K::M('qiang/order')->expired($v['order_id'], $v, 'system',',抢购已过期');
                K::M('member/member')->send($v['uid'], "订单被取消", sprintf('您的抢购订单(编号:%s)已过期，订单自动取消', $v['order_id']), array('type'=>'order', 'order_id'=>$v['order_id']));
            }
        }
    }

    $_LOGDATA['qiang_order_ids'] = implode(',', $qiang_order_ids);
    $_LOGDATA['expired_order_ids'] = implode(',', $expired_order_ids);
}
//抢购结束================================================================

//新订单消息推送给配送员
//$time_end = time();
$filter = array('staff_id' => 0, 'closed' => 0, ':OR' => array('pay_status'=>1,'online_pay'=>0),'refund_status'=>0);
$filter[':SQL'] = "((`from`='waimai' AND ((`pei_type`=1 AND `order_status` = 2 ) OR (`order_status`=0 AND `pei_type`=2))) OR (`from`='paotui' && `order_status`=0 ) AND `tmp_ltime` < " . time() . " )";

if($order_list = K::M('order/order')->items($filter)){
    $group_list = array();
    $group_tags = array();
    $send_staff_ids = array();
    foreach($order_list as $v){
        $group_list[$v['group_id']] = $v['group_id'];
        $group_tags[$v['group_id']] = 'group_'.$v['group_id'];
    }
    $group_items = K::M('pei/group')->items_by_ids($group_list);
    foreach ($order_list as $kk=>$vv){
        if($group_items[$vv['group_id']]['assign']==1&&$vv['tmp_staff_id']>0&&$vv['tmp_ltime']>time()){
            $send_staff_ids[$vv['tmp_staff_id']] = $vv['tmp_staff_id'];
        }
    }

    if($send_staff_ids){
        K::M('staff/staff')->send(array_values($send_staff_ids), '骑手来单啦', '有新的配送订单啦', array('type'=>'newOrder', 'order_id'=>0));
    }
    foreach ($group_tags as $k1=>$v1){
        //新订单推送修改 如果配送站选择指派模式 不走统一推送  2017-11-18 begin
        if($group_items[$k1]['assign']==1){
            unset($group_tags[$k1]);
        }
        //新订单推送修改 如果配送站选择指派模式 不走统一推送  2017-11-18 end
    }
    if($group_tags){
        $tag_count = count($group_tags);
        for($i=0; $i<$tag_count; $i=$i+20){
            $tags = array_splice($group_tags, $i, 20);
            foreach($tags as $k=>$v){
                if($v=='group_0'){
                    unset($tags[$k]);
                }
            }
           K::M('jpush/device')->jpush('骑手来单啦', '有新的配送订单啦', array('from'=>'staff','tag'=>$tags, 'tag_and'=>array('work_status_1','login_on')),array('type'=>'newOrder', 'order_id'=>0));
        }
    }
    $_LOGDATA['staff_new_order'] = implode(',', $send_staff_ids);
}



//超时未接单 电话提醒
$filter = array();
$filter['pay_time'] = "<:".(time()-(__CFG::ALIVOICE_TIME*60));
$filter['order_status'] = 0;// 待接单
$filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
$filter['pei_type'] = array(0, 1, 3);
$filter['refund_status'] = 0;
$filter['closed'] = 0;
$filter['from'] = 'waimai';
if(__CFG::ALIVOICE_ID&&$order_list = K::M('order/order')->items($filter,null,1,30,$count_call_shop)){
    $shop_ids = array();
    foreach($order_list as $vvv){
        $shop_ids[$vvv['shop_id']] = $vvv['shop_id'];
    }
    $shop_list = K::M('waimai/waimai')->items_by_ids($shop_ids);
    foreach($shop_list as $shop_v){
        K::M("waimai/alicall")->singleCallByTts($shop_v);
    }
}

//新增打赏订单余额抵扣一部分的  退款处理 10分钟以后退款
$filter_reward = array();
$filter_reward['pay_status'] = 0;
$filter_reward['money'] = ">:0";
$filter_reward['from'] = "reward";
$filter_reward['dateline'] = "<:".(time()-600);
if($items_reward = K::M('order/order')->items($filter_reward)){

   foreach ($items_reward as $ks1=>$vs1){
    K::M('order/order')->cancel($vs1['order_id']);
   }
}


//新增用户申请退款 超时未处理
$filter_tuikuan = array();
$filter_tuikuan['order_status'] = array(1,2,3,4);
$filter_tuikuan['refund_status'] = 1;
$filter_tuikuan[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
$filter_tuikuan['pei_type'] = array(0, 1, 3);
$filter_tuikuan['closed'] = 0;
$filter_tuikuan['from'] = 'waimai';
$filter_tuikuan[':SQL'] = " w.dateline <= ".(time()-($untreated_agree_time*60));
//$filter_tuikuan['dateline'] = '<:'.(__TIME-($untreated_agree_time*60));
if($items = K::M('order/order')->order_join_refund($filter_tuikuan)){

   foreach ($items as $kk=>$vv){
       K::M('waimai/order')->refund($vv,'','system');
   }
}

//定时脚本 定时处理 美团订单  用户申请退款无回调的问题
$filter_tuikuan = array();
$filter_tuikuan['order_status'] = array(1,2,3,4);
$filter_tuikuan['refund_status'] = 1;
$filter_tuikuan[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
$filter_tuikuan['pei_type'] = array(0, 1, 3);
$filter_tuikuan['closed'] = 0;
$filter_tuikuan['from'] = 'other';
$filter_tuikuan[':SQL'] = " w.dateline <= ".(time()-(31*60));
if($items = K::M('other/order')->items_join_by($filter_tuikuan)){
    foreach ($items as $kk=>$vv){
        if($vv['type']=='meituan'){
            K::M('meituan/order')->recievecancel($vv['ext_order_id']);
        }else if($vv['type']=='ele'){
            K::M('ele/order')->recievecancel($vv['ext_order_id']);
        }

    }
}



//print_r(K::$system->db->SQLLOG());
//K::M('system/logs')->log('cron-sql-'.date('Ymd'), K::$system->db->SQLLOG());
K::M('system/logs')->log('cron-'.date('Ymd'), $_LOGDATA);
echo 'finish:'.date('Y-m-d H:i:s')."\n";
