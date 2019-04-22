<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/27
 * Time: 13:41
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Peicard_member extends Mdl_Table {

    protected $_table = 'peicard_member';
    protected $_pk = 'cid';
    protected $_cols = 'cid,card_id,uid,title,ltime,limits,reduce,dateline';
}