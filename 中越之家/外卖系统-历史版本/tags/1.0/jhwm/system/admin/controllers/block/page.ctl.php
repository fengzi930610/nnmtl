<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: page.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Block_Page extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($items = K::M('block/page')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:block/page/items.html';
    }





    public function create()
    {
        if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                if($page_id = K::M('block/page')->create($data)){
                    $this->msgbox->add('添加内容成功');
                    $this->msgbox->set_data('forward', '?block/page-index.html');
                }
            } 
        }else{
           $this->tmpl = 'admin:block/page/create.html';
        }
    }

    public function edit($page_id=null)
    {
        if(!($page_id = (int)$page_id) && !($page_id = $this->GP('page_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('block/page')->detail($page_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($this->checksubmit('data')){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                if(K::M('block/page')->update($page_id, $data)){
                    $this->msgbox->add('修改内容成功');
                }  
            } 
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:block/page/edit.html';
        }
    }



    public function delete($page_id=null)
    {
        if($page_id = (int)$page_id){
            if(K::M('block/page')->delete($page_id)){
                $this->msgbox->add('删除成功');
            }
        }else if($ids = $this->GP('page_id')){
            if(K::M('block/page')->delete($ids)){
                $this->msgbox->add('批量删除成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }

}