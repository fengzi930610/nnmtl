<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Mall_Product extends Mdl_Table
{
    protected $_table = 'mall_product';
    protected $_pk = 'product_id';
    protected $_cols = 'product_id,cate_id,title,photo,jifen,price,freight,info,views,sales,sku,orderby,closed,clientip,dateline';

    public function select_by_ids($ids)
    {
        return $this->items_by_ids($ids);
    }

    protected function _format_row($row)
    {
        static $cate_list = null;
        if($cate_list === null){
            $cate_list = K::M('mall/cate')->fetch_all();
        }
        $row['cate_title'] = $cate_list[$row['cate_id']]['title'];
        return $row;
    }
}
