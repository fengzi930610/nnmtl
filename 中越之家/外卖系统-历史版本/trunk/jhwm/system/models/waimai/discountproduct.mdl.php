<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Discountproduct extends Mdl_Table
{   
  
    protected $_table = 'waimai_discount_product';
    protected $_pk = 'product_id';
    protected $_cols = 'product_id,huodong_id,discount_value,sale_sku,sale_count';

    public function insertAll($data,$huodong_id)
    {
        $sql = "DELETE FROM ".$this->table($this->_table)." WHERE `huodong_id`={$huodong_id}";
        $this->db->Execute($sql);
        $string = '';
        foreach($data as $value){
            $value['sale_count'] = 0;
           $string .=  "(".$value['product_id'] .','.$huodong_id .",".$value['discount_value'].",".$value['sale_sku'].",".$value['sale_count']."),";
        }
        $string = rtrim($string, ',');
        $sql = "REPLACE INTO ".$this->table($this->_table)." VALUES {$string}";
        return $this->db->Execute($sql);
    }

    protected function _format_row($row)
    {
        $row['discount_value'] = $row['discount_value'] ? $row['discount_value']/100 : 0;
        //$row['sale_sku'] = max(0,$row['sale_sku']);
        $sku = $row['sale_sku'] - $row['sale_count'];
        $row['sale_sku'] = max(0, (int)$sku);
        return $row;
    }
    
}