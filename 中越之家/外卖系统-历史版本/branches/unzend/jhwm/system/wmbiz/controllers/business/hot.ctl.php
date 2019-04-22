<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Business_Hot extends Ctl
{
    public function index($page)
    {
    	$yesterday = strtotime(date('Y-m-d'))-7*86400;// 昨天
    	$filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['order_status'] = 8;// 已完成
        $filter['pei_type'] = array(0, 1, 3);
        $filter['refund_status'] = 0;
        $filter['closed'] = 0;
        $filter['from'] = 'waimai';
        $filter['shop_id'] = $this->shop_id;
        $filter['lasttime'] = $yesterday."~".time(); // 默认取昨天
        $orderby = array('total_product_number'=>'DESC');// 默认销量第一排序
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if(is_array($SO['time'])){if($SO['time'][0] && $SO['time'][1]){$a = strtotime($SO['time'][0]); $b = strtotime($SO['time'][1])+86400;$filter['lasttime'] = $a."~".$b;}}
            if ($SO['orderby'] == 1) {
                $orderby = array('total_product_amount'=>'DESC'); // 销售额
            }
        }
        if(!$items = K::M('order/order')->items_join_order_product($filter, $orderby, $page, $limit, $count)){
        	$items = array();
        }
        $pager['count'] = $count;
        $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('business/hot:index', array('{page}')), array('SO'=>$SO));
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['yesterday'] = $yesterday;
        $this->tmpl = 'business/hot/index.html';
    }
}