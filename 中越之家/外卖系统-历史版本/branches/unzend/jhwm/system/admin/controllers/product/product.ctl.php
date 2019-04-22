<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Product_Product extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['shop_id']){$filter['shop_id'] = $SO['shop_id'];}
            if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
        }
        $filter['closed'] = 0;
        if($items = K::M('waimai/product')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $shop_ids = $cate_ids = array();
        foreach($items as $k=>$v){
            $shop_ids[$v['shop_id']] = $v['shop_id'];
            $cate_ids[$v['cate_id']] = $v['cate_id'];
        }
        $this->pagedata['shops'] = K::M('shop/shop')->items_by_ids($shop_ids);
        $this->pagedata['cates'] = K::M('product/cate')->items_by_ids($cate_ids);
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:product/product/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:product/product/so.html';
    }
    public function detail($product_id = null)
    {
        if(!$product_id = (int)$product_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('waimai/product')->detail($product_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->pagedata['shop'] = K::M('shop/shop')->detail($detail['shop_id']);
            $this->pagedata['cates'] = K::M('product/cate')->items(array('shop_id'=>$detail['shop_id']));
            $this->tmpl = 'admin:product/product/detail.html';
        }
    }
    
        public function shop($shop_id=null)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('未指定隶属商家', 211);
        }else if(!$shop = K::M('shop/shop')->detail($shop_id, true)){
            $this->msgbox->add('指定的商家不存在或删除', 212);
        }else{
            $filter = array('shop_id'=>$shop_id, 'closed'=>0);
            if($items = K::M('waimai/product')->items($filter, null, $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($shop_id, '{page}')));
                $this->pagedata['items'] = $items;
            }
            $this->pagedata['shop'] = $shop;
            $cates = K::M('product/cate')->items(array('shop_id'=>$shop_id));
            $this->pagedata['cates'] = $cates;
            $this->tmpl = 'admin:product/product/shop.html';
        } 
    }
    public function create($shop_id=null)
    { 
        if(!($shop_id = (int)$shop_id) && !($shop_id = $this->GP('shop_id'))){
             $this->msgbox->add('未指定隶属商家', 211);
        }else{
            if($data = $this->checksubmit('data')){
                        if($_FILES['data']){
                foreach($_FILES['data'] as $k=>$v){
                    foreach($v as $kk=>$vv){
                        $attachs[$kk][$k] = $vv;
                    }
                }
                $upload = K::M('magic/upload');
                foreach($attachs as $k=>$attach){
                    if($attach['error'] == UPLOAD_ERR_OK){
                        if($a = $upload->upload($attach, 'product')){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            $data['shop_id'] = $shop_id;
                if($product_id = K::M('waimai/product')->create($data)){
                    $this->msgbox->add('添加内容成功');
                    $this->msgbox->set_data('forward',  $this->mklink('waimai/product:shop',array($shop_id)));
                   
                } 
            }else{
                $this->pagedata['shop_id'] = $shop_id;
               $this->tmpl = 'admin:product/product/create.html';
            }
        }
    }
    public function edit($product_id=null)
    {
        if(!($product_id = (int)$product_id) && !($product_id = $this->GP('product_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('waimai/product')->detail($product_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
                    if($_FILES['data']){
            foreach($_FILES['data'] as $k=>$v){
                foreach($v as $kk=>$vv){
                    $attachs[$kk][$k] = $vv;
                }
            }
            $upload = K::M('magic/upload');
            foreach($attachs as $k=>$attach){
                if($attach['error'] == UPLOAD_ERR_OK){
                    if($a = $upload->upload($attach, 'product')){
                        $data[$k] = $a['photo'];
                    }
                }
            }
        }
            if(K::M('waimai/product')->update($product_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
                $this->pagedata['shop'] = K::M('shop/shop')->detail($detail['shop_id']);
                $this->pagedata['detail'] = $detail;
                $this->tmpl = 'admin:product/product/edit.html';
        }
    }
    public function delete($product_id=null)
    {
        if($product_id = (int)$product_id){
            if(!$detail = K::M('waimai/product')->detail($product_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('waimai/product')->batch($product_id,array('closed'=>1))){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('product_id')){
            if(K::M('waimai/product')->batch($ids,array('closed'=>1))){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}