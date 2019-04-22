<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Paotui_Cate extends Mdl_Table
{   
  
    protected $_table = 'paotui_cate';
    protected $_pk = 'cate_id';
    protected $_cols = 'cate_id,from,title,desc,config,dateline,photo,orderby';
    protected $_orderby = array('orderby'=>'ASC');
    protected $_pre_cache_key = 'paotui-cate-list';

    public function _format_row($row){
        $row['config'] =  $row['config']?unserialize($row['config']):array();
        return $row;

    }


}