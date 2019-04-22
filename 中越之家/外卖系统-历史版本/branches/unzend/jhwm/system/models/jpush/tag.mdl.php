<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Jpush_Tag extends Mdl_Table
{   
  
    protected $_table = 'jpush_tag';
    protected $_pk = 'tag_id';
    protected $_cols = 'tag_id,tag';
    protected $_pre_cache_key = 'table_jpush_tag_list';
}