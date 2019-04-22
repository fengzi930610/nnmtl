<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: modifier.shoprank.php 14553 2015-07-23 12:31:45Z maoge $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

function smarty_modifier_shoprank($credit, $onlyNum=false)
{
    $credit = (int)$credit;
    static $rankCfg = null;
    if($rankCfg === null){
        $rankCfg = K::$system->config->get('shop_rank');
    }
    for($i=20; $i>0; $i--){
        if($rankCfg['rank_'.$i] <= $credit){
            if($onlyNum){
                return $i;
            }else{
                return '<span class="taobao_credit" style="margin-top:8px;"><span class="rank_'.$i.'" title="'.$credit.'"></span></span>';
            }
        }
    }
    if($onlyNum){
        return 1;
    }else{
        return '<span class="taobao_credit" style="margin-top:8px;"><span class="rank_1" title="'.$credit.'"></span></span>';
    }
}