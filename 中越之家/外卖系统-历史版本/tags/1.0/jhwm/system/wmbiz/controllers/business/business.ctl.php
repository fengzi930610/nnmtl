<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Business_Business extends Ctl
{
    public function index()
    {
    	$today_amount = $today_yyamount = $week_amount = $week_yyamount = 0;
    	/*if ($today_bills = K::M('waimai/bills')->find(array('bills_sn'=>date('Ymd'), 'shop_id'=>$this->shop_id))) {
    		$today_amount = $today_bills['amount'];// 总金额 = 订单总金额减去平台服务费
    		$today_yyamount = bcadd($today_amount, $today_bills['fee'], 2); //营业额：总金额 + 平台服务费
            //$today_yyamount = $today_amount;//营业额== 对账单amount
    	}*/
        if($today_bills = K::M('site/tongji')->sum_by_filter(array('day'=>date('Ymd'), 'shop_id'=>$this->shop_id))){
            $today_amount = $today_bills['shop_amount'];// 总金额
            $today_yyamount = bcadd($today_amount, $today_bills['site_fee'], 2); //营业额：总金额 + 平台服务费
        }

        $this_week = strtotime(date('Y-m-d')."-1 week")."~".(strtotime(date('Y-m-d'))-1);// 本周（不含当天）
        $today = strtotime(date('Y-m-d'))."~".(strtotime(date('Y-m-d')."+1 day")-1);// 今天（截止23:59:59）
    	//一周预计收入
    	$week_amount = (float) K::M('site/tongji')->sum(array('dateline' => $this_week, 'shop_id'=>$this->shop_id), 'shop_amount');
        $week_fee = (float) K::M('site/tongji')->sum(array('dateline' => $this_week, 'shop_id'=>$this->shop_id), 'site_fee');
        //一周内营业总额
        $week_yyamount = bcadd($week_amount, $week_fee, 2);
    	$count = array(
            'today_amount' => number_format($today_amount, 2),
            'today_yyamount' => number_format($today_yyamount, 2),
            'week_amount' => number_format($week_amount, 2),
            'week_yyamount' => number_format($week_yyamount, 2),
            'yorder'=>K::M('order/order')->count(array('shop_id'=>$this->shop_id, ':SQL'=>"(`from` IN('waimai','qiang'))", 'dateline'=>$today)),
            'norder'=>K::M('order/order')->count(array('shop_id'=>$this->shop_id, ':SQL'=>"((`from` IN('waimai','qiang')) AND (`order_status` < 0))", 'dateline'=>$today)),
            'week_yorder'=>K::M('order/order')->count(array('shop_id'=>$this->shop_id, ':SQL'=>"(`from` IN('waimai','qiang'))", 'dateline'=>$this_week)),
            'week_norder'=>K::M('order/order')->count(array('shop_id'=>$this->shop_id, ':SQL'=>"((`from` IN('waimai','qiang')) AND (`order_status` < 0))", 'dateline'=>$this_week))
        );
        $this->pagedata['count'] = $count;
        $this->pagedata['time_list'] = K::M('helper/date')->get_date_list($this->waimai_shop['dateline'], __TIME);// 获取2个时间之间的月份数组（不论前后顺序）
        $this->pagedata['now_day'] = time();
        $this->tmpl = 'business/business/index.html';
    }

    // 根据年月日时间过去当前月收入统计
    public function get_statistic()
    {
        if (!$month_time = (int) $this->GP('month_time')) {
        	$this->msgbox->add('查询日期格式不正确',211);
        }elseif ($month_time > __TIME) {
        	$this->msgbox->add('超出查询的日期的范围',212);
        }elseif ($month_data = K::M('waimai/bills')->get_all_bussiness($this->waimai_shop, $month_time)) {// 获取商家指定月销售情况
        	$this->msgbox->set_data('month_data', $month_data); 
        }else{
        	$this->msgbox->add('查询失败',213);
        }
    }

    public function get_day_business($month_time){
        if (!$month_time) {
            $this->msgbox->add('查询日期格式不正确',211);
        }elseif ($month_time > __TIME) {
            $this->msgbox->add('超出查询的日期的范围',212);
        }elseif ($month_data = K::M('waimai/bills')->get_day_business($this->shop_id,date('Y-m-d',$month_time))) {// 获取商家指定月销售情况
            $this->msgbox->set_data('data', $month_data);
        }else{
            $this->msgbox->add('查询失败',213);
        }
    }
    
    public function month($month=null)
    {
        if(!$month = (int)$month){
            $month = date("Ym", __TIME);
        }
        if($month > date('Ym')){
            $this->msgbox->add('超出查询的日期的范围',212);
        }else{
            $filter = $items = $day_data = $amount_data = $jiesuan_data = array();
            $total_amount = $total_jiesuan = 0;
            $filter = array('shop_id'=>$this->shop_id);
            $filter['day'] = "{$month}01~{$month}31";
            $ym = date('Ym', __TIME);
            $day_count = date('t', __TIME);
            for($i=1; $i<=$day_count; $i++){
                $day = sprintf("{$ym}%02d", $i);
                $amount_data[$day] = $jiesuan_data[$day] = 0;
                $day_data[] = $day;
            }
            /*if($items = K::M('waimai/bills')->items($filter, array('bills_id'=>'ASC'))){
                foreach($items as $v){
                    $amount_data[$v['bills_sn']] = (float)($v['amount']+$v['fee']);
                    $jiesuan_data[$v['bills_sn']] = (float)($v['amount']);
                    $total_amount += $v['amount'];
                    $total_jiesuan += (float)($v['amount'] - $v['fee']);
                }
            }*/
            if($bills = K::M('site/tongji')->items($filter, array('tongji_id'=>'ASC'))){
                foreach ($bills as $k => $v) {
                    $amount_data[$v['day']] += (float)($v['shop_amount']+$v['site_fee']);
                    $jiesuan_data[$v['day']] += (float)($v['shop_amount']);
                    $total_amount += $v['shop_amount']+$v['site_fee'];
                    $total_jiesuan += (float)($v['shop_amount']);
                }
            }

            $total_order_count = K::M('order/order')->count(array(':SQL'=>"(`from` IN('waimai','qiang'))", 'shop_id'=>$this->shop_id, 'day'=>$filter['day']));
            $cancel_order_count = K::M('order/order')->count(array(':SQL'=>"(`from` IN('waimai','qiang'))", 'shop_id'=>$this->shop_id, 'order_status'=>'<:0', 'day'=>$filter['day']));
            $this->msgbox->set_data('data', array(
                    'day_data'=>array_values($day_data), 
                    'amount_data'=>array_values($amount_data),
                    'jiesuan_data' => array_values($jiesuan_data),
                    'total_amount' => $total_amount, 
                    'total_jiesuan' => $total_jiesuan, 
                    'total_order_count' => $total_order_count, 
                    'cancel_order_count' => $cancel_order_count
                )
            );
        }
    }
}