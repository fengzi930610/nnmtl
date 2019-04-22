<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Member_Feedback extends Mdl_Table
{   
  
    protected $_table = 'member_feedback';
    protected $_pk = 'fid';
    protected $_cols = 'fid,uid,content,clientip,dateline';
}