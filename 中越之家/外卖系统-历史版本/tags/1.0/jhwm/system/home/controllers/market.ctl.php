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
            }else if(!$sms_code = $data['sms_code']){
                $this->msgbox->add(L('短信验证码不存在'),216);
            }else if($m = K::M('member/member')->member($mobile, 'mobile')){
                $this->msgbox->add(L('该红包只有新用户才能领取'), 213);
            }else if($s = K::M('shop/shop')->shop($mobile, 'mobile')){
                $this->msgbox->add(L('该红包只有新用户才能领取'), 214);
            }else if($s = K::M('ditui/ditui')->ditui($mobile, 'mobile')){
                $this->msgbox->add(L('该红包只有新用户才能领取'), 215);
            }/*else if(K::M('system/session')->start()->get('code_'.$mobile) != $sms_code){
                $this->msgbox->add(L('短信验证码错误'),217);
            }*/else if(K::M('cache/cache')->get('code_'.$mobile) != $sms_code){
                $this->msgbox->add(L('短信验证码错误'),217);
            }else if(!$detail) {
                $this->msgbox->add(L('邀请人不存在'), 211);
            }else{
                $a_data['mobile'] = $mobile;
                $a_data['passwd'] = $a_data['paypasswd'] = md5(uniqid());
                $a_data['nickname'] = substr($mobile,0,3).'****'.substr($mobile,-4);
                $a_data['pmid'] = $detail['pid'];
                /*被邀请人为用户*/
                if($invite_uid = K::M('member/account')->create($a_data)){ 
                    if(($invite_reg_money = (float)$inviteCfg['invite_reg_money'])>0){
                        //多种情况，有可能pmid是会员，商户推荐，地推推荐的
                        if($invite_type == 'ditui') {
                            //增加注册奖励 更新账户余额 增加资金变动日志
                            $ditui_data['ditui_id'] = $invite_id;
                            $ditui_data['uid'] = $invite_uid;
                            $ditui_data['signup_amount'] = $invite_reg_money;
                            if($mid = K::M('ditui/member')->create($ditui_data)) {
                                if($ditui_money = K::M('ditui/ditui')->update_money($invite_id,$invite_reg_money)) {
                                    K::M('ditui/ditui')->update_regcount($invite_id);
                                    $ditui_log['ditui_id'] = $invite_id;
                                    $ditui_log['money'] = $invite_reg_money;
                                    $ditui_log['intro'] = L('推荐用户注册奖励金额');
                                    K::M('ditui/log')->create($ditui_log);
                                }
                            }
                        }
                        if($invite_type == 'member') {
                            //增加邀请记录 更新账户余额 增加资金变动日志
                            $miv['invite_uid'] = $invite_uid;
                            $miv['uid'] = $invite_id;
                            $miv['mobile'] = $mobile;
                            $miv['money'] = $invite_reg_money;
                            K::M('member/invite')->create($miv);
                            $lig = K::M('member/member')->update_money($invite_id, $invite_reg_money,L('推荐用户注册奖励金额')); 
                            // 分发奖励红包
                            if ($inviteCfg['is_inviter_hongbao'] == 1) { // 邀请人是否奖励红包
                                K::M('member/invite')->send_hongbao_by_cfg($inviteCfg['inviter_hongbao_cfg'], $invite_id); //邀请人
                            }
                            if ($inviteCfg['is_invitee_hongbao'] == 1) { // 被邀请人是否奖励红包
                                K::M('member/invite')->send_hongbao_by_cfg($inviteCfg['invitee_hongbao_cfg'], $invite_uid);//被邀请人
                            }
                        }else if($invite_type == 'shop') {
                            //增加邀请记录 更新商户余额
                            K::M('shop/shop')->update_money($invite_id, $invite_reg_money, L('推荐用户注册奖励金额'), '');
                        }
                    }
                    /*$hongbao = array('min_amount'=>$inviteCfg['hongbao_min_amount'], 'amount'=>$inviteCfg['hongbao_amount'], 'ltime'=>__TIME+86400*30); // 去掉地推，改为用户推广邀请送红包。红包分发可配置
                    $hongbao['title'] = L('好友邀请新人红包');
                    $hongbao['type'] = 5;
                    K::M('hongbao/hongbao')->send($invite_uid, $hongbao);*/
                    $this->msgbox->add(L('领取成功'),0);
                }
            }
        }
    }

    public function detail()
    {
        $inviteCfg = $this->system->config->get('invite');
        if (!empty($inviteCfg['share_title']) && !empty($inviteCfg['intro'])) {
            $this->pagedata['inviteCfg'] = $inviteCfg;
            $this->tmpl = "market/detail.html";
        }else{
            $this->msgbox->add('活动详情不存在',212);
        }
    }
    

}