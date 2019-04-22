<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Staff_Verify extends Ctl
{
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['staff_id']){$filter['staff_id'] = $SO['staff_id'];}
            if($SO['id_name']){$filter['id_name'] = $SO['id_name'];}
            if($SO['mobile']){$staff_filter['mobile'] = $SO['mobile'];}
            if($SO['id_number']){$filter['id_number'] = $SO['id_number'];}
            if($SO['verify']){$filter['verify'] = $SO['verify'];}
            if(is_array($SO['verify_time'])){if($SO['verify_time'][0] && $SO['verify_time'][1]){$a = strtotime($SO['verify_time'][0]); $b = strtotime($SO['verify_time'][1])+86400;$filter['verify_time'] = $a."~".$b;}}
            if(is_array($SO['updatetime'])){if($SO['updatetime'][0] && $SO['updatetime'][1]){$a = strtotime($SO['updatetime'][0]); $b = strtotime($SO['updatetime'][1])+86400;$filter['updatetime'] = $a."~".$b;}}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            
            //4.0模糊查询
            if ($SO['keywords']) {
                $filter[':SQL'] = " (o.id_name LIKE '%".$SO['keywords']."%' OR o.id_number LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        /*if($staff_filter['mobile']){
            $staff = K::M('staff/staff')->find($staff_filter);
            $filter['staff_id'] = $staff['staff_id'];
        }*/

        if($items = K::M('staff/verify')->items_join_staff($filter, array('dateline'=>"desc"), $page, $limit, $count)){
            /*$staff_ids = array();
            foreach($items as $v){
              $staff_ids[] = $v['staff_id'];
            }
            $staff_ids = implode($staff_ids,',');
            $staff_info = K::M('staff/staff')->items_by_ids($staff_ids);
            foreach($items as $k=>$v){
               foreach($staff_info as $kk=>$info){
                 if($info['staff_id'] == $v['staff_id']){
                    $items[$k]['from'] = $info['from'];
                    $items[$k]['name'] = $info['name'];
                    $items[$k]['mobile'] = $info['mobile'];
                 }
               }
            }*/
           	$pager['count'] = $count;
          	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['verify'] = $this->_verify;
        $this->tmpl = 'admin:staff/verify/items.html';
    }

    public function so()
    {
        $this->tmpl = 'admin:staff/verify/so.html';
    }

    public function detail($staff_id = null)
    {
        if(!$staff_id = (int)$staff_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('staff/verify')->detail($staff_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $staff = K::M('staff/staff')->detail($staff_id);
            $this->pagedata['staff']  = $staff;
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:staff/verify/detail.html';
        }
    }

    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($staff_id = K::M('staff/verify')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?staff/verify-index.html');
            }
        }else{
           $this->tmpl = 'admin:staff/verify/create.html';
        }
    }

    public function edit($staff_id=null)
    {
        if(!($staff_id = (int)$staff_id) && !($staff_id = $this->GP('staff_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('staff/verify')->detail($staff_id)){
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
            $data['verify_time'] = __TIME;
            if(K::M('staff/verify')->update($staff_id, $data)){
                K::M('staff/staff')->update($staff_id, array('verify_name'=>$data['verify'], 'audit'=>$data['verify']));
                $this->msgbox->add('修改内容成功');
            }
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:staff/verify/edit.html';
        }
    }

    public function audit($staff_id=null, $audit=null)
    {
        if($staff_id = (int)$staff_id){
            if(K::M('staff/verify')->batch($staff_id, array('verify'=>$audit))){
               K::M('staff/staff')->batch($staff_id,array('verify_name'=>$audit,'audit'=>$audit));
                $this->msgbox->add('审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }

    public function delete($staff_id=null)
    {
        if($staff_id = (int)$staff_id){
            if(!$detail = K::M('staff/verify')->detail($staff_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('staff/verify')->delete($staff_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('staff_id')){
            if(K::M('staff/verify')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}
