<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Member_Invite extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = $uids = $inviteuids = $items = $members = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        
        if($items = K::M('member/invite')->items($filter, array('invite_uid'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        foreach($items as $v) {
            $uids[] = $v['uid'];
            $inviteuids[] = $v['invite_uid'];
        }
        $uids = array_unique(array_merge($uids, $inviteuids));
        if($members = K::M('member/member')->items_by_ids($uids)) {
            $this->pagedata['mems'] = $members;
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:member/invite/items.html';
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            
            if($invite_uid = K::M('member/invite')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?member/invite-index.html');
            } 
        }else{
           $this->tmpl = 'admin:member/invite/create.html';
        }
    }
    public function edit($invite_uid=null)
    {
        if(!($invite_uid = (int)$invite_uid) && !($invite_uid = $this->GP('invite_uid'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('member/invite')->detail($invite_uid)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            
            if(K::M('member/invite')->update($invite_uid, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:member/invite/edit.html';
        }
    }

}