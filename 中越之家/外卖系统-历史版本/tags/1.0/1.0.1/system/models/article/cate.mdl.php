<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: cate.mdl.php 2108 2013-12-11 11:21:31Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Article_Cate extends Mdl_Table
{   
  
    protected $_table = 'article_cate';
    protected $_pk = 'cat_id';
    protected $_cols = 'cat_id,parent_id,title,level,from,seo_title,seo_keywords,seo_description,orderby,hidden,dateline';
    protected $_orderby = array('parent_id'=>'ASC','orderby'=>'ASC','cat_id'=>'ASC');
    protected $_pre_cache_key = 'article-cate-list';
    protected static $_allow_from = array('about','help','page','article','pchelp', 'news');
    
    public function fetch_all()
    {
        if($this->_pre_cache_key === null){
            trigger_error('Table '.$this->_table.' has not cache_ke defined');
        }else if(isset(self::$_CACHE_TABLES[$this->_pre_cache_key])){
            return self::$_CACHE_TABLES[$this->_pre_cache_key];
        }else if(!$items = $this->cache->get($this->_pre_cache_key)){
            $sql = "SELECT * FROM ".$this->table($this->_table)." ORDER BY level ASC,orderby ASC,cat_id ASC";
            if($rs = $this->db->Execute($sql)){
                while($row = $rs->fetch()){
                    $items[$row[$this->_pk]] = $row;
                }
            }
            self::$_CACHE_TABLES[$this->_pre_cache_key] = $items;
            $this->cache->set($this->_pre_cache_key, $items, $this->_cache_ttl);
        }
        return $items;        
    }
    public function items_by_from($from)
    {
        $items = array();
        if($cats = $this->fetch_all()){
            foreach((array)$cats as $k=>$v){
                if($v['from'] == $from){
                    $items[$k] = $v;
                }
            }
        }
        return $items;
    }
    public function cate($cat_id)
    {
        if(!$cat_id = (int)$cat_id){
            return false;
        }else if($cats = $this->fetch_all()){
            return $cats[$cat_id];
        }
        return false;
    }
    public function children_ids($pid)
    {
        $pids = array($pid);
        if($class_list = $this->fetch_all()){           
            foreach($class_list as $k=>$v){
                if(in_array($v['parent_id'], $pids)){
                    $pids[] = $v['cat_id'];
                } 
            }
        }
        return implode(',', $pids);
    }
    public function create($data)
    {
        if(!$data = $this->_check($data)){
            return false;
        }
        $data['dateline'] = __CFG::TIME;
        if($cid = $this->db->insert($this->_table, $data, true)){
            $this->flush();
        }
        return $cid;
    }
    public function update($pk, $data, $checked=false)
    {
        $this->_checkpk();
        if(!$checked && !($data = $this->_check($data,  $pk))){
            return false;
        }
        if($ret = $this->db->update($this->_table, $data, $this->field($this->_pk, $pk))){
            $this->flush();
        }
        return $ret;
    }
    public function childrens($cat_id)
    {
        if(!$cat_id = (int)$cat_id){
            return false;
        }
        $childrens = array();
        if($items = $this->fetch_all()){
            foreach($items as $v){
                if($v['parent_id'] == $cat_id){
                    $childrens[$v['cat_id']] = $v;
                }
            }
        }
        return $childrens;
    }
    public function tree($from=null)
    {
        if(in_array($from, self::$_allow_from)){
            $items = $this->items_by_from($from);
        }else{
            $items = $this->fetch_all();
        }
        if($items){
            $tree = array();
            foreach($items as $k=>$v){
                if($v['level'] == '1'){
                    $tree[$k] = $v;
                }
            }
            foreach($items as $k=>$v){ 
                if($v['level'] == '2'){
                    $tree[$v['parent_id']]['children'][$k] = $v;
                }
            }
            foreach($items as $k=>$v){ 
                if($v['level'] == '3'){
                    $ppk = $items[$v['parent_id']]['parent_id'];
                    $tree[$ppk]['children'][$v['parent_id']]['children'][$k] = $v;
                }
            }
            return $tree;
        }
        return false;
    }   

    protected function _check($data, $cid=null)
    {
        $oHtml = K::M('content/html');
        if(!$cid || isset($data['title'])){
            if(empty($data['title'])){
                $this->msgbox->add('分类标题不能为空',431);
                return false;
            }else if(strlen($data['title']) > 150){
                $this->msgbox->add('分类标题长度不能大于150自己字符',431);
                return false;                
            }
            $data['title'] = $oHtml->encode($data['title']);
        }
        if($data['seo_title']){
            $data['seo_title'] = K::M('content/text')->substr($data['seo_title'], 0, 150,'');
            $data['seo_title'] = $oHtml->encode($data['seo_title']);
        }
        if($data['seo_keywords']){
            $data['seo_keywords'] = K::M('content/text')->substr($data['seo_keywords'], 0, 200, '');
            $data['seo_keywords'] = $oHtml->encode($data['seo_keywords']);
        }
        if($data['seo_description']){
            $data['seo_description'] = K::M('content/text')->substr($data['seo_description'], 0, 200, '');
            $data['seo_description'] = $oHtml->encode($data['seo_description']);
        }
        if(isset($data['from'])){
            $data['from'] = in_array($data['from'], self::$_allow_from) ? $data['from'] : 'article';
        }
        if(isset($data['parent_id'])){
            if($data['parent_id']){
                if(!$cate = $this->cate($data['parent_id'])){
                    $this->msgbox->add('要添加到的父级分类不存在', 432);
                    return false;
                }else if($cate['level']>=3){
                   $this->msgbox->add('父级分类不能为最为底层分类', 433); 
                   return false;
                }
                $data['parent_id'] = intval($data['parent_id']);
                $data['level'] = $cate['level'] + 1;
                $data['from'] = $cate['from'];
            }else{
                $data['parent_id'] = 0;
                $data['level'] = 1;
            }
        }
        if(isset($data['orderby'])){
            $data['orderby'] = intval($data['orderby']);
        }
        unset($data['cat_id'], $data['dateline']);
        return parent::_check($data);
    }      
}