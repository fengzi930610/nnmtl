<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/19
 * Time: 10:00
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::C('staff');
class Ctl_Log extends Ctl_Staff  {

    public function index(){
        $this->tmpl = 'log/index.html';
    }

    public function load($page=1){
        $page = max((int)$page,1);
        $filter = array(
            'staff_id'=>$this->staff_id
        );
        if(!$items = K::M('staff/log')->items($filter,array('log_id'=>'DESC'),$page,20,$count)){
            $items = array();
        }
        if($count <= 20){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['items'] = $items;
        $this->tmpl = 'log/loadlog.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html',$html);
        $this->msgbox->json();

    }


    public function detail($log_id){
        if(!$log_id){
            $this->msgbox->add('记录不存在',201);
        }else if(!$log = K::M('staff/log')->detail($log_id)){
            $this->msgbox->add('记录不存在',202);
        }else if($log['staff_id']!=$this->staff_id){
            $this->msgbox->add('您没有权限查看该订单',203);
        }else{
            $account = K::M('staff/account')->find(array('staff_id'=>$this->staff_id));
            if($account){
                $account['number'] = substr_replace($account['account'],'**** **** ****',0,strlen($account['account_number'])-4);
            }
            $this->pagedata['account'] = $account;
            if($log['extend']['type']==2){
                $detail = K::M('staff/tixian')->detail($log['extend']['can_id']);
                $detail['money'] = $log['money'];
                $this->pagedata['detail'] = $detail;
                $this->tmpl = 'log/tixian_detail.html';
            }else if($log['extend']['type']==1){
                $detail = K::M('staff/bills')->detail($log['extend']['can_id']);
                $this->pagedata['detail'] = $detail;
                $this->tmpl = 'log/bills_detail.html';
            }

        }
    }



}