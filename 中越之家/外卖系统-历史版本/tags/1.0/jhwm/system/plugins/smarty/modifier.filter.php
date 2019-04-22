<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: modifier.filter.php 9343 2015-03-24 07:07:00Z youyi $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

/**
 * 敏感词替换
 */
function smarty_modifier_filter($content)
{
    static $censor = null;
    if($censor === null){
        $censor = K::M('content/censor');
    }
    return $censor->filter($content);
}