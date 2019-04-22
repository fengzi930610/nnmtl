<?php
/**
 * Copy	Right Anhuike.com
 * $Id function.widget.php shzhrui<anhuike@gmail.com>
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

function smarty_block_KT($params, $content,	$smarty, &$repeat)
{
	static $block = null;
	if($block === null){
		$block = K::M('block/block');
	}	
	if(!$repeat && $content){
		return $block->block($params, $content, $smarty);
	}
}