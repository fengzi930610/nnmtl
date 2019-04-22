<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shop_Collect extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
                if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['shop_id']){$filter['shop_id'] = $SO['shop_id'];}
        }
        if($items = K::M('shop/collect')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:shop/collect/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:shop/collect/so.html';
    }
    public function detail($uid = null)
    {
        if(!$uid = (int)$uid){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('shop/collect')->detail($uid)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:shop/collect/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            
            if($uid = K::M('shop/collect')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?shop/collect-index.html');
            } 
        }else{
           $this->tmpl = 'admin:shop/collect/create.html';
        }
    }
    public function edit($uid=null)
    {
        if(!($uid = (int)$uid) && !($uid = $this->GP('uid'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('shop/collect')->detail($uid)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            
            if(K::M('shop/collect')->update($uid, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:shop/collect/edit.html';
        }
    }
    public function doaudit($uid=null)
    {
        if($uid = (int)$uid){
            if(K::M('shop/collect')->batch($uid, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('uid')){
            if(K::M('shop/collect')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($uid=null)
    {
        if($uid = (int)$uid){
            if(!$detail = K::M('shop/collect')->detail($uid)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('shop/collect')->delete($uid)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('uid')){
            if(K::M('shop/collect')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}