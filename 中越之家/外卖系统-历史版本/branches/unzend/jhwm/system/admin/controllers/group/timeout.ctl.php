<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8
 * Time: 11:12
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Group_Timeout extends Ctl {

    public function index($page=1){
        $pager['page'] = $page = max((int)$page, 1);
        $filter = array();
        if($SO = $this->GP("SO")){
            $pager['SO'] = $SO;
            if($SO['staff_id']){
                $filter['staff_id'] = $SO['staff_id'];
            }
            if($SO['order_id']){
                $filter['order_id'] = $SO['order_id'];
            }
            if($SO['stime']&&$SO['ltime']){
                $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }
            if(!$SO['stime']&&$SO['ltime']){
                $filter['dateline'] = "<:".(strtotime($SO['ltime'])+86399);
            }
            if($SO['stime']&&!$SO['ltime']){
                $filter['dateline'] = "<:".strtotime($SO['stime']);
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (w.name LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        if($items = K::M('staff/timeoutorder')->items_join_staff($filter,array('time_id'=>"DESC"),$page,50,$count)){
            /*$staff_id = array();
            foreach ($items as  $k=>$v){
                $staff_id[$v['staff_id']] = $v['staff_id'];
            }
            $staff_list = K::M('staff/staff')->items_by_ids($staff_id);*/
            foreach ($items as $kk=>$vv){
                //$items[$kk]['staff'] = $staff_list[$vv['staff_id']];
                $items[$kk]['staff'] = array('name'=>$vv['staff_name'], 'mobile'=>$vv['staff_mobile']);
                $items[$kk]['time_left'] = $this->formatTime($vv['complete_time']-$vv['timeout']);
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, 50, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));

        }
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "admin:group/timeout/index.html";

    }

    public function formatTime($strtime)
    {

        $label = '';
        $minutes = intval($strtime/60);

        if($minutes < 60){
            $label = $minutes.'分钟';
        }else if($minutes < 60*24){
            $h = intval($minutes/60);
            $m = $minutes%60;
            $label = $h.'小时'.$m.'分钟';
        }else{
            $d = intval($minutes/(60*24));
            $h = intval(intval(($minutes%(60*24)))/60);
            $m = intval(intval(($minutes%(60*24)))%60);
            $label = $d.'天'.$h.'小时'.$m.'分钟';
        }
        return $label;
    }


    public function so(){
        $this->tmpl = "admin:group/timeout/so.html";
    }

    public function delete($time_id){
        if($data = $this->checksubmit('time_id')){
            foreach ($data as $k=>$v){
                if(!$timeout = K::M('staff/timeoutorder')->detail($v)){
                    $this->msgbox->add('未指定需要删除的内容',204)->response();
                }else if(!K::M('staff/timeoutorder')->delete($v)){
                    $this->msgbox->add('未指定需要删除的内容',205)->response();
                }
            }
            $this->msgbox->add('删除成功');
        }else{
            if(!$time_id){
                $this->msgbox->add('未指定需要删除的内容',201);
            }else if(!$timeout = K::M('staff/timeoutorder')->detail($time_id)){
                $this->msgbox->add('未指定需要删除的内容',202);
            }else if(!K::M('staff/timeoutorder')->delete($time_id)){
                $this->msgbox->add('删除失败',203);
            }else{
                $this->msgbox->add('删除成功');
            }
        }

    }



}