<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/20
 * Time: 11:22
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Finance_Chong extends Ctl {


    public function index($page=1){
        $page = max((int)$page,1);
        $limit = 50;
        $filter = array();
        if($SO = $this->checksubmit('SO')){
            if($SO['stime']){
                $filter['dateline'] ='>:'.strtotime($SO['stime']);
            }
            if($SO['ltime']){
                $filter['dateline'] = '<:'.(strtotime($SO['ltime'])+86399);
            }
            if($SO['stime']&&$SO['ltime']){
                $filter['dateline']  = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }
        }
        if($items = K::M('chongzhi/bills')->items($filter,array('bills_id'=>'DESC'),$page,$limit,$count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('finance/chong:index', array('{page}')), array('SO'=>$SO));
        }

        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = 'admin:finance/chongzhi/items.html';
    }

    public function detail($bills_id,$page=1){
        if(!$bills_id){
            $this->msgbox->add('对账单不存在',201);
        }else if(!$bills = K::M('chongzhi/bills')->detail($bills_id)){
            $this->msgbox->add('对账单不存在',202);
        }else{
            $filter = array();
            $filter['bills_id'] = $bills_id;
            $limit = 50;
            $page = max((int)$page,1);
            $uids = array();
            $admin_ids = array();
            if($items = K::M('chongzhi/billslog')->items($filter,array('log_id'=>'DESC'),$page,$limit,$count)){
                foreach($items as $k=>$v){
                    $uids[] = $v['uid'];
                    $admin_ids[] = $v['admin_id'];
                }
                $member_list = K::M('member/member')->items_by_ids($uids);
                foreach($items as $kk=>$vv){
                    $items[$kk]['member'] = $member_list[$vv['uid']];
                }
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('finance/chong:detail', array($bills_id,'{page}')), array());
            }
            $this->pagedata['items'] = $items;
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'admin:finance/chongzhi/detail.html';

        }
    }

    public function so(){
        $this->tmpl = 'admin:finance/chongzhi/so.html';
    }



}