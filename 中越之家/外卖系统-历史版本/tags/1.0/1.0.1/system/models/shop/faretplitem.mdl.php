<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Shop_Faretplitem extends Mdl_Table
{

    protected $_table = 'shop_faretpl_item';
    protected $_pk = 'item_id';
    protected $_cols = 'item_id,tpl_id,shop_id,region_names,region_ids,first,first_price,renew,renew_price,orderby,closed,dateline';

    public function create($data)
    {
        $data['orderby'] = 50;
        $data['closed'] = 0;
        $data['dateline'] = __TIME;
        return $this->db->insert($this->_table, $data, true);
    }
}
