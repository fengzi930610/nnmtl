<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: widget.php 9343 2015-03-24 07:07:00Z youyi $
 */

class Widget_Data extends Model
{

    public function index(&$params)
    {
        
    }

    public function province(&$params)
    {
        if(!$params['tpl']){
            if(!in_array($params['type'], array('label', 'checkbox', 'radio', 'option'))){
                $params['type'] = 'option';
            }
            $params['tpl'] = 'widget:default/'.$params['type'].'.html';
        }
        $data = $params;
        if($params['type'] == 'checkbox' || $params['type'] == 'label'){
            $data['value'] = array();
            if($params['value']){
                if(!is_array($params['value'])){
                    $data['value'] = explode(',', $params['value']);
                }else{
                    $data['value'] = $params['value'];
                }
            }
        }else{
            $data['value'] = $params['value'] ? $params['value'] : 0;
        }
        $data['style'] = 'width:80px;';
        $data['name'] = $params['name'] ? $params['name'] : 'province_id';
        $data['separator'] = $params['separator'] ? $params['separator'] : '';
        $data['options'] = K::M('data/province')->options();
        return $data;             
    }

    public function city(&$params)
    {
        if(!$params['tpl']){
            if(!in_array($params['type'], array('label', 'checkbox', 'radio', 'option'))){
                $params['type'] = 'option';
            }
            $params['tpl'] = 'widget:default/'.$params['type'].'.html';
        }
        $data = $params;
        if($params['type'] == 'checkbox' || $params['type'] == 'label'){
            $data['value'] = array();
            if($params['value']){
                if(!is_array($params['value'])){
                    $data['value'] = explode(',', $params['value']);
                }else{
                    $data['value'] = $params['value'];
                }
            }
        }else{
            $data['value'] = $params['value'] ? $params['value'] : 0;
        }
        $data['style'] = 'width:80px;';
        $data['name'] = $params['name'] ? $params['name'] : 'city_id';
        $data['separator'] = $params['separator'] ? $params['separator'] : '';
        $data['options'] = K::M('data/city')->options();
        return $data;       
    }

    public function area(&$params)
    {
        if(!$params['tpl']){
            if(!in_array($params['type'], array('label', 'checkbox', 'radio', 'option'))){
                $params['type'] = 'option';
            }
            $params['tpl'] = 'widget:default/'.$params['type'].'.html';
        }
        $data = $params;
        if($params['type'] == 'checkbox' || $params['type'] == 'label'){
            $data['value'] = array();
            if($params['value']){
                if(!is_array($params['value'])){
                    $data['value'] = explode(',', $params['value']);
                }else{
                    $data['value'] = $params['value'];
                }
            }
        }else{
            $data['value'] = $params['value'] ? $params['value'] : 0;
        }
        $data['style'] = 'width:80px;';
        $data['name'] = $params['name'] ? $params['name'] : 'area_id';
        $data['separator'] = $params['separator'] ? $params['separator'] : '';
        $data['options'] = K::M('data/area')->options();
        return $data;       
    }

    public function business(&$params)
    {
        if(!$params['tpl']){
            if(!in_array($params['type'], array('label', 'checkbox', 'radio', 'option'))){
                $params['type'] = 'option';
            }
            $params['tpl'] = 'widget:default/'.$params['type'].'.html';
        }
        $data = $params;
        if($params['type'] == 'checkbox' || $params['type'] == 'label'){
            $data['value'] = array();
            if($params['value']){
                if(!is_array($params['value'])){
                    $data['value'] = explode(',', $params['value']);
                }else{
                    $data['value'] = $params['value'];
                }
            }
        }else{
            $data['value'] = $params['value'] ? $params['value'] : 0;
        }
        $data['style'] = 'width:80px;';
        $data['name'] = $params['name'] ? $params['name'] : 'business_id';
        $data['separator'] = $params['separator'] ? $params['separator'] : '';
        $data['options'] = K::M('data/business')->options();
        return $data;       
    }

    public function region(&$params)
    {
        if(!$params['tpl']){
            $params['tpl'] = 'widget:data/region.html';
        }
        $area_id = $city_id = $province_id = 0;
        $area_id = (int)$params['area_id'];
        $city_id = (int)$params['city_id'];
        $province_id = (int)$params['province_id'];
        $level = (int)$params['level'] ? (int)$params['level'] : 4;
        if($value = (int)$params['value']){
            if($level < 2){
                $province_id = $value;
            }else if($level == 2){
                $city_id = $value;
            }else if($level > 2){
                $area_id = $value;
            }
        }
        if($city_id){
            if($city = K::M('data/city')->city($city_id)){
                $province_id = $city['province_id'];
            }
        }else if($area_id){
            if($area = K::M('data/area')->area($area_id)){
                if($city_id = $area['city_id']){
                    if($city = K::M('data/city')->city($city_id)){
                        $province_id = $city['province_id'];
                    }
                }
            }
        }
        $data = $params;
        $data['level'] = $level;
        $data['area_id'] = $area_id;
        $data['city_id'] = $city_id;
        $data['province_id'] = $province_id;
        $data['provinces'] = K::M('data/province')->options();
        if($data['level'] > 1){
            $data['citys'] = K::M('data/city')->options($province_id);
        }
        if($data['level'] > 2){
            $data['areas'] = K::M('data/area')->options($city_id);
        }
        if($data['level'] > 3){
            $data['business_list'] = K::M('data/business')->options($area_id);
        }
        return $data;   
    }

    public function ttl(&$params)
    {
        $params['tpl'] = 'widget:default/option.html';
        $data['options'] = K::M('data/data')->ttl();
        $data['value'] = $params['value'] ? $params['value'] : '86400';
        return $data;    
        
    }

    public function mapmarker(&$params)
    {
        $params['tpl'] = 'mapmarker.html';
        //117.332856,31.898782
        $data['lng'] = $params['lng'];
        $data['lat'] = $params['lat'];
		$data['city_name'] = K::$system->request['city']['city_name'];
        return $data;
    }


    public function bank(&$params)
    {
        if(!$params['tpl']){
            if(!in_array($params['type'], array('label', 'checkbox', 'radio', 'option'))){
                $params['type'] = 'option';
            }
            $params['tpl'] = 'widget:default/'.$params['type'].'.html';
        }
        $bank_list = K::M('data/data')->bank_list();
        $data['options'] = array_combine($bank_list, $bank_list);
        $data['value'] = $params['value'] ? $params['value'] : '';
        return $data;   
    }

}