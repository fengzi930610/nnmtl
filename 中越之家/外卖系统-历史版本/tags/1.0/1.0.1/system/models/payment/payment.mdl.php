<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: payment.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Payment_Payment extends Mdl_Table
{
    protected $_table = 'payment';
    protected $_pk    = 'payment_id';
    protected $_cols  = 'payment_id,payment,title,logo,config,status,dateline';
    protected $_pre_cache_key = 'payment-list';
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        if($id = $this->db->insert($this->_table, $data, true)){
            $this->flush();
        }
        return $id;
    }
    public function update($pk, $data, $checked=false)
    {
        $this->_checkpk();
        if(!$checked && !$data = $this->_check_schema($data,  $pk)){
            return false;
        }
        if($rs =$this->db->update($this->_table, $data, $this->field($this->_pk, $pk))){
            $this->flush();
        }
        return $rs;
    }
    public function payment($key)
    {
        if($items = $this->fetch_all()){
            foreach($items as $item){
                if($item['payment'] == $key){
                    return $item;
                }
            }
        }
        return false;
    }
    protected function _format_row($row)
    {
        $row['config'] = unserialize($row['config']);
        return $row;
    }
    protected function _check($data, $payment_id=null)
    {
        if(isset($data['payment']) || empty($payment_id)){
            if(empty($data['payment'])){
                $this->msgbox->add('接口标识不能为空', 211);
                return false;
            }
            if($row = $this->payment($data['payment'])){
                if(empty($payment_id) || ($payment_id != $row['payment_id'])){
                    $this->msgbox->add('接口标识已经存，请更换其它', 211);
                    return false;
                }
            }
        }
        if(isset($data['config'])){
            $data['config'] = serialize($data['config']);
        }
        return parent::_check($data);
    }
}
