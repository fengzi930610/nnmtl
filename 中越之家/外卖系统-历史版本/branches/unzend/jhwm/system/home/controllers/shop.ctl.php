<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2015-07-22 01:25:14Z wanglei $
 */
if (!defined('__CORE_DIR')) {
    exit("Access Denied");
}

class Ctl_Shop extends Ctl
{
    public function index()
    {
        //查询商家分类
        $cate_list = K::M('shop/cate')->fetch_all();
        $pager = array();

        if($title = (int)$this->GP('title')){
             $pager['title'] = $title;
        }
        if($order = in_array($this->GP('order'), array('time', 'juli', 'sales', 'score', 'price'))){
            $pager['order'] = $order;
        }
        if($cate_id = $this->GP('cate_id')){
            if($cate = $cate_list[$cate_id]){
                $pager['cate'] = $cate;
                $pager['cate_id'] = $cate_id;
            }            
        }
        //筛选
        if($this->GP('is_new')) {//是否新店
            $pager['is_new'] = 1;
        }
        if($this->GP('online_pay')) {// 是否支持在线支付
            $pager['online_pay'] = 1;
        }
        if($this->GP('youhui_first')) {  // 首单优惠
            $pager['youhui_first'] = 1;
        }
        if($this->GP('youhui_order')) {  // 下单立减
            $pager['youhui_order'] = 1;
        }
        if(in_array($this->GP('pei_type'),array(1,2))) {          // 配送类型
            $pager['pei_type'] = $this->GP('pei_type');
        }
        
        if($area = K::M('data/area')->items()){
            $this->pagedata['area'] = $area;
        }
        
        if($business_items = K::M('data/business')->items()) {
            $this->pagedata['business'] = $business_items;
        }
        $this->pagedata['pager'] = $pager;
        $this->pagedata['cate_tree'] = K::M('shop/cate')->tree();

        $this->tmpl = 'shop/index.html';
    }

    public function loaditems()
    {
        
        if(!$this->checksubmit()){
            $this->msgbox->add('请求出错', -2)->response();
        }

        $lng = $this->GP('lng');
        $lat = $this->GP('lat');        
        if(!$lng || !$lat){
            $lng = $this->request['UxLocation']['lng'];
            $lat = $this->request['UxLocation']['lat'];
        }        
        
        if($lng && $lat){            
            $cate_list = K::M('shop/cate')->fetch_all();            
            $filter = $pager = $orderby = array();            
            //使用此函数计算得到结果后，带入sql查询。
            $site_config = K::M('system/config')->get('site');
            
            if($range = $this->GP('range')) {
                $distance = $range;
            }else {
                $distance = $site_config['pei_range'];
            }
            
            
            $squares = K::M('helper/round')->returnSquarePoint($lng, $lat,$distance, 5);
            $filter['lat'] = $squares['left-bottom']['lat'].'~'.$squares['right-top']['lat'];
            $filter['lng'] = $squares['left-bottom']['lng'].'~'.$squares['right-top']['lng'];
            
            // 分类条件
            if($cate_id = (int)$this->GP('cate_id')){
                if($ids = K::M('shop/cate')->children_ids($cate_id)){
                    $cate_ids = explode(',', $ids);
                }
                $cate_ids[] = $cate_id;
                $filter['cate_id'] = $cate_ids;
            }

            // 区域条件
            if($business_id = $this->GP('business_id')) {
                $filter['business_id'] = $business_id;
                unset($filter['lng']);unset($filter['lat']);
            }else if($area_id = $this->GP('area_id')) {
                $filter['area_id'] = $area_id;
                unset($filter['lng']);unset($filter['lat']);
            }

            //排序
            if($this->GP('order') == 'time') { // 送餐速度最快
                //$orderby['pei_time'] = 'ASC';
            }else if($this->GP('order') == 'sales') { // 销量最高
                $orderby['orders'] = 'DESC';
            }else if($this->GP('order') == 'score') { // 评价最好
                //$orderby['`score`/`comments`'] = 'DESC';
            }else if($this->GP('order') == 'price') { // 人均消费最低
                $orderby['avg_amount'] = 'ASC';
            }

            $page = max((int)$this->GP('page'), 1);
            $filter['audit'] = 1;
            $filter['closed'] = 0;

            if(($page <= 100) && $shop_list = K::M('shop/shop')->items($filter, $orderby, 1, 10000, $count)){
                $shop_ids = array(); 
                foreach($shop_list as $k=>$val) {
                    $val = $this->filter_fields('shop_id,city_id,city_name,juli,youhui,orders,cate_title,title,cate_id,phone,freight,logo,lat,lng,addr,score,score_fuwu,score_kouwei,pei_time,comments,praise_num,min_amount,first_amount,pei_amount,pei_type,yy_status,yy_stime,yy_ltime,is_new,online_pay,info,orders,url,have_waimai,have_tuan,have_quan,have_maidan,have_paidui,have_dingzuo,have_diancan,have_weidian,avg_amount,coupon,coupon_title,tuan_title,quan_title',$val);
                    $val['juli'] = K::M('helper/round')->juli($val['lng'], $val['lat'], $lng, $lat);  //距离
                    $val['juli_label'] = K::M('helper/format')->juli($val['juli']);
                    $val['collect'] = 0;
                    $val['url'] = $this->mklink('shop/detail', array($val['shop_id']));
                    $val['avg_score'] = ($val['score']/$val['comments']) ? round($val['score']/$val['comments'],2) : 0 ;
                    $shop_list[$k] = $val;
                    $shop_ids[$val['shop_id']] = $val['shop_id'];
                    $shop_list[$k]['verify'] = 0;
                }
                if($this->GP('order') == 'juli'){
                    uasort($shop_list, array($this, 'juli_order'));
                }else if($this->GP('order') == 'score'){
                    uasort($shop_list, array($this, 'score_order'));
                }                
                $items = array_slice($shop_list, ($page-1)*10, 10, true);
            }else {
                $items = array();
            }
            $this->msgbox->add('success');
            $this->msgbox->set_data('data', array('items'=>array_values($items)));
        }else{
            $this->msgbox->add('没有指定经纬度', 211);
        }
    }
    
