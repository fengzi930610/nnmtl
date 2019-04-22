<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Ucenter_Index extends Ctl_Ucenter
{
   public function index()
   {
        //待评价订单
        $filter = array();
        $filter['uid'] = $this->uid;
        $filter['order_status'] = 8;
        $filter['pay_status'] = 1;
        $filter['comment_status'] = 0;
        if($nums = K::M('order/order')->count($filter)) {
            $this->pagedata['comments'] = $nums;
        }
        $wait_payment_count = $wait_ticket_count = $wait_comment_count = $new_msg_count = $hongbao_count = 0;
        //待付款
        $wait_payment_count = K::M('order/order')->count(array('uid'=>$this->uid, 'from'=>"<>:'maidan'", 'pay_status'=>0, 'order_status'=>0, 'online_pay'=>1, 'closed'=>0)); 
        //待使用
        $wait_ticket_count = K::M('tuan/ticket')->count(array('uid'=>$this->uid, 'status'=>0, 'ltime'=>'>:'.__TIME));
        //待评价
        $map = array('uid'=>$this->uid, 'comment_status'=>0, 'order_status'=>'8', 'closed'=>0);
        $map['from'] = '<>:weidian';
        $wait_comment_count = K::M('order/order')->count($map);
        $pager = array('wait_payment_count'=>$wait_payment_count, 'wait_ticket_count'=>$wait_ticket_count, 'wait_comment_count'=>$wait_comment_count);
        //未使用红包数
        $pager['hongbao_count'] = K::M('hongbao/hongbao')->count(array('uid'=>$this->uid, 'order_id'=>0,'ltime'=>'>:'.__TIME));
        //新消息数
        $pager['new_msg_count'] = K::M('member/message')->count(array('uid'=>$this->uid, 'is_read'=>0));
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'ucenter/index.html';
   }
}
