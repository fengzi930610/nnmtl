<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Ucenter_Help extends Ctl
{
   public function index($page=1) 
    {
        $filter = array();
        $filter['cat_id'] = 1;
        $page= max(intval($page), 1);
        $count = 0;
        $items = K::M('article/article')->items($filter, array('article_id'=>'desc'), $page, 20, $count);
        $this->pagedata['items'] = $items;
        $this->pagedata['count'] = $count;
    	$this->tmpl = "ucenter/help/index.html";
    }

    public function detail($article_id)
    {
        if(!$article_id = (int)$article_id){
            $this->msgbox->add('服务问题不存在',211);
        }else if(!$detail = K::M('article/article')->detail($article_id)){
            $this->msgbox->add('服务问题不存在',212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = "ucenter/help/detail.html";
        }
    }
    
    
}
