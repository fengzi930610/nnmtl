<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Msg extends Mdl_Table
{   
  
    protected $_table = 'staff_msg';
    protected $_pk = 'msg_id';
    protected $_cols = 'msg_id,staff_id,title,content,is_read,clientip,dateline';
    public function send($staff_id, $title, $content)
    {
        return $this->create(array('staff_id'=>$staff_id, 'title'=>$title, 'content'=>$content));
    }
    
    public function create($data,$checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
    	$data['dateline'] = $data['dateline'] ? $data['dateline']: __CFG::TIME;
    	$data['clientip'] = __IP;
    	if ($msg_id = $this->db->insert($this->_table, $data, true)) {
            return $msg_id;
        }
    }
    
}