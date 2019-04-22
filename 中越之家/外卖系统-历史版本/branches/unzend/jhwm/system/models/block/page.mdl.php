<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: page.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Block_Page extends Mdl_Table
{   
  
    protected $_table = 'block_page';
    protected $_pk = 'page_id';
    protected $_cols = 'page_id,title,orderby';
    protected $_orderby = array('orderby'=>'ASC', 'page_id'=>'ASC');
    protected $_pre_cache_key = 'block_page_list';
    
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        if($page_id = $this->db->insert($this->_table, $data, true)){
            $this->flush();
        }
        return $page_id;
    }
    public function update($pk, $data, $checked=false)
    {
        $this->_checkpk();
        if(!$checked && !$data = $this->_check_schema($data,  $pk)){
            return false;
        }
        if($ret = $this->db->update($this->_table, $data, $this->field($this->_pk, $pk))){
            $this->flush();
        }
        return false;
    }
}