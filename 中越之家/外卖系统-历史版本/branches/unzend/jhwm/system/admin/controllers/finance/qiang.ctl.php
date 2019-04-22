<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Finance_Qiang extends Ctl
{
    public function bills($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1])+86400;
                    $filter['dateline'] = $a."~".$b;
                }
            }
        }
        $bills_items = K::M('qiang/bills')->select($filter,array('bills_sn'=>"DESC"));
        $sn_ids = array();
        foreach($bills_items as $k=>$v){
            $sn_ids[$v['bills_sn']] = $v['bills_sn'];
        }
        $_items = array();
        foreach($sn_ids as $k=>$v){
            foreach($bills_items as $k1=>$v1){
                if($v1['bills_sn'] == $v){
                    $_items[$k]['amount'] += $v1['amount'];
                    $_items[$k]['fee'] += $v1['fee'];
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
        $this->tmpl = 'admin:finance/qiang/bills.html';
    }


    public function index($sn,$page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['status'] >= 0){
                $filter['status'] = $SO['status'];
            }
            if($SO['shop_id']){$filter['shop_id'] = $SO['shop_id'];}
        }
        if($items = K::M('qiang/bills')->items($filter, array('bills_id'=>'desc'), $page, $limit, $count)){
            $shop_ids = array();
            $total = 0;
            foreach($items as $k=>$v){
                $total += $v['fee'];
                $shop_ids[$v['shop_id']] = $v['shop_id'];
                $c_time = (int)$v['bills_sn'];
                $now_time =(int)date('Ymd');
                if($c_time < $now_time){
                    $items[$k]['ru'] = 1;
                }else{
                    $items[$k]['ru'] = 0;
                }
            }
            $this->pagedata['shops'] = K::M('waimai/waimai')->items_by_ids($shop_ids);
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['sn'] = $sn;
        $this->pagedata['total'] = $total;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:finance/qiang/items.html';
    }

    public function so($shop_id=null,$sn=null)
    {
        $shop_id = (int)$shop_id;
        $this->pagedata['shop_id'] = $shop_id;
        $this->pagedata['sn'] = $sn;
        $this->tmpl = 'admin:finance/qiang/so.html';
    }

    public function shop($shop_id = null)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$shop = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $filter = $pager = array();
            $pager['page'] = max(intval($page), 1);
            $pager['limit'] = $limit = 50;
            $filter['shop_id'] = $shop_id;
            if($SO = $this->GP('SO')){
                $pager['SO'] = $SO;
                if($SO['status']>=0){
                    $filter['status'] = $SO['status'];
                }
                if(is_array($SO['dateline'])){
                    if($SO['dateline'][0] && $SO['dateline'][1]){
                        $a = strtotime($SO['dateline'][0]);
                        $b = strtotime($SO['dateline'][1])+86400;
                        $filter['dateline'] = $a."~".$b;
                    }
                }
            }
            if($items = K::M('qiang/bills')->items($filter, array('bills_id'=>'desc'), $page, $limit, $count)){
                $total = 0;
                foreach($items as $k=>$v){
                    $total += $v['fee'];
                }
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            }
            $this->pagedata['total'] = $total;
            $this->pagedata['items'] = $items;
            $this->pagedata['pager'] = $pager;
            $this->pagedata['shop'] = $shop;
            $this->tmpl = 'admin:finance/qiang/shop.html';
        }
    }


    public function detail($bills_id = null)
    {
        if(!$bills_id = (int)$bills_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$bills = K::M('qiang/bills')->detail($bills_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else if(!$shop = K::M('waimai/waimai')->detail($bills['shop_id'])){
            $this->msgbox->add('对账商家不存在或已删除', 213);
        }else{
            $filter = $pager = array();
            $pager['page'] = max(intval($page), 1);
            $pager['limit'] = $limit = 50;
            $filter['bills_id'] = $bills_id;
            if($items = K::M('qiang/billslog')->items($filter, array('log_id'=>'desc'), $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            }
            $this->pagedata['items'] = $items;
            $this->pagedata['pager'] = $pager;
            $this->pagedata['bills'] = $bills;
            $this->pagedata['shop'] = $shop;
            $this->tmpl = 'admin:finance/qiang/detail.html';
        }
    }

    //入账
    public function comfirm_bills($bill_id){
        $now_time = (int)date('Ymd');
        if($bills_list = $this->GP('bills_id')){
            $filter = array();
            $filter['bills_id'] = $bills_list;
            $filter['status'] = 0;
            if($items = K::M('qiang/bills')->items($filter,array(),1,9999,$count)){
                foreach($items as $v){
                    if((int)$v['bills_sn'] >= $now_time){
                        continue; // 跳过不能入账的
                    }
                    if(K::M('qiang/bills')->update($v['bills_id'],array('status'=>1))){
                        if($log_id = K::M('shop/shop')->update_money($v['shop_id'],$v['amount'],'您'.$v['bills_sn'].'的抢购对账单已入账：金额:'.$v['amount'],'')){
                            K::M('shop/log')->update($log_id,array('extend'=>serialize(array('type'=>6,'can_id'=>$v['bills_id']))));
                            K::M('qiang/billslog')->update_status($v['bills_id']);
                        }else{
                            K::M('qiang/bills')->update($v['bills_id'],array('status'=>0));
                            $this->msgbox->add('入账失败',201)->response();
                        }
                    }
                }
            }
            $this->msgbox->add('批量入账成功');
        }else{
            if(!$bill_id){
                $this->msgbox->add('对账单不存在',201);
            }else if(!$bills = K::M('qiang/bills')->detail($bill_id)){
                $this->msgbox->add('对账单不存在',202);
            }else if($bills['status']==1){
                $this->msgbox->add('对账单已入账',203);
            } else {
                if((int)$bills['bills_sn'] >= $now_time){
                    $this->msgbox->add('当前对账单不可入账',205)->response();
                }else{
                    $amount = $bills['amount'];
                    if(K::M('qiang/bills')->update($bill_id,array('status'=>1))){
                        if($log_id = K::M('shop/shop')->update_money($bills['shop_id'],$amount,'您'.$bills['bills_sn'].'的抢购对账单已入账：金额:'.$bills['amount'],'')){
                            K::M('shop/log')->update($log_id,array('extend'=>serialize(array('type'=>6,'can_id'=>$bill_id))));
                            K::M('qiang/billslog')->update_status($bill_id);
                            $this->msgbox->add('入账成功');
                        }else{
                            K::M('qiang/bills')->update($bill_id,array('status'=>0));
                            $this->msgbox->add('入账失败',204);
                        }
                    }
                }
            }
        }
    }


}