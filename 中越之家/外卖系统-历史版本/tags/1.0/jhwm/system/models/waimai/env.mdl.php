<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/3
 * Time: 11:24
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Class Mdl_Waimai_Env extends Mdl_Table {
    protected $_table = 'waimai_env';
    protected $_pk = 'photo_id';
    protected $_cols = 'photo_id,shop_id,photo,dateline';
    protected $_orderby = array('photo_id' => 'DESC', 'dateline' => 'DESC');
    
}