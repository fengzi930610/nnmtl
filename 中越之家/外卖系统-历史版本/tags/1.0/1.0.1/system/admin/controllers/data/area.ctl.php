<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Data_area extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
                if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['city_id']){$filter['city_id'] = "LIKE:%".$SO['city_id']."%";}
if($SO['area_name']){$filter['area_name'] = "LIKE:%".$SO['area_name']."%";}
        }
        if($items = K::M('data/area')->items($filter, array('area_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        foreach($items as $k=>$v) {
            $cityids[] = $v['city_id'];
        }
        if($citys = K::M('data/city')->items_by_ids($cityids))  {
            $this->pagedata['citys'] = $citys;
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:data/area/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:data/area/so.html';
    }
    public function detail($area_id = null)
    {
        if(!$area_id = (int)$area_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('data/area')->detail($area_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:data/area/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($area_id = K::M('data/area')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?data/area-index.html');
            } 
        }else{
           $this->tmpl = 'admin:data/area/create.html';
        }
    }
    public function edit($area_id=null)
    {
        if(!($area_id = (int)$area_id) && !($area_id = $this->GP('area_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('data/area')->detail($area_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            
            if(K::M('data/area')->update($area_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:data/area/edit.html';
        }
    }
    public function doaudit($area_id=null)
    {
        if($area_id = (int)$area_id){
            if(K::M('data/area')->batch($area_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('area_id')){
            if(K::M('data/area')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($area_id=null)
    {
        if($area_id = (int)$area_id){
            if(!$detail = K::M('data/area')->detail($area_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('data/area')->delete($area_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('area_id')){
            if(K::M('data/area')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  

    public function options($city_id)
    {
        if(!$city_id = intval($city_id)){
            $this->msgbox->add('未指定城市', 211);
        }if(!$options = K::M('data/area')->options($city_id)){
            $options = array();
        }
        $this->msgbox->set_data('options', $options);
        $this->msgbox->json();
    }
}