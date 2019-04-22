<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: links.mdl.php 2034 2013-12-07 03:08:33Z $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Links_Links extends Mdl_Table
{   
  
    protected $_table = 'links';
    protected $_pk = 'link_id';
    protected $_cols = 'link_id,title,link,logo,desc,city_id,audit,closed,dateline';
    protected $_orderby = array('link_id'=>'ASC');
    protected $_pre_cache_key = 'links-list';
    
}