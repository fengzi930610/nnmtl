<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8
 * Time: 14:58
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Group_Refund extends Ctl {

    public function index($page=1){
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 50;
        $filter['order_status'] = -2;
        $filter['from'] = 'waimai';
        $filter['staff_id'] = ">:0";
        if($SO = $this->GP("SO")){
            $pager['SO'] = $SO;
            if($SO['order_id']){
                $filter['order_id'] = $SO['order_id'];
            }
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

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (w.name LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        if($items = K::M('order/order')->items_join_by_staff($filter,array('order_id'=>"DESC"), $page, $limit, $count)){
            $shop_ids = $staff_ids = array();
            foreach ($items as $k=>$v){
                $shop_ids[$v['shop_id']] = $v['shop_id'];
                //$staff_ids[$v['staff_id']] = $v['staff_id'];
            }
            $waimai_list = K::M('waimai/waimai')->items_by_ids($shop_ids);
            //$staff_list = K::M('staff/staff')->items_by_ids($staff_ids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['waimai'] = $waimai_list[$vv['shop_id']];
                //$items[$kk]['staff'] = $staff_list[$vv['staff_id']];
                $items[$kk]['staff'] = array('name'=>$vv['staff_name'], 'mobile'=>$vv['staff_mobile']);
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = "admin:group/refund/index.html";
    }

    public function so(){
        $this->tmpl = "admin:group/refund/so.html";
    }
}