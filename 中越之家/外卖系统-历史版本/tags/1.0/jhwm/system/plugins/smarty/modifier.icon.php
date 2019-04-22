<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: modifier.icon.php 9343 2015-03-24 07:07:00Z youyi $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

function smarty_modifier_icon($data, $from='product')
{
    $icon = '';
    switch ($from) {
        case 'product':
        $icon = _smarty_format_product_icon($data);
        break;
    }
    return $icon;
}

function _smarty_format_product_icon($data)
{
    if($data['sale_type'] == 1){
        return '<span class="xianshi"></span>';
    }else if($data['sale_type'] == 1){
        return '<span class="xianliang"></span>';
    }else if($data['sale_rimai']){
        return '<span class="rimai"></span>';
    }else if($data['sale_youhui']){
        return '<span class="baokuan"></span>';
    }else if($data['sale_tuijian']){
        return '<span class="tuijian"></span>';
    }
    return '';
}