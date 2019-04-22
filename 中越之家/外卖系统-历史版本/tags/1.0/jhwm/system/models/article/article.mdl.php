<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: view.mdl.php 3436 2014-10-27 10:32:44Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Article_Article extends Mdl_Table
{   
    protected $_table = 'article';
    protected $_pk = 'article_id';
    protected $_cols = 'article_id,city_id,cat_id,from,page,title,thumb,desc,linkurl,views,favorites,allow_comment,comments,photos,hidden,orderby,audit,closed,dateline';
    protected $_orderby = array('orderby'=>'ASC', 'article_id'=>'DESC');
    protected $_hot_orderby = array('views'=>'DESC', 'orderby'=>'ASC');
    protected $_hot_filter = array('from'=>'article','hidden'=>'0', 'audit'=>'1', 'closed'=>'0');
    protected $_new_orderby = array('article_id'=>'DESC');
    protected $_new_filter = array('from'=>'article','hidden'=>'0', 'audit'=>'1', 'closed'=>'0');
    protected $_page_sep = '<hr style="page-break-after:always;" class="ke-pagebreak" />';
    protected static $_allow_from = array('about','help','page','article','pchelp', 'news');
    
    public function create($data)
    {
        if(!$data = $this->_check($data)){
            return false;
        }
        $data['dateline'] = __CFG::TIME;
        if($article_id = $this->db->insert($this->_table, $data, true)){
            K::M('article/content')->create($article_id, $this->article_ext);
            //正则获取thumb,photoIds
            if(preg_match_all("/(photo\/\d+\/\d{8}_[\dA-F]{32}\.(jpg|gif|png|jpeg))\?PID(\d+)/i", $this->article_ext['content'], $matches)){
                $a = array();
                if(empty($data['thumb'])){
                    $a['thumb'] = $matches[1][0].'_thumb.jpg';
                }
                $pids = implode(',',$matches[3]); //组合photoId为字符串 
                if($count = K::M('article/photo')->update_by_article($article_id, $pids)){
                    $a['photos'] = $count;
                }
                if($a){
                    $this->update($article_id, $a);
                }
            }            
        }
        return $article_id;
    }
    public function update($article_id, $data, $checked=false)
    {
        if(!$article_id = intval($article_id)){
            return false;
        }else if(!$checked && !($data = $this->_check($data,  $article_id))){
            return false;
        }
        $ret = $this->db->update($this->_table, $data, $this->field($this->_pk, $article_id));
        if($this->article_ext){
            K::M('article/content')->update($article_id, $this->article_ext);
        }
        return $ret;
    }
    public function detail($article_id, $closed=false)
    {
        if(!$article_id = intval($article_id)){
            return false;
        }
        $where = "a.article_id='$article_id'";
        if($closed){
            $where .= " AND a.closed=0";
        }
        $sql = "SELECT c.*,a.* FROM ".$this->table($this->_table)." a LEFT JOIN ".$this->table('article_content')." c ON a.article_id=c.article_id WHERE $where LIMIT 1";
        if($detail = $this->db->GetRow($sql)){
            $cate = K::M('article/cate')->cate($detail['cat_id']);
            $detail['cat_title'] = $cate['title'];
        }
        //分页处理
        $detail['content_list'] = explode($this->_page_sep, $detail['content']);
        $detail['content_count'] = count($detail['content_list']);
        return $detail;
    }
    
    
    
    
    public function prev_item($article_id, $cat_id=0)
    {
        $where = '';
        if(!$article_id = (int)$article_id){
            return false;
        }else if($cat_id = (int)$cat_id){
            $where = "cat_id='{$cat_id}' AND ";
        }
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE $where article_id<$article_id AND `from`='article' AND closed=0 ORDER BY article_id DESC LIMIT 1";
        $row = $this->db->GetRow($sql);  
        return $this->_format_row($row);
    }
    public function next_item($article_id, $cat_id=0)
    {
        $where = '';
        if(!$article_id = (int)$article_id){
            return false;
        }else if($cat_id = (int)$cat_id){
            $where = "cat_id='{$cat_id}' AND ";
        }
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE $where article_id>$article_id AND `from`='article' AND closed=0 ORDER BY article_id ASC LIMIT 1";
        $row = $this->db->GetRow($sql);  
        return $this->_format_row($row);
    }
        
    public function item_by_page($page, $city_id=0)
    {
        if(empty($page)){
            return false;
        }else if(!preg_match('/^[\w]+$/', $page)){
            return false;
        }
        $where = "a.page='{$page}' AND a.closed='0'";
        if($city_id){
            $where .= " AND a.city_id='{$city_id}'";
        }
        $sql = "SELECT c.*,a.* FROM ".$this->table($this->_table)." a  LEFT JOIN ".$this->table('article_content')." c ON a.article_id=c.article_id  WHERE $where";
        if($row = $this->db->GetRow($sql)){
            $row = $this->_format_row($row);
        }
        return $row;
    }
    public function detail_by_page($page, $city_id=0)
    {
        return $this->item_by_page($page,$city_id);
    }
    public function about($page, $city_id=0)
    {
        if(empty($page)){
            return false;
        }else if(!preg_match('/^[\w]+$/', $page)){
            return false;
        }
        $sql = "SELECT c.*,a.* FROM ".$this->table($this->_table)." a LEFT JOIN ".$this->table('article_content')." c ON a.article_id=c.article_id WHERE a.page='{$page}' AND a.from='about' LIMIT 1";
        return $this->db->GetRow($sql);
    }
    public function help($page, $city_id=0)
    {
        if(empty($page)){
            return false;
        }else if(!preg_match('/^[\w]+$/', $page)){
            return false;
        }
        $sql = "SELECT c.*,a.* FROM ".$this->table($this->_table)." a LEFT JOIN ".$this->table('article_content')." c ON a.article_id=c.article_id WHERE a.page='{$page}' AND a.from='help' LIMIT 1";
        return $this->db->GetRow($sql);        
    }

    protected function _format_row($row)
    {
        static $mdllink = null;
        if($mdllink === null){
            $mdllink = K::M('helper/link');
        }        
        if($cate = K::M('article/cate')->cate($row['cat_id'])){
            $row['cat_title'] = $cate['title'];
        }
        if(empty($row['thumb'])){
            $row['thumb'] = 'default/article_thumb.jpg';
        }
        if($row['linkurl']){
            $row['link'] = $row['linkurl'];
        }else if($row['from'] == 'help'){
            $row['link'] = $mdllink->mklink('help:'.$row['page'], array(), array(), 'www');
        }else if($row['from'] == 'about' || $row['from'] == 'page'){
            if(empty($row['city_id'])){
                $row['link'] = $mdllink->mklink($row['from'].':'.$row['page'], array(), array(), 'www');
            }else{
                $row['link'] = $mdllink->mklink($row['from'].':'.$row['page'], array(), array(), $row['city_id']);
            }
        }else{
            if(empty($row['city_id'])){
                $row['link'] = $mdllink->mklink('article:detail', array($row['article_id'], 1), array(), 'www');
            }else{
                $row['link'] = $mdllink->mklink('article:detail', array($row['article_id'], 1), array(), $row['city_id']);
            }
        }        
        return $row;
    }
    protected function _check($data, $article_id=null)
    {
        $oText = K::M('content/text');
        $oHtml = K::M('content/html');
        $article_ext = array();
        if(!$article_id || isset($data['title'])){
            if(empty($data['title'])){
                $this->msgbox->add('标题不能为空', 431);
                return false;
            }else{
                $data['title'] = $oHtml->encode($data['title']);
            }
        }
        if(!$article_id || isset($data['content'])){
            if(empty($data['content'])){
                $this->msgbox->add('内容不能为空', 432);
                return false;               
            }
            $this->text = $oHtml->text($data['content']);
            if($article_id || isset($data['desc'])){
                if(empty($data['desc'])){
                    $data['desc'] = preg_replace('/\s+/', '',$oText->substr($this->text, 0, 200));
                }else{
                    $data['desc'] = $oHtml->encode($data['desc']);
                }
            }
            $article_ext['content'] = $oHtml->filter($data['content']);
        }else if(isset($data['desc'])){
             $data['desc'] = $oHtml->encode($data['desc']);
        }
        if(isset($data['from'])){
            if(!in_array($data['from'], self::$_allow_from)){
                $data['from'] = 'article';
            }
        }
        if(isset($data['ontime'])){
            if(preg_match("/^\d{4}-\d{1,2}-\d{1,2}( \d{1,2}:\d{1,2}:\d{1,2})?$/i", $data['ontime'])){
                $data['ontime'] = strtotime($data['ontime']);
            }else{
                $data['ontime'] = 0;
            }
        }
        if(isset($data['views'])){
            $data['views'] = (int)$data['views'];
        }
        if(isset($data['favorites'])){
            $data['favorites'] = (int)$data['favorites'];
        }
        if(isset($data['comments'])){
            $data['comments'] = (int)$data['comments'];
        }
        if(isset($data['photos'])){
            $data['photos'] = (int)$data['photos'];
        }
        if(isset($data['orderby'])){
            $data['orderby'] = (int)$data['orderby'];
        }else if(!$article_id){
            $data['orderby'] = 50;
        }
        if(isset($data['linkurl'])){
            if(!K::M('verify/check')->url($data['linkurl'])){
                $data['linkurl'] = '';
            }
        }
        if(isset($data['hidden'])){
            $data['hidden'] = $data['hidden'] ? 1 : 0;
        }
        if(isset($data['closed'])){
            $data['closed'] = $data['closed'] ? 1 : 0;
        }
        if(isset($data['seo_title'])){
            $article_ext['seo_title'] = $oHtml->encode($data['seo_title']);
        }
        if(isset($data['seo_keywords'])){
            $article_ext['seo_keywords'] = $oHtml->encode($data['seo_keywords']);
        }
        if(isset($data['seo_description'])){
            $article_ext['seo_description'] = $oHtml->encode($data['seo_description']);
        }
        $this->article_ext = $article_ext;
        unset($data['content'], $data['seo_title'], $data['seo_keywords'], $data['seo_description']);
        return parent::_check($data, $article_id);        
    }
}