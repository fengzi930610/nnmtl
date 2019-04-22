<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

/* 余额 */

class Ctl_Ucenter_Integral extends Ctl_Ucenter
{

    public function index($page)
    {
        if($mall = $this->GP('mall')){
            $this->pagedata['mall'] = $mall;
        }
        $this->pagedata['jifen'] = $this->MEMBER['jifen'];
        $this->tmpl = "ucenter/integral/index.html";
    }

    public function loaditems($page = 1)
    {

        $filter = array();
        $filter['uid'] = $this->uid;
        $filter['type'] = 'jifen';
        $pager['limit'] = $limit = 10;
        $pager['page'] = $page = max((int) $page, 1);
        $count = 0;
        $orderby = array('dateline' => 'desc');
        if(!$items = K::M('member/log')->items($filter, $orderby, $page, $limit, $count)){
            $items = array();
        }
        else{
            foreach($items as $k => $v){
                $items[$k]['dateline'] = date('Y-m-d', $v['dateline']);
            }
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
        $this->tmpl = "ucenter/integral/loaditems.html";
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }

}
