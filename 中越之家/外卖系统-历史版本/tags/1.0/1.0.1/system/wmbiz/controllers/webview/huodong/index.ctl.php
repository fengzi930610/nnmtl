<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Webview_Huodong_Index extends Ctl
{

    public function index()
    {//各种活动分开处理
        //满减活动
        $manjian = K::M('waimai/huodongmj')->find(array('shop_id'=>$this->shop_id,'closed'=>0,'ltime'=>'>=:'.__TIME));
        if($manjian){
            $manjian['coupons'] = $manjian['config']; 
        }
        //满返活动
        $manfan = K::M('waimai/huodongmf')->find(array('shop_id'=>$this->shop_id,'closed'=>0,'ltime'=>'>=:'.__TIME));
        if($manfan){
            $manfan['coupons'] = $manfan['config']; 
        }
        //送券活动
        $coupon = K::M('waimai/huodongcoupon')->find(array('shop_id'=>$this->shop_id,'closed'=>0,'ltime'=>'>=:'.__TIME));
        if($coupon){
            $coupon['coupons'] = $coupon['config']; 
        }
        //首单优惠
        $first_youhui = K::M('waimai/huodongfirst')->find(array('shop_id'=>$this->shop_id,'closed'=>0,'ltime'=>'>=:'.__TIME));

        //限时折扣
        $discount = K::M('waimai/huodongdiscount')->find(array('shop_id'=>$this->shop_id,'closed'=>0,'ltime'=>'>=:'.__TIME));
        $this->pagedata['discount'] = $discount;

        //超值换购
        $huangou = K::M('waimai/huodonghuangou')->find(array('shop_id'=>$this->shop_id,'closed'=>0,'ltime'=>'>=:'.__TIME));
        $this->pagedata['huangou'] = $huangou;

        $this->pagedata['coupon'] = $coupon;
        $this->pagedata['manfan'] = $manfan;
        $this->pagedata['manjian'] = $manjian;
       /* echo '<pre>';
        print_r($first_youhui);exit;*/
        $this->pagedata['first'] = $first_youhui;
        $this->tmpl = 'webview/huodong/index.html';
    }
    
}
