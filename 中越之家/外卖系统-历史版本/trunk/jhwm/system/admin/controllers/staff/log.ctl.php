<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Staff_Log extends Ctl
{

    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
      if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['log_id']){$filter['log_id'] = $SO['log_id'];}
            if($SO['staff_id']){$filter['staff_id'] = $SO['staff_id'];}
            if(is_array($SO['day'])){if($SO['day'][0] && $SO['day'][1]){$a = strtotime($SO['day'][0]); $b = strtotime($SO['day'][1])+86400;$filter['day'] = $a."~".$b;}}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
        }
        if($items = K::M('staff/log')->items($filter, array('log_id'=>'desc'), $page, $limit, $count)){
          $staff_ids = array();
          foreach($items as $k=>$v){
             $staff_ids[] = $v['staff_id'];
          }
          $staff_ids = implode(array_unique($staff_ids),',');
          $staff = K::M('staff/staff')->items_by_ids($staff_ids);
          $staffs = array();
          foreach($staff as $k=>$v){
            $staffs[$v['staff_id']] = $v;
          }
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['staffs'] = $staffs;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:staff/log/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:staff/log/so.html';
    }
    public function detail($log_id = null)
    {
        if(!$log_id = (int)$log_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('staff/log')->detail($log_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:staff/log/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($log_id = K::M('staff/log')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?staff/log-index.html');
            }
        }else{
           $this->tmpl = 'admin:staff/log/create.html';
        }
    }
    public function edit($log_id=null)
    {
        if(!($log_id = (int)$log_id) && !($log_id = $this->GP('log_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('staff/log')->detail($log_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            if(K::M('staff/log')->update($log_id, $data)){
                $this->msgbox->add('修改内容成功');
            }
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:staff/log/edit.html';
        }
    }
    public function doaudit($log_id=null)
    {
        if($log_id = (int)$log_id){
            if(K::M('staff/log')->batch($log_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('log_id')){
            if(K::M('staff/log')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($log_id=null)
    {
        if($log_id = (int)$log_id){
            if(!$detail = K::M('staff/log')->detail($log_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('staff/log')->delete($log_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('log_id')){
            if(K::M('staff/log')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}
