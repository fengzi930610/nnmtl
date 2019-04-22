<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: widget.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Widget_Waimai extends Model {

    public function index(&$params)
    {
        
    }

    public function cate(&$params)
    {
        $params['tpl'] = 'cate_options.html';
        $data['value'] = $params['value'] ? $params['value'] : 0;
        $data['tree'] = K::M('waimai/cate')->tree($from);
        return $data;           
    }

}
