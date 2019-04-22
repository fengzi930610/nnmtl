<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_OrderProduct extends Mdl_Table
{   
  
    protected $_table = 'waimai_order_product';
    protected $_pk = 'pid';
    protected $_cols = 'pid,order_id,product_id,product_name,product_price,package_price,product_number,amount,spec_id,unit,specification,huodong_id,product_prices,product_oldprice,product_oldprices,huodong_title,basket_title,huangou_id,product_photo';
    protected $_orderby = array('pid'=>'ASC');

    public function count_sales($shop_id, $between)
    {
    	$sql_order = "SELECT `order_id` FROM {$this->table('order')} WHERE `shop_id`={$shop_id} AND `order_status`=8 AND (dateline {$between})";
    	$order_items = array();
    	if($rs_order = $this->db->Execute($sql_order)){
            while($row_order = $rs_order->fetch()){
                $order_items[] = $row_order;
            }
            foreach($order_items as $k=>$v) {
            	$orderids[$v['order_id']] = $v['order_id'];
            }
            $orderids = implode(',' , $orderids);
        }

        $sql = "SELECT `product_id`,SUM(`product_number`) as product_number FROM {$this->table($this->_table)} WHERE `order_id` IN ({$orderids}) GROUP BY `product_id`";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[] = $row;
            }
        }
        return $items;
    }

    public function _format_row($row){
       if($row['specification']){
           $row['specification'] = trim($row['specification'])?unserialize($row['specification']):array();
       }else{
           $row['specification'] = array();
       }
        return $row;
    }

    public function get_basketProducts($products)
    {
        //4.0商品分篮处理
        $basket_pros = $hg_pros = array();
        foreach ($products as $k => $v) {
            $v['product_photo'] = K::M('magic/upload')->geturl($v['product_photo']);
            if($v['specification']){
                foreach ($v['specification'] as $kk => $vv) {
                    $v['product_name'] = $v['product_name']."+".$vv['val'];
                }
            }
            $v = $this->filter_fields('order_id,product_id,product_name,product_price,package_price,product_number,amount,specification,unit,product_prices,product_oldprice,product_oldprices,huodong_id,huangou_id,basket_title,product_photo', $v);
            if(!$v['huangou_id']){
                if($v['basket_title']){
                    $basket_pros[$v['basket_title']]['basket_title'] = $v['basket_title'];
                    $basket_pros[$v['basket_title']]['product'][] = $v;
                }else{
                    $basket_pros[0]['basket_title'] = '';
                    $basket_pros[0]['product'][] = $v;
                }
            }else{
                $hg_pros[] = $v; //取出换购商品，插入第一个篮子
            }                              
        }
        $basket_pros = array_values($basket_pros);
        if($hg_pros && $basket_pros){                
            $basket_pros[0]['product'] = array_merge($basket_pros[0]['product'], $hg_pros);
        }

        $colors = array('3FC680', 'F33D1A', '169FFE', 'FD8D13', '5B66F7', 'FFB128', '2AB972');
        foreach ($basket_pros as $k => $v) {
            $v['color'] = $colors[$k%7];
            $basket_pros[$k] = $v;
        }

        $products = $basket_pros;
        return $products;
    }

    protected function filter_fields($fields, $row)
    {
        if(!is_array($fields)){
            $fields = explode(',', $fields);
        }
        foreach((array)$row as $k=>$v){
            if(!in_array($k, $fields)){
                unset($row[$k]);
            }
        }
        return $row;
    }
}