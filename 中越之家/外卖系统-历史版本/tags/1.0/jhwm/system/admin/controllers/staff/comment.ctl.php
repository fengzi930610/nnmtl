<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Staff_Comment extends Ctl
{
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        
        $filter['staff_id'] = '>:0';
        if($items = K::M('staff/comment')->items($filter, array('comment_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:staff/comment/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:staff/comment/so.html';
    }
    public function detail($comment_id = null)
    {
        if(!$comment_id = (int)$comment_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('staff/comment')->detail($comment_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:staff/comment/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($comment_id = K::M('staff/comment')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?staff/comment-index.html');
            }
        }else{
           $this->tmpl = 'admin:staff/comment/create.html';
        }
    }
    public function edit($comment_id=null)
    {
        if(!($comment_id = (int)$comment_id) && !($comment_id = $this->GP('comment_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('staff/comment')->detail($comment_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            if(K::M('staff/comment')->update($comment_id, $data)){
                $this->msgbox->add('修改内容成功');
            }
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:staff/comment/edit.html';
        }
    }
    public function doaudit($comment_id=null)
    {
        if($comment_id = (int)$comment_id){
            if(K::M('staff/comment')->batch($comment_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('comment_id')){
            if(K::M('staff/comment')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($comment_id=null)
    {
        if($comment_id = (int)$comment_id){
            if(!$detail = K::M('staff/comment')->detail(comment_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('staff/comment')->delete($comment_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('comment_id')){
            if(K::M('staff/comment')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}
