<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: widget.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Widget_Weidian extends Model {

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
        $data['value'] = $params['value'] ? $params['value'] : '';
        $data['options'] = K::M('weidian/productcate')->options_all($params['shop_id']);
        return $data;    
    }
    
    
    public function cate2(&$params)
    {
        if(!$params['tpl']){
            if(!in_array($params['type'], array('label', 'checkbox', 'radio', 'option'))){
                $params['type'] = 'option';
            }
            $params['tpl'] = 'widget:default/'.$params['type'].'.html';
        }
        $data['value'] = $params['value'] ? $params['value'] : '';
        $data['options'] = K::M('weidian/cate')->options_all();
        return $data;    
    }
    
    
}
