<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/31
 * Time: 14:35
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Ucenter_Hongbao extends Ctl_Ucenter {

    public function index($page = 1,$from=null)
    {
        $this->check_login();
        $filter = array();
        $filter['uid'] = $this->uid;
        $filter['order_id'] = 0;
        $num = 3*86400 + __TIME;
        $filter['ltime'] = __TIME.'~'.$num;
        $this->pagedata['count']= K::M('hongbao/hongbao')->count($filter);
        $this->pagedata['from'] = $from;
        $this->tmpl = 'ucenter/hongbao/index.html';

    }
    

    // ajax 红包列表
    public function loaditems($page = 1,$from = null){
        $this->check_login();
        $filter = $pager = $items = array();
        $pager['limit'] = $limit = 10;
        $pager['page'] = $page = max((int) $page, 1);
        $filter['uid'] = $this->uid;
        $filter['order_id'] = 0;
        $filter['ltime'] = ">=:".__TIME;
        if($from){
            $filter['from'] = array('all',$from);
        }
        
        if (!$items = K::M('hongbao/hongbao')->items($filter, array('hongbao_id'=>'desc'), $page, $limit, $count)) {
            $items= array();
        }else{
            foreach($items as $k => $v){
                $items[$k]['dateline'] = date('Y-m-d',$v['dateline']);
                $items[$k]['limit_stime'] = trim($v['limit_stime'])?trim($v['limit_stime']):'';
                $items[$k]['limit_ltime'] = trim($v['limit_ltime'])?trim($v['limit_ltime']):'';
            }
        }


        if($count <= $limit){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->pagedata['curr_time'] = __TIME;
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "ucenter/hongbao/loaditems.html";
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }
    
    public function history($page = 1)
    {
        $this->check_login();
        $this->tmpl = 'ucenter/hongbao/history.html';

    }
    

    // ajax 历史红包列表
    public function loaditems2($page = 1){ //历史红包
        $this->check_login();
        $filter = $pager = $items = array();
        $pager['limit'] = $limit = 10;
        $pager['page'] = $page = max((int) $page, 1);
        $filter['uid'] = $this->uid;
        $filter[":OR"] = array('order_id'=>'>:0','ltime'=>'<:'.__TIME);
        if (!$items = K::M('hongbao/hongbao')->items($filter, array('hongbao_id'=>'desc'), $page, $limit, $count)) {
            $items= array();
        }else{
            foreach($items as $k => $v){
                $items[$k]['dateline'] = date('Y-m-d',$v['dateline']);
                $items[$k]['limit_stime'] = trim($v['limit_stime'])?trim($v['limit_stime']):'';
                $items[$k]['limit_ltime'] = trim($v['limit_ltime'])?trim($v['limit_ltime']):'';
            }
        }
        if($count <= $limit){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->pagedata['curr_time'] = __TIME;
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "ucenter/hongbao/loaditems2.html";
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }
    
    

    public function lists()
    {
        $this->check_login();
        $money= $this->GP('money');
        $filter = $pager = array();
        $filter['uid'] = $this->uid;
        $filter['order_id'] = 0;
        $filter['ltime'] = '>:' . __TIME;
        $filter['min_amount'] = '<=:' .$money;
        $items = K::M('hongbao/hongbao')->items($filter, array('amount'=>'desc'));
        $this->pagedata['items'] = $items;
        $this->tmpl = 'ucenter/hongbao/lists.html';
    }

    public function hongbao_list()
    {
        $this->check_login();
        $filter = $pager = array();
        $page = max((int) $this->GP('page'), 1);
        $pager['limit'] = $limit = 10;
        $filter['uid'] = $this->uid;
        //$filter['order_id'] = 0;
        $filter['ltime'] = '>:' . __TIME;
        if (!$items = K::M('hongbao/hongbao')->items($filter, array('hongbao_id'=>'desc'), $page, $limit, $count)) {
            $items= array();
        }else{
            foreach($items as $k => $v){
                $items[$k]['dateline'] = date('Y-m-d',$v['dateline']);
            }
        }
        $this->pagedata['items'] = $items;
        echo '<pre>';print_r($items);die;
        $this->msgbox->set_data('data', array('items' => array_values($items)));
        
    }

    public function exchange()
    {
        $this->check_login();
        if ($hongbao_sn = $this->GP('hongbao_sn')) {
            $detail = K::M('hongbao/hongbao')->find(array('hongbao_sn' => $hongbao_sn, 'order_id' => 0, 'ltime' => '>:' . time()));
            if (empty($detail)) {
                $this->msgbox->add('红包不存在', 212);
            } else if ($detail['uid'] != 0) {
                $this->msgbox->add('已经被兑换了', 213);
            } else if (false !== K::M('hongbao/hongbao')->update($detail['hongbao_id'], array('uid' => $this->uid))) {
                K::M('message/message')->create(array('uid'=>$this->uid,'title'=>'恭喜你获得一个'.$detail['amount'].'元红包','type'=>1,'content'=>'红包金额'.$detail['amount'].'元,可用于支付时抵扣相应的金额','type'=>1));
                $this->msgbox->add('兑换成功');
            } else {
                $this->msgbox->add('兑换失败', 214);
            }
        } else {
            $this->tmpl = 'ucenter/hongbao/exchange.html';
        }
    }
    
    public function hbdesc()
    {
        if($cate = K::M('article/cate')->find(array('from'=>'hongbao'))) {
            if($items = K::M('article/article')->items(array('cat_id'=>$cate['cat_id']))) {
                $this->pagedata['items'] = $items;
            }
        }
        //echo '<pre>';print_r($items);die;
        $this->tmpl = 'ucenter/hongbao/desc.html';
    }

}