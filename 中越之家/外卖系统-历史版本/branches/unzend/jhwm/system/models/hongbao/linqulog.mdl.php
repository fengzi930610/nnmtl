<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/13
 * Time: 17:04
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Hongbao_Linqulog extends Mdl_Table {

    protected $_table = 'hongbao_linqu_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,hongbao_id,min_amount,amount,uid,day,clientip,dateline,type,huodong_id';
    protected $_orderby = array('log_id' => 'DESC');


    
}