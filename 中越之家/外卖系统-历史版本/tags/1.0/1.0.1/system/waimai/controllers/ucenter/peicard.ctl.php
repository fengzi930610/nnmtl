<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 16:21
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Ucenter_peicard extends Ctl_Ucenter {
    
    public function index($page=1)
    {
    	$filter = array('closed'=>0);
    	$orderby = array('orderby'=>'asc', 'card_id'=>'desc');
    	$page = max((int)$page, 1);
    	$limit = 50;
    	if($items = K::M('peicard/card')->items($filter, $orderby, $page, $limit, $count)){

    	}
    	$this->pagedata['items'] = $items;
        $this->tmpl='waimai/ucenter/peicard/index.html';        
    }

    public function mycard()
    {
        $filter = array('uid'=>$this->uid, 'ltime'=>'>:'.__TIME);
        $orderby = array('dateline'=>'desc');
        $page = 1;
        $limit = 50;
        if($items = K::M('peicard/member')->items($filter, $orderby, $page, $limit, $count)){
            $card_ids = array();
            foreach ($items as $k => $v) {
                if($v['card_id']){
                    $card_ids[$v['card_id']] = $v['card_id'];
                }
                $items[$k]['template'] = 1;
                $items[$k]['photo'] = 'default/peicard/theme1@2x.png';
            }
            if($cards = K::M('peicard/card')->items_by_ids($card_ids)){
                foreach ($items as $k => $v) {
                    if($card = $cards[$v['card_id']]){
                        $items[$k]['template'] = $card['template'];
                        $items[$k]['photo'] = $card['photo']; 
                    }
                }
            }
            $moneys = K::M('peicard/log')->sum(array('uid'=>$this->uid), 'money');
            $this->pagedata['moneys'] = $moneys;
            $this->pagedata['items'] = $items;
            $this->tmpl='waimai/ucenter/peicard/mycard.html';
        }else{
            $link = K::M('helper/link')->mklink('ucenter/peicard:index', array(), array(), 'waimai');
            header("Location:{$link}");
            exit;
        }       
    }

    public function loadlogs($page = 1){
        $filter = $pager = $items = array();
        $pager['limit'] = $limit = 10;
        $pager['page'] = $page = max((int) $page, 1);
        $filter['uid'] = $this->uid;
        $filter['dateline'] = '>=:'.strtotime(date('Y-m-01', strtotime('-6 month')));
        if (!$items = K::M('peicard/log')->items($filter, array('dateline'=>'desc'), $page, $limit, $count)) {
            $items= array();
        }

        if($count <= $limit){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "ucenter/peicard/loadlogs.html";
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }

    public function recharge($card_id)
    {
    	if(!$card_id = (int)$card_id){
    		$this->error(404);
    	}else if(!$card = K::M('peicard/card')->detail($card_id)){
    		$this->error(404);
    	}else{
    		$this->pagedata['card'] = $card;
    		$this->pagedata['rebackurl'] = $this->getrebackurl();
    		$this->tmpl='waimai/ucenter/peicard/recharge.html';
    	}
    }
}