<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Index extends Ctl
{


    public function index()
    {
       // print_r(json_encode(K::M('net/http')->apirequest('mall/config/getpaotuiconfig')));exit;

        if($this->waimai_shop['closed']==1){
            $this->msgbox->add('商铺已关闭',218)->response();
            K::M('shop/auth')->loginout();
            header("Location:".$this->mklink('login'));
        }
        $redirect = K::M('waimai/waimai')->get_redirect($this->shop_id);
        if($redirect == 1){
            header('Location:'.$this->mklink('newreg/index'));
        }
        $url = K::M('helper/link')->mklink('shop/detail', array($this->shop_id), array(), 'waimai', true,".html");
        $link = K::M('helper/link')->mklink('qrcode/index', array(), array('data'=>$url,'size'=>13), 'www', true,".html");
        $this->pagedata['link'] = $link;
        $this->pagedata['waimai']= $this->waimai_shop;
        if($this->GP("home"))
            $this->pagedata['home_url'] = trim($this->GP("home"));
	   $this->tmpl = 'index.html';
    }
	
    public function home()
	{
        $today_amount = $today_yyamount = 0;
        /*if ($today_bills = K::M('waimai/bills')->find(array('bills_sn'=>date('Ymd'), 'shop_id'=>$this->shop_id))) {
            $today_amount = $today_bills['amount'];// 总金额
            $today_yyamount = bcadd($today_amount, $today_bills['fee'], 2); //营业额：总金额 + 平台服务费
        }*/
        if($today_bills = K::M('site/tongji')->sum_by_filter(array('day'=>date('Ymd'), 'shop_id'=>$this->shop_id))){
            $today_amount = $today_bills['shop_amount'];// 总金额
            $today_yyamount = bcadd($today_amount, $today_bills['site_fee'], 2); //营业额：总金额 + 平台服务费
        }
        $today = strtotime(date('Y-m-d'))."~".(strtotime(date('Y-m-d')."+1 day")-1);// 今天（截止23:59:59）
        $count = array(
            'today_amount' => number_format($today_amount, 2),
            'today_yyamount' => number_format($today_yyamount, 2),
            'yorder'=>K::M('order/order')->count(array('shop_id'=>$this->shop_id, ':SQL'=>"(`from` IN('waimai','qiang'))", 'day'=>date('Ymd'))),
            'norder'=>K::M('order/order')->count(array('shop_id'=>$this->shop_id, ':SQL'=>"((`from` IN('waimai','qiang')) AND (`order_status` < 0))", 'day'=>date('Ymd')))
        );

        $filter = array();
        $filter['shop_id'] = $this->shop_id;
        $filter['audit'] = 1;
        $filter['closed'] = 0;
        $filter['ltime'] = '>:'.__TIME;

        $this->pagedata['manjian'] = K::M('waimai/huodongmj')->find($filter, array('huodong_id'=>'DESC'));// 满减活动
        $this->pagedata['manfan'] = K::M('waimai/huodongmf')->find($filter, array('huodong_id'=>'DESC'));// 满返活动
        $this->pagedata['coupons'] = K::M('waimai/huodongcoupon')->find($filter, array('huodong_id'=>'DESC'));// 送券活动
        $this->pagedata['first'] = $xx = K::M('waimai/huodongfirst')->find($filter, array('huodong_id'=>'DESC'));// 送券活动
        $this->pagedata['count'] = $count;
		$this->pagedata['business_hot'] = $this->business_hot();
        $this->pagedata['waimai']= $this->waimai_shop;

        $warn_nums = 0;
        if($products = K::M('waimai/product')->select(array('shop_id'=>$this->shop_id, 'closed'=>0, 'is_onsale'=>1))){
            $warn_sku = $this->waimai_shop['warn_sku'];
            $spids = $specs = array();
            
            foreach ($products as $k => $v) {
                if($v['is_spec']){
                    $spids[$v['product_id']] = $v['product_id'];
                }else if($v['sale_type'] == 1 && $v['sale_sku'] <= $warn_sku){
                    $warn_nums = $warn_nums + 1;
                }
            }
            if(!empty($spids) && $specs = K::M('waimai/productspec')->select(array('product_id'=>$spids, 'sale_type'=>1, 'sale_sku'=>'<=:'.$warn_sku))){
                $warn_nums = $warn_nums + count($specs);
            }
        }
        $this->pagedata['warn_nums'] = $warn_nums;

		$this->tmpl = 'home.html';
	}

	// 本周热门商品查询
	protected function business_hot()
	{
		$today = strtotime(date('Y-m-d'));// 今天凌晨
    	$filter = array();
        $limit = 5;

        $filter['order_status'] = 8;// 已完成
        $filter['pei_type'] = array(0, 1, 3);
        $filter['refund_status'] = 0;
        $filter['closed'] = 0;
        $filter['from'] = 'waimai';
        $filter['shop_id'] = $this->shop_id;
        $filter['lasttime'] = ($today-604800)."~".$today; // 上周 ~ 今日凌晨
        $orderby = array('total_product_number'=>'DESC');// 默认销量第一排序
        if(!$items = K::M('order/order')->items_join_order_product($filter, $orderby, null, $limit, $count)){
        	$items = array();
        }
        return $items;
	}
    //
    public function get_msg(){

        $filter_new_order = $filter_cui = $filter_tui = array();
        $filter_new_order['shop_id'] = $this->shop_id;
        $filter_new_order['from'] = 'waimai';
        $filter_new_order['order_status'] = 0;
        $filter_new_order[':OR'] = array(
            'pay_status'=>1,
            'online_pay'=>0
        );
        //新订单
        $filter_new_order['dateline'] = '>:'.strtotime(date('Y-m-d'));
        $order_ids = array();
        if($new_order = K::M('order/order')->items($filter_new_order,null,1,999,$count)){
            foreach ($new_order as $v){
                $order_ids[]= $v['order_id'];
            }
        }
        //新订单

        //新的预订单
        //两个小时以内的外卖预订单
        $filter_yuding = array();
        $filter_yuding['shop_id'] = $this->shop_id;
        $filter_yuding['order_status'] = 1;
        $filter_yuding['from'] = 'waimai';
        $end_time = time()-5400;
        $format_time = time()+3600;
        $filter_yuding[':SQL'] = ' pei_time != "0" AND  pei_time >'.$end_time.' and pei_time<'.$format_time;

        //新的预订单

        //催单
        $filter_cui['shop_id'] = $this->shop_id;
        $filter_cui['dateline'] = ">:".(time()-600);
        $filter_cui['reply_time'] = 0;



        $filter_tui['order_status'] = array(1,2,3,4);// 允许退款的订单状态
        $filter_tui['refund_status'] = 1;// 退款申请
        $filter_tui[':OR'] = array('pay_status'=>1, 'online_pay'=>0);// 已付款 || 货到付款
        $filter_tui['pei_type'] = array(0, 1, 3);
        $filter_tui['closed'] = 0;
        $filter_tui['from'] = 'waimai';
        $filter_tui['shop_id'] = $this->shop_id;

        //催单
        $msg = array(
            'new'=>K::M('order/order')->count($filter_new_order),
            'cui'=>K::M('order/cuilog')->count($filter_cui),
            'tui'=>K::M('order/order')->count($filter_tui),
            'yuding'=>K::M('order/order')->count($filter_yuding),
            'new_order'=>$order_ids
        );
        $this->msgbox->set_data('data',$msg);
        $this->msgbox->json();

    }

    public function get_yunprint(){
        $filter = array();
        $filter['shop_id'] = $this->shop_id;
        $filter['from'] = 'ylyun';
        //$filter['status'] = 1;
        if(!$print = K::M('shop/print')->items($filter)){
            $print = array();
        }
        $this->msgbox->add('success');
        $this->msgbox->set_data('data',array_values($print));
        $this->msgbox->json();

    }
}