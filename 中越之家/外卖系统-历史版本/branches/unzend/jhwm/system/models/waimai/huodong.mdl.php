<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Huodong extends Mdl_Table
{   
    protected $_table = 'waimai_huodong';
    protected $_pk = 'huodong_id';
    protected $_cols = 'huodong_id,title,banner,tmpl,stime,ltime,dateline,clientip';
    
    
}