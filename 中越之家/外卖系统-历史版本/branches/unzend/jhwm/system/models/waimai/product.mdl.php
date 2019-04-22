<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Product extends Mdl_Table
{
    protected $_table = 'waimai_product';
    protected $_pk = 'product_id';
    protected $_cols = 'product_id,area_id,business_id,shop_id,cate_id,cat_id,title,photo,price,package_price,sales,sale_type,sale_sku,sale_count,intro,orderby,closed,is_hot,is_spec,clientip,dateline,spec,is_onsale,good,bad,unit,specification,cate_ids,is_must,is_tuijian';
    public function count_sales($shop_id, $between)
    {
        $table1 =  $this->table('waimai_product_cate');
        $table2 =  $this->table($this->_table);
        $sql = "SELECT a.cate_id,a.title,SUM(`sales`) as sale_cnt FROM {$table1} as a LEFT JOIN {$table2} b ON(a.cate_id=b.cate_id) WHERE a.shop_id={$shop_id} AND (b.dateline {$between}) GROUP BY a.cate_id";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[] = $row;
            }
        }
        return $items;
    }
    /**
     * 商品库存处理
     * @param $product_ids,产品ID集合
     */
    public function updatesku_by_ids($product_ids)
    {
        $sql = "UPDATE $this->table($this->_table) SET `sale_sku`=`sale_sku`-1,`sales`=`sales`+1,`sale_count`=`sale_count`+1 WHERE `product_id` IN ({$product_ids})";
        return $this->db->Execute($sql);
    }
    public function update_spec($product_id)
    {
        $is_spec = $sale_sku = 0;
        $spec = array();
        if($spec_list = K::M('waimai/productspec')->items(array('product_id'=>$product_id))){
            $is_spec = 1;
            foreach($spec_list as $v){
                $sale_sku += $v['sale_sku'];
                $spec[] = array('spec_id'=>$v['spec_id'], 'spec_name'=>$v['spec_name'], 'price'=>$v['price']);
            }
            $spec = json_encode($spec);
            $a = array('is_spec'=>1, 'sale_sku'=>$sale_sku, 'spec'=>$spec);
        }else{
            $a = array('is_spec'=>0);
        }
        return $this->update($product_id, $a);
    }
    protected function _format_row($row)
    {

        if($row['specification']){
            $tmp_arr = array();
            $unser_arr = unserialize($row['specification']);
            foreach($unser_arr as $k=>$v){
                $tmp_arr[$v['key']] = $v;
            }
            sort($tmp_arr);
            $row['specification'] = $tmp_arr;

        }else{
            $row['specification'] = array();
        }
        //$row['specification'] = $row['specification']?unserialize($row['specification']):array();
        $row['cate_ids'] = $row['cate_ids'] ? explode(',',trim($row['cate_ids'],',')):array($row['cate_id']);
        //新增商品无限库存的处理
        if($row['sale_type']==0){
            $row['sale_sku'] = 9999;
        }
        $row['sale_sku'] = max(0, (int)$row['sale_sku']);

        $row['photo'] = $row['photo'] ? $row['photo'] : 'default/product.png';
        //

        return $row;
    }
    protected function _check($row, $product_id=null)
    {
       /* if(empty($product_id) || (isset($row['sale_type']) && empty($row['sale_type']))){
            $row['sale_type'] = 1;
        }*/
        return parent::_check($row, $product_id);
    }

    public function update_product($id){
        if(!$id){
            return false;
        }else if(is_array($id)){
            $vs = "'".implode("','",$id)."'";
            $sql ="UPDATE ".$this->table('waimai_product')." SET is_onsale='0' WHERE shop_id IN (".$vs.')';
        }else{
           
            $sql ="UPDATE ".$this->table('waimai_product')." SET is_onsale='0' WHERE shop_id = ".$id;
        }
       return $this->db->Execute($sql);
    }


}
