<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Adv extends Mdl_Table
{   
    protected $_table = 'waimai_adv';
    protected $_pk = 'adv_id';
    protected $_cols = 'adv_id,shop_id,title,link,photo,stime,ltime,orderby,closed,dateline';
    protected $_orderby = array('orderby'=>'ASC');    
}