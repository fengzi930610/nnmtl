<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: function.YMD.php 9343 2015-03-24 07:07:00Z youyi $
 */

/**
 *  需要加载Widget.YDM.js挂件一起使用
 */ 


function smarty_function_YMD($params, $smarty)
{
	$GUID = K::GUID(microtime());
	$class = $params['class'] ? ' '.$params['class'] : '';
	$YMD = $params['YMD'] ? $params['YMD'] : date('Y-n-j');
	list($Y, $M, $D) = explode('-', $YMD);
	$Y = isset($params['Y']) ? $params['Y'] : $Y;
	$M = isset($params['M']) ? $params['M'] : $M;
	$D = isset($params['D']) ? $params['D'] : $D;
    $start = $params['start'] ? $params['start_year'] : 1970;
    $end = $params['end'] ? $params['end_year'] : date('Y');
    $name = $params['name'] ? $params['name'] : 'YMD';
    $yy = $mm = $dd = '';
    $yy .= '<select name="'.$name.'[Y]" Y="'.$Y.'" class="w-100'.$class.'">';
    for($i = $end; $i>=$start; $i--){
        $selected = ($Y == $i) ? ' selected="selected"' : '';
    	$yy .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
    }
    $yy .= '</select>';
    $mm .= '<select name="'.$name.'[M]" M="'.$M.'" class="w-50'.$class.'">';
    for($i = 1; $i<=12; $i++){
        $selected = ($M == $i) ? ' selected="selected"' : '';
    	$mm .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
    }
    $mm .= '</select>';
    $dd .= '<select name="'.$name.'[D]" D="'.$D.'" class="w-50'.$class.'">';
    for($i = 1; $i<=31; $i++){
        $selected = ($D == $i) ? ' selected="selected"' : '';
    	$dd .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
    }
    $dd .= '</select>';
    $html = "<span id='{$GUID}'>{$yy}&nbsp;年&nbsp;&nbsp;{$mm}&nbsp;月&nbsp;&nbsp;{$dd}&nbsp;日</span>";
    $html .= "<script>(function(K, $){Widget.YMD.init('#{$GUID}');})(window.KT, window.jQuery);</script>";
    return $html;
}