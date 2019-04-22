<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Views extends Mdl_Table
{     
    protected $_table = 'waimai_views';
    protected $_pk = 'view_id';
    protected $_cols = 'view_id,shop_id,uid,views,updatetime,dateline';

    public function update_views($shop_id, $uid)
    {
        if(!$shop_id || !$uid){
            return false;
        }else if($view = $this->find(array('shop_id'=>$shop_id, 'uid'=>$uid))){
            $this->update($view['view_id'], array('views'=>'`views`+1', 'updatetime'=>__TIME));
        }else{
            $data = array('shop_id'=>$shop_id, 'uid'=>$uid, 'views'=>1, 'updatetime'=>__TIME, 'dateline'=>__TIME);
            $this->create($data);
        }
    }

    public function group_by_shop_id($filter = array())
    {
        $where = $this->where($filter);
        $items = $arr = array();
        $sql = "SELECT sum(`views`) as views,`shop_id` FROM " . $this->table($this->_table) . " WHERE {$where} GROUP BY shop_id ";
        if ($res = $this->db->Execute($sql)) {
            while ($row = $res->fetch()) {
                $items[$row['shop_id']] = $row;
            }
        }
        return $items;
    }
}