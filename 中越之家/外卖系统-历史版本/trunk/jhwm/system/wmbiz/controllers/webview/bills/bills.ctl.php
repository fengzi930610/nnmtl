<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/13
 * Time: 16:31
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Webview_Bills_Bills extends Ctl {

    //首页
    public function index(){
        $filter = array();
        $waimai = $this->waimai_shop;
        //提现筛选器
        $filter_tixian = array();
        $filter_tixian['shop_id'] = $this->shop_id;
        $tixian = K::M('shop/tixian')->find($filter_tixian,array('tixian_id'=>'DESC'));
      //  0:待审核 1:已通过 2:以拒绝 4：已完成
        switch($tixian['status']){
            case 0:
                $tixian['status_label'] = '待审核';
                break;
            case 1:
                $tixian['status_label'] = '已通过';
                break;
            case 2:
                $tixian['status_label'] = '已拒绝';
                break;
            case 4:
                $tixian['status_label'] = '已完成';
                break;
            default:
                $tixian['status_label'] = '待审核';
        }


        //外卖所有订单数量
         $count_waimai_order=   K::M('order/order')->count(array('shop_id'=>$this->shop_id, 'from'=>'waimai', 'day'=>date('Ymd')));
        //所有订单金额
        $sum_amount = K::M('order/order')->sum(array('shop_id'=>$this->shop_id, 'from'=>'waimai', 'day'=>date('Ymd')),'amount');
        $sum_money =  K::M('order/order')->sum(array('shop_id'=>$this->shop_id, 'from'=>'waimai', 'day'=>date('Ymd')),'money');


        //外卖异常订单数目
        $count_waimai_order_yichang = K::M('order/order')->count(array('shop_id'=>$this->shop_id, 'from'=>'waimai', 'day'=>date('Ymd'),'refund_status'=>array(1,3)));
        $sum_no_amount =K::M('order/order')->sum(array('shop_id'=>$this->shop_id, 'from'=>'waimai', 'day'=>date('Ymd'),'refund_status'=>array(1,3)),'amount');
        $sum_no_money = K::M('order/order')->sum(array('shop_id'=>$this->shop_id, 'from'=>'waimai', 'day'=>date('Ymd'),'refund_status'=>array(1,3)),'money');

        //预计收入
       $bill_amount = K::M('waimai/bills')->sum(array('shop_id'=>$this->shop_id,'bills_sn'=>date('Ymd')),'amount');
        $count = array(
            'yes_order'=>$count_waimai_order,
            'no_order'=>$count_waimai_order_yichang,
            'yes_money'=>$sum_amount+$sum_money,
            'no_money'=>$sum_no_amount+$sum_no_money,
            'yuji'=>$bill_amount?$bill_amount:0,
            'all'=>$count_waimai_order+$count_waimai_order_yichang,
            'day'=>date('Y-m-d'),
            'yes_bl'=>($count_waimai_order/($count_waimai_order+$count_waimai_order_yichang))*100,
            'no_bl'=>($count_waimai_order_yichang/($count_waimai_order+$count_waimai_order_yichang))*100,
        );
        $msg_data = array(
            'msg'=>''
        );
        if($tixian&&$tixian['tixian_id']){
            $msg = date('Y.m.d',$tixian['dateline']).'提现'.$tixian['money'].'元';
            if($tixian['status']==0){
                $msg.='审核中';
            }else if($tixian['status']==1){
                $msg.='已通过';
            }else if($tixian['status']==2){
                $msg.='被拒绝';
            }else if($tixian['status']==4){
                $msg.='已完成';
            }
            $msg_data['msg'] = $msg;
            $msg_data['tixian_id'] = $tixian['tixian_id'];
        }

        //获取9条历史账单+1条已完成订单
        $tixn_wancheng = K::M('waimai/bills')->find(array('shop_id'=>$this->shop_id,'status'=>1),array('bills_id'=>'DESC'));
        $filter_history = array();
        $filter_history['shop_id'] = $this->shop_id;
        $filter_history[':SQL'] = " bills_id !='".$tixn_wancheng['bills_id']."' ";
        $tixian_else = K::M('waimai/bills')->items($filter_history,array('bills_id'=>'DESC'),1,9,$counts);
        $data_all = array(
            'wancheng'=>$tixn_wancheng,
            'else'=>$tixian_else
        );
        $this->pagedata['bills_list'] = $data_all;
        $this->pagedata['msg_data'] = $msg_data;
        $this->pagedata['count'] = $count;
        $this->pagedata['shop'] = K::M('shop/shop')->detail($this->shop_id);
        $this->tmpl = 'webview/bills/bills/index.html';
    }


    //历史对账
    public function history(){
        $this->tmpl = 'webview/bills/bills/history.html';
    }

    //加载对账
    public function loadbills($page=1){
        $page = max((int)$page,1);
        $filter = array();
        $filter['shop_id'] = $this->shop_id;
        if($items = K::M('waimai/bills')->items($filter,array('bills_id'=>'DESC'),$page,20,$count)){
           foreach($items as $k=>$v){
               $items[$k]['day'] = date('Y.m.d',$v['dateline']);
           }
        }
        if($count <= 20){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['items'] = $items;
        $this->tmpl = 'webview/bills/bills/loadbills.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html',$html);
        $this->msgbox->json();
    }

    public function detail($bills_id){
        if($day = $this->GP('day')){
            $bills_sn = date("Ymd",strtotime($day));
            $filter_day = array();
            $filter_day['shop_id'] = $this->shop_id;
            $filter_day['bills_sn'] = $bills_sn;
            $detail = K::M('waimai/bills')->find($filter_day);
            if(!$detail){
                $this->msgbox->set_data('forward', K::M('helper/link')->mklink('webview/bills/bills/detail',array($bills_id),array(),'wmbiz'));
                $this->msgbox->add('当前日期没有找到对账单')->response();
            }

            $filter = array();
            $filter['bills_id'] = $detail['bills_id'];
            $count =K::M('waimai/billslog')->count($filter);
            $detail['count_order'] = $count;
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'webview/bills/bills/detail.html';
        }else{
            if(!$bills_id){
                $this->msgbox->add('对账单不存在',201);
            }else if(!$bills = K::M('waimai/bills')->detail($bills_id)){
                $this->msgbox->add('对账单不存在',202);
            }else if($bills['shop_id']!=$this->shop_id){
                $this->msgbox->add('不可查看别的商家订单',203);
            }else{
                $filter = array();
                $filter['bills_id'] = $bills_id;
                $count =K::M('waimai/billslog')->count($filter);
                $bills['count_order'] = $count;
                $this->pagedata['detail'] = $bills;
                $this->tmpl = 'webview/bills/bills/detail.html';
            }
        }
    }

    public function loadorder($page=1){
        $page = max((int)$page,1);
        if($bills_id = $this->GP('bills_id')) {
            $filter = array();
            $filter['bills_id'] = $bills_id;
            $filter['shop_id'] = $this->shop_id;
            $log = K::M('waimai/billslog')->items($filter, array(), 1, 9999, $count_bills);
            $ids = array();
            foreach ($log as $k => $v) {
                $ids[] = $v['bills_number'];
            }
            $filter_order = array();
            $filter_order['shop_id'] = $this->shop_id;
            $filter_order['from'] = 'waimai';
            $filter_order['order_id'] = $ids;
            $items = K::M('order/order')->items($filter_order, array('order_id' => 'DESC'), $page, 20, $count);
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
        $this->tmpl = 'webview/bills/bills/loadorder.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html',$html);
        $this->msgbox->json();
    }


}