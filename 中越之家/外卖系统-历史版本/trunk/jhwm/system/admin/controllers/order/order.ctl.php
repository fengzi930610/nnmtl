<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Order_Order extends Ctl
{
    public $years = NULL;
    public $months = NULL;
    public function __construct(&$system)
    {
        parent::__construct($system);
        $year = date("Y");
        for ($i = 0; $i < 2; ++$i) { 
            $years[] = $year--; 
        }
        $this->years = $years;
        $this->months = array('01','02','03','04','05','06','07','08','09','10','11','12');
    }
    public function index($page=1)
    {
        
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['order_id']){
                $filter['order_id'] = $SO['order_id'];
            }else{
                if(in_array($SO['from'], array('tuan','waimai','paotui','maidan','weixiu','house','other'))){
                    $filter['from'] = $SO['from'];
                }
                if($SO['shop_id']){$filter['shop_id'] = $SO['shop_id'];}
                if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}                
            }
        }
        $filter['closed'] = 0;
        if($items = K::M('order/order')->items($filter, array('order_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        
        foreach($items as $k => $v){
                if($shop = K::M('shop/shop')->detail($v['shop_id'])){
                    $items[$k]['shop'] = $shop;
                }
                if($member = K::M('member/member')->detail($v['uid'])){
                    $items[$k]['member'] = $member;
                }
            }
        $staff_ids = $shop_ids = $uids = array();
        foreach($items as $k=>$val){
            $uids[$val['uid']] = $val['uid'];
            $staff_ids[$val['staff_id']] = $val['staff_id'];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->pagedata['staffs'] = K::M('staff/staff')->items_by_ids($staff_ids);
        $this->pagedata['shops'] = K::M('shop/shop')->items_by_ids($shop_ids);
        $this->pagedata['users'] = K::M('member/member')->items_by_ids($uids);
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:order/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:order/so.html';
    }
    public function delete($order_id=null)
    {
        if($order_id = (int)$order_id){
            if(!$detail = K::M('order/order')->detail(order_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('order/order')->delete($order_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('order_id')){
            if(K::M('order/order')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
    
    // 订单统计
    public function tongji($page=1) 
    {
        $f_day = NULL; $items = $month = $pager = $years = array();
        if($data = $this->GP('data')) {
            $f_day = $data['year'] . $data['month'] . "01";
            $startTime = strtotime($f_day);
            $endTime = strtotime($data['year']."-".$data['month']."-"."01"." +1 month -1 day");  
            $datayear = $data['year'];
            $datamonth = $data['month'];
        }else {
            $f_day = date('Ym01', __TIME);
            $startTime = strtotime($f_day);
            $endTime = strtotime(date('Y-m-01', __TIME).' +1 month -1 day');
            $datayear = substr($f_day,0,4);
            $datamonth = substr($f_day,4,2);
        }
        
        for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
          $day = date( 'Ymd', $i); 
          $date = date('Y-m-d', $i);
          $items[$day] = array('day'=>$day, 'date'=>$date, 'orders'=>0, 'moneys'=>0);
        }
 
        if($orders = K::M('order/order')->count_by_day(array('order_status'=>8,'day'=>$f_day."~".date('Ymd',$endTime)), 1, 31)) {
            foreach($orders as $k=>$v) {
                $dmoney = $v['day_money'] + $v['day_amount'] + $v['day_hongbao']; 
                $items[$k]['orders'] = $v['day_order']; 
                $items[$k]['moneys'] = $dmoney;        
                $month_income += $dmoney;               //本月营业额
                $month_order += $v['day_order'];        //已完成订单数
            }  
            foreach($items as $k=>$v) {
                $items[$k]['dates'] = "'".substr($v['date'],8,2)."'";  
            }   
        }else {
            $items = array();
        }
        if($orders2 = K::M('order/order')->count_by_day(array('order_status'=>8,'online_pay'=>1,'day'=>$f_day."~".date('Ymd',$endTime)), 1, 31)) {
            foreach($orders2 as $k=>$v) {
                $month_onlinepay += $v['day_money'];  //在线支付金额
            }
        }
        if($orders3 = K::M('order/order')->count(array('order_status'=>-1,'day'=>$f_day."~".date('Ymd',$endTime)))) {
            foreach($orders2 as $k=>$v) {
                $month_cancel += $v['day_order'];  //已取消订单数
            }
        }
 
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 10;
        if($shoplog = K::M('shop/log')->items(array('day'=>$f_day."~".date('Ymd',$endTime),'money'=>'>:0'),array('log_id'=>'desc'),$page, $limit, $count)) {
            foreach($shoplog as $k=>$v) {
                $order_checkout += $v['money'];
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}'), array('data[year]'=>$data['year'], 'data[month]'=>$data['month'])));
        }
        $this->pagedata['dyear'] = $datayear;
        $this->pagedata['dmonth'] = $datamonth;
        $this->pagedata['months'] = $this->months;
        $this->pagedata['years'] = $this->years;
        $this->pagedata['items'] = $items;
        $this->pagedata['loglist'] = $shoplog;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['month_order'] = $month_order;
        $this->pagedata['month_income'] = $month_income;
        $this->pagedata['month_cancel'] = $month_cancel;
        $this->pagedata['month_onlinepay'] = $month_onlinepay;
        $this->pagedata['order_checkout'] = $order_checkout;
        $this->tmpl = 'admin:order/order/tongji.html';
    }
    // 订单结算
    public function checkout($dyear, $dmonth, $page=1)
    {
        $list = $pager = array();
        if($dyear && $dmonth) {
            $f_day = $dyear . $dmonth . "01";
            $endTime = strtotime($dyear."-".$dmonth."-"."01"." +1 month -1 day");  
            $datayear = $dyear;
            $datamonth = $dmonth;
        }else {
            $f_day = date('Ym01', __TIME);
            $endTime = strtotime(date('Y-m-01', __TIME).' +1 month -1 day');
            $datayear = substr($f_day,0,4);
            $datamonth = substr($f_day,4,2);
        }
        $filter = array('order_status'=>8,'day'=>$f_day."~".date('Ymd',$endTime));
        if($list = K::M('order/order')->count_by_shopid($filter, 1, 31)) {
            foreach($list as $k=>$v) {
                $shopids[$v['shop_id']] = $v['shop_id'];
                $list[$k]['income'] = $v['day_amount'] + $v['day_money'] + $v['day_hongbao']; //营业额
                if($v['online_pay']==1) {
                    $list[$k]['onlinepay'] = $v['day_amount']; // 在线支付
                    $list[$k]['checkout'] = $v['day_amount'] + $v['day_money'] + $v['day_hongbao'];  //商家结算
                }
                if($v['shop_id'] == 0) {
                    unset($list[$k]);
                }
            }
            if($shops = K::M('shop/shop')->items_by_ids($shopids)) {
                foreach($shops as $k=>$v) {
                    $list[$v['shop_id']]['shopname'] = $v['title'];
                }
            } 
        }
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 10;
        $pager['count'] = sizeof($list);
        $pager['pagebar'] = $this->mkpage($pager['count'], $limit, $page, $this->mklink(null, array($datayear, $datamonth, '{page}')));
        $this->pagedata['dyear'] = $datayear;
        $this->pagedata['dmonth'] = $datamonth;
        $this->pagedata['months'] = $this->months;
        $this->pagedata['years'] = $this->years;
        $this->pagedata['list'] = $list;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['shop_id'] = 0;
        $this->tmpl = 'admin:order/order/checkout.html';
    }
    // 对账单
    public function checkbill($shop_id, $dyear, $dmonth, $page) 
    {
        $filter = $pager = $items = array();
        $f_day = $dyear . $dmonth . "01";
        $endTime = strtotime($dyear."-".$dmonth."-"."01"." +1 month -1 day");  
        $datayear = $dyear;
        $datamonth = $dmonth;
        $filter['shop_id'] = $shop_id;          
        $filter['order_status'] = 8;
        $filter['day'] = $f_day."~".date('Ymd',$endTime);
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if($items = K::M('order/order')->items($filter, array('order_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($shop_id, $datayear, $datamonth,'{page}')));
        }
        if($shop = K::M('shop/shop')->detail($shop_id)) {
            $shop_detail = $shop;
        }
        $this->pagedata['shop_id'] = $shop_id;
        $this->pagedata['detail'] = $shop_detail;
        $this->pagedata['dyear'] = $datayear;
        $this->pagedata['dmonth'] = $datamonth;
        $this->pagedata['months'] = $this->months;
        $this->pagedata['years'] = $this->years;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:order/order/checkbill.html';
    }
    
    public function exportform($shop_id, $dyear, $dmonth)
    {    
        $f_day = $dyear . $dmonth . "01";
        $endTime = strtotime($dyear."-".$dmonth."-"."01"." +1 month -1 day");  
        if($shop_id != 0) {
            // 商户对账单报表
            $filter = array();
            $filter['shop_id'] = $shop_id;
            $filter['order_status'] = 8;
            $filter['day'] = $f_day."~".date('Ymd',$endTime);
            $shop = K::M('shop/shop')->detail($shop_id);
            if($items = K::M('order/order')->items($filter, array('order_id'=>'DESC'), $page, 1000, $count)){
                $a = array('商户名称','订单号','在线支付','结算价','下单时间');
                $b = array();
                foreach($items as $v){
                    $jiesuan = $v['money'] + $v['amount'] + $v['hongbao'] - $v['pei_amount'];
                    $b[] = array(
                        'shop_title'=> $shop['title'],
                        'order_id'  => $v['order_id'],
                        'online_pay'=> $v['amount'],
                        'jiesuan'   => $jiesuan,
                        'date'      => date('Y-m-d H:i:s', $v['dateline']),
                    );                    
                }
                K::M('dataio/xls')->export($a, $b, '商户对账单');
            }else{
                $this->msgbox->add('没有需要导出的数据', 211);
            }
        }
        if($shop_id == 0) {
            // 订单结算报表
            $filter = array('order_status'=>8,'day'=>$f_day."~".date('Ymd',$endTime));
            if($list = K::M('order/order')->count_by_shopid($filter, $page, 31)) {
                foreach($list as $k=>$v) {
                    $list[$k]['income'] = $v['day_amount'] + $v['day_money'] + $v['day_hongbao']; //营业额
                }
            }
            $filter2 = array('order_status'=>8,'online_pay'=>1,'day'=>$f_day."~".date('Ymd',$endTime));
            if($list2 = K::M('order/order')->count_by_shopid($filter2, $page ,31)) {
                foreach($list2 as $k=>$v) {
                    $shopids[] = $v['shop_id'];
                    foreach($list as $v2) {
                        $list[$k]['onlinepay'] = $v['day_amount']; // 在线支付
                        $list[$k]['checkout'] = $v['day_amount'] + $v['day_money'] + $v['day_hongbao'];  //商家结算
                    }
                }  
                if($shops = K::M('shop/shop')->items_by_ids($shopids)) {
                    foreach($shops as $k=>$v) {
                        foreach($list as $v2) {
                            $list[$k]['shopname'] = $v['title'];
                        }
                    }
                } 
            }
            if($list){
                $a = array('编号','商户名称','本月营业额','本月订单结算','在线支付');
                $b = array();
                foreach($list as $v){
                    $jiesuan = $v['checkout'] - $v['pei_amount'];
                    $b[] = array(
                        'id'        => $v['shop_id'],
                        'shop_title'=> $v['shopname'],
                        'order_id'  => $v['income'],
                        'online_pay'=> $v['checkout'],
                        'jiesuan'   => $jiesuan,
                    );                    
                }
                K::M('dataio/xls')->export($a, $b, '订单结算报表');
            }else{
                $this->msgbox->add('没有需要导出的数据', 211);
            }
        }
    }
    // 今日汇总
    public function dashboard() 
    {
        $date = date('Ymd');
        $dunix = strtotime($date);
        //今日营业额
        if($money = K::M('order/order')->count_by_shopid(array('order_status'=>8, 'day'=>'=:'.$date), 1, NULL)) {
            foreach($money as $k=>$v) {
                $t_money += $v['day_amount']+$v['day_money']+$v['day_hongbao']-$v['day_pei_money'];
                $t_order += $v['day_order'];
            }
        }
        if($money2 = K::M('order/order')->count_by_shopid(array('order_status'=>'>:0', 'day'=>'=:'.$date), 1, NULL)) {
            foreach($money2 as $k=>$v) {
                $tnew_order += $v['day_order'];
            }
        }
        $member = K::M('member/member')->count(array('dateline'=>'>:'.$dunix));
        $shop = K::M('shop/shop')->count(array('dateline'=>'>:'.$dunix));
        $shop_tixian = K::M('shop/tixian')->count(array('dateline'=>'>:'.$dunix));
        $staff_txs = K::M('staff/tixian')->count(array('dateline'=>'>:'.$dunix));
        $this->pagedata = array(
            'money'      => $t_money,
            'order'      => $t_order,
            'new_order'  => $tnew_order,
            'new_mem'    => $member,
            'new_shop'   => $shop,
            'shop_txs'   => $shop_tixian,
            'staff_txs'  => $staff_txs,
            );
        $this->tmpl = 'admin:order/order/dashboard.html';
    }
    // 今日新订单
    public function neworder($page=1) 
    {
        $pager = $items = array();
        $date = date('Ymd');
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if($items = K::M('order/order')->items(array('day'=>$date),array('order_id'=>'desc'),$page,$limit,$count)) {
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));   
        }
        $shop_ids =array();
        foreach($items as $k=>$val){
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->pagedata['shops'] = K::M('shop/shop')->items_by_ids($shop_ids); 
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "admin:order/order/neworder.html";
    }
    // 今日新商户
    public function newshop()
    {   
        $pager = $items = array();
        $dunix = strtotime(date('Ymd'));
        if($items = K::M('shop/shop')->items(array('dateline'=>'>:'.$dunix),array('shop_id'=>'desc'),$page,$limit,$count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "admin:order/order/newshop.html";
    }
    // 今日商户提现
    public function shoptx()
    {
        $dunix = strtotime(date('Ymd'));
        if($items = K::M('shop/tixian')->items(array('dateline'=>'>:'.$dunix),array('tixian_id'=>'desc'),$page,$limit,$count)) {
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }
        $shop_ids =array();
        foreach($items as $k=>$val){
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->pagedata['shops'] = K::M('shop/shop')->items_by_ids($shop_ids); 
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "admin:order/order/shoptx.html";
    }
    // 今日配送员提现
    public function stafftx()
    {
        $dunix = strtotime(date('Ymd'));
        if($items = K::M('staff/tixian')->items(array('dateline'=>'>:'.$dunix),array('tixian_id'=>'desc'),$page,$limit,$count)) {
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }
        $staff_ids =array();
        foreach($items as $k=>$val){
            $staff_ids[$val['staff_id']] = $val['staff_id'];
        }
        $this->pagedata['staffs'] = K::M('staff/staff')->items_by_ids($staff_ids); 
        
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "admin:order/order/stafftx.html";
    }
    public function paidan($page)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['pay_status'] = 1;
        $filter[':SQL'] = "(`order_status` IN(1,2,3,4) OR (`order_status`=0 AND `pei_type`=2))";
        $filter['staff_id'] = 0;    // 0等待配送员接单
        $filter['pei_type'] = array(1,2);
        if($items = K::M('order/order')->items($filter, array('order_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }        
        $shop_ids =array();
        foreach($items as $k=>$val){
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->pagedata['shops'] = K::M('shop/shop')->items_by_ids($shop_ids); 
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:order/order/paidan.html';        
    }
    public function dopaidan($order_id=null)
    {
        if(!($order_id=(int)$order_id) && !($order_id = (int)$this->GP('order_id'))){
            $this->msgbox->set_data('未指定要派单的单号',211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
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
            }else if(K::M('order/order')->update($order_id, array('staff_id'=>$staff['staff_id'], 'order_status'=>2))){
                //记录订单日志
                K::M('order/log')->create(array('order_id'=>$order_id, 'from'=>'staff', 'log'=>'配送员('.$this->staff['name'].')准备为您配送', 'type'=>'2'));
                //增加订单统计
                K::M('staff/staff')->update_count($staff['staff_id'], 'orders', 1);
                $this->msgbox->add('指派配送员成功');
            }
        }else{
            $this->pagedata['order'] = $order;
            $this->tmpl = 'admin:order/order/dopaidan.html';
        }
    }
    
}