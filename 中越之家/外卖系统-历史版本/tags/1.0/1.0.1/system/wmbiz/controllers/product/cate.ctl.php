<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
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
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 0;
        if($items = K::M('waimai/productcate')->items($filter, array('orderby'=>'ASC','cate_id'=>'DESC'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('product/cate/index', array('{page}')), array('SO'=>$SO));
        }
        //echo '<pre>';
        //print_r($items);exit;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'product/cate/index.html';
    }
    
    public function create($parent_id=0)
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
                        if($a = $upload->upload($attach, 'product')){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            $data['shop_id'] = $this->shop_id;
            if(!$type = (int)$this->GP('type')){
                $type = 1;
            }
            /*if($data['stime']&&$data['ltime']){
                if(!preg_match('/^\d{1,2}\:\d{2}$/i', $data['stime'])||!preg_match('/^\d{1,2}\:\d{2}$/i', $data['ltime'])){
                    $this->msgbox->add('开始时间或者结束时间设置错误',201)->response();
                }
                $data['settime'] = serialize(array('stime'=>$data['stime'],'ltime'=>$data['ltime']));
            }
            if(!$data['stime']&&$data['ltime']){
                if(!preg_match('/^\d{1,2}\:\d{2}$/i', $data['ltime'])){
                    $this->msgbox->add('结束时间设置错误',202)->response();
                }
                $data['settime'] = serialize(array('stime'=>'00:00','ltime'=>$data['ltime']));
            }
            if($data['stime']&&!$data['ltime']){
                if(!preg_match('/^\d{1,2}\:\d{2}$/i', $data['stime'])){
                    $this->msgbox->add('开始时间设置错误',202)->response();
                }
                $data['settime'] = serialize(array('stime'=>$data['stime'],'ltime'=>'23:59'));
            }
            if(!$data['stime']&&!$data['ltime']){
                $data['settime'] = '';
            }*/
            if(!$data['title']){
                $this->msgbox->add('分类名称不能为空')->response();
            }else if(!in_array($data['show_type'],array(-1, 0 ,1))){    //v3.6
                $this->msgbox->add('显示设置有误')->response();                
            }else if($data['show_type'] != 1){
                $data['settime'] = '';
            }else if($data['show_type'] == 1 && (!$data['stime'] || !$data['ltime'])){
                $this->msgbox->add('时间不能为空！',211)->response();
            }else{
                $data['stime'] = $data['stime'] ? trim($data['stime']) : '00:00';
                $data['ltime'] = $data['ltime'] ? trim($data['ltime']) : '23:59';
                if(!preg_match('/^\d{1,2}\:\d{2}$/i', $data['stime'])){
                    $this->msgbox->add('开始时间格式不对',219)->response();
                }else if((strpos($data['ltime'],'次日') === false) && (!preg_match('/^\d{1,2}\:\d{2}$/i', $data['ltime']))){
                    $this->msgbox->add('结束时间格式不对',220)->response();
                }else if(!preg_match('/^\d{1,2}\:\d{2}$/i', trim(str_replace('次日','',$data['ltime'])))){
                    $this->msgbox->add('结束时间格式不对',220)->response();
                }else{
                    $data['settime'] = serialize(array('stime'=>$data['stime'],'ltime'=>$data['ltime']));
                }
            }           

            unset($data['stime']);
            unset($data['ltime']);
            if($cate_id = K::M('waimai/productcate')->create($data)){
                $this->msgbox->add('添加内容成功');
                if($type == 1){
                    $this->msgbox->set_data('forward', $this->mklink('product/cate/index'));
                }else{
                    $this->msgbox->set_data('forward', $this->mklink('product/cate/create',array($data['parent_id'])));
                }
            }
        }else{
            //$this->pagedata['cates'] = K::M('waimai/productcate')->items(array('parent_id'=>0, 'shop_id'=>$this->shop_id));
            $this->pagedata['parent_id'] = intval($parent_id);
            if($parent_id = (int)$parent_id){
                $this->pagedata['detail'] = K::M('waimai/productcate')->detail($parent_id);
            }
            $this->tmpl = 'product/cate/create.html';
        }
    }


    public function edit($cate_id=null)
    {
        if(!($cate_id = (int)$cate_id) && !($cate_id = $this->GP('cate_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('waimai/productcate')->detail($cate_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($detail['shop_id'] != $this->shop_id){
            $this->msgbox->add('非法操作', 213);
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
            /*if($data['stime']&&$data['ltime']){
                if(!preg_match('/^\d{1,2}\:\d{2}$/i', $data['stime'])||!preg_match('/^\d{1,2}\:\d{2}$/i', $data['ltime'])){
                    $this->msgbox->add('开始时间或者结束时间设置错误',201)->response();
                }
                $data['settime'] = serialize(array('stime'=>$data['stime'],'ltime'=>$data['ltime']));
            }
            if(!$data['stime']&&$data['ltime']){
                if(!preg_match('/^\d{1,2}\:\d{2}$/i', $data['ltime'])){
                    $this->msgbox->add('结束时间设置错误',202)->response();
                }
                $data['settime'] = serialize(array('stime'=>'00:00','ltime'=>$data['ltime']));
            }
            if($data['stime']&&!$data['ltime']){
                if(!preg_match('/^\d{1,2}\:\d{2}$/i', $data['stime'])){
                    $this->msgbox->add('开始时间设置错误',202)->response();
                }
                $data['settime'] = serialize(array('stime'=>$data['stime'],'ltime'=>'23:59'));
            }
            if(!$data['stime']&&!$data['ltime']){
                $data['settime'] = '';
            }*/
            if(!in_array($data['show_type'],array(-1, 0 ,1))){     //v3.6
                $this->msgbox->add('显示设置有误')->response();                
            }else if($data['show_type'] != 1){
                $data['settime'] = '';
            }else if($data['show_type'] == 1 && (!$data['stime'] || !$data['ltime'])){
                $this->msgbox->add('时间不能为空！',211)->response();
            }else{
                $data['stime'] = $data['stime'] ? trim($data['stime']) : '00:00';
                $data['ltime'] = $data['ltime'] ? trim($data['ltime']) : '23:59';
                if(!preg_match('/^\d{1,2}\:\d{2}$/i', $data['stime'])){
                    $this->msgbox->add('开始时间格式不对',219)->response();
                }else if((strpos($data['ltime'],'次日') === false) && (!preg_match('/^\d{1,2}\:\d{2}$/i', $data['ltime']))){
                    $this->msgbox->add('结束时间格式不对',220)->response();
                }else if(!preg_match('/^\d{1,2}\:\d{2}$/i', trim(str_replace('次日','',$data['ltime'])))){
                    $this->msgbox->add('结束时间格式不对',220)->response();
                }else{
                    $data['settime'] = serialize(array('stime'=>$data['stime'],'ltime'=>$data['ltime']));
                }
            }
            unset($data['stime']);
            unset($data['ltime']);
            if(K::M('waimai/productcate')->update($cate_id, $data)){
                $this->msgbox->add('修改内容成功');
                $this->msgbox->set_data('forward', $this->mklink('product/cate/index'));
            }  
        }else{
           
            $this->pagedata['cates'] = K::M('waimai/productcate')->items(array('parent_id'=>0, 'shop_id'=>$this->shop_id));
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'product/cate/edit.html';
        }    
    }
    

    public function delete($cate_id=null)
    {
        if($cate_id = (int)$cate_id){
            if(!$detail = K::M('waimai/productcate')->detail($cate_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else if($detail['shop_id'] != $this->shop_id){
                $this->msgbox->add('非法操作', 213);
            }else if(K::M('waimai/productcate')->count(array('parent_id'=>$cate_id))){
                $this->msgbox->add('该分类下有子分类不能删除', 214);
            }else if(K::M('waimai/product')->count(array('cate_id'=>$cate_id,'closed'=>0))){
                $this->msgbox->add('该分类下有商品不能删除', 215);
            }else{
                if(K::M('waimai/productcate')->delete($cate_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
    

}
