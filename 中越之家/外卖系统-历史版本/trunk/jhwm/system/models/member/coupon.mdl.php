<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Member_Coupon extends Mdl_Table
{
    protected $_table = 'member_coupon';
    protected $_pk = 'cid';
    protected $_cols = 'cid,coupon_id,uid,use_time,order_id,status,dateline,ltime,order_amount,coupon_amount,shop_id,mendian_id,title';
    protected $_orderby = array('cid' => 'DESC');
    public function create($data, $checked = false)
    {
        /* if (!$checked && !($data = $this->_check($data))) {
          return false;
        } */
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __CFG::TIME;
        if($cid = $this->db->insert($this->_table, $data, true)){
            return $cid;
        }
    }
   /* public function update($filter, $data)
    {
        $arr_coupon = K::M('member/coupon')->find($filter);
        $pk = $arr_coupon['cid'];
        if($ret = $this->db->update($this->_table, $data, $this->field($this->_pk, $pk))){
            $this->flush();
        }
        return $ret;
    }
*/
}
