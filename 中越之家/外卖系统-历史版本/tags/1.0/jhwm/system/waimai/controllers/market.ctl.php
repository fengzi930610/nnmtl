<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Market extends Ctl 
{
    
    public function adclick($item_id)
    {
        if($item = K::M('adv/item')->detail($item_id)){
            K::M('adv/item')->update_count($item_id, 'clicks');
            if(preg_match('/(http|https)\:\/\//i', $item['link'])){
                header('Location:'.$item['link']);
            }
            exit();
        }else{
            $this->error(404);
        }
    }

    public function invite($uid=null)
    {     
        
        $inviteCfg = $this->system->config->get('invite');
        //print_r($inviteCfg);die;
        $total_money = 0;
        foreach($inviteCfg['invitee_hongbao_cfg'] as $k=>$v){
            $total_money += $v['hongbao_amount'];
        }
        $first = substr($uid, -6, -5);
        $id = intval(substr($uid, 1, 5));
        if($first == 'D') {
            $detail = K::M('ditui/ditui')->detail($id); 
            $member['uid'] = $detail['pid'];
            $member['nickname'] = $detail['name'];
            $member['face'] = $detail['face'];
        }       
        if($first == 'M') {
            $detail = K::M('member/member')->detail($id);
            $member['uid'] = $detail['pid'];
            $member['nickname'] =$detail['nickname'];
            $member['face'] = $detail['face'];
        }
        if($first == 'S') {
            $detail = K::M('shop/shop')->detail($id);
            $member['uid'] = $detail['pid'];
            $member['nickname'] =$detail['title'];
            $member['face'] = $detail['logo']; 
        }
        $this->pagedata['total_money'] = $total_money;
        $this->pagedata['member'] = $member;
        $this->pagedata['inviteCfg'] = $inviteCfg;
        $this->tmpl = 'market/invite.html';
    }

    public function invite_handle()
    {
        if(IS_AJAX) {
            $inviteCfg = $this->system->config->get('invite');
            $member = $data = $ditui_data = $ditui_log = $a_data = array();
            $pmid = $this->GP('pmid');
            $data['mobile'] = $this->GP('mobile');
            $data['sms_code'] = $this->GP('sms_code');
            if(preg_match('/M(\d+)/i', $pmid, $a)) {
                $id = (int)$a[1];
            }
           
            $first = substr($pmid, -6, -5);

            if($first == 'D') {
                $detail = K::M('ditui/ditui')->detail($id); 
                $invite_id = $detail['ditui_id'];
                $invite_type = 'ditui';
            }       
            if($first == 'M') {
                $detail = K::M('member/member')->detail($id);
                $invite_id = $detail['uid'];
                $invite_type = 'member';
            }
            if($first == 'S') {
                $detail = K::M('shop/shop')->detail($id);
                $invite_id = $detail['shop_id'];
                $invite_type = 'shop';    
            }

            if(!$mobile = K::M('verify/check')->mobile($data['mobile'])){
                $this->msgbox->add(L('手机号不正确'), 212);
            }else if($m = K::M('member/member')->member($mobile, 'mobile')){
                $this->msgbox->add(L('该红包只有新用户才能领取'), 213);
            }else if($s = K::M('shop/shop')->shop($mobile, 'mobile')){
                $this->msgbox->add(L('该红包只有新用户才能领取'), 214);
            }else if($s = K::M('ditui/ditui')->ditui($mobile, 'mobile')){
                $this->msgbox->add(L('该红包只有新用户才能领取'), 215);
            }else if(!$detail) {
                $this->msgbox->add(L('邀请人不存在'), 211);
            }else{
                $a_data['mobile'] = $mobile;
                $a_data['passwd'] = $a_data['paypasswd'] = md5(uniqid());
                $a_data['nickname'] = substr($mobile,0,3).'****'.substr($mobile,-4);
                $a_data['pmid'] = $detail['pid'];
                /*被邀请人为用户*/
                if($invite_uid = K::M('member/member')->create($a_data)){ 
                    //這邊只考慮member 
                    if($invite_type == 'member') {
                        //增加邀请记录 
                        $miv['invite_uid'] = $invite_uid;
                        $miv['uid'] = $invite_id;
                        $miv['mobile'] = $mobile;
                        K::M('member/invite')->create($miv);
                        if ($inviteCfg['is_invitee_hongbao'] == 1) { // 被邀请人是否奖励红包
                            K::M('member/invite')->send_hongbao_by_cfg($inviteCfg['invitee_hongbao_cfg'], $invite_uid, 6);//被邀请人
                        }
                    }
                    $this->msgbox->set_data('invite_uid',$invite_uid);
                    $this->msgbox->add(L('恭喜你，领取成功'));
                }
            }
        }
    }

    public function detail($uid)
    {
        if(!$uid = (int)$uid){
            $this->msgbox->add('领取详情不存在',212);
        }elseif(!$member = K::M('member/member')->detail($uid)){
            $this->msgbox->add('领取详情不存在',213);
        }elseif(!$member['pid']){
            $this->msgbox->add('领取详情不存在',214);
        }else{
            $first = substr($member['pmid'], -6, -5);
            $id = intval(substr($member['pmid'], 1, 5));
            if($first == 'D') {
                $detail = K::M('ditui/ditui')->detail($id); 
                $_member['uid'] = $detail['pid'];
                $_member['nickname'] = $detail['name'];
                $_member['face'] = $detail['face'];
            }       
            if($first == 'M') {
                $detail = K::M('member/member')->detail($id);
                $_member['uid'] = $detail['pid'];
                $_member['nickname'] =$detail['nickname'];
                $_member['face'] = $detail['face'];
            }
            if($first == 'S') {
                $detail = K::M('shop/shop')->detail($id);
                $_member['uid'] = $detail['pid'];
                $_member['nickname'] =$detail['title'];
                $_member['face'] = $detail['logo']; 
            }
            $this->pagedata['hongbao_cfg'] = __CFG::$MODULE;
            $this->pagedata['_member'] = $_member;
            $hongbaos = K::M('hongbao/hongbao')->items(array('uid'=>$uid,'type'=>5,'order_id'=>0,'ltime'=>'>:'.__TIME));
            $this->pagedata['hongbaos'] = $hongbaos;
            $inviteCfg = $this->system->config->get('invite');
            $total_money = 0;
            foreach($inviteCfg['invitee_hongbao_cfg'] as $k=>$v){
                $total_money += $v['hongbao_amount'];
            }
            $this->pagedata['total_money'] = $total_money;
            $this->pagedata['member'] = $member;
            $this->pagedata['inviteCfg'] = $inviteCfg;
            $this->tmpl = "market/detail.html";
        }
    }
    

}