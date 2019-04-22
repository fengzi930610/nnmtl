<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/26
 * Time: 14:43
 */
if(!defined('__CORE_DIR')){
    exit('accedd delined');
}
class Ctl_Finance_Jifen extends Ctl {

    public function bills($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['stime']&&$SO['ltime']){
                $filter[':SQL'] =" dateline > ".strtotime($SO['stime']).' AND dateline < '.(strtotime($SO['ltime'])+86399);
            }else if(!$SO['stime']&&$SO['ltime']){
                $filter[':SQL'] = " dateline < ".(strtotime($SO['ltime']));
            }else if($SO['stime']&&!$SO['ltime']){
                $filter[':SQL'] = " dateline > ".strtotime($SO['stime']);
            }
        }
        $bills_items = K::M('mall/bills')->items($filter, array('bills_id'=>'desc'), 1, 5000, $count);
        $sn_ids = array();
        foreach($bills_items as $k=>$v){
            $sn_ids[$v['bills_sn']] = $v['bills_sn'];
        }
        $_items = array();
        foreach($sn_ids as $k=>$v){
            foreach($bills_items as $k1=>$v1){
                if($v1['bills_sn'] == $v){
                    $_items[$k]['fee'] += $v1['fee'];
                    $_items[$k]['jifen'] += $v1['jifen'];

                }
            }
        }
        $count = count($_items);
        if($items = array_slice($_items, ($page-1)*$limit, $limit, true)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:finance/jifen/bills.html';
    }

    public function so($sn=null){
        $this->pagedata['sn'] = $sn;
        $this->tmpl = 'admin:finance/jifen/so.html';
    }

    public function index($sn,$page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 5;
        if(!$sn){
            $sn = date('Ymd');
        }
        $filter['bills_sn'] = $sn;

        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['uid']){
                $filter['uid'] = $SO['uid'];
            }
        }

        $bills = K::M('mall/bills')->find(array('bills_sn'=>$sn));
        if($items = K::M('mall/billslog')->items($filter,array(),$page,50,$count)){
            $uids = $order_ids = array();
            foreach($items as $k=>$v){
                $uids[] = $v['uid'];
                $order_ids[] = $v['order_id'];
            }
            $uid_list = K::M('member/member')->items_by_ids($uids);
            $order_list = K::M('order/order')->items_by_ids($order_ids);
            foreach($items as $kk=>$vv){
                $items[$kk]['order_info'] = $order_list[$vv['order_id']];
                $items[$kk]['user_info'] = $uid_list[$vv['uid']];
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, 50, $page, $this->mklink(null, array($sn,'{page}')), array('SO'=>$SO));
        }

        $this->pagedata['sn'] = $sn;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->pagedata['bills'] = $bills;
        $this->tmpl = 'admin:finance/jifen/items.html';


    }
}

