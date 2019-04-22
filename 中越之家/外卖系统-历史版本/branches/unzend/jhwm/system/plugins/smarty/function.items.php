<?php
/**
 * Copy	Right Anhuike.com
 * $Id function.widget.php shzhrui<anhuike@gmail.com>
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

function smarty_function_items($params, &$smarty)
{
	if(!$params['mdl']){
		return false;
	}
	$filter = $orderby = array();
	foreach($params as $k=>$v){
		if(substr($k, 0, 7) == 'filter_'){
			$filter[substr($k, 7)] = $v;
		}else if(substr($k, 0, 8) == 'orderby_'){
			$orderby[substr($k, 8)] = $v;
		}
	}
	$limit = $params['limit'] ? $params['limit'] : 10;
	if($params['cache']){
		$ttl = $params['ttl'] ? (int)$params['ttl'] : 3600;
		$key = 'smarty-items-'.$params['cache'];
		if(!$items = K::$system->cache->get($key, $ttl)){
			$items = K::M($params['mdl'])->items($filter, $orderby, 1, $limit);
			K::$system->cache->set($key, $items, $ttl);
		}
	}else{
		$items = K::M($params['mdl'])->items($filter, $orderby, 1, $limit);
	}
	$smarty->assign($params['name'], $items);
}