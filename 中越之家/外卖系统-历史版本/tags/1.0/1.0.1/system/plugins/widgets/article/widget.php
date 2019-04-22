<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: widget.php 3129 2014-01-20 01:18:18Z youyi $
 */

class Widget_Article extends Model
{

    public function index(&$params)
    {
        
    }

	public function cate(&$params)
	{
		$params['tpl'] = 'cate_options.html';
		$data['value'] = $params['value'] ? $params['value'] : 0;
		$from = $params['from'] ? $params['from'] : null;
    	$data['tree'] = K::M('article/cate')->tree($from);
    	return $data;			
	}
    
    
    public function newitems(&$params){
        $data['limit'] = $params['limit'] ? $params['limit'] : 5;
        $filter['from'] = 'article';
        $filter['closed'] = 0;
        $article = K::M('article/view')->items($filter,array('article_id'=>'DESC') , 1,$data['limit']);
        $cates = K::M('article/cate')->fetch_all();
        foreach($article as $k=>$v){
            $article[$k]['cat_title'] = $cates[$v['cat_id']]['title'];
        }
        $data['article'] = $article;
        $params['tpl'] = 'newitems.html'; 
        return $data;
    }
    
    public function randitems(&$params){
        $data['limit'] = $params['limit'] ? $params['limit'] : 8;
        $filter['from'] = 'article';
        $filter['closed'] = 0;
        $count = K::M('article/view')->count(" `from`='article' AND closed=0 ");
        if($data['limit'] > $count){
             $article = K::M('article/view')->items($filter,array('article_id'=>'DESC') , 1,$data['limit']);
        }else{
             $page = rand(1,  ceil(($count-$data['limit'])/ $data['limit']));
             $article = K::M('article/view')->items($filter,array('article_id'=>'ASC') , $page,$data['limit']);
        }
       
        $cates = K::M('article/cate')->fetch_all();
        foreach($article as $k=>$v){
            $article[$k]['cat_title'] = $cates[$v['cat_id']]['title'];
        }
        $data['article'] = $article;
        $params['tpl'] = 'randitems.html'; 
        return $data;
    }
    
 

}