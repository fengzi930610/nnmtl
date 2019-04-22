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
	public function index()
    {     

        if($invite_cnt = K::M('member/invite')->invite_count($this->uid)) {
            $cnt = $invite_cnt['invite_money'];
        }
        if($detail = K::M('member/member')->detail($this->uid)) {
            $mylink= $this->mklink('market:invite', array('args'=>$detail['pid']));  // 推广链接
            $this->pagedata['mylink'] = $mylink;
        }
        $this->pagedata['invite'] = $this->system->config->get('invite');
        $this->pagedata['incnt'] = $cnt;
        $this->pagedata['from'] = $this->GP('from');
        $this->pagedata['share_img'] = '<img alt="推广链接" src="/qrcode?data='.urlencode($mylink).'&size=10"/>';
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
}
