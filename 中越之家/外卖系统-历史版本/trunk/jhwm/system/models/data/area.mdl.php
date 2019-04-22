<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Data_Area extends Mdl_Table
{   
  
    protected $_table = 'data_area';
    protected $_pk = 'area_id';
    protected $_cols = 'area_id,city_id,area_name,area_name_vn,orderby,dateline';
    protected $_pre_cache_key = 'data-area-list';
    public function options($city_id=0)
    {
        $options = array();
        if($citys = $this->fetch_all()){
            foreach($citys as $k=>$v){
                if($city_id == $v['city_id']){
                    $options[$k] = $v['area_name'];
                }
            }
        }
        return $options;
   
        
    }
    
    
    public function areas_by_city($city_id)
    {
        $areas = array();
        if($items = $this->fetch_all()){
            foreach($items as $k=>$v){
                if($v['city_id'] == $city_id){
                    $areas[$k] = $v;
                }
            }
        }
        return $areas;        
    }
    
    
}