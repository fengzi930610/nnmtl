<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Waimai_Youhui extends Ctl
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
        if($items = K::M('waimai/youhui')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:waimai/youhui/items.html';
    }
    public function shop($shop_id=null)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('未指定隶属商家', 211);
        }else if(!$shop = K::M('waimai/waimai')->detail($shop_id, true)){
            $this->msgbox->add('指定的商家不存在或删除', 212);
        }else{
            $filter = array('shop_id'=>$shop_id);
            if($items = K::M('waimai/youhui')->items($filter, null, $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($shop_id, '{page}')));
                $this->pagedata['items'] = $items;
            }
            $this->pagedata['shop'] = $shop;
            $this->tmpl = 'admin:waimai/youhui/shop.html';
        } 
    }
    
    
    
    public function so()
    {
        $this->tmpl = 'admin:waimai/youhui/so.html';
    }
    public function detail($youhui_id = null)
    {
        if(!$youhui_id = (int)$youhui_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('waimai/youhui')->detail($youhui_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:waimai/youhui/detail.html';
        }
    }
    public function create($shop_id=null)
    {
        if(!($shop_id = (int)$shop_id) && !($shop_id = $this->GP('shop_id'))){
             $this->msgbox->add('未指定隶属商家', 211);
        }else{
            if($data = $this->checksubmit('data')){
                $data['shop_id'] = $shop_id;
                if($youhui_id = K::M('waimai/youhui')->create($data)){
                    $this->msgbox->add('添加内容成功');
                    $this->msgbox->set_data('forward', $this->mklink('waimai/youhui:shop',array($shop_id)));
                } 
            }else{
               $this->tmpl = 'admin:waimai/youhui/create.html';
            }
        }
    }
    public function edit($youhui_id=null)
    {
        if(!($youhui_id = (int)$youhui_id) && !($youhui_id = $this->GP('youhui_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('waimai/youhui')->detail($youhui_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            
            if(K::M('waimai/youhui')->update($youhui_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:waimai/youhui/edit.html';
        }
    }
    public function doaudit($youhui_id=null)
    {
        if($youhui_id = (int)$youhui_id){
            if(K::M('waimai/youhui')->batch($youhui_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('youhui_id')){
            if(K::M('waimai/youhui')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($youhui_id=null)
    {
        if($youhui_id = (int)$youhui_id){
            if(!$detail = K::M('waimai/youhui')->detail($youhui_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('waimai/youhui')->delete($youhui_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('youhui_id')){
            if(K::M('waimai/youhui')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}