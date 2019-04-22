<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/30
 * Time: 16:13
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Ucenter_Coupon extends Ctl_Ucenter {
    //个人卡券
    public function index()
    {
        $this->tmpl = 'ucenter/coupon.html';

    }
    //加载卡券
    public function loadcoupon($page=1){
        $page = max((int)$page,1);
        $type= $this->GP('type');
        $filter = array();
        $filter['uid'] = $this->uid;
        $filter['coupon_amount']='>:0';
        if($type==1){
            $this->pagedata['type'] = 1;
            $filter['order_id'] = 0;
            $filter['use_time'] = 0;
            $filter['stime'] ='<:'.time();
            $filter['ltime'] = '>:'.time();
        }elseif($type==0){
            $this->pagedata['type'] = 0;
            $filter[':OR'] = array(
                'order_id'=>'>:0',
               'use_time'=>'>:0',
                'stime'=>'>:'.time(),
                'ltime'=>'<:'.time() 
            );
        }

         $format = $ids= $list = array();
        //获取符合条件的卡券
        if($items = K::M('waimai/coupon')->items($filter,array('coupon_id'=>'desc'),$page,10,$count)){
          foreach ($items as $v){
              $ids[]=$v['shop_id'];
              if(($v['order_id']>0)||($v['use_time']>0)){
                  $v['msg'] = '已使用';
              }else if($v['stime']>=time()){
                  $v['msg'] = '活动未开始';
              }else if($v['ltime']<=time()){
                  $v['msg'] = '已过期';
              }else{
                  $v['msg'] = '立即使用';
              }
              $list[] =$v;
          }
        }
        $shop = K::M('waimai/waimai')->items_by_ids($ids);
        foreach ($list as $k=>$v){
            foreach ($shop as $kk=>$vv){
                if($list[$k]['shop_id'] == $vv['shop_id']){
                    $list[$k]['shop_title'] =$vv['title'];
                    $list[$k]['time_format']=date('Y-m-d', $list[$k]['ltime']);
                    $format[] =$list[$k];
                }
            }
        }
        if($count <= 10){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
       /* echo '<pre>';
        print_r($format);exit;*/
        $this->pagedata['coupon_list'] =$format;
        $this->tmpl = 'ucenter/loadcoupon.html';
        $html = $this->output(true);
        $this->msgbox->set_data('loadst', $loadst);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();


    }
    





}