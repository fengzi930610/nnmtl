<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Shop_Commentphoto extends Mdl_Table
{   
  
    protected $_table = 'shop_comment_photo';
    protected $_pk = 'photo_id';
    protected $_cols = 'photo_id,comment_id,photo,dateline';
}