<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_HuodongItems extends Mdl_Table
{   
    protected $_table = 'waimai_huodong_items';
    protected $_pk = 'item_id';
    protected $_cols = 'item_id,huodong_id,can_id,type,title,photo,orderby,dateline,clientip';
    
    
}