<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Product_Cate extends Ctl
{
    
    public function index($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        
        if($items = K::M('product/cate')->items($filter, null, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            $shop_ids = array();
            foreach($items as $v){
                $shop_ids[$v['shop_id']] = $v['shop_id'];
            }
            if($shop_ids){
                $this->pagedata['shop_list'] = K::M('shop/shop')->items_by_ids($shop_ids);
            }
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:product/cate/items.html';
    }

    public function edit($cate_id=null)
    {
        if(!($cate_id = (int)$cate_id) && !($cate_id = $this->GP('cate_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('product/cate')->detail($cate_id)){
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
            if(K::M('product/cate')->update($cate_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
            $this->pagedata['detail'] = $detail;
            $this->pagedata['shop'] = K::M('shop/shop')->detail($detail['shop_id']);
            $this->tmpl = 'admin:product/cate/edit.html';
        }
    }
    public function doaudit($cate_id=null)
    {
        if($cate_id = (int)$cate_id){
            if(K::M('product/cate')->batch($cate_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('cate_id')){
            if(K::M('product/cate')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($cate_id=null)
    {
        if($cate_id = (int)$cate_id){
            if(!$detail = K::M('product/cate')->detail($cate_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('product/cate')->delete($cate_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('cate_id')){
            if(K::M('product/cate')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}