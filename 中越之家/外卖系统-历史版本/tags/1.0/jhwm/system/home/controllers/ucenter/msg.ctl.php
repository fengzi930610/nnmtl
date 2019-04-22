<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

/* 消息中心 */

class Ctl_Ucenter_Msg extends Ctl_Ucenter
{

    public function index()
    {
        if(!$msg = K::M('member/message')->count(array('uid' => $this->uid))){
            $msg = 0;
        }
        $this->pagedata['msgnums'] = $msg;
        $this->tmpl = "ucenter/msg/index.html";
    }
    
        public function loaditems($page=1)
    {
        $page = max((int)$page, 1);
        $filter = array();
        $filter['uid'] = $this->uid;
        $pager['limit'] = $limit = 20;
        
        if(!$items = K::M('member/message')->items($filter, array('message_id' => 'desc'), $page, $limit, $count)){
            $items = array();
        }
        foreach ($items as $v){
            K::M('member/message')->update($v['message_id'],array('is_read'=>1));
        }

        $count_num = K::M('member/message')->count($filter);
        if($count_num <= $limit){
            $loadst = 0; 
        }else{
            $loadst = 1; 
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "ucenter/msg/loaditems.html";
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }
    
    public function getmsgs()
    {
        $filter = array();
        $filter['uid'] = $this->uid;
        $filter['is_read'] = 0;
        $counts = K::M('member/message')->count($filter);
        $this->msgbox->add('success');
        $this->msgbox->set_data('data', array('rows' => $counts));
    }

    public function setread()
    {
        $data = array();
        $data['message_id'] = intval($this->GP('message_id'));
        if(!$detail = K::M('member/message')->detail($data['message_id'])){
            $this->msgbox->add("你要设置的消息不存在或已经删除", 210);
        }
        else if($detail['uid'] != $this->uid){
            $this->msgbox->add("非法的数据请求", 212);
        }
        else{
            if($detail['is_read'] == 0){
                $data['is_read'] = 1;
                if(K::M('member/message')->update($data['message_id'], $data)){
                    $this->msgbox->add('success');
                }
                else{
                    $this->msgbox->add('error', 213);
                }
            }
        }
    }

}
