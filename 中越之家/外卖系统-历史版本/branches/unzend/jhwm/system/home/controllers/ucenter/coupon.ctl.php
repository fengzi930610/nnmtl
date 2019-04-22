<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Ucenter_Coupon extends Ctl_Ucenter
{

    public function index($type)
    {
        $this->check_login();
        if(!in_array($type, array('tuan', 'quan'))){
            $type = 'tuan';
        }
        $this->pagedata['type'] = $type;
        $this->tmpl = 'ucenter/coupon/index.html';
    }

    public function loaditems($page=1)
    {
        $type = $this->GP('type');
        if(!in_array($type, array('tuan', 'quan'))){
            $type = 'tuan';
        }
        $filter = $pager = $items = array();
        $pager['limit'] = $limit = 10;
        $pager['page'] = $page = max((int) $page, 1);
        $filter['uid'] = $this->uid;
        $filter['type'] = $type;
        $filter['status'] = 0;
        $filter['use_time'] = 0;
        $filter['type'] = $type;
        $config = K::M('system/config')->get('site');
        if($items = K::M('tuan/ticket')->items($filter, array('ticket_id' => 'desc'), $page, $limit, $count)){
            foreach($items as $k => $v){
                $detail = K::M('tuan/tuan')->detail($v['tuan_id']);
                $tuan_order = K::M('tuan/order')->detail($v['order_id']);
                $shop = K::M('shop/shop')->detail($v['shop_id']);
                if($detail && $shop){
                    $items[$k]['detail'] = $detail;
                    $items[$k]['shop'] = $shop;
                }
                if($tuan_order){
                    $items[$k]['tuan_number'] = $tuan_order['tuan_number'];
                }
                $items[$k]['qrcode'] = $config['site_url'] . $this->mklink('qrcode?data=' . $v['number']);
                $items[$k]['curr_time'] = __TIME;
            }
            $count_num = K::M('tuan/ticket')->count($filter);
            if($count_num <= $limit){
                $loadst = 0;
            }
            else{
                $loadst = 1;
            }
        }
        else{
            $items = array();
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = 'ucenter/coupon/loaditems.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
        
        
    }

    public function detail($ticket_id)
    {
        if($ticket_id = (int) $ticket_id){
            if($ticket = K::M('tuan/ticket')->detail($ticket_id)){
                $tuan = K::M('tuan/tuan')->detail($ticket['tuan_id']);
                $tuan_order = K::M('tuan/order')->detail($ticket['order_id']);
                $shop = K::M('shop/shop')->detail($tuan['shop_id']);
                $config = K::M('system/config')->get('site');
                $data['title'] = $shop['title'];
                $data['price'] = $tuan['price'];
                $data['nums'] = $tuan_order['tuan_number'];
                $data['number'] = $ticket['number'];
                $data['ltime'] = $ticket['ltime'];
                $data['qrcode'] = $config['site_url'] . $this->mklink('qrcode?data=' . $data['number']);
            }
        }
        $this->pagedata['data'] = $data;
        $this->tmpl = 'ucenter/coupon/detail.html';
    }

}
