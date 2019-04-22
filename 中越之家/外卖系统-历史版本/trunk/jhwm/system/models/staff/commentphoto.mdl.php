<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Commentphoto extends Mdl_Table
{   
  
    protected $_table = 'staff_comment_photo';
    protected $_pk = 'photo_id';
    protected $_cols = 'photo_id,comment_id,photo,dateline';
    public function photos_by_comment($cids)
    {
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE `comment_id` IN ({$cids}) ";
        $rs = $this->db->Execute($sql);
        while($row = $rs->fetch()){
            $items[] = $this->_format_row($row);
        }
        return $items;
    }
}