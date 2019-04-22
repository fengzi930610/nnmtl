<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: logs.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Member_Log extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['logs_id']){$filter['logs_id'] = $SO['logs_id'];}
            if($SO['uid']){$filter['uid'] = $SO['uid'];}
            if($SO['from']){$filter['from'] = $SO['from'];}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1]);$filter['dateline'] = $a."~".$b;}}
            
            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (w.nickname LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        if($items = K::M('member/log')->items_join_member($filter, array('log_id'=>'desc'), $page, $limit, $count)){
            /*$uids = array();
            foreach($items as $k=>$v){
                $uids[$v['uid']] = $v['uid'];
            }
            $this->pagedata['member_list'] = K::M('member/member')->items_by_ids($uids);*/
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));;
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:member/log/items.html';
    }

    public function so()
    {
        $this->tmpl = 'admin:member/log/so.html';
    }

    public function member($uid)
    {
    	$this->pagedata['detail'] = K::M('member/log')->detail($pk);
    	$this->tmpl = 'admin:member/log/detail.html';
    }

    public function delete($pk=null)
    {
        if(!empty($pk)){
            if(K::M('member/log')->delete($pk)){
                $this->msgbox->add('删除成功');
            }
        }else if($pks = $this->GP('logs_id')){
            if(K::M('member/log')->delete($pks)){
                $this->msgbox->add('批量删除成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}