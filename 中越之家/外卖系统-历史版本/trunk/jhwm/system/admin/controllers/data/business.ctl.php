<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Data_Business extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
                if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['area_id']){$filter['area_id'] = "LIKE:%".$SO['area_id']."%";}
if($SO['business_name']){$filter['business_name'] = "LIKE:%".$SO['business_name']."%";}
        }
        if($items = K::M('data/business')->items($filter, array('business_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        foreach($items as $k=>$v) {
            $areaids[] = $v['area_id'];
        }
        if($areas = K::M('data/area')->items_by_ids($areaids))  {
            $this->pagedata['areas'] = $areas;
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:data/business/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:data/business/so.html';
    }
    public function detail($business_id = null)
    {
        if(!$business_id = (int)$business_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('data/business')->detail($business_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:data/business/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($business_id = K::M('data/business')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?data/business-index.html');
            } 
        }else{
            $this->pagedata['citys'] = K::M('data/city')->items();
            $this->tmpl = 'admin:data/business/create.html';
        }
    }
    public function edit($business_id=null)
    {
        if(!($business_id = (int)$business_id) && !($business_id = $this->GP('business_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('data/business')->detail($business_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            
            if(K::M('data/business')->update($business_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
            $this->pagedata['areas'] = K::M('data/area')->items(array('city_id'=>$detail['city_id']));
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:data/business/edit.html';
        }
    }
    public function doaudit($business_id=null)
    {
        if($business_id = (int)$business_id){
            if(K::M('data/business')->batch($business_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('business_id')){
            if(K::M('data/business')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($business_id=null)
    {
        if($business_id = (int)$business_id){
            if(!$detail = K::M('data/business')->detail($business_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('data/business')->delete($business_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('business_id')){
            if(K::M('data/business')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
    public function options($area_id)
    {
        if(!$area_id = intval($area_id)){
            $this->msgbox->add('未指定区县', 211);
        }else if(!$options = K::M('data/business')->options($area_id)){
            $options = array();
        }
        $this->msgbox->set_data('options', $options);
        $this->msgbox->json();
    }
}