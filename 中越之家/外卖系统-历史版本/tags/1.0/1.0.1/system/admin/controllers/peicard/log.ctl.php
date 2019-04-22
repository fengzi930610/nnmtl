<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Peicard_Log extends Ctl
{
    
    public function index($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;

        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['order_id']){
                $filter['order_id'] = $SO['order_id'];
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
                $filter['order_id'] = $SO['keywords'];
            }
        }
        
        if($items = K::M('peicard/log')->items($filter, array('log_id'=>'desc'), $page, $limit, $count)){
            $cids = $uids = array();
            foreach ($items as $k => $v) {
                if($v['uid']){
                    $uids[$v['uid']] = $v['uid'];
                }
                if($v['cid']){
                    $cids[$v['cid']] = $v['cid'];
                }
            }
            $members = K::M('member/member')->items_by_ids($uids);
            $pcards = K::M('peicard/member')->items_by_ids($cids);
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['members'] = $members;
        $this->pagedata['pcards'] = $pcards;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:peicard/log/items.html';
    }

    public function so()
    {
        $this->tmpl = 'admin:peicard/log/so.html';
    }  
}