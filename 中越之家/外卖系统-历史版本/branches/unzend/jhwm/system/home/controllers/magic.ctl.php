<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Magic extends Ctl 
{
    
    public function index()
    {
        
    }

    public function region($from='city', $parent_id=0)
    {
        if($from == 'city'){
            if(!$province_id = intval($parent_id)){
                $this->msgbox->add('未指定省份', 211);
            }else{
                $citys = K::M('data/city')->items_by_province($province_id);
                $this->msgbox->set_data('citys', array_values((array)$citys));
            }            
        }else if($from = 'area'){
            if(!$city_id = intval($parent_id)){
                $this->msgbox->add('未指定城市ID', 211);
            }else{
                $areas = K::M('data/area')->areas_by_city($city_id);
                $this->msgbox->set_data('areas', array_values((array)$areas));
            }
        }
        $this->msgbox->json();
    }

    public function area($city_id)
    {
        if(!$city_id = intval($city_id)){
            $this->msgbox->add('未指定城市ID', 211);
        }else{
            $areas = K::M('data/area')->areas_by_city($city_id);
            $this->msgbox->set_data('areas', array_values((array)$areas));
        }
        $this->msgbox->json();     
    }

    public function editorupload()
    {
        if(!$this->uid){
            $this->msgbox->add('您没有权限上传图片', 210);
        }else if(!$attach = $_FILES['imgFile']){
            $this->msgbox->add('上传文件失败', 211);
        }else if(UPLOAD_ERR_OK != $attach['error']){
            $this->msgbox->add('上传文件失败', 212);
        }else if($data = K::M('magic/upload')->xheditor($attach)){
            $cfg = $this->system->config->get('attach');
            $this->msgbox->set_data('url', $cfg['attachurl'].'/'.$data['photo'].'?PID'.$data['photo_id']);
        }
        $this->msgbox->json();       
    }


    public function verify()
    {
        K::M('magic/verify')->output();
    }
    
    //空壳控制器
    public function shell()
    {
        //todo;
    }

}