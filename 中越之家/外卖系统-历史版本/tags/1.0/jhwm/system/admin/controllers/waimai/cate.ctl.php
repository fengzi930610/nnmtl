<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Waimai_Cate extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 500;
        
        if($items = K::M('waimai/cate')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:waimai/cate/items.html';
    }
    public function detail($cate_id = null)
    {
        if(!$cate_id = (int)$cate_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('waimai/cate')->detail($cate_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:waimai/cate/detail.html';
        }
    }
    public function create($parent_id=null)
    {
        //print_r($parent_id);die;
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
            if($cate_id = K::M('waimai/cate')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?waimai/cate-index.html');
            } 
        }else{
           $cates = K::M('waimai/cate')->items(array('parent_id'=>0));
           $cate_ids = array();
           foreach($cates as $k=>$v){
               $cate_ids[$v['cate_id']] = $v['cate_id'];
           }
           $cates2 = K::M('waimai/cate')->items(array('parent_id'=>$cate_ids));
           foreach($cates as $k=>$v){
               foreach($cates2 as $k1=>$v1){
                   if($v['cate_id'] == $v1['parent_id']){
                       $cates[$k]['son'][] = $v1;
                   }
               }
           }
           $this->pagedata['cates'] = $cates;
           $this->pagedata['parent_id'] = intval($parent_id);
           $this->tmpl = 'admin:waimai/cate/create.html';
        }
    }
    public function edit($cate_id=null)
    {
        if(!($cate_id = (int)$cate_id) && !($cate_id = $this->GP('cate_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('waimai/cate')->detail($cate_id)){
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
            if(K::M('waimai/cate')->update($cate_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
            $cates = K::M('waimai/cate')->items(array('parent_id'=>0));
            $cate_ids = array();
            foreach($cates as $k=>$v){
                $cate_ids[$v['cate_id']] = $v['cate_id'];
            }
            $cates2 = K::M('waimai/cate')->items(array('parent_id'=>$cate_ids));
            foreach($cates as $k=>$v){
                foreach($cates2 as $k1=>$v1){
                    if($v['cate_id'] == $v1['parent_id']){
                        $cates[$k]['son'][] = $v1;
                    }
                }
            }
            $this->pagedata['cates'] = $cates;
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:waimai/cate/edit.html';
        }
    }

    public function delete($cate_id=null)
    {
        if($cate_id = (int)$cate_id){
            if(!$detail = K::M('waimai/cate')->detail($cate_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211); 
            }else{
                if(K::M('waimai/cate')->delete($cate_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('cate_id')){
            $cids = array();
            foreach ($ids as $k => $v) {
                $childids = K::M('waimai/cate')->getChildren($v,true);
                $cids = array_merge($cids, $childids);
            }
            $cids = array_unique($cids);

            if(K::M('waimai/cate')->delete($cids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
    
    
    public function update()
    {
        if($orders = $this->GP('orderby')){
            $obj = K::M('waimai/cate');
            foreach($orders as $k=>$v){
                $obj->update($k, array('orderby'=>$v));
            }
            $this->msgbox->add('更新数据成功');
        }
    }

    //设置排序及显示时间
    public function show_time_and_week($cate_id){
        if(!$cate_id){
            $this->msgbox->add('未指定需要设置的分类',201);
        }else if(!$cate = K::M('waimai/cate')->detail($cate_id)){
           $this->msgbox->add('设置的分类不存在',202);
        }else if($cate['parent_id']>0){
            $this->msgbox->add('暂时只支持一级分类的设置',203);
        }else if($data = $this->checksubmit('data')){


        }else{
            $this->pagedata['detail'] =$cate;
            $this->tmpl = 'admin:waimai/cate/showtimeandweek.html';
        }

    }


}