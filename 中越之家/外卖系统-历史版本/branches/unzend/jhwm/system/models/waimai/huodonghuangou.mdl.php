<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_HuodongHuangou extends Mdl_Table
{
    protected $_table = 'waimai_huodong_huangou';
    protected $_pk = 'huodong_id';
    protected $_cols = 'huodong_id,shop_id,title,stime,ltime,order_amount,period_times,period_weeks,quota,products,audit,closed,dateline,clientip';
    
    protected function _format_row($row)
    {
    	$row['period_weeks'] = explode(',', $row['period_weeks']);
    	$row['period_times'] = $row['period_times'] ? unserialize($row['period_times']) : array();
    	$row['products'] = explode(',', $row['products']);
    	return $row;
    }

    public function get_huangou($shop_id, $order_amount=0)
    {
        $now_week = $now_week = date('w',__TIME);
        if(!$shop_id = (int)$shop_id){
            return array();
        }else if($order_amount && !$huangou = K::M('waimai/huodonghuangou')->find(array('shop_id'=>$shop_id,'ltime'=>'>=:'.__TIME,'stime'=>'<=:'.__TIME,'audit'=>1,'closed'=>0,'order_amount'=>'<=:'.$order_amount))){
            return array();
        }else if(!$order_amount && !$huangou = K::M('waimai/huodonghuangou')->find(array('shop_id'=>$shop_id,'ltime'=>'>=:'.__TIME,'stime'=>'<=:'.__TIME,'audit'=>1,'closed'=>0))){
            return array();
        }else if(!in_array($now_week,$huangou['period_weeks']) || strtotime($huangou['period_times']['stime']) >= __TIME || strtotime($huangou['period_times']['ltime']) <= __TIME){
            return array();
        }else if(!$huangou_pros = K::M('waimai/huangouproduct')->items(array('huodong_id'=>$huangou['huodong_id'], 'sale_sku'=>'>:0'))){
            return array();                                 
        }else{
            foreach ($huangou_pros as $k => $v) {
                $huangou_pros[$k] = array_merge($huangou, $v);
            }
            $huangou['products'] = $huangou_pros;
            return $huangou;
        }       
    }

    public function get_newProducts($huangou, $products)
    {
        $products = is_array($products) ? $products : array();
        foreach ($products as $k => $v) {
            $v['is_huangou'] = 0;
            $v['discval'] = 100;
            $v['oldprice'] = $v['price'];
            $v['diffprice'] = 0;
            $v['huodong_id'] = 0;
            $v['quota'] = 0;
            $v['quotalabel'] = '';
            if($huangou && $dp=$huangou['products'][$v['product_id']]){
                $v['is_huangou'] = 1;
                $v['huodong_id'] = $huangou['huodong_id'];
                $v['sale_sku'] = $dp['sale_sku'];
                $v['sale_type'] = 1;
                $v['quota'] = (int)$huangou['quota'];                                    
                $v['quotalabel'] = '不限购';
                if($v['quota'] > 0){
                    $v['quotalabel'] = '限购'.$v['quota'].'份';
                }
                $dp['disc_price'] = $v['price'] - $dp['discount_value'];                                              
                $v['price'] = max(0,sprintf("%.2f",$dp['disc_price']));
                $v['diffprice'] = (float)($v['oldprice']-$v['price']);     
            }
            $products[$k] = $v;
        }
        return $products;
    }

    public function get_newProduct($huangou, $product)
    {
        $product['is_huangou'] = 0;
        $product['oldprice'] = $product['price'];
        $product['diffprice'] = 0;
        $product['huodong_id'] = 0;
        $product['quota'] = 0;
        $product['quotalabel'] = '';
        if($huangou && $dp=$huangou['products'][$product['product_id']]){
            $product['is_huangou'] = 1;
            $product['huodong_id'] = $huangou['huodong_id'];
            $product['sale_sku'] = $dp['sale_sku'];
            $product['sale_type'] = 1;
            $product['quota'] = (int)$huangou['quota'];                                    
            $product['quotalabel'] = '不限购';
            if($product['quota'] > 0){
                $product['quotalabel'] = '限购'.$product['quota'].'份';
            }
            $dp['disc_price'] = $product['price'] - $dp['discount_value'];                                              
            $product['price'] = max(0,sprintf("%.2f",$dp['disc_price']));
            $product['diffprice'] = (float)($product['oldprice']-$product['price']);     
        }
        return $product;
    }
}
