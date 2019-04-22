<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Finance extends Ctl
{
    
    //我的账户
    public function index() 
    {
        $filter = $pager = array();
        $filter['shop_id'] = $this->shop_id;
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 10;
        $type = (int)$this->GP('type');
        $begin_time = strtotime($this->GP('begin_time'));
        $end_time = strtotime($this->GP('end_time')) + 86399;
        if($type == 2){
            $filter['money'] = '>:0';
        }else if($type == 3){
            $filter['money'] = '<:0';
        }
        if($begin_time && $end_time){
            $filter['dateline'] = $begin_time."~".$end_time;
        }
        if($items = K::M('shop/log')->items($filter, array('log_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['type'] = $type;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;

        $this->tmpl = 'finance/index.html';
    }
    
    //对账单
    public function bills(){
        $filter = $pager = array();
        $filter['shop_id'] = $this->shop_id;
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 10;
        $status = (int)$this->GP('status');
        $begin_time = strtotime($this->GP('begin_time'));
        $end_time = strtotime($this->GP('end_time')) + 86399;
        if($status == 2){
            $filter['status'] = 0;
        }else if($status == 3){
            $filter['status'] = 1;
        }
        if($begin_time && $end_time){
            $filter['dateline'] = $begin_time."~".$end_time;
        }
        if($items = K::M('weidian/bills')->items($filter, array('bills_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('finance:bills', array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['status'] = $status;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'finance/bills.html';
    }
    
    //对账单记录
    public function bills_log($page){
        $bills_id = (int)$this->GP('bills_id');
        if(!$bills = K::M('weidian/bills')->detail($bills_id)){
            $this->msgbox->add('错误的帐单!',211)->response();
        }
        $filter = $pager = array();
        $filter['shop_id'] = $this->shop_id;
        $filter['bills_id'] = $bills_id;
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 10;
        if($items = K::M('weidian/billslog')->items($filter, array('log_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('finance:bills_log', array('{page}'),null,array('bills_id'=>$bills_id)));
        }
        $this->pagedata['bills'] = $bills;
        $this->pagedata['status'] = $status;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'finance/bills_log.html';
    }
	
    //账户信息
    public function info(){
        $account = K::M('shop/account')->detail($this->shop_id);
        $this->pagedata['account'] = $account;
        $this->tmpl = 'finance/info.html';
    }
    
    //充值
    public function recharge(){
        $this->tmpl = 'finance/recharge.html';
    }
    
    //提现
    public function cash(){
        if(!$account = K::M('shop/account')->detail($this->shop_id)){
            $this->msgbox->add('还未设置您的提现账户!',211)->response();
        }
        if($data = $this->checksubmit('data')){
            if(!$data = $this->check_fields($data, 'money,intro')){
                $this->msgbox->add('非法的数据提交', 211);
            }else if($data['money'] <0){
                $this->msgbox->add('金额不正确', 212);
            }else if($data['money'] > $this->shop['money']){
                $this->msgbox->add('余额不足', 213);
            }else if(K::M('shop/shop')->update_money($this->shop_id,-$data['money'],'余额提现，扣款')){
                if(K::M('shop/tixian')->create(array('shop_id'=>$this->shop_id,'money'=>$data['money'],'intro'=>$data['intro'],'account_info'=>'开户行：'.$account['account_type'].'，账户：'.$account['account_number'].',开户人：'.$account['account_name'],'end_money'=>$data['money']))){
                    $this->msgbox->add('提现成功');
                    $this->msgbox->set_data('forward',  $this->mklink('finance'));
                }
             }else{
                 $this->msgbox->add('提现失败',216);
             }
        }else{
            $this->pagedata['account'] = $account;
            $this->tmpl = 'finance/cash.html';
        }
    }
    public function cash_detail(){

    }
    
}