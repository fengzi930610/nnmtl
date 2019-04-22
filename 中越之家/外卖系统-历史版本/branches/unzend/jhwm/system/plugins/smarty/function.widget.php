<?php
/**
 * Copy	Right Anhuike.com
 * $Id function.widget.php shzhrui<anhuike@gmail.com>
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
function smarty_function_widget($params, &$smarty){
    if($smarty->widgets_mdl === null){
        $smarty->widgets_mdl = K::M('system/widget');
    }
    return $smarty->widgets_mdl->load($params, $smarty);
}