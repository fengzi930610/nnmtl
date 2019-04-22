<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Member_Collect extends Mdl_Table
{
    protected $_table = 'member_collect';
    protected $_pk    = 'collect_id';
    protected $_cols  = 'collect_id,uid,type,can_id,status,dateline';
    /* 通过UID获取列表 */
    public function items_by_uid($uid)
    {
        if(!$uid) return false;
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE `uid`=".$uid." AND `status`=0";
        $rs = $this->db->Execute($sql);
        while($row = $rs->fetch()){
            $items[] = $row;
        }
        return $items;
    }
    public function detail_by_filter($type, $can_id)
    {
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE `type`=".$type." AND `can_id`={$can_id}";
        $rs = $this->db->Execute($sql);
        while($row = $rs->fetch()){
            $items[] = $row;
        }
        return $items;
    }
    public function detail_by_collect($uid, $can_id, $type=1)
    {
        if(!($uid = (int)$uid) || !($can_id = (int)$can_id)){
            return false;
        }
        $type = (int)$type;
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE uid=$uid AND can_id=$can_id AND `type`=$type";
        return $this->db->GetRow($sql);
    }
    public function removeRow($type, $can_id, $uid)
    {
        $sql = "DELETE FROM ".$this->table($this->_table)." WHERE `type`={$type} AND `can_id`={$can_id} AND `uid`={$uid}";
        return $this->db->Execute($sql);
    }
}
