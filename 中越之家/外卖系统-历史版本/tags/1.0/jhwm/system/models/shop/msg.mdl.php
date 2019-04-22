<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Shop_Msg extends Mdl_Table
{   
  
    protected $_table = 'shop_msg';
    protected $_pk = 'msg_id';
    protected $_cols = 'msg_id,shop_id,order_id,title,content,type,is_read,dateline,clientip';
    public function create($data,$checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
    	$data['dateline'] = $data['dateline'] ? $data['dateline']: __CFG::TIME;
    	$data['clientip'] = __IP;
    	if ($comment_id = $this->db->insert($this->_table, $data, true)) {
            return $comment_id;
        }
    }

    public function send($shop_id, $title, $content)
    {
        return $this->create(array('shop_id'=>$shop_id, 'title'=>$title, 'content'=>$content));
    }
}