<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: comment.mdl.php 2034 2013-12-07 03:08:33Z $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Article_Comment extends Mdl_Table
{   
  
    protected $_table = 'article_comment';
    protected $_pk = 'comment_id';
    protected $_cols = 'comment_id,article_id,uid,content,closed,clientip,dateline';
    protected $_orderby = array('comment_id'=>'ASC');
    
    public function create($data)
    {
        if(!$data = $this->_check_schema($data)){
            return false;
        }
        return $this->db->insert($this->_table, $data, true);
    }
    public function update($pk, $data, $checked=false)
    {
        $this->_checkpk();
        if(!$checked && !($data = $this->_check_schema($data,  $pk))){
            return false;
        }
        return $this->db->update($this->_table, $data, $this->field($this->_pk, $pk));
    }    
}