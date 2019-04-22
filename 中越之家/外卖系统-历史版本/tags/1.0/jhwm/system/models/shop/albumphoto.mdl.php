<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Shop_Albumphoto extends Mdl_Table
{   
  
    protected $_table = 'shop_album_photo';
    protected $_pk = 'photo_id';
    protected $_cols = 'photo_id,shop_id,title,album_id,photo,dateline,type';
}