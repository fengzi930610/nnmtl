<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/17
 * Time: 9:34
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::C('staff');
class Ctl_Index extends Ctl_Staff {

    public function index(){

        $filter_last = $filter_else = array();
        $filter_last['staff_id'] = $this->staff_id;
        $last_bills = K::M('staff/bills')->find($filter_last,array('bills_id'=>'DESC'));
        $filter_else['staff_id'] = $this->staff_id;
        $filter_else['bills_id'] = '<:'.$last_bills['bills_id'];
        $else = K::M('staff/bills')->items($filter_else,array('bills_id'=>'DESC'),1,9,$count);
        $data = array(
            'last'=>$last_bills,
            'else'=>$else,
            'day'=>date('Y-m-d')
        );
        //今日预计收入
        $today_bills = K::M('staff/bills')->find(array('staff_id'=>$this->staff_id,'bills_sn'=>date('Ymd')),array('bills_id'=>'DESC'));
        $data['bills'] = $today_bills?$today_bills:array();


        //正常订单
        $filter_zhengchang  = array();
        $filter_zhengchang['staff_id'] = $this->staff_id;
        $filter_zhengchang['bills_sn'] = date('Ymd');
        $bills_today = K::M('staff/bills')->find($filter_zhengchang);

        //异常订单
        $filter_yc = array();
        $filter_yc['staff_id'] = $this->staff_id;
        $filter_yc['from'] = 'waimai';
        $filter_yc['dateline'] = strtotime(date('Y-m-d')).'~'.(strtotime(date('Y-m-d'))+86399);
        $filter_yc[':OR'] = array(
            'order_status'=>-2,
            'refund_status'=>array(1,2,3)
        );
        $no_order = K::M('order/order')->count($filter_yc);
        $no_amount = K::M('order/order')->sum($filter_yc,'pei_amount');


        $data_format = array();
        $data_format['yes_order'] = $bills_today['orders']? $bills_today['orders']:0;
        $data_format['yes_amount'] = $bills_today['amount']?$bills_today['amount']:0;
        $data_format['no_order'] = $no_order;
        $data_format['no_amount'] = $no_amount;
        if($data_format['yes_order']==0&& $data_format['no_order']==0){
            $data_format['yes_bl'] =  $data_format['no_bl'] = 0;
        }else{
            $data_format['yes_bl'] = ($data_format['yes_order']/($data_format['yes_order']+$data_format['no_order']))*100;
            $data_format['no_bl'] = ($data_format['no_order']/($data_format['yes_order']+$data_format['no_order']))*100;
        }
        $filter_cash = array();
        $filter_cash['staff_id'] = $this->staff_id;
        $sum = K::M('cash/bills')->sum($filter_cash,'amount');
        $count = K::M('cash/bills')->count($filter_cash);
        $data_format['cash_sum'] = $sum;
        $data_format['cash_count'] = $count;

        $this->pagedata['format'] = $data_format;
        // amount 最终结算的订单
        // freight_amount 计算前的配送费
        $this->pagedata['data'] = $data;
        $this->pagedata['staff']=$xx = K::M('staff/staff')->detail($this->staff_id);
        $this->tmpl = 'index/index.html';
    }

    public function day($page=1){
        $page = max((int)$page,1);
        $arr = $this->mouth($this->staff['dateline'],__TIME);
        if($page<=100&$arr){
            $silce_arr = array_slice($arr,($page-1)*10,10,true);
        }
        $items = array();
        if($silce_arr){
            foreach($silce_arr as $k=>$v){
                $filter = array();
                $filter['staff_id'] = $this->staff_id;
                $stime = strtotime($v);
                $ltime =strtotime('+1 month', strtotime($v))-1;
                $filter['dateline'] = $stime.'~'.$ltime;
                //完成订单数
                $wc_order = K::M('staff/bills')->sum($filter,'orders');
                $dd_amount =  K::M('staff/bills')->sum($filter,'amount');
                $year = date('Y',$stime);
                $mouth = date('m',$stime).'月';
                $items[] = array(
                    'order'=>$wc_order,
                    'amount'=>$dd_amount,
                    'year'=>$year,
                    'mouth'=>$mouth,
                    'parmas'=>$stime
                );
            }
        }
        if(count($items) <= 10){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['items'] = $items;
        $this->tmpl = 'index/loaditems.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html',$html);
        $this->msgbox->json();
    }

    public function mouth($stime=0, $ltime=0){
        $monarr = array();
        $first = date("Y-m-1");
        if($month_count = K::M('helper/date')->get_month_count($stime, $ltime)){
            $monarr[] = $first; // 当前月;
            for ($i=1; $i < $month_count; $i++) {
                $monarr[] = date("Y-m-d",strtotime("$first -1 month"));
            }
        }
        return $monarr;
    }
}