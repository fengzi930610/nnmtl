<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Order_Complaint extends Mdl_Table
{   
  
    protected $_table = 'order_complaint';
    protected $_pk = 'complaint_id';
    protected $_cols = 'complaint_id,order_id,uid,shop_id,staff_id,title,content,reply,reply_time,clientip,dateline';
}