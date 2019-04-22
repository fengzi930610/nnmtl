<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: function.area.php 9343 2015-03-24 07:07:00Z youyi $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}


/**
 *  需要加载Widget.YDM.js挂件一起使用
 */ 


function smarty_function_area($params, $smarty)
{
	$GUID = K::GUID(microtime());
	$YMD = $params['YMD'] ? $params['YMD'] : date('Y-n-j');
	list($Y, $M, $D) = explode('-', $YMD);
	$Y = isset($params['Y']) ? isset($params['Y']) : $Y;
	$M = isset($params['M']) ? isset($params['M']) : $M;
	$D = isset($params['D']) ? isset($params['D']) : $D;
    $start = $params['start'] ? $params['start_year'] : 1970;
    $end = $params['end'] ? $params['end_year'] : date('Y');
    $name = $params['name'] ? $params['name'] : 'YMD';
    $yy = $mm = $dd = '';
    $yy .= '<select name="'.$name.'[Y]" Y="'.$Y.'">';
    for($i = $start; $i<=$end; $i++){
    	$yy .= '<option value="'.$i.'">'.$i.'</option>';
    }
    $yy .= '</select>';
    $mm .= '<select name="'.$name.'[M]" M="'.$M.'">';
    for($i = 1; $i<=12; $i++){
    	$mm .= '<option value="'.$i.'">'.$i.'</option>';
    }
    $mm .= '</select>';
    $dd .= '<select name="'.$name.'[D]" D="'.$D.'">';
    for($i = 1; $i<=31; $i++){
    	$dd .= '<option value="'.$i.'">'.$i.'</option>';
    }
    $dd .= '</select>';
    $html = "<span id='{$GUID}''>{$yy}年{$mm}月{$dd}日</span>";
    $html .= "<script>(function(T, $){Widget.YMD.init('#{$GUID}');})(window.TP, window.jQuery);</script>";
    return $html;
}