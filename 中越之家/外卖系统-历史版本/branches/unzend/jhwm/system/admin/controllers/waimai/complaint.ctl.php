<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7
 * Time: 15:02
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Waimai_Complaint extends Ctl
{

    public function index($page = 1)
    {
        $pager = $filter = array();
        $pager['page'] = $page = max((int)$page,1);
        $pager['limit'] = $limit = 50;       
        $filter['staff_id'] = 0;
        if($SO=$this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }
            if($SO['stime']&&$SO['ltime']){
                $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }
            if(!$SO['stime']&&$SO['ltime']){
                $filter['dateline']  = "<:".(strtotime($SO['ltime'])+86399);
            }
            if($SO['stime']&&!$SO['ltime']){
                $filter['dateline']  = ">:".strtotime($SO['stime']);
            }
            if($SO['uid']){
                $filter['uid'] =$SO['uid'];
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (o.content LIKE '%".$SO['keywords']."%' OR w.nickname LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%' OR ext.title LIKE '%".$SO['keywords']."%' OR ext.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        if($items = K::M('waimai/complaint')->items_join_member_shop($filter, array('complaint_id'=>'DESC'), $pager, $limit, $count)){
            /*$uids = $shop_ids = array();
            foreach ($items as $k=>$v){
                $uids[$v['uid']] =$v['uid'];
                $shop_ids[$v['shop_id']] =$v['shop_id'];
            }
            $member_list = K::M('member/member')->items_by_ids($uids);
            $waimai_list = K::M('waimai/waimai')->items_by_ids($shop_ids);
            $shop_list = K::M('shop/shop')->items_by_ids($shop_ids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['member'] = $member_list[$vv['uid']]['nickname'].'('.$member_list[$vv['uid']]['mobile'].')';
                $items[$kk]['shop'] = $waimai_list[$vv['shop_id']]['title'].'('.$shop_list[$vv['shop_id']]['mobile'].')';
            }*/
            foreach ($items as $k=>$v){
                $items[$k]['member'] = $v['member_nickname'].'('.$v['member_mobile'].')';
                $items[$k]['shop'] = $v['shop_title'].'('.$v['shop_mobile'].')';
            }

            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink("waimai/complaint/index", array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pagers'] = $pager;
        $this->tmpl = "admin:waimai/complaint/index.html";
    }

    public function detail($complaint_id){
       if($data = $this->GP('data')){
           if(!$complaint_id = $data['complaint_id']){
               $this->msgbox->add('投诉不存在',203);
           }else if(!$data['reply']){
               $this->msgbox->add('请填写回复内容',204);
           }else{
               if(K::M('waimai/complaint')->update($complaint_id,array('reply'=>$data['reply'],'reply_time'=>__TIME))){
                  $this->msgbox->add('回复成功');
               }else{
                   $this->msgbox->add('回复失败',205);
               }
           }
       }else{
           if(!$complaint_id){
               $this->msgbox->add('投诉不存在',201);
           }else if(!$complaint = K::M('waimai/complaint')->detail($complaint_id)){
               $this->msgbox->add('投诉不存在',202);
           }else{
               $member = K::M('member/member')->detail($complaint['uid']);
               $waimai = K::M('waimai/waimai')->detail($complaint['shop_id']);
               $photo = K::M('waimai/complaintphoto')->items(array('complaint_id'=>$complaint_id));
               $this->pagedata['member'] = $member;
               $this->pagedata['waimai'] = $waimai;
               $this->pagedata['photo'] = $photo;
               $this->pagedata['complaint']=$complaint;
               $this->tmpl = "admin:waimai/complaint/detail.html";
           }
       }
    }

    public function delete($complaint_id){
        if($this->checksubmit()){
            if(!$data = $this->checksubmit('complaint_id')){
                $this->msgbox->add('未指定需要删除的投诉',201);
            }else{
                foreach ($data as $k=>$v){
                    if(!$complaint = K::M('waimai/complaint')->detail($v)){
                        $this->msgbox->add('需要删除的投诉不存在',202)->response();
                    }else{
                        if(!K::M('waimai/complaint')->delete($v)){
                            $this->msgbox->add('删除失败',203)->response();
                        }
                    }
                }
                $this->msgbox->add('删除成功');
            }
        }else{

            if(!$complaint_id){
                $this->msgbox->add('需要删除的投诉不存在',204);
            }else if(!$complaint = K::M('waimai/complaint')->detail($complaint_id)){
                $this->msgbox->add('需要删除的投诉不存在',205);
            }else if(K::M('waimai/complaint')->delete($complaint_id)){
                $this->msgbox->add('删除成功');
            }else{
                $this->msgbox->add('删除失败',206);
            }
        }
    }

    public function so(){
        $this->tmpl = "admin:waimai/complaint/so.html";
    }

}
