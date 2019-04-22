<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: function.region.php 9343 2015-03-24 07:07:00Z youyi $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

/**
 *  需要加载Widget.YDM.js挂件一起使用
 */ 


function smarty_function_region($params, $smarty)
{
	$GUID = K::GUID(microtime());
    $name = $params['name'] ? $params['name'] : 'region';
    $regionId = $params['region'] ? $params['region'] : 0;
    $oRegion = K::M('data/region');
    $pid = 0; $region = null;
    if($regionId && ($region = $oRegion->region($regionId))){
    	if($region['region_typ'] == 2){
    		$pid = $region['parent_id'];
    	}else if($region['region_type'] == 1){
    		$pid = $region['region_id'];
    	}
    }
    $provinces = $oRegion->provice_list();
    $html .= '<span id="'.$GUID.'"><select name="area[province]" val="'.$pid.'">';
    foreach($provinces as $k=>$v){
    	$html .="<option value=\"{$v['region_id']}\">{$v['region_name']}</option>";
    }
    $html .= '</select>';
    $html .= '<select name="area[city]" val="'.$regionId.'">';
    if($pid){
    	$city_list = $oRegion->city_list($pid);
    	foreach($city_list as $v){
    		$html .="<option value=\"{$v['region_id']}\">{$v['region_name']}</option>";
    	}
    }else{
    	$html .="<option value=''>请选择</option>";
    }
    $html .= '</select></span>';
    $html .= "<script>(function(T, $){Widget.Area.init('#{$GUID}');})(window.TP, window.jQuery);</script>";
    return $html;
}