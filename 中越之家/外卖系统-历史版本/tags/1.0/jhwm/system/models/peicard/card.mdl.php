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
class Mdl_Peicard_Card extends Mdl_Table {

    protected $_table = 'peicard';
    protected $_pk = 'card_id';
    protected $_cols = 'card_id,title,days,amount,limits,reduce,template,photo,orderby,closed,dateline';
    protected $_orderby = array('orderby'=>'asc');
}