<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Index extends Ctl
{
    public function __construct(&$system) {
        parent::__construct($system);
    }

    public function ddd(){
        $orders = K::M('order/order')->items(array('shop_id'=>506438));
        $order_ids = array(); 
        foreach($orders as $k=>$v){
            $order_ids[$v['order_id']] = $v['order_id'];
        }
        K::M('waimai/order')->delete($order_ids,true);
        K::M('order/order')->delete($order_ids,true);
    }

    public function index()
    {
        $link =K::M('helper/link')->mklink('index',null,null,'waimai');
        header('location:'.$link);exit;
        if(false && !defined('IS_MOBILE') && !$this->system->cookie->get('is_view')){
            header("Location:".$this->mklink('welcome/index'));
            exit;
        }else{
            //发送消息
            $this->send_member_coupon_msg($this->uid);
            //查询首页推荐商家
            //获取活动
            $this->gethuodong();
            
            $lng = (float)$this->request['UxLocation']['lng'];
            $lat = (float)$this->request['UxLocation']['lat'];
            if($lng && $lat){
                $filter = $pager = array();
                $filter['audit'] = 1;
                $filter['closed'] = 0;
                //$filter['have_waimai'] = 1;
                //使用此函数计算得到结果后，带入sql查询。
                $site_config = K::M('system/config')->get('site');
                $squares = K::M('helper/round')->returnSquarePoint($lng, $lat,$site_config['pei_range']);
                $filter['lat'] = $squares['left-bottom']['lat'].'~'.$squares['right-top']['lat'];
                $filter['lng'] = $squares['left-bottom']['lng'].'~'.$squares['right-top']['lng'];
                $filter['yy_status'] = 1;// 取手动营业中的
                if($waimai_items = K::M('waimai/waimai')->items($filter, null, 1, 100, $count)) {
                    $shop_ids = array();
                    $i = 0;
                    foreach ($waimai_items as $k => $v) {
                        if ($v['yysj_status'] == 1) {// 取序列化配置的营业时间
                            if ($area_price = K::M('waimai/waimai')->get_shipping_fee($v['area_polygon'], $lat, $lng)) {// 配送范围内
                                $shop_ids[$k] = $k;
                                $v['min_amount'] = $area_price['min_price'];
                                $v['freight'] = $area_price['shipping_fee'];
                                unset($v['area_polygon']);
                                $_waimai_items[$k] = $v;
                                $i++;
                            }
                        }
                        if ($i == 2) {break;}
                    }
                    if ($_waimai_items) {
                        foreach ($_waimai_items as $k => $v) {
                            $items[$k]['shop_id'] = $v['shop_id'];
                            $items[$k]['products'] = K::M('waimai/product')->items(array('shop_id'=>$v['shop_id'],'closed'=>0,'is_onsale'=>1),null,1,4);
                            $items[$k]['waimai'] = $v;
                            $items[$k]['juli'] = K::M('helper/round')->juli($v['lng'], $v['lat'], $lng, $lat);
                            $items[$k]['juli_label'] = K::M('helper/format')->juli($items[$k]['juli']);
                            unset($items[$k]['passwd']);
                        }
                    }
                    if($shop_verify_items = K::M('shop/verify')->items(array('shop_id'=>$shop_ids))) {
                        foreach($shop_verify_items as $k=>$v) {
                            $items[$v['shop_id']]['verify'] = $v['verify'];
                        }
                    }
                    uasort($items, array($this, 'juli_order'));
                    $shop_list = array_slice($items, ($page-1)*10, 10, true);
                }else{
                    $items = array();
                }
            }else{
                $this->msgbox->add('没有指定经纬度', 211);

            }
            foreach ($shop_list as $k=>$v){
                $shop_list[$k]['waimai']['avg_score'] =  ($v['waimai']['score']/$v['waimai']['comments']) ? round($v['waimai']['score']/$v['waimai']['comments'],2) : 0 ;
                $shop_list[$k]['waimai'] = K::M('waimai/waimai')->format_data($v['waimai']);
            }

            $this->pagedata['items'] = $shop_list;
            if($this->uid){
                $order_4 = K::M('order/order')->items(array('uid'=>$this->uid,'from'=>'waimai','order_status'=>8),array('order_id'=>'desc'),1,4);
                $order_ids = array();
                foreach($order_4 as $v){
                    $order_ids[] = $v['order_id'];
                }
                $order_products = K::M('waimai/orderproduct')->items(array('order_id'=>$order_ids));
                $product_ids = $likes2 = $likes = array();

                foreach($order_products as $k=>$v){
                    $product_ids[$v['product_id']] = $v['product_id'];
                }
                $products = K::M('waimai/product')->items_by_ids($product_ids);
                foreach($order_products as $k=>$v){
                    foreach($products as $k1=>$v1){
                        if($v['product_id'] == $v1['product_id']){
                            $likes2[$v1['product_id']] = $v1;
                        }
                    }
                }
                $num=0;
                foreach($likes2 as $k=>$v){
                    if($num<4&&($v['sale_sku']>0)){
                        $likes[$k] = $v;
                    }
                    $num++;
                }
                //print_r($likes);die;
                $count = (int)count($likes);
                if($count<4){
                    $_count = 4 - $count;
                    $likes_o = K::M('waimai/product')->items(array('closed'=>0,'is_onsale'=>1,'sale_sku'=>'>:0'),array('sales'=>'desc'),1,$_count);
                    $likes = $likes+$likes_o;
                }
                //$likes = K::M('waimai/orderproduct')->items(array('uid'=>$this->uid));
            }else{   
                $likes = K::M('waimai/product')->items(array('closed'=>0,'is_onsale'=>1,'sale_sku'=>'>:0'),array('sales'=>'desc'),1,4);
            }
            $shop_ids = array();
            foreach ($likes as $k=>$v){
                $shop_ids[$v['shop_id']] = $v['shop_id'];
            }
            $waimai_shop = K::M('waimai/waimai')->items_by_ids($shop_ids);
            foreach ($likes as $kk=>$vv){
                foreach ($waimai_shop as $kkk=>$vvv){
                    if($vv['shop_id']==$vvv['shop_id']){
                        $likes[$kk]['shop_title'] = $vvv['title'];
                    }
                }
            }
            
            $this->pagedata['likes'] = $likes;
            $cate = K::M('article/cate')->find(array('parent_id'=>0,'title'=>'头条新闻'));
            $article_cates = K::M('article/cate')->items(array('parent_id'=>$cate['cat_id'])); 
            $cat_ids = array();
            foreach($article_cates as $k=>$v){
                $cat_ids[$v['cat_id']] = $v['cat_id'];
            }
            $this->pagedata['news'] = K::M('article/article')->find(array('audit'=>1,'closed'=>0,'cat_id'=>$cat_ids));
            $this->tmpl = 'index.html';
                     
        }
    }

    protected function juli_order($a, $b)
    {
        if ($a['juli'] == $b['juli']) {
            return 0;
        }
        return ($a['juli'] < $b['juli']) ? -1 : 1;
    }
        
    public function get_addr(){
        $lat = $this->GP('lat');
        $lng = $this->GP('lng');
        $url = 'http://api.map.baidu.com/geocoder?location='.$lat.','.$lng.'&output=json&pois=1';
        $json = file_get_contents($url);
        $json = json_decode($json, true);  
        $addr= $json['result']['addressComponent']['city'];
        $this->msgbox->set_data('addr',$addr);
    }
       
    public function cookie()
    {
        $a = $this->cookie->get('UxLocation');
        $this->cookie->delete("UxLocation");
        $this->cookie->clear();
        $this->cookie->set('UxLocation', '{}');
        echo "<!doctype html><html><body>";
        echo "<pre>";
        print_r($a);
        print_r($_COOKIE);
        print_r($this->cookie->_COOKIE);
        //print_r($_SERVER);
        echo 'clear cookie success';
        echo "</pre>";
        echo "<script>localStorage={},localStorage.clear();</script></body></html>";
        exit();
    }

    // 发送卡券消息
    protected function send_member_coupon_msg($uid){
        if($uid){
            //微店  外卖
            $filter_used=$filter_guoqi = array();
            $filter_used['uid'] = $uid;
            $filter_guoqi['uid']= $uid;
            //微店优惠券已使用
            $filter_used[':OR']=array(
                'status'=>1,
                'use_time'=>'>:1',
                'order_id'=>'>:1'
            );
            //过期
            $filter_guoqi['ltime']='<:'.time();
            $items_used = K::M('member/weidiancoupon')->items($filter_used);
            foreach ($items_used as $v){
                if(!$count=K::M("member/message")->count(array('uid'=>$uid,'type'=>5,'can_id'=>$v['coupon_id']))){
                    $data=array();
                    $data['uid'] =$uid;
                    $data['title'] ='优惠券'.$v['title'].'已使用';
                    $data['type'] =5;
                    $data['order_id']=0;
                    $data['is_read'] = 0;
                    $data['can_id'] =$v['coupon_id'];
                    $data['content'] ='您的微店优惠券-'.$v['title']/*'(ID:'.$v['coupon_id'].')'*/.'已使用';
                    K::M('member/message')->create($data);
                }
            }
            $items_guoqi =  K::M('member/weidiancoupon')->items($filter_guoqi);
            foreach ($items_guoqi as $v){
                if(!$count=K::M("member/message")->count(array('uid'=>$uid,'type'=>5,'can_id'=>$v['coupon_id']))){
                    $data=array();
                    $data['uid'] =$uid;
                    $data['title'] ='优惠券'.$v['title'].'已过期';
                    $data['type'] =5;
                    $data['order_id']=0;
                    $data['is_read'] = 0;
                    $data['can_id'] =$v['coupon_id'];
                    $data['content'] ='您的微店优惠券-'.$v['title']./*'(ID:'.$v['coupon_id'].')'.*/'于'.date('Y-m-d H:i:s',$v['ltime']).'过期';
                    K::M('member/message')->create($data);
                }
            }
            //外卖优惠券
            $filter_waimai_used = $filter_waimai_guoqi = array();
            $filter_waimai_used['uid'] =$uid;
            $filter_waimai_used[':OR'] = array(
                'order_id'=>'>:0',
                'use_time'=>'>:0',
            );
            $filter_waimai_guoqi['uid'] =$uid;
            $filter_waimai_guoqi['ltime'] ="<:".time();

            //已使用
            $items_waimai_used = K::M('waimai/coupon')->items($filter_waimai_used);
            foreach ($items_waimai_used as $v){
                if(!$count=K::M("member/message")->count(array('uid'=>$uid,'type'=>6,'can_id'=>$v['coupon_id']))){
                    $data=array();
                    $data['uid'] =$uid;
                    $data['title'] ='优惠券'.$v['title'].'已使用';
                    $data['type'] =6;
                    $data['order_id']=0;
                    $data['is_read'] = 0;
                    $data['can_id'] =$v['coupon_id'];
                    $data['content'] ='您的外卖优惠券-'.$v['title'].'(ID:'.$v['coupon_id'].')于'.date('Y-m-d H:i:s',$v['use']).'已使用';
                    K::M('member/message')->create($data);
                }
            }

            //已过期
            $items_waimai_guoqi = K::M('waimai/coupon')->items($filter_waimai_guoqi);
            foreach ($items_waimai_guoqi as $v){
                if(!$count=K::M("member/message")->count(array('uid'=>$uid,'type'=>6,'can_id'=>$v['coupon_id']))){
                    $data=array();
                    $data['uid'] =$uid;
                    $data['title'] ='优惠券'.$v['title'].'已过期';
                    $data['type'] =6;
                    $data['order_id']=0;
                    $data['is_read'] = 0;
                    $data['can_id'] =$v['coupon_id'];
                    $data['content'] ='您的外卖优惠券-'.$v['title'].'(ID:'.$v['coupon_id'].')'.'已过期';
                    K::M('member/message')->create($data);
                }
            }
        }
    }

    public function gethuodong(){

        $tuan = K::M('activity/activity')->items(array('stime'=>'<:'.time(),'ltime'=>'>:'.time(),'cate_id'=>1),array('active_id'=>'DESC'),1,2,$count1);
        $shop = K::M('activity/activity')->items(array('stime'=>'<:'.time(),'ltime'=>'>:'.time(),'cate_id'=>2),array('active_id'=>'DESC'),1,2,$count2);
        $this->pagedata['tuan'] = $tuan;
        $this->pagedata['shop'] = $shop;
    }

    //异步上传文件
    public function uploadimg(){
        $upload = K::M('magic/upload')->upload($_FILES['file']);
        $this->msgbox->set_data('file',$upload);
        $this->msgbox->json();
    }

}