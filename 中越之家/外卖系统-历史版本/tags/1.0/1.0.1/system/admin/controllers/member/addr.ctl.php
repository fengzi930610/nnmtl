<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Member_Addr extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['contact']){$filter['contact'] = "LIKE:%".$SO['contact']."%";}
            if($SO['mobile'] && is_array($SO['mobile'])){$filter['mobile'] = $SO['mobile'];}else if(K::M("verify/check")->ids($SO['mobile'])){$filter['mobile'] = "IN:".$SO['mobile'];}
            if($SO['addr'] && is_array($SO['addr'])){$filter['addr'] = $SO['addr'];}else if(K::M("verify/check")->ids($SO['addr'])){$filter['addr'] = "IN:".$SO['addr'];}
            if($SO['is_default']){$filter['is_default'] = $SO['is_default'];}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            
            //4.0模糊查询
            if($SO['keywords']){
                $filter[':OR'] = array(
                    'contact'=>"LIKE:%".$SO['keywords']."%",
                    'mobile'=>"LIKE:%".$SO['keywords']."%",
                    'addr'=>"LIKE:%".$SO['keywords']."%"
                    );
            }
        }
        if($items = K::M('member/addr')->items($filter, array('addr_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $uids = array();
        $n_name = array();
        foreach($items as $k => $v){
            if(!in_array($v['uid'],$uids)){
                $uids[] = $v['uid'];
            }
        }
        
        $nicknames = K::M('member/member')->items_by_ids($uids);
        $this->pagedata['nicknames'] = $nicknames;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:member/addr/items.html';
    }

    public function so()
    {
        $this->tmpl = 'admin:member/addr/so.html';
    }

    public function detail($addr_id = null)
    {
        if(!$addr_id = (int)$addr_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('member/addr')->detail($addr_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:member/addr/detail.html';
        }
    }

    public function create()
    {
        if($data = $this->checksubmit('data')){
            
            if($addr_id = K::M('member/addr')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?member/addr-index.html');
            } 
        }else{
           $this->tmpl = 'admin:member/addr/create.html';
        }
    }

    public function edit($addr_id=null)
    {
        if(!($addr_id = (int)$addr_id) && !($addr_id = $this->GP('addr_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('member/addr')->detail($addr_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            
            if(K::M('member/addr')->update($addr_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:member/addr/edit.html';
        }
    }

    public function doaudit($addr_id=null)
    {
        if($addr_id = (int)$addr_id){
            if(K::M('member/addr')->batch($addr_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('addr_id')){
            if(K::M('member/addr')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }

    public function delete($addr_id=null)
    {
        if($addr_id = (int)$addr_id){
            if(!$detail = K::M('member/addr')->detail($addr_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('member/addr')->delete($addr_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('addr_id')){
            if(K::M('member/addr')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}