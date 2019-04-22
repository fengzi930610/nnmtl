<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Staff_Complaint extends Ctl
{
	public function index($page=1)
	{
		$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($order_items = K::M('order/order')->items(array('staff_id'=>'>:0'),array('order_id'=>'desc'),1,10000,$counts)) {
        	foreach($order_items as $v) {
        		$orderids[$v['order_id']] = $v['order_id'];
        	}
        	$filter['order_id'] = $orderids;
        }
        if($items = K::M('order/complaint')->items($filter, array('complaint_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:staff/complaint/items.html';
	}
	public function detail($complaint_id)
	{
		if(!$complaint_id = (int)$complaint_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('order/complaint')->detail($complaint_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:staff/complaint/detail.html';
        }
	}
}