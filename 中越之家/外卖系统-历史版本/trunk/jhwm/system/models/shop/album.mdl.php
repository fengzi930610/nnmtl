<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Shop_Album extends Mdl_Table
{   
  
    protected $_table = 'shop_album';
    protected $_pk = 'album_id';
    protected $_cols = 'album_id,shop_id,name,photo,orderby,dateline';
}