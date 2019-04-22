<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Member_Feedback extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            /*if($SO['fid']){$filter['fid'] = $SO['fid'];}
            if($SO['uid']){$filter['uid'] = $SO['uid'];}*/
            if($SO['content']){$filter['content'] = "LIKE:%".$SO['content']."%";}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
        }
        if($items = K::M('member/feedback')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            $uids = array();
            foreach($items as $k=>$v){
                $uids[$v['uid']] = $v['uid'];
            }
            if($uids){
                $this->pagedata['member_list'] = K::M('member/member')->items_by_ids($uids);
            }
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:member/feedback/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:member/feedback/so.html';
    }
    public function detail($fid = null)
    {
        if(!$fid = (int)$fid){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('member/feedback')->detail($fid)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->pagedata['member'] = K::M('member/member')->detail($detail['uid']);
            $this->tmpl = 'admin:member/feedback/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            
            if($fid = K::M('member/feedback')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?member/feedback-index.html');
            } 
        }else{
           $this->tmpl = 'admin:member/feedback/create.html';
        }
    }


    public function delete($fid=null)
    {
        if($fid = (int)$fid){
            if(!$detail = K::M('member/feedback')->detail(fid)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('member/feedback')->delete($fid)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('fid')){
            if(K::M('member/feedback')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}