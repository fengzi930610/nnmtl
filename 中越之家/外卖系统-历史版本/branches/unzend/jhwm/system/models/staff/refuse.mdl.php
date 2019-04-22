<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Refuse extends Mdl_Table
{
    protected $_table = 'staff_refuse';
    protected $_pk = 'refuse_id';
    protected $_cols = 'staff_id,refuse_id,order_id,dateline';
}