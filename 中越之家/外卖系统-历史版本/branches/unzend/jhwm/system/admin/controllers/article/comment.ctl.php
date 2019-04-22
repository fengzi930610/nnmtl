<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: comment.ctl.php 3190 2014-01-23 06:05:51Z $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Article_Comment extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['comment_id']){$filter['comment_id'] = $SO['comment_id'];}
            if($SO['article_id']){$filter['article_id'] = $SO['article_id'];}
            if($SO['uid']){$filter['uid'] = $SO['uid'];}
            if($SO['content']){$filter['content'] = "LIKE:%".$SO['content']."%";}
            if($SO['closed']){$filter['closed'] = $SO['closed'];}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1]);$filter['dateline'] = $a."~".$b;}}
        }
        $filter['closed'] = 0;
        if($items = K::M('article/comment')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));;
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:article/comment/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:article/comment/so.html';
    }
    public function detail($pk)
    {
    	$this->pagedata['detail'] = K::M('article/comment')->detail($pk);
    	$this->tmpl = 'admin:article/comment/detail.html';
    }
    public function create()
    {
        if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                if($comment_id = K::M('article/comment')->create($data)){
                    $this->msgbox->add('修改内容成功');
                    $this->msgbox->set_data('forward', '?article/comment-index.html');
                }
            } 
        }else{
           $this->tmpl = 'admin:article/comment/create.html';
        }
    }
    public function edit($pk=null)
    {
        if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else if(!$comment_id = $this->GP('comment_id')){
                $this->msgbox->add('未指要修改ID', 202);
            }else{
                if(K::M('article/comment')->update($comment_id, $data)){
                    $this->msgbox->add('修改内容成功');
                }  
            } 
        }else{
        	$this->pagedata['detail'] = K::M('article/comment')->detail($pk);
        	$this->tmpl = 'admin:article/comment/edit.html';
        }
    }
    public function delete($pk=null)
    {
        if(!empty($pk)){
            if(K::M('article/comment')->delete($pk)){
                $this->msgbox->add('删除成功');
            }
        }else if($pks = $this->GP('comment_id')){
            if(K::M('article/comment')->delete($pks)){
                $this->msgbox->add('批量删除成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}