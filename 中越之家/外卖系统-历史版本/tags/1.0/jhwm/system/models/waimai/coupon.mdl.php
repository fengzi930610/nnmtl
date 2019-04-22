<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Coupon extends Mdl_Table
{
    protected $_table = 'waimai_coupon';
    protected $_pk = 'coupon_id';
    protected $_cols = 'coupon_id,shop_id,huodong_id,order_id,uid,order_amount,coupon_amount,stime,ltime,orderby,dateline,use_time,title';
    protected $_orderby = array('coupon_id' => 'DESC', 'order_amount' => 'DESC');
    
    public function order_amount($shop_id, $amount)
    {
        $youhui = $this->find(array('shop_id' => $shop_id, 'order_amount' => '<=:' . $amount), array('coupon_amount' => 'DESC'));
        return $youhui;
    }
    
    public function get_coupon($uid, $shop_id, $amount)
    {

        $filter = array('uid'=>(int)$uid,'shop_id'=>(int)$shop_id,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME,'order_amount'=>'<=:'.$amount,'order_id'=>0,'coupon_amount'=>'>:0');
        if($coupon = $this->find($filter, array('coupon_amount'=>'desc'))){

            if($count = $this->count($filter)){
                $coupon['count'] = $count;
            }
            return $coupon;
        }
    }
    
    
    public function get_coupons($uid, $shop_id, $amount)
    {
        $filter = array('uid'=>(int)$uid,'shop_id'=>(int)$shop_id,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME,'order_amount'=>'<=:'.$amount,'order_id'=>0);
        if($coupons = $this->items($filter, array('coupon_amount'=>'desc'))){
            return $coupons;
        }
    }
    
    
    public function update_coupon($shop_id, $order_coupon = array())
    {
        if(!$shop_id = (int) $shop_id){
            return false;
        }
        $this->db->Execute("DELETE FROM " . $this->table($this->_table) . " WHERE shop_id=" . $shop_id);
        $sql = $coupon = array();
        foreach((array) $order_coupon as $k => $v){
            $k = (float) $k;
            $v = (float) $v;
            if($k && $v){
                $sql[] = "('{$shop_id}', '{$k}', '{$v}')";
                $coupon[] = "{$k}:{$v}";
            }
        }
        if($sql){
            $sql = "INSERT INTO " . $this->table($this->_table) . "(`shop_id`,`order_amount`,`coupon_amount`,`sku`) VALUES" . implode(',', $sql);
            $this->db->Execute($sql);
        }
        K::M('shop/shop')->update($shop_id, array('coupon' => implode(',', $coupon)));
        return true;
    }
    public function create($data, $checked = false)
    {
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __TIME;
        if($id = $this->db->insert($this->_table, $data, true)){
            return $id;
        }
    }

    public function refund_coupon($coupon_id){
        if($coupon_id&&($detail=$this->detail($coupon_id))){
            $data = array(
                'order_id'=>0,
                'use_time'=>0
            );
            $this->update($coupon_id, $data); 
        }
    }
}
