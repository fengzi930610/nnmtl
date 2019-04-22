<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Shop_Account extends Mdl_Table
{   
  
    protected $_table = 'shop_account';
    protected $_pk = 'shop_id';
    protected $_cols = 'shop_id,account_type,account_name,account_number,account_branch';
    
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        return $this->db->insert($this->_table, $data, false);
    }
    
}