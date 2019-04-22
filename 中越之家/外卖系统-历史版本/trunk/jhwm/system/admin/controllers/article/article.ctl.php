<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: article.ctl.php 6080 2014-08-13 15:20:01Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Article_Article extends Ctl
{
    protected $article_from = 'article';
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['article_id']){$filter['article_id'] = $SO['article_id'];}
            if($SO['cat_id']){
                if($cids = K::M('article/cate')->children_ids($SO['cat_id'])){
                    $filter['cat_id'] = explode(',', $cids);
                }
            }
            if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]); 
                    $b = strtotime($SO['dateline'][1]);
                    $filter['dateline'] = $a."~".$b;
                }
            }
            if(is_numeric($SO['hidden'])){
                $filter['hidden'] = $SO['hidden'] ? 1 : 0;
            }
            if(is_numeric($SO['audit'])){
                $filter['audit'] = $SO['audit'] ? 1 : 0;
            }            
        }
        $this->pagedata['from'] = $this->article_from;
        $filter['closed'] = 0;
        $filter['from'] = $pager['from'] = $this->article_from;
        $orderby = array('orderby'=>'ASC','article_id'=>'DESC');
        if($items = K::M('article/article')->items($filter, $orderby, $page, $limit, $count)){
        	$pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink("article/{$this->article_from}:index", array("{page}")), array("SO"=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['city_list'] = K::M('data/city')->fetch_all();        
        $this->tmpl = 'admin:article/article/items.html';
    }
    public function so($target=null, $multi=null)
    {
        $pager['from'] = $this->article_from;
        if($target == 'dialog'){
            $pager['multi'] = $multi == 'Y' ? 'Y' : 'N';
            $pager['target'] = $target;
        }
        $this->pagedata['pager'] = $pager;   
        $this->tmpl = 'admin:article/article/so.html';
    }
    public function dialog($multi=1, $page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 10;
        $pager['multi'] = $multi = $multi ? 1 : 0;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['article_id']){$filter['article_id'] = $SO['article_id'];}
            if($SO['cat_id']){
                if($cids = K::M('article/cate')->children_ids($SO['cat_id'])){
                    $filter['cat_id'] = explode(',', $cids);
                }
            }
            if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]); 
                    $b = strtotime($SO['dateline'][1]);
                    $filter['dateline'] = $a."~".$b;
                }
            }
            if(is_numeric($SO['hidden'])){
                $filter['hidden'] = $SO['hidden'] ? 1 : 0;
            }
            if(is_numeric($SO['audit'])){
                $filter['audit'] = $SO['audit'] ? 1 : 0;
            }            
        }
        $filter['closed'] = 0;
        $filter['from'] = $pager['from'] = $this->article_from;
        $orderby = array('orderby'=>'ASC','article_id'=>'DESC');
        if($items = K::M('article/article')->items($filter, $orderby, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($multi, '{page}')), array('SO'=>$SO));;
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:article/article/dialog.html';       
    }
    public function create()
    {   
        if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                $data['from'] = $this->article_from;
                if($cate = (int)$data['cat_id']){
                    if($cate = K::M('article/cate')->detail($cate_id)){
                        $data['from'] = $data['from'];
                    }
                }
                
                if(CITY_ID){
                    $data['city_id'] = CITY_ID;
                }
                if($article_id = K::M('article/article')->create($data)){
                    if($photos = $this->__upload()){
                        K::M('article/article')->update($article_id, $photos);
                    }
                    $this->msgbox->add('添加文章成功');
                    $this->msgbox->set_data('forward', '?article/'.$this->article_from.'-index.html');
                }
            }
        }else{
            $pager['from'] = $this->article_from;
            $this->pagedata['pager'] = $pager;            
            $this->tmpl = 'admin:article/article/create.html';
        }
    }
    public function edit($article_id=null)
    {
        if(!($article_id = (int)$article_id) && !($article_id = (int)$this->GP('article_id'))){
            $this->msgbox->add('未指要修改文章ID', 211);
        }else if(!$detail = K::M('article/article')->detail($article_id)){
            $this->msgbox->add('文章不存在或已经删除', 212);
        }else if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                if(K::M('article/article')->update($article_id, $data)){
                    if($photos = $this->__upload($detail)){
                        K::M('article/article')->update($article_id, $photos);
                    }
                    $this->msgbox->add('修改文章成功');
                }                
            } 
        }else{
            $pager['from'] = $this->article_from;
            $this->pagedata['pager'] = $pager;
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:article/article/edit.html';
        }
    }
    public function doaudit($article_id=null)
    {
        if($article_id = (int)$article_id){
            if(K::M('article/article')->batch($article_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('article_id')){
            if(K::M('article/article')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($pk=null)
    {
        if(!empty($pk)){
            if(K::M('article/article')->delete($pk)){
                $this->msgbox->add('删除成功');
            }
        }else if($pks = $this->GP('article_id')){
            if(K::M('article/article')->delete($pks)){
                $this->msgbox->add('批量删除成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
    protected function __upload($article=array())
    {
        $photos = array();
        if($_FILES['data']){
            foreach($_FILES['data'] as $k=>$v){
                foreach($v as $kk=>$vv){
                    $attachs[$kk][$k] = $vv;
                }
            }
            $upload = K::M('magic/upload');
            foreach($attachs as $k=>$attach){
                if($attach['error'] == UPLOAD_ERR_OK){
                    if($a = $upload->upload($attach, 'article', $article[$k])){
                        $photos[$k] = $a['photo'];
                    }
                }
            }
        }
        return $photos;      
    }
    public function upload($article_id=0)
    {
        if($article_id = (int)$article_id){
            $article = K::M('article/article')->detail($article_id);
        }
        if(!$attach = $_FILES['imgFile']){
            $this->msgbox->add('上传文件失败', 211);
        }else if(UPLOAD_ERR_OK != $attach['error']){
            $this->msgbox->add('上传文件失败', 212);
        }else if($data = K::M('article/photo')->upload($article_id, $attach)){
            $cfg = $this->system->config->get('attach');
            $this->msgbox->set_data('url', $cfg['attachurl'].'/'.$data['photo'].'?PID'.$data['photo_id']);
            if($article && (empty($article['thumb']) || substr($article['thumb'], 0, 8) == 'default/')){
                K::M('article/article')->update($article_id, array('thumb'=>$data['photo'].'_thumb.jpg'), true);
            }
        }
        $this->msgbox->json();        
    }
}