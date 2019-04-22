<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Staff_Tixian extends Ctl
{
    protected $_status = array(0=>'待处理',1=>'通过',2=>'拒绝');
    public function index($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
      if($SO = $this->GP('SO')){
         $pager['SO'] = $SO;
         if($SO['tixian_id']){$filter['tixian_id'] = $SO['tixian_id'];}
         if($SO['staff_id']){$filter['staff_id'] = $SO['staff_id'];}
         if($SO['status']){$filter['status'] = $SO['status'];}
         if(is_array($SO['updatetime'])){if($SO['updatetime'][0] && $SO['updatetime'][1]){$a = strtotime($SO['updatetime'][0]); $b = strtotime($SO['updatetime'][1])+86400;$filter['updatetime'] = $a."~".$b;}}
      }
      $orderby = array('tixian_id'=>'desc');
      if($items = K::M('staff/tixian')->items($filter, $orderby, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
      }
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
      $this->pagedata['staffs'] = $staffs;
      $this->pagedata['items']  = $items;
      $this->pagedata['pager']  = $pager;
      $this->pagedata['status'] = $this->_status;
      $this->tmpl = 'admin:staff/tixian/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:staff/tixian/so.html';
    }
    public function detail($tixian_id = null)
    {
        if(!$tixian_id = (int)$tixian_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('staff/tixian')->detail($tixian_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:staff/tixian/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($tixian_id = K::M('staff/tixian')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?staff/tixian-index.html');
            }
        }else{
           $this->tmpl = 'admin:staff/tixian/create.html';
        }
    }
    public function edit($tixian_id=null)
    {
        if(!($tixian_id = (int)$tixian_id) && !($tixian_id = $this->GP('tixian_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('staff/tixian')->detail($tixian_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            if(K::M('staff/tixian')->update($tixian_id, $data)){
                $this->msgbox->add('修改内容成功');
            }
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:staff/tixian/edit.html';
        }
    }
    public function doaudit($tixian_id=null)
    {
        if($tixian_id = (int)$tixian_id){
            if(K::M('staff/tixian')->update($tixian_id, array('status'=>1,'updatetime'=>__TIME))){
                $this->msgbox->add('通过审核成功');
            }
        }else if($ids = $this->GP('tixian_id')){
            if(K::M('staff/tixian')->update($ids, array('status'=>1,'updatetime'=>__TIME))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function reason($tixian_id=null)
    {
        if(!($tixian_id = (int)$tixian_id) && !($tixian_id = (int)$this->GP('tixian_id'))){
            $this->msgbox->add('未指要操作的体现ID', 211);
        }else if(!$detail = K::M('staff/tixian')->detail($tixian_id)){
            $this->msgbox->add('提现不存在或已经删除', 212);
        }else if(!empty($detail['status'])){            
            $this->msgbox->add('提现申请状态不可退回', 213);
        }else if($reason_content = $this->checksubmit('reason')){
            if(K::M('staff/tixian')->update($tixian_id, array('status'=>2, 'reason'=>$reason_content, 'updatetime'=>__TIME))){
               $log_id =  K::M('staff/staff')->update_money($detail['staff_id'], $detail['money'], $reason_content.',退回到帐户余额');
                K::M('staff/log')->update($log_id,array('extend'=>serialize(array('type'=>2,'can_id'=>$tixian_id))));
                $this->msgbox->add('退回提现申请成功');
            }            
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:staff/tixian/reason.html';
        }
    } 
    public function delete($tixian_id=null)
    {
        if($tixian_id = (int)$tixian_id){
            if(!$detail = K::M('staff/tixian')->detail($tixian_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('staff/tixian')->delete($tixian_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('tixian_id')){
            if(K::M('staff/tixian')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}
