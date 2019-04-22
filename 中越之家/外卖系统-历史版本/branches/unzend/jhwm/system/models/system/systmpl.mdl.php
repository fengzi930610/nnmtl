<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: systmpl.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_System_Systmpl extends Mdl_Table
{   
  
    protected $_table = 'systmpl';
    protected $_pk = 'systmpl_id';
    protected $_cols = 'systmpl_id,is_open,title,intro,from,key,tmpl,tmpl1,tmpl2,dateline';
    protected $_orderby = array('systmpl_id'=>'ASC');
    protected $_pre_cache_key = 'system_systmpl_list';    
    
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        return $this->db->insert($this->_table, $data, true);
    }
    public function update($pk, $data, $checked=false)
    {
        $this->_checkpk();
        if(!$checked && !$data = $this->_check_schema($data,  $pk)){
            return false;
        }
        return $this->db->update($this->_table, $data, $this->field($this->_pk, $pk));
    }
    
    public function load_systmpl()
    {
        $data = $this->fetch_all();
        $return = array();
        foreach($data as $v){
            $return[$v['key']] = $v;
        }
        return $return;
    }  
    
    public function detail_by_key($key)
    {
		$data = $this->load_systmpl();        
		return $data[$key];
    }
    protected function _format_row($row)
    {
        
        if($row['intro']){
            $row['config'] = unserialize($row['intro']);
        }else{
            $row['config'] = array();
        }
       
        return $row;
    }
}