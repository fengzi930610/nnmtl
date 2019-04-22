<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11
 * Time: 13:45
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Finance_Tongcheng extends Ctl {

    public function index($page){
        $page = max((int)$page,1);
        $filter = array();
        if($SO = $this->GP('SO')){
          if($SO['group_id']){
              $filter['group_id'] = $SO['group_id'];
          }
          if($SO['stime']&&$SO['ltime']){
              $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
          }
          if($SO['stime']&&!$SO['ltime']){
              $filter['dateline'] = ">:". strtotime($SO['stime']);
          }
          if(!$SO['stime']&&$SO['ltime']){
              $filter['dateline'] = "<:".(strtotime($SO['ltime'])+86399);
          }


        }
        if($items = K::M('intracity/bills')->items($filter,array('bills_id'=>"DESC"),$page,50,$count)){
            $group_ids = array();
            foreach($items as $k=>$v){
                $group_ids[$v['group_id']] = $v['group_id'];
            }
            $group_list = K::M('pei/group')->items_by_ids($group_ids);
            foreach($items as $kk=>$vv){
                $items[$kk]['group'] = $group_list[$vv['group_id']];
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, 50, $page, $this->mklink('finance/tongcheng:index', array('{page}')), array('SO' => $SO));
        }

         $this->pagedata['items'] = $items;
         $this->pagedata['pagers'] = $pager;
        $this->tmpl = "admin:finance/tongcheng/index.html";

    }

    public function detail($bills_id,$page){
        $page = max((int)$page,1);
        $limit = 50;
        if(!$bills_id){
            $this->msgbox->add('对账单不存在',201);
        }else if(!$detail = K::M('intracity/bills')->detail($bills_id)){
            $this->msgbox->add('对账单不存在',202);
        }else{
            $filter = array();
            $filter['bills_id'] = $bills_id;
            if($items = K::M('intracity/billslog')->items($filter,array('log_id'=>"DESC"),$page,$limit,$count)){
                $group_ids = $staff_ids = array();
                foreach($items as $k=>$v){
                    $group_ids[$v['group_id']] = $v['group_id'];
                    $staff_ids[$v['staff_id']] = $v['staff_id'];
                }
                $staff_list = K::M('staff/staff')->items_by_ids($staff_ids);
                $group_list = K::M('pei/group')->items_by_ids($group_ids);
                foreach($items as $kk=>$vv){
                    $items[$kk]['group'] = $group_list[$vv['group_id']];
                    $items[$kk]['staff'] =$staff_list[$vv['staff_id']];
                }
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('finance/tongcheng:detail', array($bills_id,'{page}')), array('SO' => $SO));

            }
            $this->pagedata['items'] = $items;
            $this->pagedata['pagers']  = $pager;
            $this->tmpl = "admin:finance/tongcheng/detail.html";

        }

    }

    public function so(){
        $this->tmpl = "admin:finance/tongcheng/so.html";
    }






}