<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: province.ctl.php 3159 2014-01-22 01:54:39Z youyi $
 */
class Ctl_Data_Province extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['province_id']){$filter['province_id'] = $SO['province_id'];}
            if($SO['province_name']){$filter['province_name'] = "LIKE:%".$SO['province_name']."%";}
        }
        if($items = K::M('data/province')->items($filter, array('province_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            $this->pagedata['themes'] = K::M('system/theme')->options();
            $this->pagedata['items'] = $items;            
        }
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:data/province/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:data/province/so.html';
    }
    public function detail($pk)
    {
    	$this->pagedata['detail'] = K::M('data/province')->detail($pk);
    	$this->tmpl = 'admin:data/province/detail.html';
    }
    public function create()
    {
        if($data = $this->GP('data')){
            if($province_id = K::M('data/province')->create($data)){
                $this->msgbox->add('修改内容成功');
                $this->msgbox->set_data('forward', '?data/province-index.html');
            }
        }else{
            $this->tmpl = 'admin:data/province/create.html';
        }
    }
    public function edit($pk=null)
    {
        if($data = $this->GP('data')){
            if(!$province_id = $this->GP('province_id')){
                $this->msgbox->add('未指要修改ID', 202);
            }else if(K::M('data/province')->update($province_id, $data)){
                $this->msgbox->add('修改内容成功');
            }
        }else{
            $this->pagedata['themes'] = K::M('system/theme')->options();
        	$this->pagedata['detail'] = K::M('data/province')->detail($pk);
        	$this->tmpl = 'admin:data/province/edit.html';
        }
    }
    public function delete($province_id=null)
    {
        if($province_id = (int)$province_id){
            if(K::M('data/province')->delete($province_id)){
                $this->msgbox->add('删除成功');
            }
        }else if($pks = $this->GP('province_id')){
            if(K::M('data/province')->delete($pks)){
                $this->msgbox->add('批量删除成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}