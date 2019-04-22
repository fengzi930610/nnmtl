<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Productspec extends Mdl_Table
{   
  
    protected $_table = 'waimai_product_spec';
    protected $_pk = 'spec_id';
    protected $_cols = 'spec_id,product_id,price,package_price,spec_name,spec_photo,sale_sku,sale_count,sale_type';

    protected function _format_row($row){
        //新增商品无限库存的处理
        if($row['sale_type']==0){
            $row['sale_sku'] = 9999;
        }
        $row['sale_sku'] = max(0, (int)$row['sale_sku']);
        //
        return $row;
    }

    //2019-01-26 新增 items的自动添加附加费版
    public function items_inc_addone($filter = array(), $orderby = NULL, $p = 1, $l = 50, &$count = 0)
    {
        $res = $this->items($filter,$orderby,$p,$l,$count);
        if(is_array($res))
        {
            //先更新商品ID对应的店铺ID缓存
            $pIds = [];
            foreach($res as $key => &$item)
            {
                if(isset($item['product_id']))
                    $pIds[(int)$item['product_id']] = (int)$item['product_id'];
                unset($item);
            }
            $this->___updateShopIdCacheByProductIds($pIds);
            foreach($res as $key => &$item)
            {
                if(isset($item['price']) && isset($item['product_id']) && K::M('waimai/waimai')->is_custom_mgr_shop($this->___getShopId($item['product_id'])))
                    $res[$key]['price'] = (string)((float)$item['price'] + K::M('waimai/freightcalcconfig')->get_goods_addone());   //因为原数据返回的是字段是字符串，所以这里也要转为字符串，以保证数据的统一性，实际上转为字符串不是必要的
                unset($item);
            }
        }
        return $res;
    }

    //2019-01-26 新增 重载父级接口items_by_ids，增加第三个参数，表示是否自动为单价加上附加费
    public function items_by_ids($ids, $orderby = NULL,$useAddone=false)
    {
        $res = parent::items_by_ids($ids,$orderby);
        if(is_array($res) && $useAddone)
        {
            //先更新商品ID对应的店铺ID缓存
            $pIds = [];
            foreach($res as $key => &$item)
            {
                if(isset($item['product_id']))
                    $pIds[(int)$item['product_id']] = (int)$item['product_id'];
                unset($item);
            }
            $this->___updateShopIdCacheByProductIds($pIds);

            foreach($res as $key => &$item)
            {
                if(isset($item['price']) && isset($item['product_id']) && K::M('waimai/waimai')->is_custom_mgr_shop($this->___getShopId($item['product_id'])))
                    $res[$key]['price'] = (string)((float)$item['price'] + K::M('waimai/freightcalcconfig')->get_goods_addone());   //因为原数据返回的是字段是字符串，所以这里也要转为字符串，以保证数据的统一性，实际上转为字符串不是必要的
                unset($item);
            }
        }
        return $res;
    }

    //2019-01-26 新增 重载父级接口detail，增加第三个参数，表示是否自动为单价加上附加费
    public function detail($pk, $closed = false,$useAddone=false)
    {
        $res = parent::detail($pk,$closed);
        if(is_array($res) && $useAddone && isset($res['price']) && isset($res['product_id']) && K::M('waimai/waimai')->is_custom_mgr_shop($this->___getShopId($res['product_id'])))
            $res['price'] = (string)((float)$res['price'] + K::M('waimai/freightcalcconfig')->get_goods_addone()); //因为原数据返回的是字段是字符串，所以这里也要转为字符串，以保证数据的统一性，实际上转为字符串不是必要的
        return $res;
    }
    
    //=============================================
    //2019-01-26 新增 商品对应的店铺ID缓存
    private static $___productShopIdCache = [];

    //2019-01-26 新增 更新店铺ID缓存
    protected function ___updateShopIdCacheByProductIds($ids)
    {
        if(is_string($ids))
            $ids = explode(',', trim($ids));
        else if(is_numeric($ids))
        {
            $ids = (int)$ids;
            if($ids > 0)
                $ids = [$ids];
        }
        else if(!is_array($ids))
            return false;
        $qIds = [];
        foreach($ids as $id)
        {
            if(is_string($id) || is_numeric($id))
                $id = (int)trim($id);
            else
                continue;
            if($id > 0 && !isset(self::$___productShopIdCache[$id]) && !isset($qIds[$id]))
                $qIds[$id] = $id;
        }
        if(count($qIds) > 0)
        {
            $items = K::M('waimai/product')->items(['product_id'=>"IN:".implode(',', $qIds)]);
            if($items)
            {
                foreach($items as &$item)
                {
                    self::$___productShopIdCache[(int)$item['product_id']] = (int)$item['shop_id'];
                    unset($item);
                }
            }
        }
        return true;
    }

    //2019-01-26 新增 获取商品对应的店铺ID
    protected function ___getShopId($product_id)
    {
        $ret = NULL;
        $product_id = (int)$product_id;
        if($product_id > 0)
        {
            if(isset(self::$___productShopIdCache[$product_id]))
                $ret = self::$___productShopIdCache[$product_id];
            else
            {
                $this->___updateShopIdCacheByProductIds([$product_id]);
                if(isset(self::$___productShopIdCache[$product_id]))
                    $ret = self::$___productShopIdCache[$product_id];
            }
        }
        return $ret;
    }
    //==========================================
}