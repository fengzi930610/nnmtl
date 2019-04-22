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

    public function index()
    {
        
        $this->tmpl = "ucenter/money/index.html";
    }
    
    
    public function loaditems($page = 1){
        
        $filter = array();
        $filter['uid'] = $this->uid;
        $filter['type'] = 'money';
        $count = 0;
        $pager['limit'] = $limit = 10;
        $pager['page'] = $page = max((int) $page, 1);
        if(!$items = K::M('member/log')->items($filter, $orderby, $page, $limit, $count)){
            $items = array();
        }else{
            $count_num = K::M('member/log')->count($filter);
            if($count_num <= $limit){
                $loadst = 0;
            }
            else{
                $loadst = 1;
            }
        }
        
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "ucenter/money/loaditems.html";
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }
    
    

    public function recharge()
    {
        $money_pack = array();
        if($money_pack = K::M('member/money')->package()){
            $this->pagedata['money_pack'] = $money_pack;
            $this->pagedata['uid'] = $this->uid;
        }
        $this->tmpl = "ucenter/money/recharge.html";
    }

}
