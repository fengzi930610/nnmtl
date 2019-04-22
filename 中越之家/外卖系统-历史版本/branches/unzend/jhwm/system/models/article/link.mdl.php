<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: link.mdl.php 6383 2014-10-04 09:52:23Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Article_Link extends Mdl_Table
{   
  
    protected $_table = 'article_link';
    protected $_pk = 'link_id';
    protected $_cols = 'link_id,title,link,orderby,dateline';
    protected $_pre_cache_key = 'article-link-list';
    protected $_orderby = array('orderby'=>'DESC');
    
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
    public function filter($content, $limit=5)
    {
        static $filter = null;
        if($filter === null){
            $a = $b = array();
            if($items = $this->fetch_all()){
                $count = 0;
                foreach($items as $v){
                    $a['find'][] = "/{$v['title']}/";
                    $c = md5($v['title']);
                    $a['replace'][] = "@{$c}@";
                    $b['find'][] = "/@{$c}@/";
                    $b['replace'][] = "<a href=\"{$v['link']}\" target=\"_blank\">{$v['title']}</a>";
                }
            }
            $filter = array('a'=>$a, 'b'=>$b);
        }
        $_filter_ext = array();
        if(preg_match_all('/(title|alt)=\"(.*?)\"/i', $content, $m)){
            $a = $b = array();
            foreach($m[0] as $v){
                $a['find'][] = "/".addslashes($v)."/"; ///{$v}/";
                $c = md5($v);
                $a['replace'][] = "@{$c}@";
                $b['find'][] = "/@{$c}@/";
                $b['replace'][] = $v;
            }
            $_filter_ext = array('a'=>$a, 'b'=>$b);
            if($_filter_ext['a']){
                $content = preg_replace($_filter_ext['a']['find'], $_filter_ext['a']['replace'], $content);
            }
        }
        $limit = (int)$limit;
        if($filter['a']['find'] && $filter['a']['replace']){
            $content = preg_replace($filter['a']['find'],$filter['a']['replace'], $content, $limit);
            $content = preg_replace($filter['b']['find'],$filter['b']['replace'], $content, $limit);
        }
        if($_filter_ext['b']['find']){
            $content = preg_replace($_filter_ext['b']['find'], $_filter_ext['b']['replace'], $content);
        }
        return $content;
    }
}