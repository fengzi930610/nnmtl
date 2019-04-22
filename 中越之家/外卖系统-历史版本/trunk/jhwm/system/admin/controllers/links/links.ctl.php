<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: links.ctl.php 2034 2013-12-07 03:08:33Z $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Links_Links extends Ctl
{
    
    public function index($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;            
        }
        $filter['closed'] = 0;
        if($items = K::M('links/links')->items($filter, null, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:links/items.html';
    }

    public function so()
    {
        $this->tmpl = 'admin:links/so.html';
    }

    public function create()
    {
        if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else {
                if(!$link_id = K::M('links/links')->create($data)){
                    $this->msgbox->add('添加内容成功');
                    $this->msgbox->set_data('forward', '?links/links-index.html');         
                }                   
            } 
        }else{
           $this->tmpl = 'admin:links/create.html';
        }
    }

    public function edit($link_id=null)
    {
        if(!($link_id = (int)$link_id) && !($link_id = $this->GP('link_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('links/links')->detail($link_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($this->checksubmit('data')){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                 if(K::M('links/links')->update($link_id,$data)){
                    $this->msgbox->add('修改内容成功');
                    $this->msgbox->set_data('forward', '?links/links-index.html'); 
                }  
            }   
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:links/edit.html';
        }
    }

    public function delete($link_id=null)
    {
        if($link_id = (int)$link_id){
           if (!$detail = K::M('links/links')->detail($link_id)) {
                $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
            }else if(K::M('links/links')->delete($link_id)){
                $this->msgbox->add('删除成功');
            }
        }else if($ids = $this->GP('link_id')){
            foreach($ids as $id){
               K::M('links/links')->delete($ids);
            }
             $this->msgbox->add('批量删除成功');
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }

}