<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/30
 * Time: 17:32
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Subsidy_Member extends Ctl {

    public function items(){
        $filter = array();
        $step_first = strtotime(date('Y-m-01'));  //获取本月第一天时间戳
        $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01')." +1 month")-1)))+86399;
        $filter['dateline'] = $step_first.'~'.$step_last;
        $filter[':SQL']= ' `uid` > 0 ';
        $step = 'd';
        //$sdate = strtotime("2017-6-1");
        $sdate = strtotime(date('Y-m-01', strtotime('-11 month')));
        $mouth_arr = K::M('helper/date')->get_date_list($sdate, __TIME);// 获取2个时间之间的月份数组（不论前后顺序）
        foreach ($mouth_arr as $k=>$v){
            $mouth_arr[$k] = date('Y-m',$v);
        }
        $this->pagedata['bills_month'] = $mouth_arr;
        if($this->checksubmit()){
            $data_form = $this->checksubmit('data');
            $data = array(
                'order'=>0,
                'amount'=>0,
                'sum_platform'=>0,
                'sum_shop'=>0,
                'platform_first'=>0,
                'platform_mj'=>0,
                'platform_hongbao'=>0,
                'shop_first'=>0,
                'shop_mj'=>0,
                'shop_coupon'=>0,
                'shop_discount'=>0,
                'shop_huangou'=>0,
                'platform_peicard'=>0
            );
            foreach ($data_form as $kk_form=>$vv_form){
                if($vv_form){
                    $data[$kk_form] = 1;
                }
            }
        }else{
            $data = array(
                'order'=>1,
                'amount'=>1,
                'sum_platform'=>0,
                'sum_shop'=>0,
                'platform_first'=>0,
                'platform_mj'=>0,
                'platform_hongbao'=>0,
                'shop_first'=>0,
                'shop_mj'=>0,
                'shop_coupon'=>0,
                'shop_discount'=>0,
                'shop_huangou'=>0,
                'platform_peicard'=>0
            );
        }
        if($SO = $this->GP("SO")){
            if($SO['uid']){
                $filter['uid'] = $SO['uid'];
                $member = K::M('member/member')->detail($SO['uid']);
                $this->pagedata['member'] = $member;
            }
            if($SO['mouth']){
                $step_first = strtotime(date('Y-m-01',strtotime($SO['mouth'])));  //获取本月第一天时间戳
                $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01',$step_first)." +1 month")-1)))+86399;
                $filter['dateline'] = $step_first.'~'.$step_last;
            }
            if($SO['show']==1){
                $step = 'h';
            }else{
                $step = 'd';
            }
            $this->pagedata['SO'] = $SO;
            $this->pagedata['member'] = $member;
            $SO = array('SO'=>$SO);
            if(!empty($SO) && is_array($SO)){
                $params = http_build_query($SO);
                $params = '&'.$params;
            }else{
                $params =  '';
            }
            $this->pagedata['query'] = $params;

        }else{
            $this->pagedata['SO'] = array(
                'mouth'=>date('Y-m')
            );
        }
        $high_data = K::M('subsidy/waimai')->group_by_type($filter,$step_first,$step_last,$step,$data);
        $this->pagedata['high_data'] = json_encode($high_data);
        $this->pagedata['data'] = $data;

        $this->tmpl = "admin:subsidy/member/items.html";
    }

    public function load_table($page=1){
        $page = max((int)$page,1);
        $limit = 50;
        $filter = array();
        $step_first = strtotime(date('Y-m-01'));  //获取本月第一天时间戳
        $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01')." +1 month")-1)))+86399;
        $filter['dateline'] = $step_first.'~'.$step_last;
        $filter[':SQL']= ' `uid` > 0 ';
        if($SO = $this->GP("SO")){
            if($SO['uid']){
                $filter['uid'] = $SO['uid'];
            }
            if($SO['mouth']){
                $step_first = strtotime(date('Y-m-01',strtotime($SO['mouth'])));  //获取本月第一天时间戳
                $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01',$step_first)." +1 month")-1)))+86399;
                $filter['dateline'] = $step_first.'~'.$step_last;
            }
        }

        if($items = K::M('subsidy/waimai')->items_join_by_type($filter,$page,$limit,array('dateline'=>"DESC"),'uid',$count)){
            $waimai_ids = array();
            foreach ($items as $k=>$v){
                $waimai_ids[$v['uid']] = $v['uid'];
            }
            $waimai_list  = K::M('member/member')->items_by_ids($waimai_ids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['member'] = $waimai_list[$vv['uid']];
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));

        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['data'] = K::M('subsidy/waimai')->sum_and_count($filter);
        $this->tmpl = "admin:subsidy/member/load_table.html";
    }

    public function detail($uid,$day,$page=1){
        $page = max((int)$page,1);
        $limit = 50;
        if(!$uid||!$day){
            $this->msgbox->add('未指定需要查看的记录',201);
        }else if(!$member = K::M('member/member')->detail($uid,true)){
            $this->msgbox->add('指定查看的用户不存在',202);
        }else {
            $filter = array();
            $filter['uid'] = $uid;
            $filter['day'] = $day;
            if($items = K::M('subsidy/waimai')->items($filter,array('dateline'=>"DESC"),$page,$limit,$count)){
                $order_ids = array();
                foreach ($items as $k=>$v){
                    $order_ids[$v['order_id']] = $v['order_id'];
                }
                $order_list = K::M('order/order')->items_by_ids($order_ids);
                foreach ($items as $kk=>$vv){
                    $items[$kk]['from'] = $order_list[$vv['order_id']]['from'];
                }

                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($uid,$day,'{page}')), array('SO'=>array()));
            }
            $this->pagedata['items'] = $items;
            $this->pagedata['pager'] = $pager;
            $this->pagedata['day'] = $day;
            $this->pagedata['member'] = $member;
            $this->tmpl = "admin:subsidy/member/detail.html";
        }
    }
}