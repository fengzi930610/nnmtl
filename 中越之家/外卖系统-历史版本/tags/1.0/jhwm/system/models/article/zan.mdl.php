<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/4
 * Time: 17:25
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Article_Zan extends Mdl_Table{
    protected $_table = 'article_zan';
    protected $_pk = 'zan_id';
    protected $_cols = 'zan_id,article_id,uid,dateline';
    protected $_orderby = array('zan_id'=>'DESC');

}