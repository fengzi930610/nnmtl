<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/17
 * Time: 14:04
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::C('staff');
class Ctl_Bills extends Ctl_Staff {

    public function get_mouth_bills($parmas){
        if(!$parmas){
            $this->msgbox->add('未指定日期',201);
        }else{
            $stime = $parmas;
            $ltime =strtotime('+1 month', $stime)-1;
            $filter = array();
            $filter['staff_id'] = $this->staff_id;
            $filter['dateline'] = $stime.'~'.$ltime;
            if($items = K::M('staff/bills')->items($filter,array('bills_id'=>'DESC'),1,31,$count)){
              foreach($items as $k=>$v){
                  $items[$k]['day'] = date('Y-m-d',$v['dateline']);
              }
            }
            $day = date('Y-m',$parmas);
            $wc_order = K::M('staff/bills')->sum($filter,'orders');
            $dd_amount =  K::M('staff/bills')->sum($filter,'amount');
            $this->pagedata['data'] = array(
                'day'=>$day,
                'order'=>$wc_order,
                'amount'=>$dd_amount
            );


            $this->pagedata['items'] = $items;
            $this->tmpl = 'bills/index.html';
        }
    }

    public function detail($bills_id){
        if($day = $this->GP('day')){
            $bills_sn = date("Ymd",strtotime($day));
            $filter_day = array();
            $filter_day['shop_id'] = $this->shop_id;
            $filter_day['bills_sn'] = $bills_sn;
            $detail = K::M('staff/bills')->find($filter_day);
            if(!$detail){
                $this->msgbox->add('当前日期没有找到对账单')->response();
                $this->msgbox->set_data('forward',$this->mklink('index',array($bills_id),array(),'staff'));
            }

            $filter = array();
            $filter['bills_id'] = $detail['bills_id'];
            $count =K::M('staff/billslog')->count($filter);
            $detail['count_order'] = $count;
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'bills/detail.html';
        }else{
            if(!$bills_id){
                $this->msgbox->add('对账单不存在',201);
            }else if(!$bills = K::M('staff/bills')->detail($bills_id)){
                $this->msgbox->add('对账单不存在',202);
            }else if($bills['staff_id']!=$this->staff_id){
                $this->msgbox->add('不可查看别的商家订单',203);
            }else{
                $filter = array();
                $filter['bills_id'] = $bills_id;
                $count =K::M('staff/billslog')->count($filter);
                $bills['count_order'] = $count;
                $this->pagedata['detail'] = $bills;
                $this->tmpl = 'bills/detail.html';
            }
        }
    }

    public function loadorder($page=1){
        $page = max((int)$page,1);
        if($bills_id = $this->GP('bills_id')) {
            $filter = array();
            $filter['bills_id'] = $bills_id;
            $filter['staff_id'] = $this->staff_id;
            $log = K::M('staff/billslog')->items($filter, array(), 1, 9999, $count_bills);
            $ids = array();
            foreach ($log as $k => $v) {
                $ids[] = $v['order_id'];
            }
            $filter_order = array();
            $filter_order['staff_id'] = $this->staff_id;
            $filter_order['from'] = 'waimai';
            $filter_order['order_id'] = $ids;
            $items = K::M('order/order')->items($filter_order, array('order_id' => 'DESC'), $page, 20, $count);
            foreach($items as $k=>$v){
                foreach($log as $k1=>$v1){
                    if($v['order_id']==$v1['order_id']){
                        $items[$k]['true_amount'] = $v1['amount'] ;
                    }

                }
            }
        }else{
            $items  = array();
            $count = 0;
        }
        if($count <= 20){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['items'] = $items;
        $this->tmpl = 'bills/loadorder.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html',$html);
        $this->msgbox->json();
    }







}