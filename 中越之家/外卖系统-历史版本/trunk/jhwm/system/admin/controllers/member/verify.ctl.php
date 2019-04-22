<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: verify.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Member_Verify extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['uid']){
                    $filter['uid'] = $SO['uid'];
            }else{
                if($SO['name']){$filter['name'] = "LIKE:%".$SO['name']."%";}
                if(is_numeric($SO['verify'])){$filter['verify'] = (int)$SO['verify'];}
                if(is_array($SO['verify_time'])){if($SO['verify_time'][0] && $SO['verify_time'][1]){$a = strtotime($SO['verify_time'][0]); $b = strtotime($SO['verify_time'][1])+86400;$filter['verify_time'] = $a."~".$b;}}
                if(is_array($SO['request_time'])){if($SO['request_time'][0] && $SO['request_time'][1]){$a = strtotime($SO['request_time'][0]); $b = strtotime($SO['request_time'][1])+86400;$filter['request_time'] = $a."~".$b;}}
            }
        }
        if($items = K::M('member/verify')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
            $uids = array();
            foreach($items as $k=>$v){
                $uids[$v['uid']] = $v['uid'];
            }
            if($uids){
                $this->pagedata['member_list'] = K::M('member/member')->items_by_ids($uids);
            }
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:member/verify/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:member/verify/so.html';
    }
    public function create()
    {
        if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                if($_FILES['data']){
                    foreach($_FILES['data'] as $k=>$v){
                        foreach($v as $kk=>$vv){
                            $attachs[$kk][$k] = $vv;
                        }
                    }
                    $upload = K::M('magic/upload');
                    foreach($attachs as $k=>$attach){
                        if($attach['error'] == UPLOAD_ERR_OK){
                            if($a = $upload->upload($attach, 'member')){
                                $data[$k] = $a['photo'];
                            }
                        }
                    }
                }
                if($uid = K::M('member/verify')->create($data)){
                    $this->msgbox->add('添加内容成功');
                    $this->msgbox->set_data('forward', '?member/verify-index.html');
                }
            } 
        }else{
           $this->tmpl = 'admin:member/verify/create.html';
        }
    }
    public function edit($uid=null)
    {
        if(!($uid = (int)$uid) && !($uid = $this->GP('uid'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('member/verify')->detail($uid)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($this->checksubmit('data')){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                if($_FILES['data']){
                    foreach($_FILES['data'] as $k=>$v){
                        foreach($v as $kk=>$vv){
                            $attachs[$kk][$k] = $vv;
                        }
                    }
                    $upload = K::M('magic/upload');
                    foreach($attachs as $k=>$attach){
                        if($attach['error'] == UPLOAD_ERR_OK){
                            if($a = $upload->upload($attach, 'member')){
                                $data[$k] = $a['photo'];
                            }
                        }
                    }
                }
                if(K::M('member/verify')->update($uid, $data)){
                    $this->msgbox->add('修改内容成功');
                    if(isset($data['verify']) && ($data['verify'] == 'refuse' || $data['verify'] != $detail['verify_from'])){
                        K::M('member/verify')->update_verify($uid, $data['verify'], $data['refuse']);
                    }
                }
            } 
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:member/verify/edit.html';
        }
    }
    public function audit()
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['verify'] = 0;
        if($items = K::M('member/verify')->items($filter, null, $page, $limit, $count)){
            $pager['count'] = $count;
            $uids = array();
            foreach($items as $k=>$v){
                $uids[$v['uid']] = $v['uid'];
            }
            if($uids){
                $this->pagedata['member_list'] = K::M('member/member')->items_by_ids($uids);
            }
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:member/verify/items.html';        
    }
    public function dorefuse($itemIds=null)
    {
        if(!($itemIds = $itemIds) && !($itemIds = $this->GP('itemIds'))){
            $this->msgbox->add('未选定要拒绝的用户', 212);            
        }else if(!$items = K::M('member/verify')->items_by_ids($itemIds)){
            $this->msgbox->add('要操作的用户不存在', 213);
        }else if($this->checksubmit()){
            if(!$refuse = $this->GP('refuse')){
                $this->msgbox->add('请输入拒绝理由', 214);
            }else if(K::M('member/verify')->update_verify($itemIds, 'refuse', $refuse)){
                $this->msgbox->add('批量操作成功');
            }
        }else{
            $pager = array('itemIds'=>$itemIds);
            $pager['total'] = count($items);
            $this->pagedata['pager'] = $pager;
            $this->pagedata['items'] = $items;
            $this->tmpl = 'admin:member/verify/dorefuse.html';
        }
    }
    public function dopass($uid=null)
    {
        if($uid = (int)$uid){
            $verify = (int)$verify;
            if(K::M('member/verify')->update_verify($uid, 'pass')){
                $this->msgbox->add('审核通过成功');
            }
        }else if($uids = $this->GP('uid')){
            if(K::M('member/verify')->update_verify($ids, 'pass')){
                $this->msgbox->add('批量审核通过成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($uid=null)
    {
        if($uid = (int)$uid){
            if(K::M('member/verify')->delete($uid)){
                $this->msgbox->add('删除成功');
            }
        }else if($ids = $this->GP('uid')){
            if(K::M('member/verify')->delete($ids)){
                $this->msgbox->add('批量删除成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}