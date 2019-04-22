<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Finance_Qiang_balance extends Ctl
{ //对账单

    public function index($page=1){
        $filter = $pager = array();
        $filter['shop_id'] = $this->shop_id;
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 10;
        $status = (int)$this->GP('status');
        $begin_time = strtotime($this->GP('begin_time'));
        $end_time = strtotime($this->GP('end_time')) + 86399;
        $this->pagedata['sel_time'] = array(
            'stime'=>$this->GP('begin_time'),
            'ltime'=>$this->GP('end_time')
        );
        if($status == 2){
            $filter['status'] = 0;
        }else if($status == 3){
            $filter['status'] = 1;
        }
        if($begin_time && $end_time){
            $filter['dateline'] = $begin_time."~".$end_time;
            
        }
        if($items = K::M('qiang/bills')->items($filter, array('bills_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('finance/qiang_balance:index', array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['status'] = $status;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'finance/qiang_balance/bills.html';
    }
    
    //对账单记录
    public function bills_log($page){
        $bills_id = (int)$this->GP('bills_id');
        if(!$bills = K::M('qiang/bills')->detail($bills_id)){
            $this->msgbox->add('错误的帐单!',211)->response();
        }
        $filter = $pager = array();
        $filter['shop_id'] = $this->shop_id;
        $filter['bills_id'] = $bills_id;
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 10;
        if($items = K::M('qiang/billslog')->items($filter, array('log_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('finance/qiang_balance:bills_log', array('{page}'),null,array('bills_id'=>$bills_id)));
        }
        $this->pagedata['bills'] = $bills;
        $this->pagedata['status'] = $status;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'finance/qiang_balance/bills_log.html';
    }
    

}
