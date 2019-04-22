<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Hongbao_Orderlog extends Mdl_Table
{   
  
    protected $_table = 'hongbao_order_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,order_id,uid,status,min_amount,amount,day,face,nickname,wx_openid,wx_unionid,clientip,dateline';
    
}
