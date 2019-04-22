<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2015-07-22 01:25:14Z wanglei $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_About extends Ctl
{

    public function __construct($system)
    {
        parent::__construct($system);
        if(!in_array($system->request['act'], array('detail'))){
            $this->about($system->request['act']);
            $system->request['args'] = array($system->request['act']);
            $system->request['act'] = 'about';
        }
    }

    public function about($page)
    {
        if($page == 'detail'){
            $this->detail($article_id);
        }elseif(!$detail = K::M('article/article')->detail_by_page($page)){
            $this->error(404);
        }elseif($detail['linkurl']){
            header("Location:{$detail['linkurl']}");
            exit;     
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'page/page.html';
        }
    }

    //商家
    public function detail($article_id)
    {
        if(!$detail = K::M('article/article')->detail($article_id)){
            $this->error(404);
        }elseif($detail['linkurl']){
            header("Location:{$detail['linkurl']}");
            exit;     
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'page/about.html';
        }
    }

}
