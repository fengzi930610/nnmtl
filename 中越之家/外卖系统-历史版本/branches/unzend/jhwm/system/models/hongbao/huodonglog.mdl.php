<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Hongbao_Huodonglog extends Mdl_Table
{   
  
    protected $_table = 'hongbao_huodong_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,huodong_id,uid,day,clientip,dateline';
    
}
