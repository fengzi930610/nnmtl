<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Huodong_Manjian extends Ctl
{

    public function create()
    {
        if(K::M('waimai/huodongmj')->count(array('shop_id'=>$this->shop_id, 'closed'=>0))){
            $this->msgbox->add('已有活动，只有撤销后才能创建', 211);
        }elseif($post = $this->checksubmit('data')){
            $data = array();
            $data['stime'] = strtotime($post['stime']);
            $data['ltime'] = strtotime($post['ltime']) + 86399;
            $data['shop_id'] = $this->shop_id;
            $data['dateline'] = __TIME;
            $data['clientip'] = __IP;
            if($data['ltime'] < __TIME){
                $this->msgbox->add('结束不能早于当前时间', 211);
            }elseif(!$config = $this->checksubmit('config')){
                $this->msgbox->add('满减活动规则错误', 212);
            }else{
                $manjian = array();
                foreach($config as $v){
                    if(!empty($v['order_amount']) && !empty($v['coupon_amount'])){
                        $order_amount = (float)$v['order_amount'];
                        $coupon_amount = (float)$v['coupon_amount'];
                        if($coupon_amount > 0 && ($coupon_amount <= $order_amount)){
                            $manjian[] = array(
                                    'order_amount'  => $order_amount, 
                                    'coupon_amount' => $coupon_amount, 
                                    'shop_amount'   => $coupon_amount, 
                                    'roof_amount'   => 0
                                );
                        }
                    }
                }
                if(empty($manjian)){
                    $this->msgbox->add('满减活动规则错误', 213);
                }else{
                    $data['config'] = serialize($manjian);
                    if($huodong_id = K::M('waimai/huodongmj')->create($data)){
                        $this->msgbox->set_data('forward', $this->mklink('huodong/shop'));
                        $this->msgbox->add('创建满减活动成功');
                    }
                }
            }
        }else{
            $this->tmpl = 'huodong/manjian/create.html';
        }
    }

    public function detail($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('该活动不存在',211);
        }elseif(!$detail = K::M('waimai/huodongmj')->detail($huodong_id, true)){
            $this->msgbox->add('该活动不存在',212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->pagedata['config'] = $detail['config'];
            $this->tmpl = 'huodong/manjian/detail.html';
        }
    }
    
    //历史记录
    public function history($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['shop_id'] = $this->shop_id;
        if($huodong_id = (int)$this->GP('huodong_id')){
            $filter['huodong_id'] = "<>:".$huodong_id;
        }
        if($items = K::M('waimai/huodongmj')->items($filter, array('huodong_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        
        $this->tmpl = 'huodong/manjian/history.html';
    }

    public function close($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('该活动不存在',211);
        }elseif(!$detail = K::M('waimai/huodongmj')->detail($huodong_id)){
            $this->msgbox->add('该活动不存在',212);
        }elseif($detail['closed'] ==1){
            $this->msgbox->add('该活动已撤销',213);
        }else{
            if(K::M('waimai/huodongmj')->update($huodong_id,array('closed'=>1))){
                if(!K::M('waimai/waimai')->update_huodong_ltime($this->shop_id,'mj')){
                    K::M('waimai/huodongmj')->update($huodong_id, array('closed'=>0));
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

}
