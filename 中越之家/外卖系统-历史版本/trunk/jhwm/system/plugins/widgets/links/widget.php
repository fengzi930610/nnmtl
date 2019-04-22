<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: widget.php 9343 2015-03-24 07:07:00Z youyi $
 */

class Widget_Links extends Model
{

	public function index(&$params)
	{
		$params['tpl'] = 'default.html';
		$links = array();
		if($items = K::M('market/links')->fetch_all()){
			foreach($items as $k=>$v){
				if(empty($v['audit'])){
					continue;
				}
				if($params['city_id']){
					if(!is_array($v['city_ids'])){
						$v['city_ids'] = explode(',', $v['city_ids']);
					}
					if(!in_array($params['city_id'], (array)$v['city_ids'])){
						continue;
					}
				}
				$links[$k] = $v;
			}
		}
		return $links;
	}
}