<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shop_Comment extends Ctl
{
    public function index($page = 1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }
            if($SO['uid']){
                $filter['uid'] = $SO['uid'];
            }
            if(is_array($SO['reply_time'])){
                if($SO['reply_time'][0] && $SO['reply_time'][1]){
                    $a = strtotime($SO['reply_time'][0]);
                    $b = strtotime($SO['reply_time'][1]) + 86400;
                    $filter['reply_time'] = $a . "~" . $b;
                }
            }
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1]) + 86400;
                    $filter['dateline'] = $a . "~" . $b;
                }
            }
        }
        if($items = K::M('shop/comment')->items($filter, array('comment_id' => 'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO' => $SO));
            $shop_ids = $uids = array();
            foreach($items as $k => $v){
                $shop_ids[$v['shop_id']] = $v['shop_id'];
                $uids[$v['uid']] = $v['uid'];
            }
            $shops = K::M('shop/shop')->items_by_ids($shop_ids);
            $members = K::M('member/member')->items_by_ids($uids);
            foreach($items as $k => $v){
                $items[$k]['shop'] = $shops[$v['shop_id']];
                $items[$k]['member'] = $members[$v['uid']];
            }
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:shop/comment/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:shop/comment/so.html';
    }
    public function detail($comment_id = null)
    {
        if(!$comment_id = (int) $comment_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }
        else if(!$detail = K::M('shop/comment')->detail($comment_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }
        else{
            if($detail['have_photo'] == 1){
                $detail['photo'] = K::M('shop/commentphoto')->items(array('comment_id'=>$detail['comment_id']));
            }
            $detail['member'] = K::M('member/member')->detail($detail['uid']);
            $detail['shop'] = K::M('shop/shop')->detail($detail['shop_id']);
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:shop/comment/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($comment_id = K::M('shop/comment')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?shop/comment-index.html');
            }
        }
        else{
            $this->tmpl = 'admin:shop/comment/create.html';
        }
    }
    public function edit($comment_id = null)
    {
        if(!($comment_id = (int) $comment_id) && !($comment_id = $this->GP('comment_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }
        else if(!$detail = K::M('shop/comment')->detail($comment_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }
        else if($data = $this->checksubmit('data')){
            if(K::M('shop/comment')->update($comment_id, $data)){
                $this->msgbox->add('修改内容成功');
            }
        }
        else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:shop/comment/edit.html';
        }
    }
    public function doaudit($comment_id = null)
    {
        if($comment_id = (int) $comment_id){
            if(K::M('shop/comment')->batch($comment_id, array('audit' => 1))){
                $this->msgbox->add('审核内容成功');
            }
        }
        else if($ids = $this->GP('comment_id')){
            if(K::M('shop/comment')->batch($ids, array('audit' => 1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }
        else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($comment_id = null)
    {
        if($comment_id = (int) $comment_id){
            if(!$detail = K::M('shop/comment')->detail($comment_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }
            else{
                if(K::M('shop/comment')->delete($comment_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }
        else if($ids = $this->GP('comment_id')){
            if(K::M('shop/comment')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }
        else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}