    protected function score_order($a, $b)
    {
         if ($a['avg_score'] == $b['avg_score']) {
            return 0;
        }
        return ($a['avg_score'] < $b['avg_score']) ? 1 : -1;       
    }

    protected function juli_order($a, $b)
    {
        if ($a['juli'] == $b['juli']) {
            return 0;
        }
        return ($a['juli'] < $b['juli']) ? -1 : 1;
    }

    public function detail($shop_id)
    {
        //$this->check_login();
        $shop_id = (int) $shop_id;
        if(!$shop_id) {
            $this->msgbox->add('商家不存在', 301)->response();
        }else if(!$detail = K::M('shop/shop')->detail($shop_id)) {
            $this->msgbox->add('商家不存在', 302)->response();
        }else if($detail['audit'] != 1 || $detail['closed'] = 0) {
            $this->msgbox->add('商家未通过审核或商家被删除', 303)->response();
        }else{
            if($detail['have_maidan'] == 1) {
                $detail['maidan'] = K::M('maidan/maidan')->find(array('shop_id'=>$detail['shop_id']));
                $detail['maidan']['config'] = unserialize($detail['maidan']['config']);
            }
            $detail['quan'] = K::M('tuan/tuan')->find(array('type'=>'quan','shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1));
            if($detail['have_waimai']){
				$detail['waimai'] = K::M('waimai/waimai')->detail($detail['shop_id']);
                $this->pagedata['waimai'] = $detail['waimai'];
            }
            if($detail['have_quan']){
                $this->pagedata['quan_list'] = K::M('tuan/tuan')->items(array('type'=>'quan','shop_id'=>$detail['shop_id'],'closed'=>0,'is_onsale'=>1),null,1,2);
            }
            if($detail['have_tuan']){
                $this->pagedata['tuan_list'] = K::M('tuan/tuan')->items(array('type'=>'tuan','shop_id'=>$detail['shop_id'],'closed'=>0,'is_onsale'=>1),null,1,2);
            }
            if($comment_list = K::M('shop/comment')->items(array('shop_id'=>$detail['shop_id']),array('comment_id'=>'DESC'), 1, 5, $comment_total_count)){
                $uids = $comment_ids = array();
                foreach($comment_list as $k=>$v){
                    $comment_ids[$v['comment_id']] = $v['comment_id'];
                    $uids[$v['uid']] = $v['uid'];
                }
                $this->pagedata['member_list'] = K::M('member/member')->items_by_ids($uids);
                if($comment_photo_list = K::M('shop/commentphoto')->items(array('comment_id'=>$comment_ids))){
                    foreach($comment_photo_list as $k=>$v){
                        $comment_list[$v['comment_id']]['photos'][$k] = $v;
                    }
                }               
                $this->pagedata['comment_list'] = $comment_list;
                $pager['comment_total_count'] = $comment_total_count;
            }
            $detail['collect'] = 0;
            if($this->uid) {
                if(K::M('member/collect')->count(array('uid'=>$this->uid,'type'=>1,'can_id'=>$shop_id,'status'=>1))){
                    $detail['collect'] = 1;
                }
            }
            foreach($detail['waimai']['freight_stage'] as $vv) {
                $km[] = $vv['fkm'];
            }
            $detail['score'] = ($detail['score']/$detail['comments']) ? round($detail['score']/$detail['comments'],2) : 0 ;
            $detail['waimai']['km'] = max($km);
        }
        $this->pagedata['pager'] = $pager;
        $this->pagedata['detail'] = $detail;
        $this->pagedata['domain'] = __CFG::C_DOMAIN;
        $this->tmpl = 'shop/detail.html';
    }

    public function product($product_id) {
        $product_id = (int) $product_id;
        if (!$product_id) {
            $this->msgbox->add('商品不存在', 301);
        } else if (!$product = K::M('product/product')->detail($product_id)) {
            $this->msgbox->add('商品不存在', 302);
        } else if ($product['closed'] = 0) {
            $this->msgbox->add('该商品已下架', 303);
        }
        $carts = $this->getcart($product['shop_id']);
        if ($carts[$product_id]['cart_num'] > 0) {
            $product['cart_num'] = $carts[$product_id]['cart_num'];
        } else {
            $product['cart_num'] = 0;
        }
        if ($product['sale_type'] == 1) {
            if ($product['sale_sku'] - $product['sale_count'] > 0) {
                $product['sku'] = $product['sale_sku'] - $product['sale_count'];
            } else {
                $product['sku'] = 0;
            }
        } else {
            $product['sku'] = 999;
        }
        $total = 0;
        foreach ($carts as $k=>$val){
            $total += $val['cart_num'];
        }
        $this->pagedata['total'] = $total;
        $this->pagedata['product'] = $product;
        $detail = K::M('shop/shop')->detail($product['shop_id']);
        $this->pagedata['detail'] = $detail;
        $this->tmpl = 'shop/product.html';
    }

    public function shop($shop_id) {   //商家
        $shop_id = (int) $shop_id;
        if (!$shop_id) {
            $this->msgbox->add('商家不存在', 301);
        } else if (!$detail = K::M('shop/shop')->detail($shop_id)) {
            $this->msgbox->add('商家不存在', 302);
        } else if ($detail['audit'] != 1 || $detail['closed'] = 0) {
            $this->msgbox->add('商家未通过审核或商家被删除', 303);
        }
        if ($detail['youhui']) {
            $youhui = unserialize($detail['youhui']);
            $str = "在线支付";
            foreach ($youhui as $kk => $v) {
                $str .= "满" . (int) $v['order_amount'] . '减' . (int) $v['youhui_amount'] . ',';
            }
        }
        $detail['youhui'] = substr($str, 0, -1);
        if($res = K::M('shop/collect')->find(array('uid'=>$this->uid,'shop_id'=>$shop_id))){
            $detail['collect'] = 1;
        }else{
            $detail['collect'] = 0;
        }
        $this->pagedata['detail'] = $detail;
        $this->tmpl = 'shop/shop.html';
    }

    public function comment($shop_id) {    //点评
        $shop_id = (int) $shop_id;
        if (!$shop_id) {
            $this->msgbox->add('商家不存在', 301);
        } else if (!$detail = K::M('shop/shop')->detail($shop_id)) {
            $this->msgbox->add('商家不存在', 302);
        } else if ($detail['audit'] != 1 || $detail['closed'] = 0) {
            $this->msgbox->add('商家未通过审核或商家被删除', 303);
        }
        $detail['avg_score'] = round($detail['score']/$detail['comments'], 2);
        $this->pagedata['detail'] = $detail;
        $this->tmpl = 'shop/comment.html';
    }

    public function commentitems() {  //加载评论
        $shop_id = (int)$this->GP('shop_id');
        $page = max((int)$this->GP('page'), 1);
 
        if( ($page <= 10) && $comment_list = K::M('shop/comment')->items(array('shop_id'=>$shop_id), array('comment_id'=>'desc'), 1, 10000, $count)){
            $comment_ids = array();
            foreach ($comment_list as $k=>$val){
                $comment_ids[$val['comment_id']] = $val['comment_id'];
                $uids[] = $val['uid'];
            }
            $photo_list = K::M('shop/commentphoto')->items(array('comment_id'=>$comment_ids));
            foreach($photo_list as $kk=>$v){
                $comment_list[$v['comment_id']]['photos'][] = $v;
            }
            foreach ($comment_list as $k=>$val){
                $items[] = $this->filter_fields('comment_id,shop_id,uid,order_id,score,score_fuwu,score_kouwei,pei_time,content,reply,reply_time,dateline,photos', $val);
            }                
            foreach($items as $k => $v){
                $detail = K::M('member/member')->detail($v['uid']);
                $items[$k]['nickname'] = $detail['nickname'];
                $items[$k]['face'] = $detail['face'];
                $items[$k]['reply_time'] = date('Y-m-d H:i:s',$v['reply_time']);
                $items[$k]['dateline'] = date('Y-m-d H:i:s',$v['dateline']);
            } 
            $items = array_slice($items, ($page-1)*10, 10, true);              
        }else{
            $items = array();
        }
        $this->msgbox->add('success');
        $this->msgbox->set_data('data', array('items'=>array_values($items)));     
    }


    public function collect($shop_id)
    {
        $this->check_login();
        if(!$shop_id = (int)$shop_id) {
            $this->msgbox->add('该商家不存在',202);
        }else if(!$detail = K::M('shop/shop')->detail($shop_id)){
            $this->msgbox->add('该商家不存在',203);
        }else if(empty($detail['audit'])) {
            $this->msgbox->add('商家未审核',204);
        }else if($result = K::M('shop/collect')->find(array('uid'=>$this->uid,'shop_id'=>$shop_id))){
            $this->msgbox->add('您已经收藏过该商家了',205);
        }else if(K::M('shop/collect')->create(array('uid'=>$this->uid,'shop_id'=>$shop_id))){
            $this->msgbox->add('收藏成功');
        }else{
            $this->msgbox->add('收藏成功');
        }
    }

    public function cancel($shop_id)
    {
        $this->check_login();
        if(!$shop_id = (int)$shop_id) {
            $this->msgbox->add('该商家不存在',202);
        }else if(!$detail = K::M('shop/shop')->detail($shop_id)){
            $this->msgbox->add('该商家不存在',203);
        }else if($detail['audit'] !=1||$detail['closed'] !=0) {
            $this->msgbox->add('该商家不存在或已被删除',204);
        }else if(!$result = K::M('shop/collect')->find(array('uid'=>$this->uid,'shop_id'=>$shop_id))){
            $this->msgbox->add('您还没有收藏该商家',205);
        }else {
            if(K::M('shop/collect')->delete('shop_id='.$shop_id.' and uid='.$this->uid)){  //
                $this->msgbox->add('取消收藏成功');
            }
        }
    }
    /* 优惠买单 */
    public function youhui($shop_id = null)
    {
        $this->check_login();
        if(!$shop_id){
            $this->msgbox->add('参数不正确',205);
        }else if(!$detail=K::M('shop/shop')->detail($shop_id)){
            $this->msgbox->add('非法操作',206);
        }else if($detail['have_maidan'] != 1){
            $this->msgbox->add('该商户还没开通优惠买单！',207);
        }else{
            $options = K::M('maidan/maidan')->find(array('shop_id'=>$shop_id));
            $this->pagedata['detail'] = $detail;
            if($options){ 
                $options['config'] = unserialize($options['config']);
                $this->pagedata['options'] = $options;
                $this->tmpl = 'shop/youhui.html';        //开通了买单功能并且设置了买单优惠
            }else{
                $this->tmpl = 'shop/youhui_no.html';     //商户开通了买单功能但是没有设置买单优惠
            }
        }
    }

    public function ajax_youhui($shop_id){
        $data = $this->GP('data');
        if(!$shop_id){
            $this->msgbox->add('参数不正确',211);
        }else if(!$shop = K::M('shop/shop')->detail($shop_id)){
            $this->msgbox->add('商铺不存在或者已经删除',212);
        }else if(!$shop['have_maidan']){
            $this->msgbox->add('商家没有开通买单功能',213);
        }else if($data['money']<=0){
            $this->msgbox->add('金额不正确',214);
        }else if($data['money']<$data['no_money']){
            $this->msgbox->add('不参与优惠金额不能大于消费金额',215);
        }else{

            $youhui_amount = K::M('maidan/maidan')->get_maidan_youhui($shop_id, $data['money']-$data['no_money']);
            $this->msgbox->set_data('money', $youhui_amount);
        }

    }
    
    public function youhui_check($shop_id, $money)
    {
        $this->check_login();
        if(!$shop_id){
            $this->msgbox->add('参数不正确',211);
        }else if(!$shop = K::M('shop/shop')->detail($shop_id)){
            $this->msgbox->add('店铺不存在或已经删除',212);
        }else if(!$shop['have_maidan']){
            $this->msgbox->add('商户没有开通买单功能',213);
        }else{
            $youhui_amount = K::M('maidan/maidan')->get_maidan_youhui($shop_id, $money);
            $this->msgbox->set_data('money', $money-$youhui_amount);
        }
    }

    public function youhui_create()
    {
        $this->check_login();
        if(!$shop_id = (int)$this->GP('shop_id')) {
            $this->msgbox->add('该商家不存在',210);   
        }else if(!$shop = K::M('shop/shop')->detail($shop_id)){
            $this->msgbox->add('该商家不存在',210);  
        }else if(!$money = (float)$this->GP('money')) {
            $this->msgbox->add('消费金额不正确',211);
        }else if(($no_money = (float)$this->GP('no_money')) && $no_money > $money){
            $this->msgbox->add('不享受优惠的金额不正确',213);
        }else if($shop['have_maidan'] == 0){
            $this->msgbox->add('商家还未开通优惠买单',214);
        }else{
            $youhui_amount = K::M('maidan/maidan')->get_maidan_youhui($shop_id, $money-$no_money);
            $data = array(
                'city_id'            => $shop['city_id'],
                'shop_id'            => $shop_id,
                'staff_id'           => 0,
                'uid'                => $this->uid,
                'contact'            => $this->MEMBER['nickname'],
                'mobile'             => $this->MEMBER['mobile'],
                'total_price'        => $money,
                'amount'             => $money - $youhui_amount,
                'order_youhui'       => $youhui_amount,
                'from'               => 'maidan',
                'note'               => '优惠买单支付',
                'order_from'         => (defined('IN_WEIXIN') ? 'weixin' : 'wap')
            );
            if($order_id = K::M('order/order')->create($data)) {
                K::M('maidan/order')->create(array('order_id'=>$order_id,'maidan_amount'=>$money,'unyouhui'=>$no_money)); 
				$this->msgbox->add('恭喜您下单成功');
                $this->msgbox->set_data('data', array('order_id'=>$order_id));
            }else{
                $this->msgbox->add('下单失败',401);
            }
        }
    }
    
    /**
     * 商户相册
     * @param   int  $shop_id  
     * @param   int  $cate_id  1:环境,2:商品
     */
    public function album($shop_id=null, $cate_id=null)
    {
        if($shop_id = (int)$shop_id) {
            $filter['shop_id'] = $shop_id;
        }
        if($cate_id = (int)$cate_id) {
            $type = $filter['type'] = $cate_id;
        }else if($cate_id = null) {
            $filter['type'] = array(1,2);
            $type = 0;
        }
        $shop_photo_items = K::M('shop/albumphoto')->items($filter,array('photo_id'=>'desc'),1,500,$count);
        $this->pagedata['items'] = $shop_photo_items;
        $this->pagedata['shop_id'] = $shop_id;
        $this->pagedata['type'] = $type;
        $this->tmpl = 'shop/album.html';
    }

    /**
     * 商户在地图上的地理位置
     * @param   int  $shop_id  
     */
    public function location($shop_id=null)
    {
        if($shop_id = (int)$shop_id) {
            if($detail = K::M('shop/shop')->detail($shop_id)) {
                $this->pagedata['shop'] = $detail;
            }
        }
        $this->tmpl = 'shop/location.html';
    }
}
