<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/23
 * Time: 17:21
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Group_Assessment extends Ctl {

    public function index($page){
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page,1);
        $pager['limit'] = $limit = 50;
        $filter['from'] = 'paotui';
        $filter['audit'] = 1;
        $filter['closed'] = 0;
        $filter_date = array(
            'dateline'=>'>:0'
        );
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['staff_id']){
                $filter['staff_id'] = $SO['staff_id'];
            }
            if($SO['stime']&&$SO['ltime']){
                $filter_date['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }
            if(!$SO['stime']&&$SO['ltime']){
                $filter_date['dateline'] = "<:".(strtotime($SO['ltime'])+86399);
            }
            if($SO['stime']&&!$SO['ltime']){
                $filter_date['dateline'] ='>:'.strtotime($SO['stime']);
            }
            //搜索配送站
            if($SO['group_id']){
                $filter['group_id'] = $SO['group_id'];
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (o.name LIKE '%".$SO['keywords']."%' OR o.mobile LIKE '%".$SO['keywords']."%' OR w.group_name LIKE '%".$SO['keywords']."%')";
            }
        }
        if($staff = K::M('staff/staff')->items_join_group($filter, array('staff_id'=>"DESC"), $page, $limit, $count)){
            $staff_ids = array();
            $group_id = array();
            foreach ($staff as $k=>$v){
                $staff_ids[$v['staff_id']] = $v['staff_id'];
                $group_id[$v['group_id']] = $v['group_id'];
            }

            $all_order = K::M('order/order')->assessment_group_by_staff_order(array('staff_id'=>array_values($staff_ids),'dateline'=>$filter_date['dateline']));
            $compltet_order = K::M('order/order')->assessment_group_by_staff_order(array('staff_id'=>array_values($staff_ids),'order_status'=>array(4,8),'dateline'=>$filter_date['dateline']));
            $yichang_order = K::M('order/order')->assessment_group_by_staff_order(array('staff_id'=>array_values($staff_ids),'order_status'=>-2,'from'=>"waimai",'dateline'=>$filter_date['dateline']));
            $tousu_order = K::M('waimai/complaint')->assessment_group_by_staff_order(array('staff_id'=>array_values($staff_ids),'shop_id'=>0,'dateline'=>$filter_date['dateline']));
            $timeout_order  = K::M('staff/timeoutorder')->assessment_group_by_staff_order(array('staff_id'=>array_values($staff_ids),'dateline'=>$filter_date['dateline']));
            //$pei_group = K::M('pei/group')->items_by_ids($group_id);
            foreach ($staff as $kk=>$vv){
                $staff[$kk]['all'] = $all_order[$vv['staff_id']]['orders']?$all_order[$vv['staff_id']]['orders']:0;
                $staff[$kk]['compltet'] = $compltet_order[$vv['staff_id']]['orders']?$compltet_order[$vv['staff_id']]['orders']:0;
                $staff[$kk]['yichang'] = $yichang_order[$vv['staff_id']]['orders']?$yichang_order[$vv['staff_id']]['orders']:0;
                $staff[$kk]['tousu'] = $tousu_order[$vv['staff_id']]['orders']?$tousu_order[$vv['staff_id']]['orders']:0;
                $staff[$kk]['timeout'] = $timeout_order[$vv['staff_id']]['orders']?$timeout_order[$vv['staff_id']]['orders']:0;
                //$staff[$kk]['group'] = $pei_group[$vv['group_id']]?$pei_group[$vv['group_id']]:array();
                $staff[$kk]['group'] = array('group_name'=>$vv['group_name']);
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $staff;
        $this->tmpl = "admin:group/assessment/index.html";
    }

    public function so(){
        $this->tmpl = "admin:group/assessment/so.html";
    }




}