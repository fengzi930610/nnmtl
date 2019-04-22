<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/16
 * Time: 10:49
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Webview_Bills_Log extends ctl {


    //余额流水
    public function index(){
        $this->tmpl = 'webview/bills/log/index.html';
    }
    //加载流水
    public function loadlog($page=1){
        $page = max((int)$page,1);
        $filter = array();
        $filter['shop_id'] = $this->shop_id;
        if(!$items = K::M('shop/log')->items($filter,array('log_id'=>'DESC'),$page,20,$count)){

        }
        if($count <= 20){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['items'] = $items;
        $this->tmpl = 'webview/bills/log/loadlog.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html',$html);
        $this->msgbox->json();
    }

    public function detail($log_id){
        if(!$log_id){
            $this->msgbox->add('未找到账单信息',201);
        }else if(!$log = K::M('shop/log')->detail($log_id)){
            $this->msgbox->add('未找到账单信息',202);
        }else if($log['shop_id']!=$this->shop_id){
            $this->msgbox->add('没有权限查看该账单信息',203);
        }else{
           // 1：外卖入账 2：提现  3：团购  4：买单 5：商城 6:抢购
            if($log['extend']['type']==1){
               $detail = K::M('waimai/bills')->detail($log['extend']['can_id']);
            }else if($log['extend']['type']==2){
                $detail = K::M('shop/tixian')->detail($log['extend']['can_id']);
            }else if($log['extend']['type']==3){
                $detail = K::M('tuan/bills')->detail($log['extend']['can_id']);
            }else if($log['extend']['type']==4){
                $detail = K::M('maidan/bills')->detail($log['extend']['can_id']);
            }else if($log['extend']['type']==5){
                $detail =K::M('weidian/bills')->detail($log['extend']['can_id']);
            }else if($log['extend']['type']==6){
                $detail =K::M('qiang/bills')->detail($log['extend']['can_id']);
            }
            if(in_array($log['extend']['type'],array(1,3,4,5,6))){
                $tmpl = 'webview/bills/log/bills_detail.html';
            }else{
                $tmpl = 'webview/bills/log/tixian_detail.html';
            }
            $account  =  K::M('shop/account')->detail($this->shop_id);
            $account['number'] = '';
            if($account){
                $account['number'] = substr_replace($account['account_number'],'**** **** ****',0,strlen($account['account_number'])-4);
            }
            $this->pagedata['account']  = $account;
            $this->pagedata['detail'] = $detail;
            $this->pagedata['log'] = $log;
            $this->tmpl = $tmpl;

        }

    }




}