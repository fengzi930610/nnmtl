<?php

class Mdl_Data_Region extends Mdl_Table
{   
    protected $_table = 'data_region';
    protected $_pk = 'region_id';
    protected $_cols = 'region_id,region_name,parent_id,path_ids,level,lng,lat,orderby,closed,city_code,adcode,option_level';
    protected $_orderby = array('level'=>'ASC','orderby'=>'ASC', 'region_id'=>'ASC');
    protected $_pre_cache_key = 'data-region-list';


    protected function _check($data, $order_id=null)
    {
        if(isset($data['lat'])){
            $data['lat'] = round(bcmul($data['lat'], 1000000));
        }
        if(isset($data['lng'])){
            $data['lng'] = round(bcmul($data['lng'], 1000000));
        }

        return parent::_check($data, $order_id);
    }


    protected function _format_row($row)
    {

        if($row['lat']){
            $row['lat'] = bcdiv($row['lat'], 1000000,6);
        }
        if($row['lng']){
            $row['lng'] = bcdiv($row['lng'], 1000000,6);
        }

        return $row;
    }



    public function options()
    {
        $options = $items = array();
        $options = K::M('data/region')->fetch_all();

        foreach($options as $k=>$v) {
        	if($v['level'] == 1) {
        		$items[$k] = $v;
        	}
        	if($v['level'] == 2 ) {
        		$items[$v['parent_id']]['children'][$k] = $v;
        	}
        }

        foreach($options as $k=>$v) {
        	if($v['level'] == 3) {
        		foreach($items as $kk=>$vv) {
        			foreach($vv['children'] as $kkk=>$vvv) {
        				if($vvv['region_id'] == $v['parent_id']) {
        					$items[$kk]['children'][$vvv['region_id']]['children'][$k] = $v;
        				}
        			}
        		}
        	}
        }
        return $items;
    }
    
  
    

}