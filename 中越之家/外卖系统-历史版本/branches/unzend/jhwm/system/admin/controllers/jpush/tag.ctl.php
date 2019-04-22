<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Jpush_Tag extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        
        if($items = K::M('jpush/tag')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:jpush/tag/items.html';
    }


    public function create()
    {
        if($data = $this->checksubmit('data')){
            
            if($tag_id = K::M('jpush/tag')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?jpush/tag-index.html');
            } 
        }else{
           $this->tmpl = 'admin:jpush/tag/create.html';
        }
    }
    public function edit($tag_id=null)
    {
        if(!($tag_id = (int)$tag_id) && !($tag_id = $this->GP('tag_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('jpush/tag')->detail($tag_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            
            if(K::M('jpush/tag')->update($tag_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:jpush/tag/edit.html';
        }
    }

    public function delete($tag_id=null)
    {
        if($tag_id = (int)$tag_id){
            if(!$detail = K::M('jpush/tag')->detail(tag_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('jpush/tag')->delete($tag_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('tag_id')){
            if(K::M('jpush/tag')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}