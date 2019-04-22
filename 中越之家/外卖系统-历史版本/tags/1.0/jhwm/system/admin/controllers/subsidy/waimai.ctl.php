<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/30
 * Time: 10:19
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Subsidy_Waimai extends Ctl  {
    public function items(){
        $filter = array();
        $step_first = strtotime(date('Y-m-01'));  //获取本月第一天时间戳
        $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01')." +1 month")-1)))+86399;
        $filter['dateline'] = $step_first.'~'.$step_last;
        $step = 'd';
        //$sdate = strtotime("2017-6-1");
        $sdate = strtotime(date('Y-m-01', strtotime('-11 month')));
        $mouth_arr = K::M('helper/date')->get_date_list($sdate, __TIME);// 获取2个时间之间的月份数组（不论前后顺序）
        foreach ($mouth_arr as $k=>$v){
            $mouth_arr[$k] = date('Y-m',$v);
        }
        $this->pagedata['bills_month'] = $mouth_arr;
        if($SO = $this->GP("SO")){
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
                $waimai = K::M('waimai/waimai')->detail($SO['shop_id']);
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
            $this->pagedata['waimai'] = $waimai;
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
        $filter[':SQL']= ' `shop_id` > 0 ';
        $high_data = K::M('subsidy/waimai')->group_by_type($filter,$step_first,$step_last,$step);
        $this->pagedata['high_data'] = json_encode($high_data);
        $this->tmpl = "admin:subsidy/waimai/items.html";
    }


    public function load_table($page=1){
        $page = max((int)$page,1);
        $limit = 50;
        $filter = array();
        $filter[':SQL']= ' `shop_id` > 0 ';
        $step_first = strtotime(date('Y-m-01'));  //获取本月第一天时间戳
        $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01')." +1 month")-1)))+86399;
        $filter['dateline'] = $step_first.'~'.$step_last;
        if($SO = $this->GP("SO")){
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }
            if($SO['mouth']){
                $step_first = strtotime(date('Y-m-01',strtotime($SO['mouth'])));  //获取本月第一天时间戳
                $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01',$step_first)." +1 month")-1)))+86399;
                $filter['dateline'] = $step_first.'~'.$step_last;
            }
        }

        if($items = K::M('subsidy/waimai')->items_join_by_type($filter,$page,$limit,array('dateline'=>"DESC"),'shop_id',$count)){
            $waimai_ids = array();
            foreach ($items as $k=>$v){
                $waimai_ids[$v['shop_id']] = $v['shop_id'];
            }
            $waimai_list  = K::M('waimai/waimai')->items_by_ids($waimai_ids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['shop'] = $waimai_list[$vv['shop_id']];
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));

        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['data'] = K::M('subsidy/waimai')->sum_and_count($filter);
        $this->tmpl = "admin:subsidy/waimai/load_table.html";



    }


    public function detail($shop_id,$day,$page){
        if(!$shop_id||!$day){
            $this->msgbox->add('未指定商户或日期',201);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id,true)){
            $this->msgbox->add('商家不存在',202);
        }else{
            $page = max((int)$page,1);
            $limit = 50;
            $filter = array();
            $filter['shop_id'] = $shop_id;
            $filter['day'] = $day;
            if($items = K::M('subsidy/waimai')->items($filter,array('dateline'=>"DESC"),$page,$limit,$count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($shop_id,$day,'{page}')), array('SO'=>array()));

            }
            $this->pagedata['items'] = $items;
            $this->pagedata['pager'] = $pager;
            $this->pagedata['day'] = $day;
            $this->pagedata['waimai'] = $waimai;
            $this->tmpl = "admin:subsidy/waimai/detail.html";

        }

    }




}
