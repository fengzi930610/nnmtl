<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Data_Unit
{   
  
    public function unit_list()
    {
        if($unit = K::M('system/config')->get('unit')){
            return $unit['unit'];
        }else{
            return array();
        }

    }

    public function taste_list(){
        return array(
            "微辣","中辣","超辣","麻辣","酸辣","酸甜","酸咸","咸鲜","咸甜","甜味",
            "苦味","原味","清淡","五香","鱼香","葱香","蒜香","奶香","酱香","咖喱",
            "孜然","果味","怪味","椒麻味","咖喱味","家常味","香辣","茄汁味","海鲜",
            "冰","不冰",""
        );
    }
}
