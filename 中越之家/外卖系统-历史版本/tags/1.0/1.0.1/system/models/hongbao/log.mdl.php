<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Hongbao_Log extends Mdl_Table
{
    protected $_table = 'hongbao_log';
    protected $_pk = 'id';
    protected $_cols = 'hongbao_id,order_id,uid';

     public function create($data)
    {
        if(!$uid = $data['uid']){
            return false;
        }else if(!$order_id = $data['order_id']){
            return false;
        }else if(!$hongbao_id = $data['hongbao_id']){
            return false;
        }else if(!$money = $data['money']){
            return false;
        }

        $a = array('uid'=>$uid, 'order_id'=>$order_id,'hongbao_id'=>$hongbao_id,'money'=>$money);
    	if(!$o = $this->insert($a, true)){
    		return false;
    	}
        return $o;
    }

     public function insert($data)
    {
        if($uid = $this->db->insert($this->_table, $data, true)){
            $this->db->Execute("INSERT INTO ".$this->table('member_fields')."(uid) VALUES('$uid')");
        }
        return $uid;
    }
    public function find_by_hongbao($hongbao_id){
        return $this->db->Execute("SELECT * FROM ".$this->table('member_fields')." WHERE `hongbao_id`={$hongbao_id}");
    }

}
