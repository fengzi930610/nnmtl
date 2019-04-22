<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Huodong_Coupon extends Ctl
{
    //领取优惠券
    public function create()
    {
        if(K::M('waimai/huodongcoupon')->count(array('shop_id'=>$this->shop_id, 'closed'=>0))){
            $this->msgbox->add('已有活动，只有撤销后才能创建', 211);
        }elseif($post = $this->checksubmit('data')){
            $data = array();
            $data['stime'] = strtotime($post['stime']);
            $data['ltime'] = strtotime($post['ltime']) + 86399;
            $data['shop_id'] = $this->shop_id;
            $data['group'] = (int)$post['group'];
            $data['limit'] = $post['num'] ? (int)$post['limit'] : -1;
            $data['num'] = $post['num'] ? (int)$post['num'] : -1;
            $data['dateline'] = __TIME;
            if($data['ltime'] < __TIME){
                $this->msgbox->add('结束不能早于当前时间', 211);
            }elseif(!$config = $this->checksubmit('config')){
                $this->msgbox->add('满减活动规则错误', 212);
            }else{
                $coupon = array();
                foreach($config as $v){
                    if(!empty($v['order_amount']) && !empty($v['coupon_amount'])){
                        $order_amount = (float)$v['order_amount'];
                        $coupon_amount = (float)$v['coupon_amount'];
                        $day = max((int)$v['day'], 1);
                        if($coupon_amount > 0 && ($order_amount <= $order_amount)){
                            $coupon[] = array(
                                    'order_amount'  => $order_amount,
                                    'coupon_amount' => $coupon_amount,
                                    'day'           => $day
                                );
                        }
                    }
                }
                if(empty($coupon)){
                    $this->msgbox->add('活动规则设置错误', 213);
                }else{
                    $data['config'] = serialize($coupon);
                    if($huodong_id = K::M('waimai/huodongcoupon')->create($data)){
                        $this->msgbox->set_data('forward', $this->mklink('huodong/shop'));
                        $this->msgbox->add('创建优惠券活动成功');
                    }
                }
            }
        }else{
            $this->tmpl = 'huodong/coupon/create.html';
        }
    }

    public function detail($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('该活动不存在',211);
        }elseif(!$detail = K::M('waimai/huodongcoupon')->detail($huodong_id, true)){
            $this->msgbox->add('该活动不存在',212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->pagedata['config'] = $detail['config'];
            $this->tmpl = 'huodong/coupon/detail.html';
        }
    }

    public function history($page=1)
    {//历史记录
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['shop_id'] = $this->shop_id;
        if($huodong_id = (int)$this->GP('huodong_id')){
            $filter['huodong_id'] = "<>:".$huodong_id;
        }
        if($items = K::M('waimai/huodongcoupon')->items($filter, array('huodong_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'huodong/coupon/history.html';
    }

    public function close($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('该活动不存在',211);
        }elseif(!$detail = K::M('waimai/huodongcoupon')->detail($huodong_id)){
            $this->msgbox->add('该活动不存在',212);
        }elseif($detail['closed'] ==1){
            $this->msgbox->add('该活动已撤销',213);
        }else{
            if(K::M('waimai/huodongcoupon')->update($huodong_id,array('closed'=>1))){
                if(!K::M('waimai/waimai')->update_huodong_ltime($this->shop_id,'coupon')){
                    K::M('waimai/huodongcoupon')->update($huodong_id,array('closed'=>0));
                    $this->msgbox->add('活动撤销失败',214);
                }else{
                    $this->msgbox->add('活动撤销成功');
                    $this->msgbox->set_data('forward',$this->mklink('huodong/shop'));
                }
            }else{
                $this->msgbox->add('活动撤销失败');
            }
        }
    }

    public function sends()
    {
        if(!$data = $this->checksubmit('data')){
            $this->msgbox->add('数据有误！',211);
        }else if(!($order_amount = $data['order_amount']) || !($coupon_amount = $data['coupon_amount']) || !($day = $data['day']) || !($uids = $data['uid'])){
            $this->msgbox->add('参数有误！',212);
        }else if($order_amount < $coupon_amount){
            $this->msgbox->add('券值不能大于订单金额！',213);
        }else if(!in_array($day,array(1,2,3,4,5,6,7))){
            $this->msgbox->add('有效期设置有误！',214);
        }else{
            $create_data = array();
            $create_data['shop_id'] = $this->shop_id;
            $create_data['order_id'] = 0;
            $create_data['type'] = 2;
            $create_data['huodong_id'] = 0;
            $create_data['order_amount'] = $order_amount;
            $create_data['coupon_amount'] = $coupon_amount;
            $create_data['stime'] = __TIME;
            $create_data['ltime'] = __TIME+86400*$day;
            $create_data['title'] = $this->waimai_shop['title'].'优惠券（赠送）';
            foreach ($uids as $k => $v) {
                $create_data['uid'] = $v;
                if(!K::M('waimai/coupon')->create($create_data)){
                    $this->msgbox->add('ID为'.$v.'的用户赠送失败，请稍后再试',215)->response();
                }
            }
            $this->msgbox->add('赠送成功！');
        }
    }
}