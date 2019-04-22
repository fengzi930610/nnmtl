<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_HuodongDiscount extends Mdl_Table
{
    protected $_table = 'waimai_huodong_discount';
    protected $_pk = 'huodong_id';
    protected $_cols = 'huodong_id,shop_id,title,stime,ltime,period_type,period_times,period_weeks,quota,discount_type,products,audit,closed,dateline,clientip';
    
    protected function _format_row($row)
    {
    	$row['period_weeks'] = explode(',', $row['period_weeks']);
    	$row['period_times'] = $row['period_times'] ? unserialize($row['period_times']) : array();
    	//$row['products'] = $row['products'] ? unserialize($row['products']) : array();
    	$row['products'] = explode(',', $row['products']);
    	return $row;
    }

    public function get_discount($shop_id)
    {
        $now_week = $now_week = date('w',__TIME);
        if(!$shop_id = (int)$shop_id){
            return array();
        }else if(!$discount = K::M('waimai/huodongdiscount')->find(array('shop_id'=>$shop_id,'audit'=>1,'closed'=>0,'ltime'=>'>=:'.__TIME,'stime'=>'<=:'.__TIME))){
            return array();
        }else if(!in_array($now_week,$discount['period_weeks']) || strtotime($discount['period_times']['stime']) >= __TIME || strtotime($discount['period_times']['ltime']) <= __TIME){
            return array();
        }else if(!$disc_pros = K::M('waimai/discountproduct')->items(array('huodong_id'=>$discount['huodong_id']))){
            return array();                                 
        }else{
            foreach ($disc_pros as $k => $v) {
                $disc_pros[$k] = array_merge($discount, $v);
            }
            $discount['products'] = $disc_pros;
            return $discount;
        }       
    }

    public function get_newProducts($discount, $products)
    {
        $products = is_array($products) ? $products : array();
        foreach ($products as $k => $v) {
            $v['is_discount'] = 0;
            $v['disctype'] = 0;
            $v['discval'] = 100;
            $v['oldprice'] = $v['price'];
            $v['diffprice'] = 0;
            $v['huodong_id'] = 0;
            $v['quota'] = 0;
            $v['disclabel'] = '';
            $v['quotalabel'] = '';
            if($discount && $dp=$discount['products'][$v['product_id']]){
                $v['is_discount'] = 1;
                $v['huodong_id'] = $discount['huodong_id'];
                $v['disctype'] = $discount['discount_type'];
                $v['sale_sku'] = $dp['sale_sku'];
                $v['sale_type'] = 1;
                $v['quota'] = (int)$discount['quota'];
                if($dp['discount_type']){
                    $dp['disc_price'] = $v['price'] - $dp['discount_value'];
                    $v['disclabel'] = '减价';
                }else{
                    $dp['disc_price'] = $v['price']*$dp['discount_value'];
                    $dp['discount_value'] = sprintf("%.1f",$dp['discount_value']*10);
                    $v['disclabel'] = $dp['discount_value'].'折';
                }
                $v['quotalabel'] = '不限购';
                if($v['quota'] > 0){
                    $v['quotalabel'] = '限购'.$v['quota'].'份';
                }                                              
                $v['discval'] = $dp['discount_value'];
                $v['price'] = max(0,sprintf("%.2f",$dp['disc_price']));
                $v['diffprice'] = (float)($v['oldprice']-$v['price']);     
            }
            $products[$k] = $v;
        }
        return $products;
    }

    public function get_newProduct($discount, $product)
    {
        $product = is_array($product) ? $product : array();
        if($product){
            $product['is_discount'] = 0;
            $product['disctype'] = 0;
            $product['discval'] = 100;
            $product['oldprice'] = $product['price'];
            $product['diffprice'] = 0;
            $product['huodong_id'] = 0;
            $product['quota'] = 0;
            $product['disclabel'] = '';
            $product['quotalabel'] = '';
            if($discount && $dp=$discount['products'][$product['product_id']]){
                $product['is_discount'] = 1;
                $product['huodong_id'] = $discount['huodong_id'];
                $product['disctype'] = $discount['discount_type'];
                $product['sale_sku'] = $dp['sale_sku'];
                $product['sale_type'] = 1;
                $product['quota'] = (int)$discount['quota'];
                if($dp['discount_type']){
                    $dp['disc_price'] = $product['price'] - $dp['discount_value'];
                    $product['disclabel'] = '减价';
                }else{
                    $dp['disc_price'] = $product['price']*$dp['discount_value'];
                    $dp['discount_value'] = sprintf("%.1f",$dp['discount_value']*10);
                    $product['disclabel'] = $dp['discount_value'].'折';
                }
                $product['quotalabel'] = '不限购';
                if($product['quota'] > 0){
                    $product['quotalabel'] = '限购'.$product['quota'].'份';
                }                                              
                $product['discval'] = $dp['discount_value'];
                $product['price'] = max(0,sprintf("%.2f",$dp['disc_price']));
                $product['diffprice'] = (float)($product['oldprice']-$product['price']);     
            }
        }
        
        return $product;
    }
}
