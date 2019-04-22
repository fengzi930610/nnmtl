<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Qiang_CommentPhoto extends Mdl_Table
{
    protected $_table = 'qiang_comment_photo';
    protected $_pk = 'photo_id';
    protected $_cols = 'photo_id,comment_id,photo,dateline';

    public function items_by_ids($ids)
    {
    	if(!$ids = K::M('verify/check')->ids($ids)){
            return false;
        }
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE `comment_id` IN($ids)";
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $row = $this->_format_row($row);
                if($row[$this->_pk]){
                    $items[$row[$this->_pk]] = $row;
                }else{
                    $items[] = $row;
                }
            }
        }
        self::$_CACHE_TABLES[$this->_pre_cache_key] = $items;
        $this->cache->set($this->_pre_cache_key, $items, $this->_cache_ttl);
        return $items;
    }
}
