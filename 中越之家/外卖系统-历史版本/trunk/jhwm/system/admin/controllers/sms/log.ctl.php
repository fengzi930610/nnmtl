<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: log.ctl.php 9847 2015-04-23 05:30:45Z maoge $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Sms_Log extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['log_id']){
                $filter['log_id'] = $SO['log_id'];
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter['mobile'] = "LIKE:%".$SO['keywords']."%";
            }
        }
        if($items = K::M('sms/log')->items($filter, array('log_id'=>'DESC'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:sms/log/items.html';
    }

    public function so()
    {
        $this->tmpl = 'admin:sms/log/so.html';
    }

    public function detail($log_id = null)
    {
        if(!$log_id = (int)$log_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('sms/log')->detail($log_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:sms/log/detail.html';
        }
    }

    public function create()
    {
        if($data = $this->checksubmit('data')){
            
            if($log_id = K::M('sms/log')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?sms/log-index.html');
            } 
        }else{
           $this->tmpl = 'admin:sms/log/create.html';
        }
    }

    public function edit($log_id=null)
    {
        if(!($log_id = (int)$log_id) && !($log_id = $this->GP('log_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('sms/log')->detail($log_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            
            if(K::M('sms/log')->update($log_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:sms/log/edit.html';
        }
    }

    public function doaudit($log_id=null)
    {
        if($log_id = (int)$log_id){
            if(K::M('sms/log')->batch($log_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('log_id')){
            if(K::M('sms/log')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }

    public function delete($log_id=null)
    {
        if($log_id = (int)$log_id){
            if(!$detail = K::M('sms/log')->detail($log_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('sms/log')->delete($log_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('log_id')){
            if(K::M('sms/log')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }

    public function admin()
    {
        $have_sms_count = K::M('sms/sms')->query();
        if(K::M('sms/sms')->admin('login',array('code'=>123456))){
            $this->msgbox->add('发送短信成功');
        }else{
            $this->msgbox->add('发送短信失败',211);
        }
    }  
}