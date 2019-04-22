<?php
/**
 * Copy	Right Anhuike.com
 * $Id function.widget.php shzhrui<anhuike@gmail.com>
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
function smarty_function_data($params, &$smarty)
{
	$params['block_id'] = $params['block_id'] ? $params['block_id'] : $params['block_id'];
    return K::M('block/block')->block($params, null, $smarty);
}