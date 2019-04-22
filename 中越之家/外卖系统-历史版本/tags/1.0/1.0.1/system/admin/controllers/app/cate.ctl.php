<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_App_Cate extends Ctl
{
    
    public function index($page=1)
    {
        $filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
       
        if($items = K::M('app/cate')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
    	$this->tmpl = 'admin:app/cate/items.html';
    }

    public function create()
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
                        if($a = $upload->upload($attach, 'mall')){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            if($data['cate_type'] == 0){
                $data['cate_link'] = $data['app_val'].$data['app_id'];
            }
            $data['cid'] = $data['app_id'];
            unset($data['app_val']);
            unset($data['app_id']);
            
            if($cate_id = K::M('app/cate')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?app/cate-index.html');
            }
        }else{
           $this->tmpl = 'admin:app/cate/create.html';
        }
    }
    public function edit($cate_id=null)
    {
        if(!($cate_id = (int)$cate_id) && !($cate_id = $this->GP('cate_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('app/cate')->detail($cate_id)){
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
                        if($a = $upload->upload($attach, 'mall')){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            if($data['cate_type'] == 1){//网页
                $data['cate_link'] = $data['cate_link'];
            }else{
                $data['cate_link'] = $data['cate_link2'];
            }
            if(K::M('app/cate')->update($cate_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:app/cate/edit.html';
        }
    }

    public function delete($cate_id=null)
    {
        if($cate_id = (int)$cate_id){
            if(!$detail = K::M('app/cate')->detail($cate_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('app/cate')->delete($cate_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('cate_id')){
            if(K::M('app/cate')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}