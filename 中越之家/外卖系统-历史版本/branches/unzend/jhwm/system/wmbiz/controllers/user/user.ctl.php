<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28
 * Time: 10:18
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_User_User extends Ctl {

    public function index($page,$order_bys = 1){
        $page = max((int)$page,1);
        $limit = 20;
        $filter = array();
        if($SO  = $this->GP('SO')){

            if($SO['start']&&$SO['end']){
                $filter['dateline'] = strtotime($SO['start']).'~'.(strtotime($SO['end'])+86399);
            }
            if(!$SO['start']&&$SO['end']){
                $filter['dateline'] = "<:".(strtotime($SO['end'])+86399);
            }
            if($SO['start']&&!$SO['end']){
                $filter['dateline'] = ">:".strtotime($SO['start']);
            }
            if($SO['stime']||$SO['ltime']||$SO['name']||$SO['mobile']){
                $filter_member = array();
                if($SO['stime']&&$SO['ltime']){
                    $filter_member['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
                }
                if(!$SO['stime']&&$SO['ltime']){
                    $filter_member['dateline'] = "<:".(strtotime($SO['ltime'])+86399);
                }
                if($SO['stime']&&!$SO['ltime']){
                    $filter_member['dateline'] = ">:".strtotime($SO['stime']);
                }
                if($SO['name']){
                    $filter_member['nickname'] = "LIKE:%".$SO['name']."%";
                }
                if($SO['mobile']){
                    $filter_member['mobile'] = "LIKE:%".$SO['mobile']."%";
                }

                if($member = K::M('member/member')->items($filter_member,array(),1,100)){

                    $member_list = array();
                    foreach ($member as $k=>$v){
                        $member_list[$v['uid']] = $v['uid'];
                    }
                    $filter['uid'] = $member_list;

                }

            }

            $this->pagedata['data'] = $SO;

        }
        $this->pagedata['order_by'] = $order_bys;
        $order_by = array("used_money"=>"DESC");
        if($order_bys==0){
            $order_by = array("used_money"=>"ASC");
        }

        $filter['shop_id'] = $this->waimai_shop['shop_id'];
        $filter['order_status'] = ">:-1";
        if($items_tongji = K::M('order/order')->get_user_items_by_shop_id($filter,$page,$limit,$count,$order_by)){
           $member_ids = array();
           foreach ($items_tongji as $kk=>$vv){
               $member_ids[$vv['uid']] = $vv['uid'];
           }
           $member_lists = K::M('member/member')->items_by_ids($member_ids);
           $member_last_order_time = K::M('order/order')->member_last_order_time(array('uid'=>$member_ids,'shop_id'=>$this->waimai_shop['shop_id']));
           foreach ($items_tongji as $kk1=>$vv1){
               $items_tongji[$kk1]['member'] = $member_lists[$vv1['uid']];
               $items_tongji[$kk1]['last'] = $member_last_order_time[$vv1['uid']];
           }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink("user:user/index", array('{page}',$order_bys)), array('SO'=>$SO));

        }

        $this->pagedata['items'] = $items_tongji;
        $this->pagedata['pagers'] = $pager;

        $this->tmpl = "user/index.html";

    }

}