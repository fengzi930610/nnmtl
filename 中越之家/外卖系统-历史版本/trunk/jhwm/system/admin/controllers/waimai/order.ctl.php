<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Waimai_Order extends Ctl
{

    public function get_shop_fees($order,$waimai_order,$shop){
        //修改外卖对账
        //1:仅商品价格
        //2:商品价格加上打包费
        //3:实际结算
        if($order['order_status']==8){
            $waimai_bills_log = K::M('waimai/billslog')->find(array('bills_number'=>$order['order_id']));
            $fee = $waimai_bills_log['fee'];
            $shop_amount = $waimai_bills_log['amount']+ $waimai_bills_log['fee'];
        }else{
            if($order['online_pay']==1){

                if($order['pei_type']==3&&$shop['is_ztsp']==1){
                    $shop['waimai_bl'] = $shop['zt_bl'];
                }
                $staff_amount = $order['pei_amount'];
                $shop_amount = $order['amount'] + $order['hongbao'] + $order['money']+$waimai_order['roof_amount']+$waimai_order['first_roof']-$staff_amount+$order['peicard_youhui'];
                $shop_amount -= K::M('order/order')->get_part_refunded_amount($order['order_id']);//2019-03-12 添加 减去已退金额
                if($shop['jiesuan_type']==1){
                    // $fee = number_format((($order['total_price']-$waimai_order['freight']-$waimai_order['package_price']) * $shop['waimai_bl'])/100, 2, '.', '');
                    $fee = number_format((($order['total_price']-$waimai_order['freight']-$waimai_order['package_price']) * $shop['waimai_bl'] - K::M('order/order')->get_part_refunded_amount($order['order_id']))/100, 2, '.', ''); //2019-03-12 添加 减去已退金额
                    if($fee<=0){
                        $fee = 0;
                    }
                }else if($shop['jiesuan_type']==2){
                    // $fee = number_format((($order['total_price']-$waimai_order['freight']) * $shop['waimai_bl'])/100, 2, '.', '');
                    $fee = number_format((($order['total_price']-$waimai_order['freight']) * $shop['waimai_bl'] - K::M('order/order')->get_part_refunded_amount($order['order_id']))/100, 2, '.', '');//2019-03-12 添加 减去已退金额
                    if($fee<=0){
                        $fee = 0;
                    }
                }else{
                    $fee = number_format(($shop_amount * $shop['waimai_bl'])/100, 2, '.', '');
                    if($shop_amount<=0){
                        $fee = 0;
                    }
                }
            }else if($order['online_pay']==0&&$order['pei_type']==1){
                $staff_amount = $order['pei_amount'];
                $shop_amount = $order['total_price']-$order['coupon']-$order['order_youhui']-$staff_amount-$order['first_youhui']+$waimai_order['roof_amount']+$waimai_order['first_roof']-$order['discount_youhui']-$order['huangou_youhui'];
                $shop_amount -= K::M('order/order')->get_part_refunded_amount($order['order_id']);//2019-03-12 添加 减去已退金额
                if($shop['jiesuan_type']==1){
                    // $fee = number_format((($order['total_price']-$waimai_order['freight']-$waimai_order['package_price']) * $shop['waimai_bl'])/100, 2, '.', '');
                    $fee = number_format((($order['total_price']-$waimai_order['freight']-$waimai_order['package_price'] - K::M('order/order')->get_part_refunded_amount($order['order_id'])) * $shop['waimai_bl'])/100, 2, '.', '');//2019-03-12 添加 减去已退金额
                    if($fee<=0){
                        $fee = 0;
                    }
                }else if($shop['jiesuan_type']==2){
                    // $fee = number_format((($order['total_price']-$waimai_order['freight']) * $shop['waimai_bl'])/100, 2, '.', '');
                    $fee = number_format((($order['total_price']-$waimai_order['freight']) * $shop['waimai_bl'] - K::M('order/order')->get_part_refunded_amount($order['order_id']))/100, 2, '.', '');//2019-03-12 添加 减去已退金额
                    if($fee<=0){
                        $fee = 0;
                    }
                }else{
                    $fee = number_format(($shop_amount * $shop['waimai_bl'])/100, 2, '.', '');
                    if($fee<=0){
                        $fee = 0;
                    }
                }
            }else{
                $shop_amount = $order['total_price']-$order['coupon']-$order['order_youhui']-$order['first_youhui']+$waimai_order['roof_amount']+$waimai_order['first_roof']-$order['discount_youhui']-$order['huangou_youhui'];
                $shop_amount -= K::M('order/order')->get_part_refunded_amount($order['order_id']);//2019-03-12 添加 减去已退金额
                $fee = 0;
            }
        }
        return array('fee'=>$fee,'shop_amount'=>$shop_amount-$fee);
    }

    public function get_shop_fee($order,$waimai_order,$shop)
    {
        $staff_amount = $staff_amount = 0;
        if($order['pei_type'] ){
            if($order['pei_type'] == 2){//代购订单，全部结算给配送员
                $staff_amount = $order['amount'] + $order['hongbao'] + $order['money'];
            }else{
                $staff_amount = $order['pei_amount'];
                $shop_amount = $order['amount'] + $order['hongbao'] + $order['money'] - $staff_amount+$waimai_order['roof_amount']+$waimai_order['first_roof'];
            }
        }else{
            //商家自己送
            $shop_amount = $order['amount'] + $order['hongbao'] + $order['money'] + $waimai_order['roof_amount'] + $waimai_order['first_roof'];
            //商家应得为 买家支付款 + 红包 + 余额抵扣+ 满减平台补贴 +首单优惠平台补贴
        }

        $fee = number_format(($shop_amount * $shop['waimai_bl'])/100, 2, '.', '');
        if($shop_amount<=0){
            $fee = 0;
        }
        return $fee;
    }
    
    public function index($page=1, $st=0)
    {
        //===20190111，根据参数，设置是否只显示虚拟商家的SESSION参数。
        session_start();
        if($this->GP('_ocs') !== NULL)
        {
            if((int)$this->GP('_ocs') === 1)
                $_SESSION['admin_order_only_custom_shop'] = true;
            else
                $_SESSION['admin_order_only_custom_shop'] = false;
        }
        //=======================================================

        $filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 10;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['order_id']){$filter['order_id'] = $SO['order_id'];}
            if($SO['shop_id'])//{$filter['shop_id'] = $SO['shop_id'];}
            {
                $SO['shop_id'] = (int)$SO['shop_id'];
                if($SO['shop_id'] > 0 && !isset($customShopIds[$SO['shop_id']]))
                    $customShopIds[$SO['shop_id']] = $SO['shop_id'];
            }
            if($SO['uid']){$filter['uid'] = $SO['uid'];}
            if(isset($SO['order_status'])){$st = $SO['order_status'];}
            /*if($SO['pay_status']>-1){$filter['pay_status'] = $SO['pay_status'];}*/
            if($SO['online_pay']>-1){$filter['online_pay'] = $SO['online_pay'];}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
        }
        $tmp = $SO?$SO:array();
        $tmp['tmp'] = "";
        $tmp['st'] = $st;
        $parmas = array(
            'SO'=> $tmp
        );

        $filter['from'] = "waimai";
        //$filter['refund_status'] = 0;
        switch ($st) {
            case '1': // 待接单
                $filter['order_status'] = 0;
                $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                $filter['pei_type'] = array(0, 1, 3);
                $filter['refund_status'] = 0;
                break;
            case '2': // 待配送
                $time = strtotime(date('Y-m-d'))+86399;
                $filter['order_status'] = 2;
                $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                $filter['pei_type'] = array(0, 1);
                $filter[':SQL'] = "refund_status != '1' AND (pei_time = '0' OR (pei_time < ".$time." AND pei_time>".strtotime(date('Y-m-d'))."))";
                break;
            case '3': // 配送中
                $filter['order_status'] = 3;
                $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                $filter['pei_type'] = array(0, 1);
                $filter[':SQL'] = "refund_status != '1'";
                break;
            case '4': // 退单
                $filter['order_status'] = array(1,2,3,4);// 允许退款的订单状态
                $filter['refund_status'] = 1;// 退款申请
                $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                $filter['pei_type'] = array(0, 1, 3);
                break;
            case '5': // 催单
                $filter['order_status'] = array(1,2,3);// 催单
                $filter['pei_type'] = array(0, 1, 3);
                $filter['refund_status'] = 0;
                $filter[':SQL'] = "cui_time > '0'";
                break;
            case '6': // 自提单
                $filter['order_status'] = array(0, 1,2);// 待接单、已接单
                $filter['refund_status'] = 0;
                $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                $filter['pei_type'] = 3;// 自提
                break;
            case '7': // 预订单
                $filter['order_status'] = 1;
                $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                $filter[':SQL'] = "refund_status != '1' AND pei_time > 0";
                break;
            case '8': // 待支付
                $filter['order_status'] = 0;
                $filter[':OR'] = array('pay_status'=>0, 'online_pay'=>0);// 已付款 || 货到付款
                $filter['pei_type'] = array(0, 1, 3);
                $filter['refund_status'] = 0;
                break;
            case "9":
                $filter['order_status'] = 8;
               // $filter[':SQL'] = "refund_status != '1'";
                break;
            case '10':
                $filter['order_status'] = array(-1,-2);
                //$filter[':SQL'] = "refund_status != '1'";
                break;
            default:
                break;
        }

        //4.0模糊查询
        if($SO && $SO['keywords']){
            if($filter[':SQL']){
                $filter[':SQL'] = $filter[':SQL']." AND (o.order_id ='".$SO['keywords']."' OR w.nickname LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%' OR ext.title LIKE '%".$SO['keywords']."%' OR ext.mobile LIKE '%".$SO['keywords']."%'".(preg_match('/^\d{15,}$/', trim($SO['keywords']))?(" OR o.order_id=".substr(trim($SO['keywords']), 14)):"").")";
            }else{
                $filter[':SQL'] = " (o.order_id ='".$SO['keywords']."' OR w.nickname LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%' OR ext.title LIKE '%".$SO['keywords']."%' OR ext.mobile LIKE '%".$SO['keywords']."%'".(preg_match('/^\d{15,}$/', trim($SO['keywords']))?(" OR o.order_id=".substr(trim($SO['keywords']), 14)):"").")";
            }
        }

        //===20190111，根据“只显示虚拟商家”的SESSION参数，设置配置项的查询条件。
        $customShopIds = null;
        $customShotCateIds = null;
        if(isset($_SESSION['admin_order_only_custom_shop']) && $_SESSION['admin_order_only_custom_shop'])
        {
            $tmpArr = K::M('waimai/waimai')->get_custom_mgr_shop_ids();
            if(count($tmpArr) > 0)
                $customShopIds = $tmpArr;
            $tmpArr = K::M('waimai/waimai')->get_custom_mgr_shop_cate_ids();
            if(count($tmpArr) > 0)
                $customShotCateIds = $tmpArr;
            unset($tmpArr);
        }

        //2019-01-26 因为模型waimai/waimai增加了获取配置ID列表的逻辑，所以不再需要直接从配置文件中检测数据的方式
        // if(isset($_SESSION['admin_order_only_custom_shop']) && $_SESSION['admin_order_only_custom_shop'])
        // {
        //     if(property_exists("__CFG","CUSTOM_MANAGE_SHOP_IDS") && is_array(__CFG::$CUSTOM_MANAGE_SHOP_IDS))
        //     {
        //         $customShopIds = [];
        //         if(count(__CFG::$CUSTOM_MANAGE_SHOP_IDS) > 0)
        //         {
        //             $ids = [];
        //             foreach(__CFG::$CUSTOM_MANAGE_SHOP_IDS as $pid)
        //             {
        //                 $pid = (int)$pid;
        //                 if($pid > 0 && !isset($ids[$pid]))
        //                     $ids[$pid] = $pid;
        //             }
        //             unset($pid);
        //             if(count($ids) > 0)
        //                 $customShopIds = $ids;
        //             unset($ids);
        //         }
        //     }

        //     if(property_exists("__CFG","CUSTOM_MANAGE_SHOP_CATEGORY_IDS") && is_array(__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS))
        //     {
        //         $customShotCateIds = [];
        //         if(count(__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS) > 0)
        //         {
        //             $ids = [];
        //             foreach(__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS as $pid)
        //             {
        //                 $pid = (int)$pid;
        //                 if($pid > 0 && !isset($ids[$pid]))
        //                     $ids[$pid] = $pid;
        //             }
        //             unset($pid);
        //             if(count($ids) > 0)
        //                 $customShotCateIds = $ids;
        //             unset($ids);
        //         }
        //     }
        // }
        //=======================================================================================================
        
        if($customShopIds !== null || $customShotCateIds !== null)
        {
            $setShopIdSql = [];
            if($customShopIds !== null)
            {
                if(count($customShopIds) > 0)
                {
                    $customShopIds = array_merge($customShopIds);
                    if(count($customShopIds) === 1)
                        $setShopIdSql[] = "o.shop_id={$customShopIds[0]}";
                    else
                        $setShopIdSql[] = "o.shop_id IN (".implode(",", $customShopIds).")";
                }
            }
            if($customShotCateIds !== null)
            {
                if(count($customShotCateIds) > 0)
                    $setShopIdSql[] = "o.shop_id IN(SELECT shop_id FROM ".K::M('waimai/waimai')->table("waimai")." WHERE cate_id IN(".implode(",", $customShotCateIds)."))";
            }
            if(count($setShopIdSql) === 0)
                $filter['shop_id'] = 0;
            else
            {
                if(empty($filter[':SQL']))
                    $filter[':SQL'] = "(".implode(" OR ", $setShopIdSql).")";
                else
                    $filter[':SQL'] .= " AND (".implode(") OR (", $setShopIdSql).")";
            }
        }
        //=====================================================================

        //==========2019-01-18 添加判断订单是否是需要中文到货通知服务 001=========
        $srvOids = [];
        $orderSrvInfo = [];
        $itemMap = [];
        //===============================================================
        if($items = K::M('order/order')->items_join_member_shop($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}', $st)), array('SO'=>$SO));

            $order_ids = $uids = $shop_ids = array();
            $group_ids = array();
            foreach ($items as $k=>$val){
                //==========2019-01-18 添加判断订单是否是需要中文到货通知服务 002=========
                $oid = (int)$val['order_id'];
                $srvOids[$oid] = $oid;
                $items[$k]['srv_arrival'] = false;
                $items[$k]['srv_arrival_admin_id'] = false;
                $items[$k]['srv_arrival_admin_name'] = false;
                $items[$k]['srv_arrival_close_time'] = 0;
                $itemMap[$oid] = &$items[$k];
                //===============================================================
                $items[$k] = K::M('waimai/order')->get_label($val);
                $order_ids[$val['order_id']] = $val['order_id'];
                $shop_ids[$val['shop_id']] = $val['shop_id'];
                $uids[$val['uid']] = $val['uid'];
                $staff_ids[$val['staff_id']] = $val['staff_id'];
                $items[$k]['user_order_count'] = $items[$k]['package_price'] = $items[$k]['fee'] = 0; //初始化
                $items[$k]['products'] = $items[$k]['waimai_order'] = array(); //初始化
                $items[$k]['detail_json'] = "";
                if($val['pei_type'] == 1 && $val['group_id']){
                    $group_ids[$val['group_id']] = $val['group_id'];
                }
            }

            if($group_ids){
                $groups = K::M('pei/group')->items_by_ids($group_ids);
            }

             // 商品列表
            if($product_list = K::M('waimai/orderproduct')->items(array('order_id'=>$order_ids), null, 1, 500)){
                foreach($product_list as $k => $v){
                    $items[$v['order_id']]['products'][] = $v;
                    $items[$v['order_id']]['package_price'] += $v['package_price']; // 打包费 = 每个商品的打包费总和
                }
            }
            // 外卖订单列表
            if ($waimai_order_list = K::M('waimai/order')->items_by_ids($order_ids)) {
                foreach ($waimai_order_list as $k => $v) {
                    $items[$k] = array_merge($items[$v['order_id']], $v);
                }
            }else{
                $waimai_order_list = array();
            }
            // 获取外卖商家
            if ($waimai_shop = K::M('waimai/waimai')->items_by_ids($shop_ids)) {
                foreach ($items as $k => $v){
                    $items[$k]['waimai_order'] = $waimai_order_list[$v['order_id']];
                    $items[$k]['fee'] = $this->get_shop_fees($v,$waimai_order_list[$v['order_id']] ,$waimai_shop[$v['shop_id']]);
                }
            }else{
                $waimai_shop = array();
            }

            if (!empty($shop_ids) && !empty($uids)) {
                $map['shop_id'] = 'IN:'.implode(',', $shop_ids);
                $map['uid'] = 'IN:'.implode(',', $uids);
                $map['from'] = 'waimai';
                $map['closed'] = 0;
                $map[':SQL'] = "order_status >= '0'";
                if ($maps = K::M('order/order')->items_group_by_ids($map)) { // 用于查询当前用户在当前商家下单的数量
                    foreach ($items as $k => $v) {
                        $items[$k]['user_order_count'] = intval($maps[$v['uid'].'_'.$v['shop_id']]);
                    }
                }
            }
            
            foreach ($items as $k => $v) {
                $pay_type = $v['online_pay'] == 1 ? "在线支付" : "餐到付款";
                $items[$k]['detail_json'] = json_encode(array(
                    'products'=>$v['products'],
                    'detail'=>array(
                        'order_id'=>$v['order_id'],
                        'dateline'=>date('Y-m-d H:i:s', $v['dateline']),
                        'pay_type'=>$pay_type,
                        'intro'=>$v['intro'],
                    ),
                    'detail_2'=>K::M('waimai/order')->_format_order_detail($v)
                ));
                switch ($v['pei_type']) {
                    case '3':
                        $pei_type_label = '用户自提';
                        break;
                    case '1':
                        $pei_type_label = '平台配送';
                        if($group = $groups[$v['group_id']]){
                            $pei_type_label .= '--'.$group['group_name'];
                        }
                        break;
                    case '0':
                        $pei_type_label = '商户配送';
                        break;
                    default:
                        $pei_type_label = '';
                        break;
                }
                $items[$k]['pei_type_label'] = $pei_type_label;
            }
            foreach($items as $klay=>$vlay){
                $items[$klay] = K::M('waimai/order')->get_label($vlay);
            }
        }

        //==========2019-01-18 添加判断订单是否是需要中文到货通知服务 003=========
        if(count($srvOids) > 0)
        {
            $tmpList = K::M('order/srv/arrival')->items_by_ids($srvOids);
            $adminIds = [];
            foreach($tmpList as &$tmpItem)
            {
                $adminId = (int)$tmpItem['admin_id'];
                $adminIds[$adminId] = $adminId;
                $oid = (int)$tmpItem['order_id'];
                $itemMap[$oid]['srv_arrival'] = true;
                $itemMap[$oid]['srv_arrival_admin_id'] = $adminId;
                $itemMap[$oid]['srv_arrival_close_time'] = (int)$tmpItem['close_time'];
                unset($tmpItem);
            }

            if(count($adminIds) > 0)
            {
                $adminInfos = [];
                $tmpList = K::M('admin/base')->items_by_ids($adminIds);
                foreach($tmpList as &$tmpItem)
                {
                    $adminId = (int)$tmpItem['admin_id'];
                    $adminInfos[$adminId] = $tmpItem['admin_name'];
                    unset($tmpItem);
                }
                foreach($itemMap as $oid => &$tmpItem)
                {
                    if($itemMap[$oid]['srv_arrival_admin_id'] > 0 && isset($adminInfos[$itemMap[$oid]['srv_arrival_admin_id']]))
                        $itemMap[$oid]['srv_arrival_admin_name'] = $adminInfos[$itemMap[$oid]['srv_arrival_admin_id']];
                    unset($tmpItem);
                }
                unset($adminInfos);
            }
            unset($tmpList,$adminIds,$tmpItem);
        }
        //====================================================================

        //$this->pagedata['users'] = K::M('member/member')->items_by_ids($uids);
        $this->pagedata['shops'] = $waimai_shop;
        $this->pagedata['staffs'] = K::M('staff/staff')->items_by_ids($staff_ids);
        $payments = K::M('payment/payment')->fetch_all();
        $pays = array();
        foreach($payments as $k=>$val){
            $pays[$val['payment']] = $val;
        }
        $this->pagedata['pays'] = $pays;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['st'] = $st;
        $this->pagedata['parmas'] = http_build_query($parmas);

        //2019-03-04 添加 是否为单类型页面
        $sigle_type_page = false;
        if($this->GP('stp') && (int)$this->GP('stp')!==0)
            $sigle_type_page = true;
        $this->pagedata['sigle_type_page'] = $sigle_type_page;
        //============================

        $this->tmpl = 'admin:waimai/order/items.html';
    }
    
    public function rights($page=1)
    {
        //===20190111，根据参数，设置是否只显示虚拟商家的SESSION参数。
        session_start();
        if($this->GP('_ocs') !== NULL)
        {
            if((int)$this->GP('_ocs') === 1)
                $_SESSION['admin_order_only_custom_shop'] = true;
            else
                $_SESSION['admin_order_only_custom_shop'] = false;
        }
        //=======================================================

    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['order_id']){$filter['order_id'] = $SO['order_id'];}
            if($SO['shop_id']){$filter['shop_id'] = $SO['shop_id'];}
            if($SO['uid']){$filter['uid'] = $SO['uid'];}
            //if($SO['order_status']>-8){$filter['order_status'] = $SO['order_status'];}
            if($SO['pay_status']>-1){$filter['pay_status'] = $SO['pay_status'];}
            if($SO['online_pay']>-1){$filter['online_pay'] = $SO['online_pay'];}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            
            //模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (o.order_id = '".$SO['keywords']."' OR w.nickname LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%' OR ext.title LIKE '%".$SO['keywords']."%' OR ext.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        $filter['from'] = "waimai";
        $filter['refund_status'] = "<>:0";

        //===20190111，根据“只显示虚拟商家”的SESSION参数，设置配置项的查询条件。
        $customShopIds = null;
        $customShotCateIds = null;
        if(isset($_SESSION['admin_order_only_custom_shop']) && $_SESSION['admin_order_only_custom_shop'])
        {
            $tmpArr = K::M('waimai/waimai')->get_custom_mgr_shop_ids();
            if(count($tmpArr) > 0)
                $customShopIds = $tmpArr;
            $tmpArr = K::M('waimai/waimai')->get_custom_mgr_shop_cate_ids();
            if(count($tmpArr) > 0)
                $customShotCateIds = $tmpArr;
            unset($tmpArr);
        }

        //2019-01-26 因为模型waimai/waimai增加了获取配置ID列表的逻辑，所以不再需要直接从配置文件中检测数据的方式
        // if(isset($_SESSION['admin_order_only_custom_shop']) && $_SESSION['admin_order_only_custom_shop'])
        // {
        //     if(property_exists("__CFG","CUSTOM_MANAGE_SHOP_IDS") && is_array(__CFG::$CUSTOM_MANAGE_SHOP_IDS))
        //     {
        //         $customShopIds = [];
        //         if(count(__CFG::$CUSTOM_MANAGE_SHOP_IDS) > 0)
        //         {
        //             $ids = [];
        //             foreach(__CFG::$CUSTOM_MANAGE_SHOP_IDS as $pid)
        //             {
        //                 $pid = (int)$pid;
        //                 if($pid > 0 && !isset($ids[$pid]))
        //                     $ids[$pid] = $pid;
        //             }
        //             unset($pid);
        //             if(count($ids) > 0)
        //                 $customShopIds = $ids;
        //             unset($ids);
        //         }
        //     }

        //     if(property_exists("__CFG","CUSTOM_MANAGE_SHOP_CATEGORY_IDS") && is_array(__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS))
        //     {
        //         $customShotCateIds = [];
        //         if(count(__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS) > 0)
        //         {
        //             $ids = [];
        //             foreach(__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS as $pid)
        //             {
        //                 $pid = (int)$pid;
        //                 if($pid > 0 && !isset($ids[$pid]))
        //                     $ids[$pid] = $pid;
        //             }
        //             unset($pid);
        //             if(count($ids) > 0)
        //                 $customShotCateIds = $ids;
        //             unset($ids);
        //         }
        //     }
        // }
        //===================================================================================================
        
        if($customShopIds !== null || $customShotCateIds !== null)
        {
            $setShopIdSql = [];
            if($customShopIds !== null)
            {
                if(count($customShopIds) > 0)
                {
                    $customShopIds = array_merge($customShopIds);
                    if(count($customShopIds) === 1)
                        $setShopIdSql[] = "o.shop_id={$customShopIds[0]}";
                    else
                        $setShopIdSql[] = "o.shop_id IN (".implode(",", $customShopIds).")";
                }
            }
            if($customShotCateIds !== null)
            {
                if(count($customShotCateIds) > 0)
                    $setShopIdSql[] = "o.shop_id IN(SELECT shop_id FROM ".K::M('waimai/waimai')->table("waimai")." WHERE cate_id IN(".implode(",", $customShotCateIds)."))";
            }
            if(count($setShopIdSql) === 0)
                $filter['shop_id'] = 0;
            else
            {
                if(empty($filter[':SQL']))
                    $filter[':SQL'] = "(".implode(" OR ", $setShopIdSql).")";
                else
                    $filter[':SQL'] .= " AND (".implode(") OR (", $setShopIdSql).")";
            }
        }
        //=====================================================================

        $order_ids = $uids = $shop_ids = array();
        if($items = K::M('order/order')->items_join_member_shop($filter, null, $page, $limit, $count)){
            foreach ($items as $k=>$val){
                $items[$k] = K::M('waimai/order')->get_label($val);
                $order_ids[$val['order_id']] = $val['order_id'];
                $shop_ids[$val['shop_id']] = $val['shop_id'];
                $uids[$val['uid']] = $val['uid'];
                $staff_ids[$val['staff_id']] = $val['staff_id'];
            }
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }

        //维权订单状态：refund  -1正常，0处理中，1同意，2商户拒绝，3平台拒绝
        if($order_ids && $refunds = K::M('waimai/order/refund')->items_by_ids($order_ids)){
            foreach ($items as $k => $v) {
                if(!$refunds[$v['order_id']]){
                    $v['refund'] = -1;
                }else{
                    $v['refund'] = $refunds[$v['order_id']]['status'];
                }
                if($v['refund'] == 3 && $v['order_status'] != 8){
                    $v['refund'] = 0; //用户申请客服
                }
                $items[$k] = $v;
            }
        }
                
        $this->pagedata['orders'] = K::M('waimai/order')->items_by_ids($order_ids);
        //$this->pagedata['users'] = K::M('member/member')->items_by_ids($uids);
        //$this->pagedata['shops'] = K::M('waimai/waimai')->items_by_ids($shop_ids);
        $this->pagedata['staffs'] = K::M('staff/staff')->items_by_ids($staff_ids);
        $payments = K::M('payment/payment')->fetch_all();
        $pays = array();
        foreach($payments as $k=>$val){
            $pays[$val['payment']] = $val;
        }
        //print_r($items);die;
        $this->pagedata['pays'] = $pays;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:waimai/order/rights.html';
    }
       
    public function so($type=0, $st=0)
    {
        $this->pagedata['type'] = $type;
        $this->pagedata['st'] = $st;
        $this->tmpl = 'admin:waimai/order/so.html';
    }
        
    public function detail($order_id = null)
    {
        if(!$order_id = (int)$order_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('waimai/order')->detail($order_id,true)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            //print_r($detail);die;
            $detail = K::M('waimai/order')->get_label($detail);

            if($detail['pei_type']==1&&$detail['online_pay']==0){
                $s_amount =$detail['total_price']-$detail['coupon']-$detail['order_youhui']-$detail['pei_amount']-$detail['first_youhui']+$detail['roof_amount']+$detail['first_roof'];
            }else{
                $s_amount = $detail['amount'] + $detail['hongbao'] + $detail['money'] - $detail['pei_amount'] + $detail['roof_amount'] + $detail['first_roof'];
            }

            $waimai = K::M('waimai/waimai')->detail($detail['shop_id']);
            $detail['r_amount'] = number_format(($s_amount * $waimai['waimai_bl'])/100, 2, '.', '');
            $detail['s_amount'] = $s_amount - $detail['r_amount'];
            $products = K::M('waimai/orderproduct')->items(array('order_id'=>$order_id));
            foreach($products as $k=>$v){
                        $shuxin = "";
                        foreach($v['specification'] as $vvt){
                            $shuxin.="+".$vvt['val'];
                        }
                        $v['shuxin'] = $shuxin;
                        $products[$k]['product_name'] = $v['product_name'].$v['shuxin'];
            }
            $detail['products'] = $products;
            $detail['user'] = K::M('member/member')->detail($detail['uid']);
            $detail['shop'] = K::M('waimai/waimai')->detail($detail['shop_id']);
            $detail['logs'] = K::M('order/log')->items(array('order_id'=>$order_id),array('log_id'=>'DESC'));
            foreach ($detail['logs'] as $log_k=>$log_v){
                if(!$log_v['log']){
                    unset($detail['logs'][$log_k] );
                }
            }
            $payments = K::M('payment/payment')->fetch_all();
            $pays = array();
            foreach($payments as $k=>$val){
                $pays[$val['payment']] = $val['title'];
            }
            $detail['payments'] = $pays;
            $detail['staff'] = K::M('staff/staff')->detail($detail['staff_id']);
            $detail['types'] = K::M('order/log')->get_log_types();
            $payments = K::M('order/order')->get_payments();
            $order_from = array('weixin'=>'微信','ios'=>'苹果APP','android'=>'安卓APP','wap'=>'wap端','www'=>'网页端');
            //print_r($detail);die;
            if($detail['order_status']==8&&$detail['comment_status']==1){
                $detail['comment'] = K::M('waimai/comment')->find(array('order_id'=>$order_id));
                $detail['comment_photo'] = K::M('waimai/commentphoto')->items(array('comment_id'=> $detail['comment']['comment_id']));
            }else{
                $detail['comment'] = array();
            }
            $zhu_order = K::M('order/order')->detail($order_id);
            $detail['fee'] = $this->get_shop_fees($zhu_order,$detail,$waimai);

            //2019-03-02 添加 加入是否需要中文到货通知数据
            $detail['srv_arrival'] = false;
            $detail['srv_arrival_close_time'] = 0;
            $srvInfo = K::M('order/srv/arrival')->find(['order_id'=>(int)$detail['order_id']]);
            if($srvInfo)
            {
                $detail['srv_arrival'] = true;
                $detail['srv_arrival_close_time'] = (int)$srvInfo['close_time'];
            }
            //=================================

            //2019-03-12 添加 获取部分退款列表数据
            $refundInfoList = K::M('order/refund')->items(['order_id'=>$order_id],"create_time ASC",1,999999);
            if(!$refundInfoList)
                $refundInfoList = [];
            $this->pagedata['part_refund_list'] = $refundInfoList;
            $this->pagedata['total_refund_amount'] = K::M('order/order')->get_part_refunded_amount($order_id);
            //===========================================

            $this->pagedata['froms'] = $order_from[$detail['order_from']];
            $this->pagedata['pay_method'] = $payments[$detail['pay_code']];
            $this->pagedata['detail'] = $detail;
            $this->pagedata['waimai'] = $waimai;

            $this->tmpl = 'admin:waimai/order/detail.html';
        }
    }

    public function create()
    {
        if($data = $this->checksubmit('data')){
            
            if($order_id = K::M('waimai/order')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?waimai/order-index.html');
            } 
        }else{
           $this->tmpl = 'admin:waimai/order/create.html';
        }
    }

    public function edit($order_id=null)
    {
        if(!($order_id = (int)$order_id) && !($order_id = $this->GP('order_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('waimai/order')->detail($order_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            //2019-03-02 添加，如果数据中备注，则调用原始订单模型进行更新
            $uptOrg = true;
            if(isset($data['remark']) || isset($data['remark_mgr']))
                $uptOrg = K::M('order/order')->update($order_id, $data);
            if($uptOrg && K::M('waimai/order')->update($order_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:waimai/order/edit.html';
        }
    }

    public function doaudit($order_id=null)
    {
        if($order_id = (int)$order_id){
            if(K::M('waimai/order')->batch($order_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('order_id')){
            if(K::M('waimai/order')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }

    public function delete($order_id=null)
    {
        if($order_id = (int)$order_id){
            if(!$detail = K::M('waimai/order')->detail(order_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('waimai/order')->delete($order_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('order_id')){
            if(K::M('waimai/order')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
    
    // 取消订单
    public function cancel($order_id=null,$is_check = true)
    {

      /*  if(!$order_id){
            $this->msgbox->add('订单不存在',300);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',300);
        }else if($order['order_status']==-1){
            $this->msgbox->add('订单已取消',300);
        }else if($order['order_status']==8){
            $this->msgbox->add('订单已完成',300);
        }else if($order['order_status']==-2){
            $this->msgbox->add('订单已完成退款',300);
        }else if(($order['order_status']==1||$order['order_status']==2)){
            $this->msgbox->add('商家已接单',301);
        }else if($order['order_status']==3){
            $this->msgbox->add('配送员已开始配送',301);
        }else if($order['order_status']==4){
            $this->msgbox->add('配送员已送达',301);
        }else{
            $this->cancel($order_id);
        }*/

        if($order_id = (int)$order_id){
            if(!$order_id) {
                $this->msgbox->add('订单号不存在',210);
            }else if(!$order = K::M('order/order')->detail($order_id)) {
                $this->msgbox->add('该订单不存在',211);
            }else if($order['order_status'] ==-1 || $order['order_status']==8){
                $this->msgbox->add('该订单不可取消',213);
            }else if($order['order_status']==1&&$is_check){
                $this->msgbox->add('商家已接单',300)->response();
            }else if($order['order_status']==2&&$is_check&&!$order['staff_id']){
                $this->msgbox->add('商家已接单',300)->response();
            }else if($order['order_status']==2&&$is_check&&$order['staff_id']){
                $this->msgbox->add('配送员已接单',300)->response();
            }else if($order['order_status']==3){
                $this->msgbox->add('配送员已开始配送',300)->response();
            }else if($order['order_status']==4){
                $this->msgbox->add('配送员已送达',300)->response();
            }else if(K::M('order/order')->cancel($order_id,$order,'admin')){
                //========== 2019-01-18 取消订单，要把到货服务数据删除 =========
                K::M('order/srv/arrival')->delete($order_id);
                //===========================================================
                $this->msgbox->add('取消订单成功');
            }else{
                $this->msgbox->add('取消订单失败',215);
            }
        }
    }

    // 订单完成
    public function complete($order_id=null,$is_check=true)
    { 
        if($order_id = (int)$order_id){
            if(!$order_id) {
                $this->msgbox->add('订单号不存在',210);
            }else if(!$order = K::M('order/order')->detail($order_id)) {
                $this->msgbox->add('该订单不存在',211);
            }else if($order['order_status']==-1){
                $this->msgbox->add('订单已取消',212);
            }else if($order['online_pay']==1&&$order['pay_status']==0){
                $this->msgbox->add('订单还未支付',213);
            }else if($order['order_status']==8){
                $this->msgbox->add('订单已完成，不可重复确认完成',214);
            }else if($order['order_status']!=4&&$is_check&&$order['pei_type']==1){
                $this->msgbox->add('配送员还未送达，是否强制完成？',300);
            }else if(K::M('order/order')->confirm($order_id,$order,'admin')){
                $this->msgbox->add('订单确认成功');
            }else{
                $this->msgbox->add('订单确认失败',216);
            } 
        }
    }

    public function jiedan($order_id)
    {
        if($order_id = (int)$order_id){
            if(!$order_id) {
                $this->msgbox->add('订单号不存在',210);
            }else if(!$order = K::M('waimai/order')->detail($order_id)) {
                $this->msgbox->add('该订单不存在',211);
            }else if($order['order_status']!=0){
                $this->msgbox->add('当前订单不可接单',214);
            }else if(($order['online_pay'] == 1&&$order['pay_status'] == 0)){
                $this->msgbox->add('该订单不可接单',213);
            }else{
                if(K::M('order/order')->update($order_id, array('order_status'=>2,'lasttime'=>__TIME))){
                    $log = '商家已接单(订单号:'.$order_id.')';
                    K::M('order/time')->update($order_id,array('shop_jiedan_time'=>__TIME));
                    K::M('order/log')->create(array('from'=>'admin', 'log'=>$log, 'order_id'=>$order_id));
                    K::M('waimai/log')->create(array('from'=>'admin', 'log'=>$log, 'order_id'=>$order_id, 'type'=>3));//-1取消，0其他，1下单，2支付，3接单，4配送，5送达，6确认完成 7.申请退款 8.已退款 9.拒绝退款
                    //通知用户,APP推送 weixin模板消息
                    $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
                    K::M('order/order')->send_member('商家已经接单', sprintf("您在[%s]下的订单(%s)，商家已接单", $waimai['title'], $order_id), $order);
                   // $this->msgbox->set_data('data',$data);
                    $this->msgbox->add('接单成功！');
                    $this->msgbox->json();
                }else{
                    $this->msgbox->add('接单失败',219);
                }
            }
        }
    }

    public function paidan($page)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['pay_status'] = 1;
        $filter['from'] = 'waimai';
        $filter[':SQL'] = "(`order_status` IN(1,2,3,4) OR (`order_status`=0 AND `pei_type`=2))";
        $filter['staff_id'] = 0;    // 0等待配送员接单
        $filter['pei_type'] = array(1,2);

        if($SO = $this->GP('SO')){
            //4.0
            $pager['SO'] = $SO;
            if($SO['order_id']){$filter['order_id'] = $SO['order_id'];}
            if($SO['shop_id']){$filter['shop_id'] = $SO['shop_id'];}
            if($SO['uid']){$filter['uid'] = $SO['uid'];}
            if($SO['online_pay']>-1){$filter['online_pay'] = $SO['online_pay'];}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            
            //模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = $filter[':SQL']." AND (o.order_id = '".$SO['keywords']."' OR w.nickname LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%' OR ext.title LIKE '%".$SO['keywords']."%' OR ext.mobile LIKE '%".$SO['keywords']."%')";
            }
        }

        if($items = K::M('order/order')->items_join_member_shop($filter, array('order_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }        
        $shop_ids = $order_ids =array();
        foreach($items as $k=>$val){
            $order_ids[$val['order_id']] = $val['order_id'];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
            $items[$k] = K::M('waimai/order')->format_data($val);
        }
        if($waimai_order_list = K::M('waimai/order')->items_by_ids($order_ids)){
            foreach($waimai_order_list as $k=>$v){
                $items[$k] = array_merge($items[$k], $v);
            }
        }
        //$this->pagedata['shops'] = K::M('waimai/waimai')->items_by_ids($shop_ids);
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:waimai/order/paidan.html';
    }

    public function dopaidan($order_id=null)
    {
        if(!($order_id=(int)$order_id) && !($order_id = (int)$this->GP('order_id'))){
            $this->msgbox->set_data('未指定要派单的单号',211);
        }else if(!$order = K::M('waimai/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已经删除', 211);
        }else if($order['staff_id']>0){
            $this->msgbox->add('该订单已经有人配送了，您可以选取消再派单',212);
        }else if(!$order['pay_status']){
            $this->msgbox->add('未支付订单不可派单', 213);
        }else if(!in_array($order['pei_type'], array(1, 2))){
            $this->msgbox->add('该订单为商家自送，不可派单', 214);
        }else if(!in_array($order['order_status'], array(0,1,2,3,4))){
            $this->msgbox->add('该订单状态不可派单', 215);
        }else if($order['order_status']==0 && (int)$order['pei_type']!==2){
            $this->msgbox->add('该订单状态不可派单', 215);
        }else if($data = $this->checksubmit('data')){
            if(!$staff = K::M('staff/staff')->detail((int)$data['staff_id'])){
                $this->msgbox->add('指派的配送员不存在', 216);
            }else if(K::M('order/order')->update($order_id, array('staff_id'=>$staff['staff_id'], 'order_status'=>2,'jd_time'=>__TIME))){
                K::M('order/time')->update($order_id,array('staff_jiedan_time'=>__TIME));
                //记录订单日志
                K::M('order/log')->create(array('order_id'=>$order_id, 'from'=>'staff', 'log'=>'配送员('.$this->staff['name'].')准备为您配送', 'type'=>'2'));
                //增加订单统计
                K::M('staff/staff')->update_count($staff['staff_id'], 'orders', 1);
                //推送消息给配送员
                $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
                $addr = $waimai['addr'].$waimai['house'];
                $title = sprintf("系统指派了[%s]外送订单(单号:%s)给您", $waimai['title'], $order_id);
                $content = sprintf("系统指派订单(单号：%s)(%s，%s)给您,取餐地址:[%s]%s", $order_id, $order['contact'], $order['mobile'], $waimai['title'], $addr);
                K::M('staff/staff')->send($staff['staff_id'], $title, $content, array('type'=>'newOrder', 'order_id'=>$order_id));
                $this->msgbox->add('指派配送员成功');
            }
        }else{
            $this->pagedata['order'] = $order;
            $this->tmpl = 'admin:waimai/order/dopaidan.html';
        }
    }
    
    
    //同意退款
    public function agree($order_id)
    {
        if (!$order_id = (int)$order_id) {
            $this->msgbox->add(L('错误的订单!'), 211);
        }elseif (!$order = K::M('waimai/order')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的退款订单!'), 212);
        }elseif (!in_array($order['order_status'], array(1,2,3,4)) && ($order['refund_status'] != 1||$order['refund_status'] != 3) && $order['closed'] != 0 && $order['from'] != 'waimai') {
            $this->msgbox->add(L('没有要操作的退款订单!'), 213);
        }elseif (!$refund_order = K::M('waimai/order/refund')->detail($order_id)) {
            $this->msgbox->add(L('没有要退款的订单!'), 214);
        }elseif (K::M('order/order')->refund($order,"","admin")) {
            $log_data = array();
            $log_data['uid'] = $order['uid'];
            $log_data['title'] = '平台同意退款';
            $log_data['content'] = "平台同意您的订单ID(".$order_id.")的退款申请";
            $log_data['type'] = 2;
            $log_data['is_read'] = 0;
            $log_data['can_id'] = 0;
            K::M('member/message')->create($log_data);
            $this->msgbox->add(L('退款成功！'));
        }else{
            $this->msgbox->add(L('退款失败！'), 214);
        }
    }

    //拒绝退款
    public function refuse($order_id)
    {
        if (!$order_id = (int)$order_id) {
            $this->msgbox->add(L('错误的订单!'), 211);
        }elseif (!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的退款订单!'), 213);
        }elseif (!in_array($order['order_status'], array(1,2,3,4)) && $order['refund_status'] != 1 && $order['closed'] != 0 && $order['from'] != 'waimai') {
            $this->msgbox->add(L('没有要操作的退款订单!'), 214);
        }elseif (!$refund_order = K::M('waimai/order/refund')->detail($order_id)) {
            $this->msgbox->add(L('没有要操作的退款订单!'), 215);
        }else{
            if($this->checksubmit()){
                if (!$reply = trim(htmlspecialchars($this->GP('reply')))) {
                    $this->msgbox->add(L('请填写拒绝原因!'), 212);
                }elseif (K::M('order/order')->refund_refused($order, $reply,'admin')) {
                    $this->msgbox->add(L('拒绝退款成功！'));
                    $log_data = array();
                    $log_data['uid'] = $order['uid'];
                    $log_data['title'] = '平台拒绝退款';
                    $log_data['content'] = "平台拒绝您的订单ID(".$order_id.")退款申请，原因:(".$reply.")";
                    $log_data['type'] = 2;
                    $log_data['is_read'] = 0;
                    $log_data['can_id'] = 0;
                    K::M('member/message')->create($log_data);
                }else{
                    $this->msgbox->add(L('拒绝退款失败！'), 215);
                }
            } else {
                $this->pagedata['order'] = $order;
                $this->tmpl = 'admin:waimai/order/refuse.html';
            } 
        }
    }

    public function export()
    {
        if($SO = $this->checksubmit('SO')){
            $st = 0;
            $filter['from'] = "waimai";
            if($SO['order_id']){
                $filter['order_id'] = $SO['order_id'];
            }
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }
            if($SO['uid']){
                $filter['uid'] = $SO['uid'];
            }
            if(isset($SO['order_status'])){
                $st = $SO['order_status'];
            }
            if($SO['online_pay']>-1){
                $filter['online_pay'] = $SO['online_pay'];
            }
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1])+86400;
                    $filter['dateline'] = $a."~".$b;
                }
                if(!$SO['dateline'][0]&&$SO['dateline'][1]){
                    $b = strtotime($SO['dateline'][1])+86400;
                    $filter['dateline'] = "<:".$b;
                }
                if($SO['dateline'][0]&&!$SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]);
                    $filter['dateline']=">:".$a;
                }
            }
            $file_str = "";
            switch ($st) {
                case '1': // 待接单
                    $file_str = "待接单";
                    $filter['order_status'] = 0;
                    $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                    $filter['pei_type'] = array(0, 1, 3);
                    $filter['refund_status'] = 0;
                    break;
                case '2': // 待配送
                    $file_str = "待配送";
                    $time = strtotime(date('Y-m-d'))+86399;
                    $filter['order_status'] = 2;
                    $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                    $filter['pei_type'] = array(0, 1);
                    $filter[':SQL'] = "refund_status != '1' AND (pei_time = '0' OR (pei_time < ".$time." AND pei_time>".strtotime(date('Y-m-d'))."))";
                    break;
                case '3': // 配送中
                    $file_str = "配送中";
                    $filter['order_status'] = 3;
                    $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                    $filter['pei_type'] = array(0, 1);
                    $filter[':SQL'] = "refund_status != '1'";
                    break;
                case '4': // 退单
                    $file_str = "退单";
                    $filter['order_status'] = array(1,2,3,4);// 允许退款的订单状态
                    $filter['refund_status'] = 1;// 退款申请
                    $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                    $filter['pei_type'] = array(0, 1, 3);
                    break;
                case '5': // 催单
                    $file_str = "催单";
                    $filter['order_status'] = array(1,2,3);// 催单
                    $filter['pei_type'] = array(0, 1, 3);
                    $filter['refund_status'] = 0;
                    $filter[':SQL'] = "cui_time > '0'";
                    break;
                case '6': // 自提单
                    $file_str = "自提单";
                    $filter['order_status'] = array(0, 1,2);// 待接单、已接单
                    $filter['refund_status'] = 0;
                    $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                    $filter['pei_type'] = 3;// 自提
                    break;
                case '7': // 预订单
                    $file_str = "预订单";
                    $filter['order_status'] = 1;
                    $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                    $filter[':SQL'] = "refund_status != '1' AND pei_time > 0";
                    break;
                case '8': // 待支付
                    $file_str = "待支付";
                    $filter['order_status'] = 0;
                    $filter[':OR'] = array('pay_status'=>0, 'online_pay'=>0);// 已付款 || 货到付款
                    $filter['pei_type'] = array(0, 1, 3);
                    $filter['refund_status'] = 0;
                    break;
                case "9"://已完成
                    $file_str = "已完成";
                    $filter['order_status'] = 8;
                    $filter[':SQL'] = "refund_status != '1'";
                    break;
                case '10'://已取消
                    $file_str = "已取消";
                    $filter['order_status'] = -1;
                    $filter[':SQL'] = "refund_status != '1'";
                    break;
                default:
                    break;
            }
            $items = K::M('order/order')->items($filter,array('order_id'=>'DESC'),1,5000,$count);
            if($count==0){
                $this->msgbox->add('没有找到需要导出的订单，请修改筛选条件',201)->response();
            }else if($count>5000){
                $this->msgbox->add('需要导出的订单大于5000条，请修改筛选条件',202)->response();
            }else {
                $file_name = $file_str . '订单列表';
                $key_arr = array(
                    "订单编号", "订单类型",'订单状态', "商家名称", "餐盒费", "配送费", "小计", "平台收取", "商家预计收入", "下单时间", "期望送达时间", "收货地址"
                );
                $row = array();
                $shop_ids = $member_ids = $order_ids = array();
                foreach ($items as $k => $v) {
                    $shop_ids[$v['shop_id']] = $v['shop_id'];
                    $member_ids[$v['uid']] = $v['uid'];
                    $order_ids[$v['order_id']] = $v['order_id'];
                }
                foreach ($items as $k1=>$v1){
                    $items[$k1] = K::M('waimai/order')->get_label($v1);
                }
                $shop_list = K::M('waimai/waimai')->items_by_ids($shop_ids);
                $member_list = K::M('member/member')->items_by_ids($member_ids);
                $waimai_order_list = K::M("waimai/order")->items_by_ids($order_ids);
                foreach ($items as $k => $v) {
                    if ($v['pei_time'] == 0) {
                        $pei_time = "尽快送达";
                    } else {
                        $pei_time = date('Y-m-d H:i', $v['pei_time']);
                    }
                    if($v['pei_type']==1&&$v['online_pay']==0){
                        $s_amount =$v['total_price']-$v['coupon']-$v['order_youhui']-$v['pei_amount']-$v['first_youhui']+$waimai_order_list[$v['order_id']]['roof_amount']+$waimai_order_list[$v['order_id']]['first_roof'];
                    }else{
                        $s_amount = $v['amount'] + $v['hongbao'] + $v['money'] - $v['pei_amount'] + $waimai_order_list[$v['order_id']]['roof_amount'] + $waimai_order_list[$v['order_id']]['first_roof'];
                    }

                  //  $s_amount = $v['amount'] + $v['hongbao'] + $v['money'] - $v['pei_amount'] + $waimai_order_list[$v['order_id']]['roof_amount'] + $waimai_order_list[$v['order_id']]['first_roof'];
                    $true_r_amount = number_format(($s_amount * $shop_list[$v['shop_id']]['waimai_bl']) / 100, 2, '.', '');
                    $true_r_amount = $true_r_amount > 0 ? $true_r_amount : 0;
                    $true_s_amount = $s_amount - $true_r_amount;
                    $row[] = array(
                        $v['order_id'],
                        "外卖",
                        $v['order_status_label'],
                        $shop_list[$v['shop_id']]['title'],
                        $waimai_order_list[$v['order_id']]['package_price'],
                        $v['pei_amount'],
                        $v['total_price'],
                        $true_r_amount,
                        $true_s_amount,
                        date('Y-m-d H:i:s', $v['dateline']),
                        $pei_time,
                        '[' . $member_list[$v['uid']]['nickname'] . ']' . $v['addr']
                    );
                }
                $this->msgbox->add('导出成功');
                K::M('dataio/xls')->export($key_arr, $row, $file_name);
            }

        }else{
            $this->tmpl = 'admin:waimai/order/export.html';
        }
    }

   /* public function export($st= 0){
        $SO = $this->GP('SO');
        $filter['from'] = "waimai";
        if($SO['order_id']){
            $filter['order_id'] = $SO['order_id'];
        }
        if($SO['shop_id']){
            $filter['shop_id'] = $SO['shop_id'];
        }
        if($SO['uid']){
            $filter['uid'] = $SO['uid'];
        }
        if(isset($SO['order_status'])){
            $st = $SO['order_status'];
        }
        if($SO['online_pay']>-1){
            $filter['online_pay'] = $SO['online_pay'];
        }
        if(is_array($SO['dateline'])){
            if($SO['dateline'][0] && $SO['dateline'][1]){
                $a = strtotime($SO['dateline'][0]);
                $b = strtotime($SO['dateline'][1])+86400;
                $filter['dateline'] = $a."~".$b;
            }
            if(!$SO['dateline'][0]&&$SO['dateline'][1]){
                $b = strtotime($SO['dateline'][1])+86400;
                $filter['dateline'] = "<:".$b;
            }
            if($SO['dateline'][0]&&!$SO['dateline'][1]){
                $a = strtotime($SO['dateline'][0]);
                $filter['dateline']=">:".$a;
            }
        }
        $file_str = "";
        switch ($st) {
            case '1': // 待接单
                $file_str = "待接单";
                $filter['order_status'] = 0;
                $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                $filter['pei_type'] = array(0, 1, 3);
                $filter['refund_status'] = 0;
                break;
            case '2': // 待配送
                $file_str = "待配送";
                $time = strtotime(date('Y-m-d'))+86399;
                $filter['order_status'] = 2;
                $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                $filter['pei_type'] = array(0, 1);
                $filter[':SQL'] = "refund_status != '1' AND (pei_time = '0' OR (pei_time < ".$time." AND pei_time>".strtotime(date('Y-m-d'))."))";
                break;
            case '3': // 配送中
                $file_str = "配送中";
                $filter['order_status'] = 3;
                $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                $filter['pei_type'] = array(0, 1);
                $filter[':SQL'] = "refund_status != '1'";
                break;
            case '4': // 退单
                $file_str = "退单";
                $filter['order_status'] = array(1,2,3,4);// 允许退款的订单状态
                $filter['refund_status'] = 1;// 退款申请
                $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                $filter['pei_type'] = array(0, 1, 3);
                break;
            case '5': // 催单
                $file_str = "催单";
                $filter['order_status'] = array(1,2,3);// 催单
                $filter['pei_type'] = array(0, 1, 3);
                $filter['refund_status'] = 0;
                $filter[':SQL'] = "cui_time > '0'";
                break;
            case '6': // 自提单
                $file_str = "自提单";
                $filter['order_status'] = array(0, 1,2);// 待接单、已接单
                $filter['refund_status'] = 0;
                $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                $filter['pei_type'] = 3;// 自提
                break;
            case '7': // 预订单
                $file_str = "预订单";
                $filter['order_status'] = 1;
                $filter[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
                $filter[':SQL'] = "refund_status != '1' AND pei_time > 0";
                break;
            case '8': // 待支付
                $file_str = "待支付";
                $filter['order_status'] = 0;
                $filter[':OR'] = array('pay_status'=>0, 'online_pay'=>0);// 已付款 || 货到付款
                $filter['pei_type'] = array(0, 1, 3);
                $filter['refund_status'] = 0;
                break;
            case "9"://已完成
                $file_str = "已完成";
                $filter['order_status'] = 8;
                $filter[':SQL'] = "refund_status != '1'";
                break;
            case '10'://已取消
                $file_str = "已取消";
                $filter['order_status'] = -1;
                $filter[':SQL'] = "refund_status != '1'";
                break;
            default:
                break;
        }
       $items = K::M('order/order')->items($filter,array('order_id'=>'DESC'),1,500,$count);
        if($count==0){
            $this->msgbox->add('没有找到需要导出的订单，请修改筛选条件',201)->response();
        }else if($count>500){
            $this->msgbox->add('需要导出的订单大于500条，请修改筛选条件',202)->response();
        }else{
            $file_name =$file_str.'订单列表';
            $key_arr   =  array(
                "订单编号","订单类型","商家名称","餐盒费","配送费","小计","平台收取","预计收入","下单时间","期望送达时间","收货地址"
            );
            $row = array();
            $shop_ids = $member_ids = $order_ids =  array();
            foreach ($items as $k=>$v){
                $shop_ids[$v['shop_id']] = $v['shop_id'];
                $member_ids[$v['uid']] = $v['uid'];
                $order_ids[$v['order_id']] = $v['order_id'];
            }
            $shop_list = K::M('waimai/waimai')->items_by_ids($shop_ids);
            $member_list = K::M('member/member')->items_by_ids($member_ids);
            $waimai_order_list = K::M("waimai/order")->items_by_ids($order_ids);
            foreach ($items as $k=>$v){
                $pei_time = "";
                if($v['pei_time']==0){
                    $pei_time = "尽快送达";
                }else{
                    $pei_time = date('Y-m-d H:i',$v['pei_time']);
                }
                $s_amount = $v['amount']+$v['hongbao']+$v['money']-$v['pei_amount']+$waimai_order_list[$v['order_id']]['roof_amount']+$waimai_order_list[$v['order_id']]['first_roof'];
                $true_r_amount = (($s_amount * $shop_list[$v['shop_id']]['waimai_bl'])/100, 2, '.', '');
                $true_r_amount = $true_r_amount>0?$true_r_amount:0;
                $true_s_amount = $s_amount-$true_r_amount;
                $true_s_amount = $true_s_amount>0?$true_s_amount:0;

                $row[] = array(
                    $v['order_id'],
                    "外卖",
                    $shop_list[$v['shop_id']]['title'],
                    $waimai_order_list[$v['order_id']]['package_price'],
                    $v['pei_amount'],
                    $v['total_price'],
                    $true_r_amount,
                    $true_s_amount,
                    date('Y-m-d H:i:s',$v['dateline']),
                    $pei_time,
                    '['.$member_list[$v['uid']]['nockname'].']'.$v['addr']
                );
            }
            K::M('dataio/xls')->export($key_arr,$row,$file_name);
        }
    }*/

   public function get_nctorder(){
       $filter_new = $filter_tui = $filter_cui = array();

       $filter_new['order_status'] = 0;
       $filter_new[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
       $filter_new['pei_type'] = array(0, 1, 3);
       $filter_new['refund_status'] = 0;


       $filter_tui['order_status'] = array(1,2,3,4);// 允许退款的订单状态
       $filter_tui['refund_status'] = 1;// 退款申请
       $filter_tui[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
       $filter_tui['pei_type'] = array(0, 1, 3);

       $filter_cui['order_status'] = array(1,2,3);// 催单
       $filter_cui['pei_type'] = array(0, 1, 3);
       $filter_cui['refund_status'] = 0;
       $filter_cui[':SQL'] = "cui_time > '0'";

        //===20190112只获取虚拟商家的相关消息统计
        $setShopIdSql = [];
        $tmpArr = K::M('waimai/waimai')->get_custom_mgr_shop_ids();
        if(count($tmpArr) === 1)
            $setShopIdSql[] = "shop_id={$tmpArr[0]}";
        else if(count($tmpArr) > 0)
            $setShopIdSql[] = "shop_id IN(".implode(",", $tmpArr).")";
        $tmpArr = K::M('waimai/waimai')->get_custom_mgr_shop_cate_ids();
        if(count($tmpArr) === 1)
            $setShopIdSql[] = "shop_id IN(SELECT shop_id FROM ".K::M('waimai/waimai')->table("waimai")." WHERE cate_id={$tmpArr[0]})";
        else if(count($tmpArr) > 0)
            $setShopIdSql[] = "shop_id IN(SELECT shop_id FROM ".K::M('waimai/waimai')->table("waimai")." WHERE cate_id IN(".implode(",", $tmpArr)."))";
        unset($tmpArr);
            
        //2019-01-26 因为模型waimai/waimai增加了获取配置ID列表的逻辑，所以不再需要直接从配置文件中检测数据的方式
        // if(property_exists("__CFG","CUSTOM_MANAGE_SHOP_IDS") && is_array(__CFG::$CUSTOM_MANAGE_SHOP_IDS))
        // {
        //     if(count(__CFG::$CUSTOM_MANAGE_SHOP_IDS) > 0)
        //     {
        //         $ids = [];
        //         foreach(__CFG::$CUSTOM_MANAGE_SHOP_IDS as $pid)
        //         {
        //             $pid = (int)$pid;
        //             if($pid > 0 && !isset($ids[$pid]))
        //                 $ids[$pid] = $pid;
        //         }
        //         unset($pid);
        //         if(count($ids) > 0)
        //             $setShopIdSql[] = "shop_id IN(".implode(",", $ids).")";
        //         unset($ids);
        //     }
        // }

        // if(property_exists("__CFG","CUSTOM_MANAGE_SHOP_CATEGORY_IDS") && is_array(__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS))
        // {
        //     if(count(__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS) > 0)
        //     {
        //         $ids = [];
        //         foreach(__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS as $pid)
        //         {
        //             $pid = (int)$pid;
        //             if($pid > 0 && !isset($ids[$pid]))
        //                 $ids[$pid] = $pid;
        //         }
        //         unset($pid);
        //         if(count($ids) > 0)
        //             $setShopIdSql[] = "shop_id IN(SELECT shop_id FROM ".K::M('waimai/waimai')->table("waimai")." WHERE cate_id IN(".implode(",", $ids)."))";
        //         unset($ids);
        //     }
        // }
        //=========================================================================================
        
        
        if(count($setShopIdSql) === 0)
        {
            $data = [
                'new' => 0,
                'tui' => 0,
                'cui' => 0
            ];
        }
        else
        {
            $filter_new[':SQL'] = "(".implode(" 0R ", $setShopIdSql).")";
            $filter_tui[':SQL'] = "(".implode(" 0R ", $setShopIdSql).")";
            $filter_cui[':SQL'] .= " AND (".implode(" 0R ", $setShopIdSql).")";

            $data = array(
               'new'=>K::M('order/order')->count($filter_new),
               'tui'=>K::M('order/order')->count($filter_tui),
               'cui'=>K::M('order/order')->count($filter_cui),

            );
        }
        //======================================

        $this->msgbox->set_data('data',$data);
   }

   public function check_order_canel($order_id,$checked = 0){
       if($checked){
           $this->cancel($order_id);
       }else{
           if(!$order_id){
               $this->msgbox->add('订单不存在',300);
           }else if(!$order = K::M('order/order')->detail($order_id)){
               $this->msgbox->add('订单不存在',300);
           }else if($order['order_status']==-1){
               $this->msgbox->add('订单已取消',300);
           }else if($order['order_status']==8){
               $this->msgbox->add('订单已完成',300);
           }else if($order['order_status']==-2){
               $this->msgbox->add('订单已完成退款',300);
           }else if(($order['order_status']==1||$order['order_status']==2)){
               $this->msgbox->add('商家已接单',301);
           }else if($order['order_status']==3){
               $this->msgbox->add('配送员已开始配送',301);
           }else if($order['order_status']==4){
               $this->msgbox->add('配送员已送达',301);
           }else{
               $this->cancel($order_id);
           }
       }
   }

    //======= 2019-01-16 中文到货提醒服务 ========
    public function srv_arrival()
    {
        session_start();
        $onlyMine = false;
        if(!isset($_SESSION['admin_order_srv_filter']) || !is_array($_SESSION['admin_order_srv_filter']))
            $_SESSION['admin_order_srv_filter'] = [];
        if(!isset($_SESSION['admin_order_srv_filter']['arrival']) || !is_array($_SESSION['admin_order_srv_filter']['arrival']))
        {
            $_SESSION['admin_order_srv_filter']['arrival'] = [
                'only_mine' => false
            ];
        }
        if(isset($_REQUEST['only_mine']))
            $onlyMine = $_SESSION['admin_order_srv_filter']['arrival']['only_mine'] = (int)$_REQUEST['only_mine']===0?false:true;
        $onlyMine = $_SESSION['admin_order_srv_filter']['arrival']['only_mine'] ? true : false;

        if($this->GP('_list'))
        {
            $adminId = (int)$this->admin->admin_id;
            //获取列表数据
            $data = [
                'page' => 1,
                'linage' => 20,
                'all_page' => 1,
                'all_count' => 0,
                'list' => []
            ];
            $page = (int)$this->GP("page");
            if($page < 1)
                $page = 1;
            $linage = (int)$this->GP("linage");
            if($linage <= 0)
                $linage = 20;
            $data['linage'] = $linage;
            $filter = [];
            if($onlyMine)
                $filter['admin_id'] = $adminId;
            $closeType = (int)$this->GP("close_type");
            if($closeType >= 1 && $closeType<=2)
            {
                --$closeType;
                if($closeType === 0)
                    $filter['close_time'] = 0;
                else
                    $filter['close_time'] = ">:0";
            }
            if(trim($this->GP("search_key")) !== "")
            {
                $sk = trim($this->GP("search_key"));
                $subFilter = [];
                if(is_numeric($sk) && (int)$sk > 0)
                    $subFilter['order_id'] = (int)$sk;
                $subFilter['user_name'] = "LIKE:%{$sk}%";
                $subFilter['user_tel'] = "LIKE:%{$sk}%";
                $subFilter['remark'] = "LIKE:%{$sk}%";
                $filter[':OR'] = $subFilter;
            }

            $items = K::M('order/srv/arrival')->items($filter,"create_time DESC,order_id DESC,close_time ASC",$page,$linage,$data['all_count']);
            if($items === false)
                $this->msgbox->add("读取数据失败",1);
            else
            {
                //计算总页数
                if($data['all_count'] > 0)
                {
                    $data['all_page'] = ceil($data['all_count']/$linage);
                    if($page > $data['all_page'])
                    {
                        $page = $data['all_page'];
                        //因为超出总页数，列表肯定会空，所以这里必须使用最后一页重新再获取一次列表
                        $items = K::M('order/srv/arrival')->items($filter,"create_time DESC,order_id DESC,close_time ASC",$page,$linage);
                    }
                    $data['page'] = $page;
                }

                if(count($items) > 0)
                {
                    //为列表加上商家、管理员名称
                    $adminIds = $shopIds = [];
                    foreach($items as $key => &$item)
                    {
                        $adminId =(int)$item['admin_id'];
                        $shopId = (int)$item['shop_id'];
                        if($adminId > 0)
                            $adminIds[$adminId] = $adminId;
                        if($shopId > 0)
                            $shopIds[$shopId] = $shopId;
                        unset($item);
                    }
                    $adminInfos = $shopInfos = [];
                    if(count($shopIds) > 0)
                    {
                        $shops = K::M('shop/shop')->items_by_ids($shopIds);
                        if($shops)
                        {
                            foreach($shops as $key => &$item)
                            {
                                $shopId = (int)$item['shop_id'];
                                $shopInfos[$shopId] = &$shops[$key];
                                unset($item);
                            }
                        }
                    }
                    if(count($adminIds) > 0)
                    {
                        $admins = K::M('admin/base')->items_by_ids($adminIds);
                        if($admins)
                        {
                            foreach($admins as $key => &$item)
                            {
                                $adminId = (int)$item['admin_id'];
                                $adminInfos[$adminId] = &$admins[$key];
                                unset($item);
                            }
                        }
                    }
                    foreach($items as $key => &$item)
                    {
                        $adminId =(int)$item['admin_id'];
                        $shopId = (int)$item['shop_id'];
                        if($adminId > 0 && isset($adminInfos[$adminId]))
                            $items[$key]['admin_name'] = $adminInfos[$adminId]['admin_name'];
                        else
                            $items[$key]['admin_name'] = "";
                        if($shopId > 0 && isset($shopInfos[$shopId]))
                            $items[$key]['shop_name'] = $shopInfos[$shopId]['title'];
                        else
                            $items[$key]['shop_name'] = "";
                        //转换数据
                        $items[$key]['order_id'] = (int)$items[$key]['order_id'];
                        $items[$key]['admin_id'] =(int) $items[$key]['admin_id'];
                        $items[$key]['member_id'] = (int)$items[$key]['member_id'];
                        $items[$key]['shop_id'] = (int)$items[$key]['shop_id'];
                        $items[$key]['create_time'] = (int)$items[$key]['create_time'];
                        $items[$key]['close_time'] = (int)$items[$key]['close_time'];
                        
                        unset($item);
                    }
                    unset($shopInfos,$adminInfos,$admins,$shops);
                    $data['list'] = $items;
                }
                
                $this->msgbox->set_data("data",$data);
                $this->msgbox->add("获取成功",0);
            }
            $this->msgbox->json();
            exit;
        }
        else
        {
            $this->pagedata['self_admin_id'] = $this->admin->admin_id;
            $this->pagedata['self_admin_role'] = $this->admin->admin['role'];
            $this->pagedata['only_mine'] = $onlyMine;
            $this->tmpl = 'admin:waimai/order/srv/arrival.html';
        }
    }

    public function srv_arrival_open()
    {
        $doneNum = 0;
        $errStr = "";
        try
        {
            $idsParam = trim($this->GP("ids"));
            if($idsParam === "")
                throw new Exception("未指定操作数据", 1);
            $ids = explode(",", $idsParam);
            foreach($ids as $key => $value)
            {
                $id = (int)trim($value);
                if($id > 0 && !in_array($id, $ids))
                    $ids[$id] = $id;
            }
            if(count($ids) === 0)
                throw new Exception("未指定操作数据", 1);
            $items = K::M('order/srv/arrival')->items_by_ids($ids);
            $optIds = $errIds = [];
            $adminId = (int)$this->admin->admin_id;
            foreach($items as &$item)
            {
                $id = (int)$item['order_id'];
                $item['admin_id'] = (int)$item['admin_id'];
                if($item['admin_id'] === 0 || $item['admin_id'] === $adminId || $this->admin->role === "system")
                {
                    if((int)$item['close_time'] > 0 && !in_array($id, $optIds))
                        $optIds[$id] = $id;
                }
                else
                    $errIds[$id] = $id;
                unset($item);
            }
            if(count($errIds) > 0)
                throw new Exception("你不能操作以下订单：".implode(" | ", $errIds), 1);
            if(count($optIds) > 0)
            {
                $doneNum = K::M('order/srv/arrival')->setClose($optIds,false);
                if($doneNum === false)
                {
                    $doneNum = 0;
                    throw new Exception("更新数据失败", 1);
                }
            }
        } catch(\Exception $e) { $errStr = $e->getMessage; }
        if($errStr === "")
        {
            $this->msgbox->set_data("data",['done' => $doneNum]);
            $this->msgbox->add("操作成功",0);
        }
        else
            $this->msgbox->add($errStr,1);
        $this->msgbox->json();
    }

    public function srv_arrival_close()
    {//var_dump(K::M('order/srv/arrival')->batch([22],['admin_id'=>11],true));exit;
        $doneNum = 0;
        $startTrans = false;
        $errStr = "";
        try
        {
            $idsParam = trim($this->GP("ids"));
            if($idsParam === "")
                throw new \Exception("未指定操作数据", 1);
            $ids = explode(",", $idsParam);
            foreach($ids as $key => $value)
            {
                $id = (int)trim($value);
                if($id > 0 && !in_array($id, $ids))
                    $ids[$id] = $id;
            }
            if(count($ids) === 0)
                throw new \Exception("未指定操作数据", 1);
            $items = K::M('order/srv/arrival')->items_by_ids($ids);
            $optIds = $errIds = $bindIds = [];
            $adminId = (int)$this->admin->admin_id;
            foreach($items as &$item)
            {
                $id = (int)$item['order_id'];
                $item['admin_id'] = (int)$item['admin_id'];
                if($item['admin_id'] === 0 || $item['admin_id'] === $adminId || $this->admin->admin['role'] === "system")
                {
                    if((int)$item['close_time'] === 0 && !in_array($id, $optIds))
                        $optIds[$id] = $id;
                    if((int)$item['close_time'] === 0 && $item['admin_id'] === 0 && !in_array($id, $bindIds))
                        $bindIds[$id] = $id;
                }
                else
                    $errIds[$id] = $id;
                unset($item);
            }
            if(count($errIds) > 0)
                throw new \Exception("你不能操作以下订单：".implode(", ", $errIds), 1);
            $this->system->db->query("START TRANSACTION");
            $startTrans = true;
            if(count($bindIds) > 0 && !K::M('order/srv/arrival')->batch($bindIds,['admin_id'=>$adminId],true))
                throw new \Exception("为以下订单关联操作管理员失败：".implode(", ", $bindIds), 1);
            if(count($optIds) > 0)
            {
                $doneNum = K::M('order/srv/arrival')->setClose($optIds,true);
                if($doneNum === false)
                {
                    $doneNum = 0;
                    throw new \Exception("更新数据失败", 1);
                }
            }
            $this->system->db->query("COMMIT");
        }
        catch(\Exception $e)
        {
            $errStr = $e->getMessage();
            if($startTrans)
                $this->system->db->query("ROLLBACK");
        }
        if($errStr === "")
        {
            // $this->msgbox->set_data("data",['done' => $doneNum]);    //怪异的数据库封闭，操作更新成功返回的true，不是影响条数，所以暂不用返回操作成功的受影响记录数
            $this->msgbox->add("操作成功",0);
        }
        else
            $this->msgbox->add($errStr,1);
        $this->msgbox->json();
        exit;
    }

    public function srv_arrival_remark()
    {
        $errStr = "";
        try
        {
            $id = (int)$this->GP("order_id");
            if($id <= 0)
                throw new \Exception("未指定操作数据", 1);
            $val = $this->GP("remark");
            if(!is_string($val))
                throw new \Exception("未指定备注内容", 1);
            $val = trim($val);
            $item = K::M('order/srv/arrival')->detail($id);
            if(!$item)
                throw new \Exception("未找到数据记录", 1);
            $adminId = (int)$this->admin->admin_id;
            if((int)$item['admin_id']!==0 && (int)$item['admin_id']!==$adminId && $this->admin->admin['role'] !== "system")
                throw new \Exception("您不能操作该记录", 1);
            if(!K::M('order/srv/arrival')->setRemark($id,$val))
                throw new \Exception("更新数据失败", 1);
            $this->msgbox->set_data("data",['order_id'=>$id,'remark'=>$val]);
        }catch(\Exception $e) { $errStr = $e->getMessage(); }

        if($errStr === "")
            $this->msgbox->add("操作成功",0);
        else
            $this->msgbox->add($errStr,1);
        $this->msgbox->json();
        exit;
    }

    public function srv_arrival_bind()
    {
        $errStr = "";
        try
        {
            $id = (int)$this->GP("order_id");
            if($id <= 0)
                throw new \Exception("未指定操作数据", 1);
            $item = K::M('order/srv/arrival')->detail($id);
            if(!$item)
                throw new \Exception("未找到数据记录", 1);
            if((int)$item['admin_id'] > 0)
                throw new \Exception("该订单已被关注", 1);
            $adminId = (int)$this->admin->admin_id;
            if(!K::M('order/srv/arrival')->bindAdmin($id,$adminId))
                throw new \Exception("更新数据失败", 1);

            //2019-03-02 添加 将原订单备注移到通知服务数据中
            $orderInfo = K::M('order/order')->detail($id);
            if($orderInfo)
                K::M('order/srv/arrival')->setRemark($id,$orderInfo['remark_mgr']);

            $adminInfo = K::M('admin/base')->detail($adminId);
            $this->msgbox->set_data("data",[
                'order_id'=>$id,
                'admin_name'=>$adminInfo?$adminInfo['admin_name']:"",
            ]);
        }catch(\Exception $e) { $errStr = $e->getMessage(); }

        if($errStr === "")
            $this->msgbox->add("操作成功",0);
        else
            $this->msgbox->add($errStr,1);
        $this->msgbox->json();
        exit;
    }
    //===========================================

    //2019-03-12 添加 部分退款接口
    public function refund_part($id)
    {
        $errStr = "";
        try
        {
            $id = (int)$id;
            if($id <= 0)
                throw new Exception("未指定操作数据", 1);
            $order = K::M('order/order')->detail($id);
            if(!$order)
                throw new Exception("未找到数据记录", 1);
            if((int)$order['order_status']<0 || (int)$order['order_status']>=8)
                throw new Exception("该订单已不能被执行操作", 1);
            if((int)$order['pay_status'] === 0)
                throw new Exception("未支付订单不能执行退款操作", 1);
            if((int)$order['online_pay'] === 0)
                throw new Exception("仅支持在线支付订单", 1);

            $amount = (float)$this->GP('amount');
            $remark = trim($this->GP('remark'));
            $amount = ((int)($amount*100))/100;
            if($amount <= 0)
                throw new Exception("退款金额错误", 1);
            $maxAmount = K::M('order/order')->get_real_refund_amount($id);
            if($amount > $maxAmount)
                throw new Exception("退款金额不能大于".$maxAmount, 1);
            if(!K::M('order/order')->refund_ex($id,$amount,$remark,"admin"))
                throw new Exception("执行退款操作错误", 1);
        }catch(\Exception $e) { $errStr = $e->getMessage(); }
        if($errStr === "")
            $this->msgbox->add("操作成功",0);
        else
            $this->msgbox->add($errStr,1);
        $this->msgbox->json();
        exit;
    }
   
}