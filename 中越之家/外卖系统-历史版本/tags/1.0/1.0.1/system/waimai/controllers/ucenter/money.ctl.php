<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

/* 余额明细 */

class Ctl_Ucenter_Money extends Ctl_Ucenter
{

    public function index($page=1)
    {
        if($rebackurl = $this->GP('rebackurl')){
            $this->pagedata['rebackurl'] = $rebackurl;
        }
        $this->pagedata['is_allow'] = K::M('waimai/config')->allow_user_tixian()?1:0;
        $data = $this->ftrst_html(1);
        $this->pagedata['data'] = $data;
        $this->tmpl = "ucenter/money/index.html";
    }
    
    
    public function loaditems($page = 1){
        
        $filter = array();
        $filter['uid'] = $this->uid;
        $filter['type'] = 'money';
        $count = 0;
        $pager['limit'] = $limit = 30;
        $pager['page'] = $page = max((int) $page, 1);
        if(!$items = K::M('member/log')->items($filter, array('log_id'=>'DESC'), $page, $limit, $count)){
            $items = array();
        }
        if($count <= $limit){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "ucenter/money/loaditems.html";
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }

    public function ftrst_html($page=1){
        $filter = array();
        $filter['uid'] = $this->uid;
        $filter['type'] = 'money';
        $count = 0;
        $pager['limit'] = $limit = 30;
        $pager['page'] = $page = max((int) $page, 1);
        if(!$items = K::M('member/log')->items($filter, array('log_id'=>'DESC'), $page, $limit, $count)){
            $items = array();
        }
        if($count <= $limit){
            $loadst = 0;
        }else{
            $loadst = 1;
        }

        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "ucenter/money/loaditems.html";
        $html = $this->output(true);
        return array(
            'html'=>$html,
            'loadst'=>$loadst
        );

    }
    
    

    public function recharge()
    {
        $money_pack = array();
        if($money_pack = K::M('member/money')->package()){
            $this->pagedata['money_pack'] = $money_pack;
            $this->pagedata['uid'] = $this->uid;
        }
        /*if($rebackurl = $this->GP('rebackurl')){
            $this->pagedata['rebackurl'] = $rebackurl;
        }*/
        $this->pagedata['rebackurl'] = $this->getrebackurl();
        $this->tmpl = "ucenter/money/recharge.html";
    }


    public function apply(){
        if($data = $this->checksubmit('data')){
            if(!$money = (float)$data['money']){
                $this->msgbox->add('金额非法',202);
            }else if($money<=0){
                $this->msgbox->add('金额非法',206);
            }else if($money>$this->MEMBER['money']){
                $this->msgbox->add('提现金额不能大于余额',203);
            }else if(!K::M('waimai/config')->allow_user_tixian()){
                $this->msgbox->add('平台未开启用户提现功能',204);
            }else if(!$data['intro']){
                $this->msgbox->add('请填写提现信息',205);
            }else if(!K::M('cache/cache')->islock('member_tixian'.$this->uid,3)){
                $this->msgbox->add('处理中..',207)->response();
            } else{
                $insert_data = array();
                $insert_data['uid'] = $this->uid;
                $insert_data['money'] = $money;
                $insert_data['intro'] = $data['intro'];
                $insert_data['status'] = 0;
                if(K::M('member/tixian')->tixian($this->uid,$insert_data)){
                    K::M('cache/cache')->unlock('member_tixian'.$this->uid);
                    $this->msgbox->add("申请成功");
                }else{
                    $this->msgbox->add("申请失败",206);
                }
            }
        }else{
            $this->msgbox->add('非法数据请求',201);
        }

    }

}
