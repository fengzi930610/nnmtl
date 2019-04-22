<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/30
 * Time: 15:35
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Finance_Cash extends Ctl {

    public function index($page=1){
        $filter = array();
        if($SO=$this->GP('SO')){
            if($SO['stime']&&$SO['ltime']){
                $filter[':SQL'] =" dateline > ".strtotime($SO['stime']).' AND dateline < '.(strtotime($SO['ltime'])+86399);
            }else if(!$SO['stime']&&$SO['ltime']){
                $filter[':SQL'] = " dateline < ".(strtotime($SO['ltime']));
            }else if($SO['stime']&&!$SO['ltime']){
                $filter[':SQL'] = " dateline > ".strtotime($SO['stime']);
            }
        }
        $page = max(1,(int)$page);
        if($items = K::M('cash/bills')->items_by_day($filter,$page,50,$count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, 50, $page, $this->mklink("finance/cash/index", array('{page}')), array('SO'=>$SO));


        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = "admin:finance/cash/index.html";

    }

    public function so(){
        $this->tmpl = "admin:finance/cash/so.html";
    }

    public function bills($bills_sn,$page=1){
        if(!$bills_sn){
            $this->msgbox->add('未指定日期',201);
        }else{
            $filter = array();
            $filter['bills_sn'] = $bills_sn;
            $page = max(1,(int)$page);
            if($SO=$this->GP('SO')){
                if($SO['shop_id']){
                    $filter['shop_id'] = $SO['shop_id'];
                }
                if($SO['staff_id']){
                    $filter['staff_id'] = $SO['staff_id'];
                }
            }
            if($items = K::M('cash/bills')->items($filter,array('bills_id'=>"DESC"),$page,1000,$count)){
                $staff_ids = array();
                foreach ($items as $k=>$v){
                    $staff_ids[$v['staff_id']] = $v['staff_id'];
                }
                $staff_list = K::M('staff/staff')->items_by_ids($staff_ids);
                foreach ($items as $kk=>$vv){
                    $items[$kk]['staff'] = $staff_list[$vv['staff_id']];
                }
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, 50, $page, $this->mklink("finance/cash/index", array($bills_sn,'{page}')), array('SO'=>$SO));
            }
            $this->pagedata['items'] = $items;
            $this->pagedata['pager'] = $pager;
            $this->pagedata['sn'] = $bills_sn;
            $this->pagedata['bills_sn_label'] = date('Y-m-d',strtotime($bills_sn));
            $this->pagedata['bills_sn'] = $bills_sn;
            $this->tmpl = "admin:finance/cash/bills.html";


        }

    }

    public function comfirm($bills_id){
        if(!$bills_id){
            $this->msgbox->add('对账单不存在',201);
        }else if(!$bills = K::M('cash/bills')->detail($bills_id)){
           $this->msgbox->add('对账单不存在',202);
        }else if($bills['status']==1){
            $this->msgbox->add('该对账单已确认上缴',203);
        }else{
            if(K::M('cash/bills')->update($bills_id,array('status'=>1))){
                $this->msgbox->add('操作成功');
            }else{
                $this->msgbox->add('操作失败',204);
            }

        }
    }

    public function detail($bills_id,$page=1){
        $page = max((int)$page,1);
        if(!$bills_id){
            $this->msgbox->add('没指定需要查看的代收款对账单',201);
        }else if(!$cash = K::M('cash/bills')->detail($bills_id)){
            $this->msgbox->add('对账单不存在',202);
        }else{
            if($item = K::M('cash/billslog')->items(array('bills_id'=>$bills_id),array('log_id'=>"DESC"),$page,50,$count)){
                $staff_ids = $shop_ids = array();
                foreach ($item as $kk=>$vv){
                    $staff_ids[$vv['staff_id']] =$vv['staff_id'];
                    $shop_ids[$vv['shop_id']] = $vv['shop_id'];

                }
                $staff_list = K::M('staff/staff')->items_by_ids($staff_ids);
                $waimai_list = K::M('waimai/waimai')->items_by_ids($shop_ids);
                foreach ($item as $k=>$v){
                    $item[$k]['shop'] = $waimai_list[$v['shop_id']];
                    $item[$k]['staff'] = $staff_list[$v['staff_id']];
                }

                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, 50, $page, $this->mklink("finance/cash/detail", array($bills_id,'{page}')));

            }
            $this->pagedata['pagers'] = $pager;
            $this->pagedata['items'] = $item;
            $this->tmpl = "admin:finance/cash/detail.html";

            


        }

    }

    public function billso($bills_sn){
        $this->pagedata['bills_sn'] = $bills_sn;
        $this->tmpl = "admin:finance/cash/billso.html";

    }



    public function unpay_bills($page=1){
        $page = max((int)$page,1);
        $limit = 50;
        $filter = array();
        $filter['status'] = 0;
        if($SO = $this->GP('SO')){
            if($SO['staff_id']){
                $filter['staff_id'] = $SO['staff_id'];
            }
            if($SO['stime']&&$SO['ltime']){
                $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }
            if(!$SO['stime']&&$SO['ltime']){
                $filter['dateline'] = "<:".(strtotime($SO['ltime'])+86399);
            }
            if($SO['stime']&&!$SO['ltime']){
                $filter['dateline'] = ">:".strtotime($SO['stime']);
            }
        }
        if($cash_bills = K::M('cash/bills')->items($filter,array('bills_id'=>"ASC"),$page,$limit,$count)){
            $staff_ids = array();
            foreach($cash_bills as $k=>$v){
                $staff_ids[$v['staff_id']] = $v['staff_id'];
            }
            $staff_list = K::M('staff/staff')->items_by_ids($staff_ids);
            foreach($cash_bills as $kk=>$vv){
                $cash_bills[$kk]['staff'] = $staff_list[$vv['staff_id']];
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, 50, $page, $this->mklink("finance/cash:unpay_bills", array('{page}'),array('SO'=>$SO)));

        }
        $this->pagedata['items'] = $cash_bills;
        $this->pagedata['pagers'] = $pager;
      /*  echo '<pre>';
        print_r($cash_bills);exit;*/
        $this->tmpl = "admin:finance/cash/unpay_bills.html";

    }

   public function unpay_bills_so(){
       $this->tmpl = "admin:finance/cash/unpay_bills_so.html";
   }












}