<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: city.ctl.php 3159 2014-01-22 01:54:39Z youyi $
 */
class Ctl_Data_City extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['city_id']){$filter['city_id'] = $SO['city_id'];}
            if($SO['city_name']){$filter['city_name'] = "LIKE:%".$SO['city_name']."%";}
        }
        if($items = K::M('data/city')->items($filter, array('city_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            $this->pagedata['themes'] = K::M('system/theme')->options();
            $this->pagedata['items'] = $items;            
        }
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:data/city/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:data/city/so.html';
    }
    public function detail($pk)
    {
    	$this->pagedata['detail'] = K::M('data/city')->detail($pk);
    	$this->tmpl = 'admin:data/city/detail.html';
    }
    public function create()
    {
        if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                if ($_FILES['data']) {
                    foreach ($_FILES['data'] as $k => $v) {
                        foreach ($v as $kk => $vv) {
                            $attachs[$kk][$k] = $vv;
                        }
                    }
                    $cfg = K::$system->config->get('attach');
                    $oImg = K::M('image/gd');
                    $upload = K::M('magic/upload');
                    foreach ($attachs as $k => $attach) {
                        if ($attach['error'] == UPLOAD_ERR_OK) {
                            if ($a = $upload->upload($attach, 'city')) {
                                $data[$k] = $a['photo'];
                            }
                        }
                    }
                }
                if($city_id = K::M('data/city')->create($data)){
                    $this->msgbox->add('添加城市分站成功');
                    $this->msgbox->set_data('forward', '?data/city-index.html');
                }                
            } 
        }else{
            $this->pagedata['themes'] = K::M('system/theme')->options();
            $this->tmpl = 'admin:data/city/create.html';
        }
    }
    public function edit($pk=null)
    {
        if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else if(!$city_id = $this->GP('city_id')){
                $this->msgbox->add('未指要修改ID', 202);
            }else{
                if ($_FILES['data']) {
                    foreach ($_FILES['data'] as $k => $v) {
                        foreach ($v as $kk => $vv) {
                            $attachs[$kk][$k] = $vv;
                        }
                    }
                    $cfg = K::$system->config->get('attach');
                    $oImg = K::M('image/gd');
                    $upload = K::M('magic/upload');
                    foreach ($attachs as $k => $attach) {
                        if ($attach['error'] == UPLOAD_ERR_OK) {
                            if ($a = $upload->upload($attach, 'city')) {
                                $data[$k] = $a['photo'];
                            }
                        }
                    }
                }
                if(K::M('data/city')->update($city_id, $data)){
                    $this->msgbox->add('修改城市分站配置成功');
                }               
            } 
        }else{
            $this->pagedata['themes'] = K::M('system/theme')->options();
        	$this->pagedata['detail'] = K::M('data/city')->detail($pk);
        	$this->tmpl = 'admin:data/city/edit.html';
        }
    }
    public function province($province_id)
    {
        if(!$province_id = intval($province_id)){
            $this->msgbox->add('未指定省份', 211);
        }else{
            $citys = K::M('data/city')->items_by_province($province_id);
            $this->msgbox->set_data('citys', array_values((array)$citys));
        }
        $this->msgbox->json();
    }
    
    public function delete($city_id=null)
    {
        if($city_id = (int)$city_id){
            if(K::M('data/city')->delete($city_id)){
                $this->msgbox->add('删除成功');
            }
        }else if($pks = $this->GP('city_id')){
            if(K::M('data/city')->delete($pks)){
                $this->msgbox->add('批量删除成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}