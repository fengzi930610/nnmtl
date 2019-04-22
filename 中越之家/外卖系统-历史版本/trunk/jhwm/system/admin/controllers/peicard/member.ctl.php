<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Peicard_Member extends Ctl
{
    
    public function index($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;

        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['title']){
                $filter['title'] = 'LIKE:%'.$SO['title'].'%';
            }
            if($SO['uid']){
                $filter['uid'] = $SO['uid'];
            }
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1])+86400;
                    $filter['dateline'] = $a."~".$b;
                }
            }
            if($SO['keywords']){
                $filter['title'] = 'LIKE:%'.$SO['keywords'].'%';
            }
        }
        
        if($items = K::M('peicard/member')->items($filter, array('cid'=>'desc'), $page, $limit, $count)){
            $uids = array();
            foreach ($items as $k => $v) {
                if($v['uid']){
                    $uids[$v['uid']] = $v['uid'];
                }
            }
            $members = K::M('member/member')->items_by_ids($uids);
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['members'] = $members;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:peicard/member/items.html';
    }

    public function detail($cid=null)
    {
        if(!($cid = (int)$cid)){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('peicard/member')->detail($cid)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['member'] = K::M('member/member')->detail($detail['uid']);
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:peicard/member/detail.html';
        }
    }

    public function delete($cid=null)
    {
        if($cid = (int)$cid){
            if(!$detail = K::M('peicard/member')->detail($cid)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('peicard/member')->delete($cid)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('cid')){
            if(K::M('peicard/member')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }

    public function so()
    {
        $this->tmpl = 'admin:peicard/member/so.html';
    }  
}