<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: block.mdl.php 10075 2015-05-06 07:09:12Z wanglei $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Book_Book extends Mdl_Table
{   
  
    protected $_table = 'book';
    protected $_pk = 'book_id';
    protected $_cols = 'book_id,uid,nickname,content,dateline,clientip';
    protected $_orderby = array('book_id'=>'DESC');

    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check($data)){
            return false;
        }
        $data['dateline'] = __CFG::TIME;
        $data['clientip'] = __IP;
        if($block_id = $this->db->insert($this->_table, $data, true)){
            $this->flush();
        }
        return $block_id;
    }
    
}