<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 17:12
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Subsidy_Staff extends Ctl {

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
            if($SO['staff_id']){
                $filter['staff_id'] = $SO['staff_id'];
                $staff = K::M('staff/staff')->detail($SO['staff_id']);
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
            $this->pagedata['staff'] = $staff;
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

        $high_chat = K::M('subsidy/staff')->group_by_data($filter,$step_first,$step_last,$step);
        $this->pagedata['high_chat'] = json_encode($high_chat);
        $this->tmpl = "admin:subsidy/staff/items.html";
    }

    public function load_table($page=1){
        $page = max((int)$page,1);
        $limit = 20;
        $filter = array();
        $step_first = strtotime(date('Y-m-01'));  //获取本月第一天时间戳
        $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01')." +1 month")-1)))+86399;
        $filter['dateline'] = $step_first.'~'.$step_last;
        if($SO = $this->GP("SO")){
            if($SO['mouth']){
                $step_first = strtotime(date('Y-m-01',strtotime($SO['mouth'])));  //获取本月第一天时间戳
                $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01',$step_first)." +1 month")-1)))+86399;
                $filter['dateline'] = $step_first.'~'.$step_last;
            }
            if($SO['staff_id']){
                $filter['staff_id'] = $SO['staff_id'];
            }
        }
        if($subsidy_list = K::M('subsidy/staff')->items_join_by_staff_id($filter,$page,$limit,array('day'=>"DESC"),$count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            $staff_ids = $staff_level_ids = array();
            foreach ($subsidy_list as $k=>$v){
                $staff_ids[$v['staff_id']] =$v['staff_id'];
            }
            $staff_list = K::M('staff/staff')->items_by_ids($staff_ids);
            foreach ($staff_list as $kk=>$vv){
                $staff_level_ids[$vv['level_id']] = $vv['level_id'];
            }
            $level_list = K::M('staff/level')->items_by_ids($staff_level_ids);
            foreach ($staff_list as $kkk=>$vvv){
                $staff_list[$kkk]['level'] = $level_list[$vvv['level_id']];
            }
            foreach ($subsidy_list as $k1=>$v1){
                $subsidy_list[$k1]['staff'] = $staff_list[$v1['staff_id']];
            }

        }
        $sum_diff_amount = K::M('subsidy/staff')->sum($filter,'diff_amount');
        $count_diff = K::M('subsidy/staff')->count($filter);
        $this->pagedata['sum_amount'] = $sum_diff_amount;
        $this->pagedata['count_amount'] = $count_diff;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $subsidy_list;
        $this->tmpl = "admin:subsidy/staff/load_table.html";

    }

    public function detail($staff_id,$day,$page){
        if(!$staff_id){
            $this->msgbox->add('未指定配送员',201);
        }else if(!$day){
            $this->msgbox->add('未指定日期',202);
        }else if(!$detail = K::M('staff/staff')->detail($staff_id,true)){
            $this->msgbox->add('需要查看的配送员不存在',203);
        }else{
            $page = max((int)$page,1);
            $limit = 50;
            $filter = array();
            $filter['day'] = $day;
            $filter['staff_id'] = $staff_id;
            if($items =  K::M('subsidy/staff')->items($filter,array('day'=>"DESC"),$page,$limit,$count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($staff_id,$day,'{page}')), array('SO'=>$SO));
            }
            $level = K::M('staff/level')->detail($detail['level_id']);
            $this->pagedata['items'] = $items;
            $this->pagedata['staff'] = $detail;
            $this->pagedata['level'] = $level;
            $this->pagedata['pager'] = $pager;

            $this->pagedata['day'] = $day;
            $this->tmpl = "admin:subsidy/staff/detail.html";



        }


    }






}