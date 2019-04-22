<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: base.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Module_Params extends Mdl_Table
{
	protected $_table = 'system_module_params';
	protected $_pk = 'id';
	protected $_cols = 'id,mod_id,arg_str,query_str,orderby,visible';

	public function get_menu_tree_ids($tree)
	{
		$ids = [];
		if(is_array($tree))
		{
			foreach($tree as &$item)
			{
				if(isset($item['mod_id']))
				{
					$id = (int)$item['mod_id'];
					if($id > 0)
						$ids[$id] = $id;
				}
				if(isset($item['menu']) && is_array($item['menu']) && count($item['menu']) > 0)
				{
					$subIds = $this->get_menu_tree_ids($item['menu']);
					if(count($subIds) > 0)
					{
						foreach($subIds as $sid)
							$ids[$sid] = $sid;
					}
				}
			}
		}
		if(count($ids) > 0)
			$ids = array_merge($ids);
		return $ids;
	}

	public function items_group_modid($filter = array(), $orderby = NULL, $p = 1, $l = 50, &$count = 0)
	{
		$items = $this->items($filter,$orderby,$p,$l,$count);
		if(!$items)
			return $items;
		$group = [];
		foreach($items as $key => &$item)
		{
			$modId = (int)$item['mod_id'];
			if($modId > 0)
			{
				if(!isset($group[$modId]))
					$group[$modId] = [];
				if($this->_pk)
					$group[$modId][$item[$this->_pk]] = &$items[$key];
				else
					$group[$modId][] = &$items[$key];
			}
		}
		return $group;
	}
}