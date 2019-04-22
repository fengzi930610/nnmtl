<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shop_Msg extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
                if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['shop_id']){$filter['shop_id'] = $SO['shop_id'];}
            if($SO['order_id']){$filter['order_id'] = $SO['order_id'];}
            if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
            if($SO['type']){$filter['type'] = $SO['type'];}
            if($SO['is_read']){$filter['is_read'] = $SO['is_read'];}
        }
        if($items = K::M('shop/msg')->items($filter, array('msg_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $shop_ids = array();
        foreach ($items as $val){
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->pagedata['shops'] = K::M('shop/shop')->items_by_ids($shop_ids);
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:shop/msg/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:shop/msg/so.html';
    }
    
    public function detail($msg_id = null)
    {
        if(!$msg_id = (int)$msg_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('shop/msg')->detail($msg_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:shop/msg/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            
            if($msg_id = K::M('shop/msg')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?shop/msg-index.html');
            } 
        }else{
           $this->tmpl = 'admin:shop/msg/create.html';
        }
    }

    public function delete($msg_id=null)
    {
        if($msg_id = (int)$msg_id){
            if(!$detail = K::M('shop/msg')->detail($msg_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('shop/msg')->delete($msg_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('msg_id')){
            if(K::M('shop/msg')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}