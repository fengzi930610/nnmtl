<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: widget.php 9343 2015-03-24 07:07:00Z youyi $
 */

class Widget_Block extends Model
{

    public function index(&$params)
    {
		if(!$params['name']){
			return false;
		}else if(!$block = K::M('block/block')->item_by_name($params['name'])){
			return false;
		}
		$items = K::M('block/item')->items_by_block($block['block_id'], $block['ttl']);
		$nums = (int)$params['limit'];
		if($nums > 0){
			$item_list = array_slice($item_list, 0, $nums);
		}
		if($params['force']){
			$items = array();
			foreach($items as $k=>$v){
				$ids[$v['itemId']] = $v['itemId'];
			}
			if($mdl = K::M('block/block')->load_mdl($block['from'])){
				$items = $mdl->items_by_ids($ids);
			}
		}
		if($params['var']){
			$this->smarty->tpl_vars[$params['var']] = $items;
		}else{
			$this->smarty->tpl_vars['block_items'] = $items;
		}
    }

    public function page(&$params)
    {
		if(!$params['tpl']){
			if(!in_array($params['type'], array('label', 'checkbox', 'radio', 'option'))){
				$params['type'] = 'option';
			}
			$params['tpl'] = 'widget:default/'.$params['type'].'.html';
		}
        $data['value'] = $params['value'] ? $params['value'] : 0;
        $options = array();
        if($items = K::M('block/page')->fetch_all()){
        	foreach($items as $k=>$v){
        		$options[$k] = $v['title'];
        	}
        }
        $data['options'] = $options;
        return $data;           
    }

    public function from(&$params)
    {
		if(!$params['tpl']){
			if(!in_array($params['type'], array('label', 'checkbox', 'radio', 'option'))){
				$params['type'] = 'option';
			}
			$params['tpl'] = 'widget:default/'.$params['type'].'.html';
		}        $data['value'] = $params['value'] ? $params['value'] : 0;
        $data['options'] = K::M('block/block')->from_list();
        return $data;
    }

	public function option(&$params)
	{
		if(!$params['tpl']){
			if(!in_array($params['type'], array('label', 'checkbox', 'radio', 'option'))){
				$params['type'] = 'option';
			}
			$params['tpl'] = 'widget:default/'.$params['type'].'.html';
		}        $data['value'] = $params['value'] ? $params['value'] : 0;
		$data['from'] = $params['from'] ? $params['from'] : null;
		$options = array();
		if($blocks = K::M('block/block')->items_by_from($data['from'])){
			$page_list = K::M('block/page')->fetch_all();
			foreach($page_list as $k=>$v){
				foreach($blocks as $kk=>$vv){
					if($vv['page_id'] == $v['page_id']){
						$options[$v['title']][$vv['block_id']] = $vv['title'];
					}
				}
			}
		}
        $data['options'] = $options;
        return $data;		
	}
}