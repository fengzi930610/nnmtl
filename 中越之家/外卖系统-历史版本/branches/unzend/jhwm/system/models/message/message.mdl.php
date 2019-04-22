<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Message_Message extends Mdl_Table
{   
  
    protected $_table = 'member_message';
    protected $_pk = 'message_id';
    protected $_cols = 'message_id,uid,title,content,type,is_read,dateline,clientip';
}