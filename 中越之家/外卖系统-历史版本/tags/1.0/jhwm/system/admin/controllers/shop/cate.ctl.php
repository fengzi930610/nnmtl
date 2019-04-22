<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shop_Cate extends Ctl
{
    
    public function index()
    {
        $items = K::M('shop/cate')->fetch_all();
        $this->pagedata['items'] = $items;
        $this->tmpl = 'admin:shop/cate/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:shop/cate/so.html';
    }
    public function detail($cate_id = null)
    {
        if(!$cate_id = (int)$cate_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('shop/cate')->detail($cate_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:shop/cate/detail.html';
        }
    }
    public function create($parent_id=null)
    {
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
                        if($a = $upload->upload($attach, 'shop')){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            if($cate_id = K::M('shop/cate')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?shop/cate-index.html');
            } 
        }else{
           $this->pagedata['cates'] = K::M('shop/cate')->items(array('parent_id'=>0));
           $this->pagedata['parent_id'] = intval($parent_id);
           $this->tmpl = 'admin:shop/cate/create.html';
        }
    }
    public function edit($cate_id=null)
    {
        if(!($cate_id = (int)$cate_id) && !($cate_id = $this->GP('cate_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('shop/cate')->detail($cate_id)){
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
                    if($a = $upload->upload($attach, 'shop')){
                        $data[$k] = $a['photo'];
                    }
                }
            }
        }
            if(K::M('shop/cate')->update($cate_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
            $this->pagedata['cates'] = K::M('shop/cate')->items(array('parent_id'=>0));
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:shop/cate/edit.html';
        }
    }
    public function doaudit($cate_id=null)
    {
        if($cate_id = (int)$cate_id){
            if(K::M('shop/cate')->batch($cate_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('cate_id')){
            if(K::M('shop/cate')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($cate_id=null)
    {
        if($cate_id = (int)$cate_id){
            if(!$detail = K::M('shop/cate')->detail($cate_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('shop/cate')->delete($cate_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('cate_id')){
            if(K::M('shop/cate')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
    public function update()
    {
        if($orders = $this->GP('orderby')){
            $obj = K::M('shop/cate');
            foreach($orders as $k=>$v){
                $obj->update($k, array('orderby'=>$v));
            }
            $this->msgbox->add('更新数据成功');
        }
    }
    
}