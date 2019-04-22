<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/22
 * Time: 16:15
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Member_Tixian extends Ctl {

    public function items($page=1){
        $page = max((int)$page,1);
        $limit = 50;
        $filter = array();
        if($SO = $this->GP("SO")){
            $pager['SO'] = $SO;
            if($SO['uid']){
                $filter['uid'] =  $SO['uid'];
            }
            if($SO['status']){
                if($SO['status']==1){
                    $filter['status'] = 0;
                }else if($SO['status']==2){
                    $filter['status'] = 2;
                }
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
                $filter[':SQL'] = " (w.nickname LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        if($items = K::M('member/tixian')->items_join_member($filter,array('tixian_id'=>"DESC"),$page,$limit,$count)){
            /*$uid = array();
            foreach ($items as $k=>$v){
                $uid[$v['uid']] = $v['uid'];
            }
            $member_list = K::M('member/member')->items_by_ids($uid);
            foreach ($items as $kk=>$vv){
                $items[$kk]['member'] = $member_list[$vv['uid']];
            }*/
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink("member/tixian:items", array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = "admin:member/tixian/items.html";

    }

    public function so(){
        $this->tmpl ="admin:member/tixian/so.html";

    }

    public function apply($tixian_id){
        if($tixian_ids = $this->GP("tixian_id")){
            foreach ($tixian_ids as $k=>$v){
                if(!K::M('member/tixian')->agree($v)){
                    $this->msgbox->add("操作失败",203)->response();
                }
            }
            $this->msgbox->add("操作成功");

        }else if($tixian_id){
            if(!K::M('member/tixian')->agree($tixian_id)){
                $this->msgbox->add("操作失败",202);
            }else{
                $this->msgbox->add("操作成功");
            }
        }else{
            $this->msgbox->add('未指定需要操作的内容',201);
        }
    }

    public function refuse($tixian_id){
       if(!$tixian_id){
           $this->msgbox->add('未指定需要操作的内容',201);
       }else if($data = $this->checksubmit('data')){
        if(!$resaon = $data['reason']){
            $this->msgbox->add('请填写拒绝退款',202);
        }else if(!K::M('member/tixian')->unagree($tixian_id,$resaon)){
            $this->msgbox->add('操作失败',203);
        }else{
            $this->msgbox->add('操作成功');
        }

       }else{
           $this->pagedata['tixian_id'] = $tixian_id;
           $this->tmpl = "admin:member/tixian/refuse.html";
       }


    }




}