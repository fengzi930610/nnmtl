<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: widget.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Widget_Product extends Model {

    public function index(&$params)
    {
        
    }

    public function cate(&$params)
    {
        if(!$params['tpl']){
            if(!in_array($params['type'], array('label', 'checkbox', 'radio', 'option'))){
                $params['type'] = 'option';
            }
            $params['tpl'] = 'widget:default/'.$params['type'].'.html';
        }
        $shop_id = (int)$params['shop_id'];
        $data['value'] = $params['value'] ? $params['value'] : '';
        $data['options'] = K::M('waimai/productcate')->options($shop_id);
        return $data;    
    }
}
