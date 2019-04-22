<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Ucenter_Share extends Ctl_Ucenter
{
	/*public function index()
    {     
        if($invite_cnt = K::M('member/invite')->invite_count($this->uid)) {
            $cnt = $invite_cnt['invite_money'];
        }
        if($detail = K::M('member/member')->detail($this->uid)) {
            $mylink= $this->mklink('market:invite', array('args'=>$detail['pid']),null,'waimai');  // 推广链接
            $this->pagedata['mylink'] = $mylink;
        }

        $cfg = K::$system->config->get('invite');
        $cfg['intro'] = str_replace("\r\n",'',$cfg['intro']);
        $this->pagedata['cfg'] = $cfg;
        $this->pagedata['incnt'] = $cnt;
        $this->pagedata['from'] = $this->GP('from');
        $this->pagedata['share_img'] = '<img alt="面对面邀请" src="/qrcode?data='.urlencode($mylink).'&size=10"/>';
	    $this->tmpl = "ucenter/share/index.html";
	}*/

    public function invite_data()
    {
        $data = array('invite_money'=>0, 'invite_count'=>0, 'invite_ing'=>0);
        if($invite_cnt = K::M('member/invite')->invite_count($this->uid)) {
            $data['invite_money'] = $invite_cnt['invite_money'];
            $data['invite_count'] = $invite_cnt['invite_count'];
            $data['invite_ing'] = K::M('member/invite')->count(array('uid'=>$this->uid, 'status'=>array(0, 1)));
        }
        return $data;
    }

    public function index()
    {
        //邀请排行
        if($ranks = K::M('member/invite')->items_rank(null, null, 1, 10 ,$count)){
            $uids = array();
            foreach ($ranks as $k => $v) {
                if($v['uid']){
                    $uids[$v['uid']] = $v['uid'];
                }
            }
            $members = K::M('member/member')->items_by_ids($uids);
        }else{
            $ranks = $members = array();
        }

        //已获得红包金额,已邀请人数
        $invite_data = $this->invite_data();

        // 推广链接
        if($detail = K::M('member/member')->detail($this->uid)) {
            $mylink= $this->mklink('invite:index', array('args'=>$detail['pid']),null,'waimai');  
            $this->pagedata['mylink'] = $mylink;
        }

        //邀请配置
        $cfg = K::$system->config->get('invite');
        $cfg['intro_format'] = str_replace("\r\n",'<br>',$cfg['intro']);
        $cfg['intro'] = str_replace("\r\n",'',$cfg['intro']);
        $cfg['inviter_money'] = $cfg['invitee_money'] = 0;
        if($cfg['is_inviter_hongbao']){ //邀请人
            foreach($cfg['inviter_hongbao_cfg'] as $k=>$v){
                $cfg['inviter_money'] += $v['hongbao_amount'];
            }
        }

        if($cfg['is_invitee_hongbao']){ //被邀请人
            foreach($cfg['invitee_hongbao_cfg'] as $k=>$v){
                $cfg['invitee_money'] += $v['hongbao_amount'];
            }
        }

        $this->pagedata['ranks'] = array_values($ranks);
        $this->pagedata['members'] = $members;
        $this->pagedata['cfg'] = $cfg;
        $this->pagedata['invite_data'] = $invite_data;
        $this->pagedata['from'] = $this->GP('from');
        $this->pagedata['share_img'] = '<img alt="面对面邀请" src="/qrcode?data='.urlencode($mylink).'&size=10"/>';
        $this->tmpl = "ucenter/share/index.html";
    }
    
    public function detail()
    {
        if($detail = K::M('member/member')->detail($this->uid)) {
            $this->pagedata['mylink'] = $this->mklink('market:invite', array('args'=>$detail['pid']));  // 推广链接
        }
        $this->pagedata['invite'] = $this->system->config->get('invite');
    	$this->tmpl = "ucenter/share/detail.html";
    }

    public function logone()
    {
        $invite_data = $this->invite_data();
        $this->pagedata['invite_data'] = $invite_data;
        $this->tmpl = 'ucenter/share/logone.html';
    }

    public function logtwo()
    {
        $invite_data = $this->invite_data();
        $this->pagedata['invite_data'] = $invite_data;
        $this->tmpl = 'ucenter/share/logtwo.html';
    }

    public function logthree()
    {
        $invite_data = $this->invite_data();
        $this->pagedata['invite_data'] = $invite_data;
        $this->tmpl = 'ucenter/share/logthree.html';
    }

    public function loadone($page=1)
    {
        $filter = $orderby = $pager = $items = array();
        $pager['limit'] = $limit = 10;
        $pager['page'] = $page = max((int) $page, 1);
        $filter['uid'] = $this->uid;
        $filter['money'] = '>:0';
        if ($items = K::M('member/invite')->items($filter, $orderby, $page, $limit, $count)) {
            $mids = array();
            foreach($items as $k => $v){
                $mids[$v['invite_uid']] = $v['invite_uid'];
            }
            $members = K::M('member/member')->items_by_ids($mids);
            foreach ($members as $k => $v) {
                $members[$k]['mobile'] = substr($v['mobile'], 0, 3).'****'.substr($v['mobile'], -4);
            }
        }

        if($count <= $limit){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->pagedata['members'] = $members;
        $this->tmpl = "ucenter/share/loadone.html";
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }

    public function loadtwo($page=1)
    {
        $filter = $orderby = $pager = $items = array();
        $pager['limit'] = $limit = 10;
        $pager['page'] = $page = max((int) $page, 1);
        $filter['uid'] = $this->uid;
        $filter['status'] = array(0, 1);
        if ($items = K::M('member/invite')->items($filter, $orderby, $page, $limit, $count)) {
            $mids = array();
            foreach($items as $k => $v){
                $mids[$v['invite_uid']] = $v['invite_uid'];
            }
            $members = K::M('member/member')->items_by_ids($mids);
            foreach ($members as $k => $v) {
                $members[$k]['mobile'] = substr($v['mobile'], 0, 3).'****'.substr($v['mobile'], -4);
            }
        }

        if($count <= $limit){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->pagedata['members'] = $members;
        $this->tmpl = "ucenter/share/loadtwo.html";
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }

    public function loadthree($page=1)
    {
        $filter = $orderby = $pager = $items = array();
        $pager['limit'] = $limit = 10;
        $pager['page'] = $page = max((int) $page, 1);
        $filter['uid'] = $this->uid;
        if ($items = K::M('member/invite')->items($filter, $orderby, $page, $limit, $count)) {
            $mids = array();
            foreach($items as $k => $v){
                $mids[$v['invite_uid']] = $v['invite_uid'];
            }
            $members = K::M('member/member')->items_by_ids($mids);
            foreach ($members as $k => $v) {
                $members[$k]['mobile'] = substr($v['mobile'], 0, 3).'****'.substr($v['mobile'], -4);
            }
        }

        if($count <= $limit){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->pagedata['members'] = $members;
        $this->tmpl = "ucenter/share/loadthree.html";
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }


}
