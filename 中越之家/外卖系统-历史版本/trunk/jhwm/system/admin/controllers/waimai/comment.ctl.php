<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Waimai_Comment extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['order_id']){$filter['order_id'] = $SO['order_id'];}
            if($SO['content']){$filter['content'] = "LIKE:%".$SO['content']."%";}
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }
            if($SO['uid']){
                $filter['uid'] = $SO['uid'];
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (o.content LIKE '%".$SO['keywords']."%' OR w.nickname LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%' OR ext.title LIKE '%".$SO['keywords']."%' OR ext.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        if($items = K::M('waimai/comment')->items_join_member_shop($filter, array('comment_id'=>"DESC"), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        /*$uids = $shop_ids = array();
        foreach($items as $k=>$val){
            $uids[$val['uid']] = $val['uid'];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->pagedata['shops'] = K::M('waimai/waimai')->items_by_ids($shop_ids);
        $this->pagedata['users'] = K::M('member/member')->items_by_ids($uids);*/
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:waimai/comment/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:waimai/comment/so.html';
    }
    public function detail($comment_id = null)
    {
        if(!$comment_id = (int)$comment_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('waimai/comment')->detail($comment_id,true)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else if($reply_content = $this->checksubmit('reply_content')){
            $a = array('reply'=>$reply_content, 'reply_time'=>__TIME, 'reply_ip'=>__IP);
            if(K::M('waimai/comment')->update($comment_id, $a)){
                $this->msgbox->add('回复评论成功');
            }
        }else{
            $detail['shop'] = K::M('waimai/waimai')->detail($detail['shop_id']);
            $detail['user'] = K::M('member/member')->detail($detail['uid']);
            $detail['photos'] = K::M('waimai/commentphoto')->items(array('comment_id'=>$comment_id)); 
            if($detail['staff_id']){
                $this->pagedata['staff'] = K::M('staff/staff')->detail($detail['staff_id']);
            }
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:waimai/comment/detail.html';
        }
    }
    
    public function delete($comment_id=null)
    {
        if($comment_id = (int)$comment_id){
            if(!$detail = K::M('waimai/comment')->detail($comment_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('waimai/comment')->batch($comment_id, array('closed'=>1))){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('comment_id')){
            if(K::M('waimai/comment')->batch($ids, array('closed'=>1))){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
    
    public function recovery($comment_id=null)
    {
        if($comment_id = (int)$comment_id){
            if(!$detail = K::M('waimai/comment')->find(array('comment_id'=>$comment_id))){
                $this->msgbox->add('你要恢复的内容不存在', 211);
            }else{
                if(K::M('waimai/comment')->batch($comment_id, array('closed'=>0))){
                    $this->msgbox->add('恢复内容成功');
                }
            }
        }else if($ids = $this->GP('comment_id')){
            if(K::M('waimai/comment')->batch($ids, array('closed'=>0))){
                $this->msgbox->add('批量恢复内容成功');
            }
        }else{
            $this->msgbox->add('未指定要恢复的内容ID', 401);
        }
    }  
}