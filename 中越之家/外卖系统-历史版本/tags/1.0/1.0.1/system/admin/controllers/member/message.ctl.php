<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Member_Message extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
                if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['message_id']){$filter['message_id'] = $SO['message_id'];}
if($SO['uid']){$filter['uid'] = $SO['uid'];}
if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
if(is_array($SO['clientip'])){if($SO['clientip'][0] && $SO['clientip'][1]){$a = strtotime($SO['clientip'][0]); $b = strtotime($SO['clientip'][1])+86400;$filter['clientip'] = $a."~".$b;}}
        }
        if($items = K::M('member/message')->items($filter, array('message_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $uids = array();
        foreach($items as $k=>$val){
            $uids[$val['uid']] = $val['uid'];
        }
        $this->pagedata['users'] = K::M('member/member')->items_by_ids($uids);
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['types'] = K::M('member/message')->get_message_type();
        $this->tmpl = 'admin:member/message/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:member/message/so.html';
    }
    public function detail($message_id = null)
    {
        if(!$message_id = (int)$message_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('member/message')->detail($message_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $detail['user'] = K::M('member/member')->detail($detail['uid']);
            $this->pagedata['detail'] = $detail;
            $this->pagedata['types'] = K::M('member/message')->get_message_type();
            $this->tmpl = 'admin:member/message/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            
            if($message_id = K::M('member/message')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?member/message-index.html');
            } 
        }else{
           $this->pagedata['types'] = K::M('member/message')->get_message_type();
           $this->tmpl = 'admin:member/message/create.html';
        }
    }
    public function edit($message_id=null)
    {
        if(!($message_id = (int)$message_id) && !($message_id = $this->GP('message_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('member/message')->detail($message_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            
            if(K::M('member/message')->update($message_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
        	$this->pagedata['detail'] = $detail;
            $this->pagedata['member'] = K::M('member/member')->detail($detail['uid']);
            $this->pagedata['types'] = K::M('member/message')->get_message_type();
        	$this->tmpl = 'admin:member/message/edit.html';
        }
    }

    public function delete($message_id=null)
    {
        if($message_id = (int)$message_id){
            if(!$detail = K::M('member/message')->detail($message_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('member/message')->delete($message_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('message_id')){
            if(K::M('member/message')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}