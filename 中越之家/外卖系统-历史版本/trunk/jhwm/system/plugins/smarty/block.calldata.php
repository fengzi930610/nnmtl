<?php
/**
 * Copy	Right Anhuike.com
 * $Id function.widget.php shzhrui<anhuike@gmail.com>
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

function smarty_block_calldata($params, $content, $smarty, &$repeat)
{   
	static $obj = null;
	if($obj === null){
		$obj = K::M('block/block');
	}	
	if(!$repeat && $content){
		return $obj->calldata($params, $content, $smarty);
	}	
}