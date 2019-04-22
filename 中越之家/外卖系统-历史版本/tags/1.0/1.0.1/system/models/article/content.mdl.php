<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: content.mdl.php 2034 2013-12-07 03:08:33Z $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Article_Content extends Mdl_Table
{   
  
    protected $_table = 'article_content';
    protected $_pk = 'article_id';
    protected $_cols = 'article_id,seo_title,seo_keywords,seo_description,content,clientip';
    public function create($article_id, $data)
    {
        if(!$article_id = intval($article_id)){
            return false;
        }else if(!$data = $this->_check($data)){
            return false;
        }
        $data['article_id'] = $article_id;
        $data['clientip'] = __IP;
        return $this->db->insert($this->_table, $data);
    }
    public function update($article_id, $data, $checked=false)
    {
        if(!$article_id = intval($article_id)){
            return false;
        }else if($checked && !($data = $this->_check($data,  $article_id))){
            return false;
        }
        return $this->db->update($this->_table, $data, $this->field($this->_pk, $article_id));
    }
}