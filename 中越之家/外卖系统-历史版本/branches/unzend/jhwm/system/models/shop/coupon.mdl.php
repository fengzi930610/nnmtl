<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Shop_Coupon extends Mdl_Table
{
    protected $_table = 'shop_coupon';
    protected $_pk = 'coupon_id';
    protected $_cols = 'coupon_id,shop_id,order_amount,coupon_amount,stime,ltime,use_count,sku,orderby,dateline,closed,picked,type,mendian_id,title';
    protected $_orderby = array('coupon_id' => 'DESC', 'order_amount' => 'DESC');
    public function order_amount($shop_id, $amount)
    {
        $youhui = $this->find(array('shop_id' => $shop_id, 'order_amount' => '<=:' . $amount), array('coupon_amount' => 'DESC'));
        return $youhui;
    }
    public function get_coupon($uid, $shop_id, $amount)
    {
        $arr_user_filter = array();
        $arr_user_filter['status'] = 0;
        $arr_user_filter['uid'] = $uid;
        $arr_user_filter['ltime'] = '>=:' . __TIME;
        $arr_user_filter['order_amount'] = '<=:' . $amount;
        $arr_user_filter['shop_id'] = (int) $shop_id;
        $orderby = array();
        $orderby['coupon_amount'] = 'DESC';
        if($m_coupon = K::M('member/coupon')->find($arr_user_filter, $orderby)){
            return $m_coupon;
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
}
