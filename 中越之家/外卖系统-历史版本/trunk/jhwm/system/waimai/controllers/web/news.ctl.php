<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2015-07-22 01:25:14Z $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Web_News extends Ctl
{
    
    public function index($page=1)
    {
    	$pager = $filter = array();
    	$pager['page'] = $page = max((int)$page, 1); 
    	$pager['limit'] = $limit = 10;
    	$filter = array('from'=>'article', 'audit'=>1, 'hidden'=>0, 'closed'=>0);
    	if($items = K::M('article/article')->items($filter, null, $page, $limit, $count)){
			$pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
    	}
    	$this->pagedata['items'] = $items;
    	$this->pagedata['pager'] = $pager;
        $this->tmpl = 'web/news/news.html';
    }

    public function detail($article_id)
    {
        if(!$article_id = (int)$article_id){
            $this->error(404);
        }else if(!$detail = K::M('article/article')->detail($article_id)){
            $this->error(404);
        }else if(!$detail['audit']){
            $this->msgbox->add('内容审核中', 211);
        }else if(preg_match('/^(http|https)/i', $detail['linkurl'])){
        	K::M('article/article')->update_count($article_id, 'views', 1);
            header("Location:".$detail['linkurl']);
            exit;
        }else{
            K::M('article/article')->update_count($article_id, 'views', 1);
            $this->pagedata['cate'] = K::M('article/cate')->detail($detail['cat_id']);
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'web/news/detail.html';
        }
    }
}