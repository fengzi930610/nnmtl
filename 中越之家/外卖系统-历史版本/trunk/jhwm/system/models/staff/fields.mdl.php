<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Fields extends Mdl_Table
{   
  
    protected $_table = 'staff_fields';
    protected $_pk = 'staff_id';
    protected $_cols = 'staff_id,id_name,id_number,id_photo,verify_photo,account_type,account_name,account_number,info';
}