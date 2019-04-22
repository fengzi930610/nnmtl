<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Waimai_OrderProduct extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
                if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['pid']){$filter['pid'] = $SO['pid'];}
if($SO['order_id']){$filter['order_id'] = $SO['order_id'];}
if($SO['product_id']){$filter['product_id'] = $SO['product_id'];}
if($SO['product_name']){$filter['product_name'] = "LIKE:%".$SO['product_name']."%";}
if(is_array($SO['product_price'])){$a = intval($SO['product_price'][0]);$b=intval($SO['product_price'][1]);if($a && $b){$filter['product_price'] = $a."~".$b;}}
if(is_array($SO['package_price'])){$a = intval($SO['package_price'][0]);$b=intval($SO['package_price'][1]);if($a && $b){$filter['package_price'] = $a."~".$b;}}
if(is_array($SO['amount'])){$a = intval($SO['amount'][0]);$b=intval($SO['amount'][1]);if($a && $b){$filter['amount'] = $a."~".$b;}}
        }
        if($items = K::M('waimai/orderproduct')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:waimai/order/product/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:waimai/order/product/so.html';
    }
    public function detail($pid = null)
    {
        if(!$pid = (int)$pid){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('waimai/orderproduct')->detail($pid)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:waimai/order/product/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            
            if($pid = K::M('waimai/orderproduct')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?waimai/orderproduct-index.html');
            } 
        }else{
           $this->tmpl = 'admin:waimai/order/product/create.html';
        }
    }
    public function edit($pid=null)
    {
        if(!($pid = (int)$pid) && !($pid = $this->GP('pid'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('waimai/orderproduct')->detail($pid)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            
            if(K::M('waimai/orderproduct')->update($pid, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:waimai/order/product/edit.html';
        }
    }
    public function doaudit($pid=null)
    {
        if($pid = (int)$pid){
            if(K::M('waimai/orderproduct')->batch($pid, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('pid')){
            if(K::M('waimai/orderproduct')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($pid=null)
    {
        if($pid = (int)$pid){
            if(!$detail = K::M('waimai/orderproduct')->detail(pid)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('waimai/orderproduct')->delete($pid)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('pid')){
            if(K::M('waimai/orderproduct')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}