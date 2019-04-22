<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Shop_Faretpl extends Mdl_Table
{

    protected $_table = 'shop_faretpl';
    protected $_pk = 'tpl_id';
    protected $_cols = 'tpl_id,shop_id,title,lasttime,orderby,closed,dateline';

    public function create($data)
    {
        $data['lasttime'] = __TIME;
        $data['orderby'] = 50;
        $data['closed'] = 0;
        $data['dateline'] = __TIME;
        return $this->db->insert($this->_table, $data, true);
    }
}
