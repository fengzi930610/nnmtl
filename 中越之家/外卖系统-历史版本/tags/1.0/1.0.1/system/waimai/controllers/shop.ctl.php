<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 * check view code by shzhrui
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shop extends Ctl
{
    /* 外送商家列表
     * #cate_id,lng,lat,order,is_new,online_pay,youhui_first,youhui_order,pei_type,title,page
     */
    public function index($cate_id)
    {
        $cate_list = K::M('waimai/cate')->fetch_all();
        if($cate_id = (int)$cate_id){
            $this->pagedata['cate_id'] = $cate_id;
        }else {
            $this->pagedata['cate_id'] = 0;
        }
        $this->pagedata['cate_tree'] = K::M('waimai/cate')->tree();

        $this->tmpl = 'shop/index.html';
    }

    // 下拉加载商家
    public function loadshopitems()
    {
        $filter = $pager = $orderby = array();
        $filter = array('closed'=>0, 'audit'=>1);
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
            $cate_list = K::M('waimai/cate')->fetch_all();            
            $squares = K::M('helper/round')->returnSquarePoint($lng, $lat);
            $filter['lat'] =$squares['left-bottom']['lat'].'~'.$squares['right-top']['lat'];
            $filter['lng'] = $squares['left-bottom']['lng'].'~'.$squares['right-top']['lng'];
        }
        if($cate_id = (int)$this->GP('cate_id')){
            if($ids = K::M('shop/cate')->children_ids($cate_id)){
                $cate_ids = explode(',', $ids);
            }
            $cate_ids[] = $cate_id;
            $filter['cate_id'] = $cate_ids;
        }   

        if($this->GP('order') == 'time') { 
            $orderby['pei_time'] = 'ASC';
        }else if($this->GP('order') == 'sales') { 
            $orderby['orders'] = 'DESC';
        }else if($this->GP('order') == 'score') { 
            $orderby['score'] = 'DESC';
        }else if($this->GP('order') == 'price') { 
            $orderby['min_amount'] = 'ASC';
        }
        if($this->GP('sort') == 'is_new') {
            $filter['is_new'] = 1;
        }
        if($this->GP('sort') == 'online_pay') {
            $filter['online_pay'] = 1;
        }
        if($this->GP('sort') == 'first_amount') {  
            $filter['first_amount'] = '>:0';
        }
        if($this->GP('sort') == 'youhui_order') {  
            $filter[':SQL'] = "youhui !=''";
        }
        
        $page = max((int)$this->GP('page'), 1);        
        if($page <= 100 && $waimai_items = K::M('waimai/waimai')->items($filter, $orderby, 1, $limit, $count)) {
            $shop_ids = array();
            foreach($waimai_items as $k=>$val) {
                $val['juli'] = (int)K::M('helper/round')->juli($val['lng'], $val['lat'], $lng, $lat);  // 用户与商户的距离米
                $val['juli_label'] = K::M('helper/format')->juli($val['juli']);
                $val['score'] = ($val['score']/$val['comments']) ? round($val['score']/$val['comments'],2) : 0 ;
                $val['url'] = $this->mklink('waimai/product:index', array($val['shop_id']));
                $waimai_items[$k] = $val; 
            }
            $items = $waimai_items;
            if($this->GP('order') == 'juli') {
                uasort($items, array($this, 'juli_order'));   
            }    
            $items = array_slice( $items, ($page-1)*10, 10, true);  // 每次取10条记录，偏移量为$page-1
        }

        $this->msgbox->add('success');
        $this->msgbox->set_data('data', array('items'=>array_values($items)));
    }

    /*商家详情页*/
    /*public function detail($shop_id = null)
    {
        $lng = (float)$this->request['UxLocation']['lng'];
        $lat = (float)$this->request['UxLocation']['lat'];

        if(!$shop_id = (int)$shop_id){
           $this->msgbox->add('商家不存在',280);
        }else if(!$detail = K::M('waimai/waimai')->detail($shop_id)){
           $this->msgbox->add('商家不存在',281)->response();
        }
        if($this->GP('is_must')){
            $this->pagedata['must_select'] = 1;
        }else{
            $this->pagedata['must_select'] = 0;
        }
        $detail = K::M('waimai/waimai')->format_data($detail);
        $this->pagedata['yystat'] = $detail['yy_status'];

        $detail['youhui_label'] = $detail['youhui_title'];// _format_row 已经处理过了
        $detail['distance'] = K::M('helper/round')->juli($detail['lng'], $detail['lat'], $lng, $lat);
        $detail['juli_label'] = K::M('helper/format')->juli($detail['distance']);
        $detail['collect'] = 0;
        if($this->uid) {
            if(K::M('member/collect')->count(array('uid'=>$this->uid,'type'=>'waimai','can_id'=>$shop_id,'status'=>1))){
                $detail['collect'] = 1;
            }
        }

        //外卖优惠券--start
        $coupons = K::M('waimai/huodongcoupon')->items(array('shop_id'=>$shop_id,'audit'=>1,'closed'=>0,'num' => '>:0', 'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
        $coupon_amount = 0;
        $coupon_num = 0;
        $str = '';
        $coupons = array_values($coupons);
        foreach ($coupons[0]['config'] as $v){
            if($v['coupon_amount']){
                $coupon_amount+=$v['coupon_amount'];
                $coupon_num++;
                $str = '满'.$v['order_amount'].'减'.$v['coupon_amount'];
            }

        }
        $coupon_format = array(
            'num'=>$coupon_num,
            'amount'=>$coupon_amount,
            'str'=>$str
        );
        $area_price = K::M('waimai/waimai')->get_shipping_fee($detail['area_polygon'], $lat, $lng);
        if($detail['pei_type']==0){
            $detail['freight'] = $area_price['shipping_fee'];// 兼容旧版，重新赋值配送费
            $detail['min_amount'] = $area_price['min_price'];// 兼容旧版，重新赋值起送价
        }else{
            $jili = (int)K::M('helper/round')->juli($detail['lng'], $detail['lat'], $lng, $lat);  // 用户与商户的距离米
            $group = K::M("pei/group")->detail($detail['group_id']);
            //新增商家读取单独配置
            if($detail['is_separate']==0){
                $detail['min_amount'] = $group['min_amount'];
            }
            if($detail['is_separate']==1&&$detail['config']){
                $detail['freight'] =   K::M('waimai/waimai')->shipping_fee_by_type($detail['config'],$jili);
            }else{
                $detail['freight'] =   K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($detail['group_id']),$jili);
            }

        }
        $this->pagedata['coupon'] = $coupon_format;
        //首页进入的商品
        if($pid = (int)$this->GP('pid')){
            $this->pagedata['pid'] = $pid;
        }
        //修改分类显示时间 20171120 begin --叶超

        $waimai_cates = K::M('waimai/productcate')->items(array('shop_id'=>$shop_id,'parent_id'=>0),array());
        foreach ($waimai_cates as $k2=>$v2){
            if($v2['hidden']==1){
                unset($waimai_cates[$k2]);
            }
        }
        $this->pagedata['waimai_cates'] = $waimai_cates;
        // K::M('waimai/productcate')->items(array('shop_id'=>$shop_id),array('cate_id'=>'desc'));

        //修改分类显示时间 20171120 end --叶超

        //新增折扣商品
        $is_discount = 0;
        $disc_pids = array();
        if(!empty($detail['discount'])){
            $is_discount = 1;
            $disc_pids = array_keys($detail['discount']['products']);
        }
        $this->pagedata['json_discount'] = json_encode($detail['discount']);
        $this->pagedata['is_discount'] = $is_discount;

        //$filter = array('shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1,'price'=>"<=:".$detail['min_amount'],'is_spec'=>0,'');
        $filter = array('shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1,'is_spec'=>0,'product_id'=>'NOTIN:'.implode(',',$disc_pids));
        $this->pagedata['items2'] = $items2 = K::M('waimai/product')->items($filter,array('price'=>'asc'),1,10);
        $detail['avg_time'] = $detail['pei_time'];

        $this->pagedata['detail']  = $detail;
        $this->pagedata['shop_id'] = $shop_id;
        //购物车
        $hot = K::M('waimai/product')->count(array('shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1,'is_hot'=>1));
        $this->pagedata['count_hot'] = $hot>0?true:false;
        $this->pagedata['shop'] =  K::M('shop/shop')->detail($shop_id);
        //增加判断商家是否有必点商品
        
        $must_count = K::M('waimai/product')->items(array('shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1,'is_must'=>1),array('product_id'=>"DESC"),1,100,$count);
        $must_ids = array();
        foreach($must_count as $k=>$v){
            $must_ids[] = $v['product_id'];
        }
        if($must_count){
            $this->pagedata['is_must'] = 1;
        }else{
            $this->pagedata['is_must'] = 0;
        }

        //4.0用户浏览记录
        if($this->uid){           
            K::M('waimai/views')->update_views($shop_id, $this->uid);
        }

        $this->pagedata['json_product'] = json_encode($must_ids);
        $this->tmpl = 'shop/detail.html';
    }*/

    public function detail($shop_id = null)
    {
        $lng = (float)$this->request['UxLocation']['lng'];
        $lat = (float)$this->request['UxLocation']['lat'];
        if(!$lng||!$lat){
            $lng = trim($_COOKIE['lng']);
            $lat = trim($_COOKIE['lat']);
        }
        if(!$lng||!$lat){
            $UxLocation = $_COOKIE['KT-UxLocation'];
            $UxLocations = explode(',',$UxLocation);
            $lat = $UxLocations[0];
            $lng = $UxLocations[1];
        }
        if(!$lng||!$lat){
            $uxlocal = $this->GP('uxlocal');
            $uxlocals = explode(',',$uxlocal);
            $lng = $uxlocals[0];
            $lat = $uxlocals[1];
        }    
        if(!$shop_id = (int)$shop_id){
           $this->msgbox->add('商家不存在',280);
        }else if(!$detail = K::M('waimai/waimai')->detail($shop_id)){
           $this->msgbox->add('商家不存在',281)->response();
        }
        if($this->GP('is_must')){
            $this->pagedata['must_select'] = 1;
        }else{
            $this->pagedata['must_select'] = 0;
        }
        $detail = K::M('waimai/waimai')->format_data($detail);
        $huodong = K::M('waimai/waimai')->get_huodong($shop_id);
        $detail['huodong'] = (array)$huodong[$shop_id];
        //echo '<pre>';print_r($detail);die;
        
        $this->pagedata['yystat'] = $detail['yy_status'];

        $detail['youhui_label'] = $detail['youhui_title'];// _format_row 已经处理过了
        $detail['distance'] = K::M('helper/round')->juli($detail['lng'], $detail['lat'], $lng, $lat);
        $detail['juli_label'] = K::M('helper/format')->juli($detail['distance']);
        $detail['collect'] = 0;
        if($this->uid) {
            if(K::M('member/collect')->count(array('uid'=>$this->uid,'type'=>'waimai','can_id'=>$shop_id,'status'=>1))){
                $detail['collect'] = 1;
            }
        }

        //外卖优惠券--start
        $coupon_filter = array('shop_id'=>$shop_id,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME);
        $coupon_filter[':SQL'] = " (`num`>0 OR `num`=-1)";
        $coupons = K::M('waimai/huodongcoupon')->items($coupon_filter);
        $coupon_amount = 0;
        $coupon_num = 0;
        $str = '';
        $coupons = array_values($coupons);
        foreach ($coupons[0]['config'] as $v){
            if($v['coupon_amount']){
                $coupon_amount+=$v['coupon_amount'];
                $coupon_num++;
                $str = '满'.$v['order_amount'].'减'.$v['coupon_amount'];
            }

        }
        $coupon_format = array(
            'num'=>$coupon_num,
            'amount'=>$coupon_amount,
            'str'=>$str
        );
        $this->pagedata['coupon'] = $coupon_format;

        $detail['is_distance'] = 1; //4.1超出配送范围
        $area_price = K::M('waimai/waimai')->get_shipping_fee($detail['area_polygon'],$lat,$lng);
        if($detail['pei_type']==0){
            if($area_price){
                $detail['freight'] = $area_price['shipping_fee'];// 兼容旧版，重新赋值配送费
                $detail['min_amount'] = $area_price['min_price'];// 兼容旧版，重新赋值起送价
                $detail['is_distance'] = 0;
            }
        }else{
            $group = K::M("pei/group")->detail($detail['group_id']);                
            if(K::M('helper/round')->in_or_out_polygon($group['polygon_point'],$lat,$lng)){
                $detail['is_distance'] = 0;
                $jili = (int)K::M('helper/round')->juli($detail['lng'], $detail['lat'], $lng, $lat);  // 用户与商户的距离米
                if($detail['is_separate']==0){
                    $detail['min_amount'] = $group['min_amount'];
                }
                //新增商家单独配置 20171024 叶超 -begin
                if($detail['is_separate']==1&&$detail['config']){
                    $detail['freight'] =   K::M('waimai/waimai')->shipping_fee_by_type($detail['config'],$jili);
                }else{
                    $detail['freight'] =   K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($detail['group_id']),$jili);
                }
            }
        }
        
        //首页进入的商品
        if($pid = (int)$this->GP('pid')){
            $this->pagedata['pid'] = $pid;
        }
        //修改分类显示时间 20171120 begin --叶超

        $waimai_cates = K::M('waimai/productcate')->items(array('shop_id'=>$shop_id,'parent_id'=>0),array());
        foreach ($waimai_cates as $k2=>$v2){
            if($v2['hidden']==1){
                unset($waimai_cates[$k2]);
            }
        }
        $this->pagedata['waimai_cates'] = $waimai_cates;
        // K::M('waimai/productcate')->items(array('shop_id'=>$shop_id),array('cate_id'=>'desc'));

        //修改分类显示时间 20171120 end --叶超

        //新增折扣商品
        $is_discount = 0;
        $disc_pids = array();
        if(!empty($detail['discount'])){
            $is_discount = 1;
            $disc_pids = array_keys($detail['discount']['products']);
        }
        $this->pagedata['json_discount'] = json_encode($detail['discount']);
        $this->pagedata['is_discount'] = $is_discount;

        $filter = array('shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1,'is_spec'=>0,'product_id'=>'NOTIN:'.implode(',',$disc_pids));
        $this->pagedata['items2'] = $items2 = K::M('waimai/product')->items($filter,array('price'=>'asc'),1,10);
        $detail['avg_time'] = $detail['pei_time'];

        $this->pagedata['detail']  = $detail;
        $this->pagedata['shop_id'] = $shop_id;
        //购物车
        $hot = K::M('waimai/product')->count(array('shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1,'is_hot'=>1));
        $this->pagedata['count_hot'] = $hot>0?true:false;
        $this->pagedata['shop'] =  K::M('shop/shop')->detail($shop_id);
        //增加判断商家是否有必点商品
        
        $must_count = K::M('waimai/product')->items(array('shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1,'is_must'=>1),array('product_id'=>"DESC"),1,100,$count);
        $must_ids = array();
        foreach($must_count as $k=>$v){
            $must_ids[] = $v['product_id'];
        }
        if($must_count){
            $this->pagedata['is_must'] = 1;
        }else{
            $this->pagedata['is_must'] = 0;
        }
        $this->pagedata['json_product'] = json_encode($must_ids);

        //4.0用户浏览记录
        if($this->uid){           
            K::M('waimai/views')->update_views($shop_id, $this->uid);
        }

        //4.0店铺广告位
        $adv_filter = array('shop_id'=>$shop_id, 'stime'=>'<=:'.__TIME, 'ltime'=>'>:'.__TIME, 'closed'=>0);
        if($advs = K::M('waimai/adv')->items($adv_filter)){
            foreach ($advs as $k => $v) {
                $v['photo'] = K::M('magic/upload')->geturl($v['photo']);
                $advs[$k] = $this->filter_fields('adv_id,title,link,photo', $v);
            }
        }else{
            $advs = array();
        }
        $advs = array_values($advs);
        $this->pagedata['advs'] = $advs;

        //4.0推荐商品
        $tj_filter = array('shop_id'=>$shop_id, 'closed'=>0,'is_onsale'=>1, 'is_tuijian'=>1);
        if($tj_products = K::M('waimai/product')->items($tj_filter, array('product_id'=>'desc'), 1, 50, $tj_count)){
            foreach ($tj_products as $k => $v) {
                $v['discount'] = array();
                $v['json_discount'] = K::M('utility/json')->encode($dp);
                $v['is_specification'] = $v['specification'] ? 1 : 0;
                $v['specification']  =  K::M('utility/json')->encode(array_values($v['specification']));            
                $v['pcate_id'] = implode(',',$v['cate_ids']);
                $v['photo'] = $v['photo'];
                $tj_products[$k] = K::M('waimai/huodongdiscount')->get_newProduct(array(), $v);
            }
        }else{
            $tj_products = array(); 
        }
        //echo '<pre>';print_r($tj_products);die;
        $this->pagedata['tj_products'] = $tj_products;
        
        $this->tmpl = 'shop/detail.html';
    }

    public function loadgoods($page=1)
    {
        $shop_id = (int)$this->GP('shop_id');
        $filter = array('shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1);
        if($cate_id = (int)$this->GP('cate_id')){
            /*if($cate_ids = K::M('waimai/productcate')->getChildren($cate_id,true)){
                $filter['cate_id'] = $cate_ids;
            }else{
                $filter['cate_id'] = $cate_id;
            }*/
            $cate_ids = K::M('waimai/productcate')->getChildren($cate_id,true);//v3.6 商品多分类
            $filter[':OR'] = array(
                'cate_ids'=>'LIKE:%,'.$cate_id.',%',
                'cate_id'=>$cate_ids
                );
        }

        if($this->GP('cate_id')=='hot'){
            $filter['is_hot'] = 1;
        }else if($this->GP('cate_id')=='must'){
            $filter['is_must'] = 1;
        }

        //折扣商品
        /*if($discount = K::M('waimai/huodongdiscount')->find(array('shop_id'=>$row['shop_id'],'audit'=>1,'closed'=>0,'ltime'=>'>=:'.__TIME,'stime'=>'<=:'.__TIME))){
            $now_week = $now_week = date('w',__TIME);
            if(in_array($now_week,$discount['period_weeks']) && strtotime($discount['period_times']['stime']) <= __TIME && strtotime($discount['period_times']['ltime']) >= __TIME){
                $disc_pros = K::M('waimai/discountproduct')->items(array('huodong_id'=>$discount['huodong_id']));
            }
        }*/
        $discount = K::M('waimai/huodongdiscount')->get_discount($shop_id);

        if($this->GP('cate_id')=='discount'){
            if($discount){                
                $filter['product_id'] = array_keys($discount['products']);
            }
        }

        if($title = htmlspecialchars($this->GP('title'))){
            $filter['title'] = "LIKE:%".$title."%";
        }
        $page = max((int)$page, 1);
        $limit = 500;
        //$orderby = array('cate_id'=>'desc');
        if(!$items = K::M('waimai/product')->items($filter,$orderby,$page, $limit, $count)){
            $items = array();
        }
        
        /*$cates = K::M('waimai/productcate')->items(array('shop_id'=>$shop_id),array('cate_id'=>'desc'));
        foreach ($items as $ks=>$vs){
            if($cates[$vs['cate_id']]['hidden']==1 && $vs['is_hot'] == 0){
                unset($items[$ks]);
            }
        }*/

        foreach($items as $k=>$v){
            /*$items[$k]['specification']  =  K::M('utility/json')->encode(array_values($v['specification']));
            $items[$k]['is_specification'] = $v['specification']?1:0;
            $items[$k]['pcate_id'] = implode(',',$v['cate_ids']);*/
            
            $v['is_discount'] = 0;
            $v['oldprice'] = $v['price'];
            $v['disctype'] = 0;
            $v['discval'] = 10;
            $v['quota'] = 0;
            $v['huodong_id'] = 0;
            $v['disclabel'] = '';
            $v['quotalabel'] = '';
            if($dp = $discount['products'][$v['product_id']]){
                $v['is_discount'] = 1;
                $dp['discount_type'] = $dp['discount_type'];
                if($dp['discount_type']){
                    $dp['disc_price'] = $v['price'] - $dp['discount_value'];
                    $v['disclabel'] = '减价';
                }else{
                    $dp['disc_price'] = $v['price']*$dp['discount_value'];
                    $dp['discount_value'] = sprintf("%.1f",$dp['discount_value']*10);
                    $v['disclabel'] = $dp['discount_value'].'折';
                }
                $v['sale_sku'] = $dp['sale_sku'];
                $v['sale_type'] = 1;
                $v['price'] = max(0,sprintf("%.2f",$dp['disc_price']));
                $v['disctype'] = $dp['discount_type'];
                $v['discval'] = $dp['discount_value'];
                $v['quota'] = $dp['quota'];
                $v['huodong_id'] = $dp['huodong_id'];
                $v['quotalabel'] = '不限购';
                if($v['quota'] > 0){
                    $v['quotalabel'] = '限购'.$v['quota'].'份';
                }          
            }else{
                $dp = array();
            }
            $v['discount'] = $dp;
            $v['json_discount'] = K::M('utility/json')->encode($dp);
            $v['is_specification'] = $v['specification']?1:0;
            $v['specification']  =  K::M('utility/json')->encode(array_values($v['specification']));            
            $v['pcate_id'] = implode(',',$v['cate_ids']);
            $v['photo'] = $v['photo'];
            $items[$k] = $v;
            /*foreach($cates as $k1=>$v1){
                if($v['cate_id'] == $v1['cate_id']){
                    if($v1['parent_id']==0){
                        $items[$k]['pcate_id'] = $v1['cate_id'];
                    }else{
                        $items[$k]['pcate_id'] = $v1['parent_id'];
                    }
                }
            }*/
            
        } //取消cookie购物车相关功能
        //$count_num = K::M('waimai/product')->count($filter);
        if($count <= $limit){
            $loadst = 0; 
        }else{
            $loadst = 1;
        }
        //echo '<pre>';print_r($items);die;
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = 'shop/loadgoods.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }

    public function comment($shop_id)
    {
        if(!$shop_id = (int)$shop_id){
          $this->msgbox->add('商家不存在',282);
        }else if(!$detail = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('商家不存在',283);
        }else{
            $lng = $this->request['UxLocation']['lng'];
            $lat = $this->request['UxLocation']['lat'];        

            $detail['collect'] = 0;
            if($this->uid) {
                if(K::M('member/collect')->count(array('uid'=>$this->uid,'type'=>'waimai','can_id'=>$shop_id,'status'=>1))){
                    $detail['collect'] = 1;
                }
            }

            $area_price = K::M('waimai/waimai')->get_shipping_fee($detail['area_polygon'], $lat, $lng);
            if($detail['pei_type']==0){
                $detail['freight'] = $area_price['shipping_fee'];// 兼容旧版，重新赋值配送费
                $detail['min_amount'] = $area_price['min_price'];// 兼容旧版，重新赋值起送价
            }else{
                $jili = (int)K::M('helper/round')->juli($detail['lng'], $detail['lat'], $lng, $lat);  // 用户与商户的距离米           
                //新增商家读取单独配置
                if($detail['is_separate']==0){
                    $group = K::M("pei/group")->detail($detail['group_id']);
                    $detail['min_amount'] = $group['min_amount'];
                }

                if($detail['is_separate']==1&&$detail['config']){
                    $detail['freight'] = K::M('waimai/waimai')->shipping_fee_by_type($detail['config'],$jili);
                }else{
                    $detail['freight'] = K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($detail['group_id']),$jili);
                }
            }
            $detail['avg_score'] = ($detail['score']/$detail['comments']) ? round($detail['score']/$detail['comments'],2) : 0 ;
            if($detail['avg_score']<0){
                $detail['avg_score'] = 0;
            }else if($detail['avg_score']>=5){
                $detail['avg_score'] = 5;
            }

            $huodong = K::M('waimai/waimai')->get_huodong($shop_id);
            $detail['huodong'] = (array)$huodong[$shop_id];

            $this->pagedata['detail']  = $detail;
            $this->pagedata['shop_id'] = $shop_id;
            $this->pagedata['shop'] =  K::M('shop/shop')->detail($shop_id);
            $this->pagedata['count_1'] = K::M('waimai/comment')->count(array('closed'=>0,'shop_id'=>$shop_id));
            $this->pagedata['count_2'] = K::M('waimai/comment')->count(array('closed'=>0,'shop_id'=>$shop_id,'score'=>'>:3'));
            $this->pagedata['count_3'] = K::M('waimai/comment')->count(array('closed'=>0,'shop_id'=>$shop_id,'score'=>3));
            $this->pagedata['count_4'] = K::M('waimai/comment')->count(array('closed'=>0,'shop_id'=>$shop_id,'score'=>'<:3'));
            $this->pagedata['count_5'] = K::M('waimai/comment')->count(array('closed'=>0,'shop_id'=>$shop_id,'have_photo'=>1));
            $this->tmpl = 'shop/comment.html';
        }
    }

    public function loaditems($page=1)
    {
        $filter = array('closed'=>0);
        $page = max((int)$page, 1);
        $limit = 10;
        if($shop_id = (int)$this->GP('shop_id')){
            $filter['shop_id'] = $shop_id;
        }
        if($st = $this->GP('st')){
            if($st==1){
                $filter['score'] = ">:3";
            }elseif($st==2){
                $filter['score'] = 3;
            }elseif($st==3){
                $filter['score'] = "<:3";
            }elseif($st==4){
                $filter['have_photo'] = 1;
            }
        }
        if($is_null = $this->GP('is_null')){
            $filter[':SQL'] = "content !=''";
        }
        if(!$items = K::M('waimai/comment')->items($filter,array('comment_id'=>'desc'),$page, $limit, $count)){
            $items = array();
        }else{
            $comment_ids = $uids = $order_ids = array();
            foreach($items as $k => $v){
                $uids[$v['uid']] = $v['uid'];
                $comment_ids[$v['comment_id']] = $v['comment_id'];
                $order_ids[$v['order_id']] = $v['order_id'];
            }
            $orders = K::M('order/order')->items_by_ids($order_ids);
            $this->pagedata['users'] = K::M('member/member')->items_by_ids($uids);
            $photos = K::M('waimai/commentphoto')->items(array('comment_id'=>$comment_ids));
            foreach($items as $k=>$v){
                foreach($photos as $k1=>$v1){
                    if($v['comment_id'] == $v1['comment_id']){
                        $items[$k]['photos'][] = $v1;
                        $items[$k]['photo_josn'][] = K::M('magic/upload')->geturl($v1['photo']);
                    }
                }
                foreach($orders as $k2=>$v2){
                    if($v['order_id'] == $v2['order_id']){
                        $items[$k]['pei_type'] = $v2['pei_type'];
                    }
                }
            }
            foreach($items as $k=>$v){
                $items[$k]['photo_josn'] = implode(',', $items[$k]['photo_josn']);
            }
        }
        $count_num = K::M('waimai/comment')->count($filter);
        if($count_num <= $limit){
            $loadst = 0; 
        }else{
            $loadst = 1; 
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = 'shop/loaditems.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }

    public function info($shop_id)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('商家不存在',284);
        }else if(!$detail = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('商家不存在',285);
        }else{
            $detail['collect'] = 0;
            if($this->uid) {
                if(K::M('member/collect')->count(array('uid'=>$this->uid,'type'=>'waimai','can_id'=>$shop_id,'status'=>1))){
                    $detail['collect'] = 1;
                }
            }
            $lng = $this->request['UxLocation']['lng'];
            $lat = $this->request['UxLocation']['lat'];   
            
            $detail['verify'] = K::M('waimai/verify')->detail($shop_id);
            $detail['album'] = K::M('shop/albumphoto')->items(array('shop_id'=>$shop_id));
            
            $filter_env = array(
                'shop_id'=>$shop_id,
            );
            $detail['env'] = K::M('waimai/env')->items($filter_env);
            $json_view = array();
            $json_view['id_photo'] = $detail['verify']['id_photo']?K::M('magic/upload')->geturl($detail['verify']['id_photo']):"";
            $json_view['yz_photo'] = $detail['verify']['yz_photo']?K::M('magic/upload')->geturl($detail['verify']['yz_photo']):"";
            $json_view['cy_photo'] = $detail['verify']['cy_photo']?K::M('magic/upload')->geturl($detail['verify']['cy_photo']):"";
            $json_view['env'] = array();
            foreach( $detail['env'] as $kk=>$vv){
               if($vv['photo']){
                   $json_view['env'][] = K::M('magic/upload')->geturl($vv['photo']);
               }
            }

            $area_price = K::M('waimai/waimai')->get_shipping_fee($detail['area_polygon'], $lat, $lng);
            if($detail['pei_type']==0){
                $detail['freight'] = $area_price['shipping_fee'];// 兼容旧版，重新赋值配送费
                $detail['min_amount'] = $area_price['min_price'];// 兼容旧版，重新赋值起送价
            }else{
                $jili = (int)K::M('helper/round')->juli($detail['lng'], $detail['lat'], $lng, $lat);  // 用户与商户的距离米                
                //新增商家读取单独配置
                if($detail['is_separate']==0){
                    $group = K::M("pei/group")->detail($detail['group_id']);
                    $detail['min_amount'] = $group['min_amount'];
                }

                if($detail['is_separate']==1&&$detail['config']){
                    $detail['freight'] =   K::M('waimai/waimai')->shipping_fee_by_type($detail['config'],$jili);
                }else{
                    $detail['freight'] =   K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($detail['group_id']),$jili);
                }
            }

            $huodong = K::M('waimai/waimai')->get_huodong($shop_id);
            $detail['huodong'] = (array)$huodong[$shop_id];

            $this->pagedata['json_view'] = json_encode($json_view);
            $this->pagedata['detail']  = $detail;
            $this->pagedata['shop_id'] = $shop_id;
            $this->pagedata['shop'] =  K::M('shop/shop')->detail($shop_id);
            $this->tmpl = 'shop/info.html';
        }
    }
    
    public function get_spec()
    {
        if(!$porduct_id = (int)$this->GP('product_id')){
            $this->msgbox->add("该商品不存在",211);
        }elseif(!$detail = K::M('waimai/product')->detail($porduct_id)){
            $this->msgbox->add("该商品不存在",212);
        }else{
            if($spec_list = K::M('waimai/productspec')->items(array('product_id'=>$porduct_id))){
                foreach($spec_list as $k=>$v) {
                    $spec_list[$k]['pcate_id'] = implode(',',$detail['cate_ids']);
                    if($spec_list[$k]['spec_photo']){
                        $spec_list[$k]['spec_photo'] = $spec_list[$k]['spec_photo'].'_thumb.jpg';
                    }
                    
                    if(empty($v['spec_photo'])){
                        $spec_list[$k]['spec_photo'] = $detail['photo'].'_thumb.jpg';
                    }

                    /*$spec_list[$k]['sale_type'] = $detail['sale_type'];
                    if($detail['sale_type']==0){
                        $spec_list[$k]['sale_sku'] =  $detail['sale_sku'];
                    }*/
                }               
            }else{
                $spec_list = array();
            }
            $this->msgbox->add('success');
            $this->msgbox->set_data('items',  array_values($spec_list));
        }
    }

    // 收藏
    public function collect($status, $type, $can_id)
    {
        $this->check_login();
        $data = array();
        $type = $type;
        $status = (int) $status;
        $can_id = (int) $can_id;
        $detail = K::M('member/collect')->find(array('uid' => $this->uid, 'can_id' => $can_id, 'type' => $type));
        if($detail){
            if(K::M('member/collect')->update($detail['collect_id'], array('status' => $status, 'dateline' => __TIME))){
                if($status == 0){
                    $this->msgbox->add('取消收藏成功');
                }
                else{
                    $this->msgbox->add('收藏成功');
                }
            }
        }
        else{
            if($collect_id = K::M('member/collect')->create(array('uid' => $this->uid, 'type' => $type, 'can_id' => $can_id, 'status' => 1, 'dateline' => __TIME))){
                $this->msgbox->add('恭喜您，收藏成功');
            }
        }
    }

    public function cart()
    {
        $items = (array) json_decode(str_replace('\\\"', '\"', $_COOKIE['KT-ECart']), true);
        //print_r($items);die;
        foreach($items as $k=>$v){
            if(empty($v)){
                unset($items[$k]);
            }
        }
        //print_r($items);die;
        foreach($items as $k=>$v){ //循环所有店铺
            $cart_goods = explode(",", $v);
            foreach($cart_goods as $k0=>$v0){
                $cart_goods[$k0] = $v0."_".$k; //添加shop_id
            }
            foreach ($cart_goods as $key => $val) {
                if(preg_match('/^(\d+)-(\d+):(\d+)_(\d+)$/', $val, $local)){
                    $pk = $local[1].'-'.$local[2];
                    $cart_product_list[$local[4]][$pk] = array(
                        'product_id'=>$local[1], 
                        'number'=>$local[3], 
                        'spec_id'=>$local[2],
                    );
                }
                $items[$k] = $cart_product_list[$k];
            }
        }
        //print_r($items);die;
        $shop_ids = $product_ids = $spec_ids = array();
        foreach($items as $k=>$v){
            $shop_ids[$k] = $k;
            foreach($v as $k1=>$v1){
                $product_ids[$v1['product_id']] = $v1['product_id'];
                if($v1['spec_id']){
                    $spec_ids[$v1['spec_id']] = $v1['spec_id'];
                }
            } 
        }
        //print_r($items);die;
        $products = K::M('waimai/product')->items_by_ids($product_ids);
        $specs = K::M('waimai/productspec')->items_by_ids($spec_ids);
        $nums = array();
        foreach($items as $k=>$v){
            foreach($v as $k1=>$v1){
                $nums[$k]['num'] +=$v1['number'];
                foreach($products as $k2=>$v2){
                    if($v1['product_id'] == $v2['product_id']){
                        $items[$k][$k1]['product'] = $v2;
                    }
                }
                foreach($specs as $k3=>$v3){
                    if($v1['spec_id'] == $v3['spec_id']){
                        $items[$k][$k1]['spec'] = $v3;
                    }
                }
            }
        }
        //print_r($items);die;
        $this->pagedata['nums'] = $nums;
        $this->pagedata['shops'] = K::M('waimai/waimai')->items_by_ids($shop_ids);
        $this->pagedata['items'] = $items;
        $this->tmpl = 'shop/cart.html';
    }
  
    public function cart2()
    {
        $this->tmpl = 'shop/cart2.html';
    }

    public function getCart($shop_id)
    {
        $cart = json_decode(urldecode($_COOKIE['KT-ECart']), true);
        $cart_goods = explode(',',$cart[$shop_id]);
        foreach ($cart_goods as $key => $val) {
            if(preg_match('/^(\d+)-(\d+):(\d+)$/', $val, $local)){                
                //$pk = $local[1].'-'.$local[2];
                if($local[2] == 0){
                    $cart_product_list[$local[1]] = array(
                    'product_id'=>$local[1], 
                    'number'=>$local[3], 
                    'spec_id'=>$local[2],
                    );
                }
            }
        }
        $items[$shop_id] = $cart_product_list;
        return $items;
    }

    public function getcoupon($shop_id)
    {
        $this->check_login();
        if(!$shop_id){
            $this->msgbox->add('参数错误',220);
        }else if(!$coupons = K::M('waimai/huodongcoupon')->items(array('shop_id'=>$shop_id,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME),array('huodong_id'=>'DESC'),1,1,$counts)){
            $this->msgbox->add('该商家没有可以领取的优惠券',221)->response();
        }else if(!$shop = K::M('shop/shop')->detail($shop_id)){
            $this->msgbox->add('商家不存在',222)->response();
        }else if(!$waimai=K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('商家不存在',240)->response();
        }else if($data = $this->checksubmit('data')){
            if(!$data['shop_id']){
                $this->msgbox->add('商家不存在',223)->response();
            }else if($shop_id!=$data['shop_id']){
                $this->msgbox->add('参数错误',224)->response();
            }else{
                $coupons = array_values($coupons);
                $coupons = $coupons[0];

                $filter = array();
                $filter['uid'] = $this->uid;
                $filter['shop_id'] = $shop_id;
                $filter['order_status'] = '>=:0';
                $filter['from'] = 'waimai';
                //订单数量
                $count = K::M('order/order')->count($filter);
                $filter_user = array();
                $filter_user['uid'] = $this->uid;
                $filter_user['shop_id'] = $shop_id;
                $filter_user['huodong_id'] = $coupons['huodong_id'];

                /*0表示新老用户通用，1表示新用户，2表示老用户*/
                //领取的优惠券
                $count_user = K::M('waimai/coupon')->count($filter_user);

                $num = 0;
                foreach ($coupons['config'] as $v){
                    if($v['order_amount']&& $v['coupon_amount']){
                        $num++;
                    }
                }
                if(count($coupons['config'])!=0){
                    $count_user =$count_user/$num;
                }else{
                    $this->msgbox->add('该商家优惠券不能领取',226)->response();
                }
                if($coupons['num']==0){
                    $this->msgbox->add('商家优惠券已经领取完了',227)->response();
                }else if(($coupons['limit']!=-1)&&($coupons['limit']<=$count_user)){
                    $this->msgbox->add('该优惠券每人限制领取'.$coupons['limit'].'次',228)->response();
                }else if(($coupons['group']==1)&&($count>0)){
                    $this->msgbox->add('该优惠券只有新用户才能领取',229)->response();
                }else if(($coupons['group']==2)&&($count==0)){
                    $this->msgbox->add('该优惠券只有老用户才能领取',230)->response();
                }else{
                    foreach ($coupons['config'] as $v){
                        if($v['order_amount']>0&& $v['coupon_amount']>0){
                            $create_data = array();
                            $create_data['shop_id'] = $shop_id;
                            $create_data['order_id'] = 0;
                            $create_data['type'] = 2;
                            $create_data['uid'] = $this->uid;
                            $create_data['huodong_id'] =$coupons['huodong_id'];
                            $create_data['order_amount'] = $v['order_amount'];
                            $create_data['coupon_amount'] = $v['coupon_amount'];
                            $create_data['stime'] = $coupons['stime'];
                            $create_data['ltime'] = __TIME+86400*$v['day'];
                            $create_data['title']=$waimai['title'].'优惠券';
                            if(($create_data['order_amount']&&$create_data['coupon_amount'])&&!K::M('waimai/coupon')->create($create_data)){
                                $this->msgbox->add('领取失败，请稍后再试',231)->response();
                            };
                        }
                    }
                    if($coupons['num'] >0){
                        if(K::M('waimai/huodongcoupon')->update_count($coupons['huodong_id'],'num',-1)){
                            $this->msgbox->add('领取成功');
                        }else{
                            $this->msgbox->add('领取失败请稍后再试',232)->response();
                        }
                    }else{
                        $this->msgbox->add('领取成功');
                    }
                }
            }
        }else{
            $this->pagedata['shops'] = $waimai;
            $this->pagedata['coupon'] = $coupons;
            $this->tmpl = 'shop/coupon.html';
        }       
    }

    public function map($shop_id)
    {
        if(!$shop_id){
            $this->msgbox->add('商家不存在',233);
        }else if(!$shop = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('商家不存在',234);
        }else {
            $location = array(
                'lng'=>$shop['lng'],
                'lat'=>$shop['lat']
            );
            $this->pagedata['location'] = $location;
            $this->tmpl = 'shop/map.html';
        }
    }
    
    public function check_cart($shop_id)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('该商家不存在或已删除',200);
        }elseif(!$detail = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('该商家不存在或已删除',200);
        }else{
            $cart = json_decode(urldecode($_COOKIE['KT-ECart']), true);
            $cart_goods = explode(',',$cart[$shop_id]);
            foreach ($cart_goods as $key => $val) {
                $arr = explode("&",$val);
                $tmp_arr = explode("-",$arr[1]);
                $signstr = "";
                foreach($tmp_arr as $k=>$v){
                    if($v){
                        $tmp_v = explode('_',$v);
                        $signstr.="+".$tmp_v[1];
                    }
                }
                if(preg_match('/^(\d+)-(\d+):(\d+)/', $val, $local)){
                    $pk = $local[1].'-'.$local[2];
                    $cart_product_list[$pk] = array(
                        'product_id'=>$local[1], 
                        'number'=>$local[3], 
                        'spec_id'=>$local[2],
                        'signstr'=>$signstr,
                        );
                }                
                
                /*if(preg_match('/^(\d+)-(\d+)-(\d+):(\d+)/', $val, $local)){
                    $pk = $local[1].'-'.$local[2].'-'.$local[3];
                    $cart_product_list[$pk] = array(
                        'product_id'=>$local[1], 
                        'number'=>$local[4], 
                        'spec_id'=>$local[2],
                        'huodong_id'=>$local[3],//折扣活动ID
                        );
                }*/
            }
            $product_ids = $spec_ids = array();
            //$huodong_id = 0;
            foreach($cart_product_list as $k=>$v){
                $product_ids[$v['product_id']] = $v['product_id'];
                if($v['spec_id']){
                    $spec_ids[$v['spec_id']] = $v['spec_id'];
                }
                /*if(!$huodong_id && $v['huodong_id']){
                    $huodong_id = $v['huodong_id'];
                }*/
            }
            $products = K::M('waimai/product')->items_by_ids($product_ids);
            $specs = K::M('waimai/productspec')->items_by_ids($spec_ids);
            $err1 = $err2 = $err3 = $err4 = $err5 = 0;
            //$err1 商品不存在、下架、删除
            //$err2 商品库存不足
            //$err3 商品规格删除、改变
            //$err4 规格商品库存发生变化
            //$err5 折扣商品发生变化(折扣类型，限购数量，对应商品的库存，折扣比例)
            
            //折扣商品            
            $discount = K::M('waimai/huodongdiscount')->get_discount($shop_id);
            
            foreach($cart_product_list as $k=>$v){
                if(!$products[$v['product_id']]||$products[$v['product_id']]['is_onsale']==0||$products[$v['product_id']]['closed']==1){
                    unset($cart_product_list[$k]);
                    $err1 += 1;
                }elseif(!$products[$v['product_id']]['is_spec']&&((!$discount['products'][$v['product_id']]&&$products[$v['product_id']]['sale_sku']<$v['number']) || ($discount['products'][$v['product_id']]&&$discount['products'][$v['product_id']]['sale_sku']<$v['number']))){
                    unset($cart_product_list[$k]);                                                      
                    $pro_title = $products[$v['product_id']]['title'];
                    $err2 += 1;  
                }elseif($products[$v['product_id']]['is_spec']==1&&!$specs[$v['spec_id']]){
                    unset($cart_product_list[$k]);
                    $err3 += 1;
                }elseif($products[$v['product_id']]['is_spec']==1&&$specs[$v['spec_id']]&&((!$discount['products'][$v['product_id']]&&$specs[$v['spec_id']]['sale_sku']<$v['number']) || ($discount['products'][$v['product_id']]&&$discount['products'][$v['product_id']]['sale_sku']<$v['number']))){
                    unset($cart_product_list[$k]);                                                      
                    $pro_title = $products[$v['product_id']]['title']."(".$specs[$v['spec_id']]['spec_name'].")";
                    $err4 += 1;  
                }/*elseif((!$v['huodong_id'] && $discount['products'][$v['product_id']]) || ($v['huodong_id'] && !$discount)){
                    unset($cart_product_list[$k]);
                    $err5 += 1;
                }elseif($v['huodong_id'] && $discount && ($v['huodong_id'] != $discount['huodong_id'])){
                    unset($cart_product_list[$k]);
                    $err5 += 1;
                }elseif(!$discount['products'][$v['product_id']] || $discount['products'][$v['product_id']]['sale_sku'] < $v['number']){
                    unset($cart_product_list[$k]);
                    $err5 += 1;
                }*/
            }
            //print_r($cart_product_list);die;
            //重组购物车信息
            $product_ids = $spec_ids = array();
            foreach($cart_product_list as $k=>$v){
                $product_ids[$v['product_id']] = $v['product_id'];
                if($v['spec_id']){
                    $spec_ids[$v['spec_id']] = $v['spec_id'];
                }  
            }
            $products = K::M('waimai/product')->items_by_ids($product_ids);
            $specs = K::M('waimai/productspec')->items_by_ids($spec_ids);
            $cart = array();
            foreach ($cart_product_list as $k=>$v){
                if($v['spec_id']>0){//有规格
                    foreach ($specs as $k1=>$v1){
                        if($v['spec_id'] == $v1['spec_id']){
                            $cart[$v['product_id'].'-'.$v['spec_id']] = array(
                                'product_id' => $v['product_id'],
                                'title'      => $products[$v['product_id']]['title'],
                                'spec_name'  => $v1['spec_name'],
                                'price'      => $v1['price'],
                                'spec_photo' => $v1['spec_photo'],
                                'package'    => $v1['package_price'],
                                'sale_type'  => $products[$v1['product_id']]['sale_type'],
                                'sale_sku'   => $v1['sale_sku'],
                                'sku_id'     => $v1['product_id'].'-'.$v1['spec_id'],
                                'num'        => $v['number'],
                                'signstr'    => $v['signstr'],
                                'str_obj'    => $products[$v['product_id']]['specification'],
                            );
                        }
                    }
                }else{//无规格
                    foreach ($products as $k1=>$v1){
                        if($v['product_id'] == $v1['product_id']&&!$v1['is_spec']){
                            $cart[$v['product_id'].'-'.'0'] = array(
                                'product_id'  => $v['product_id'],
                                'title'       => $v1['title'],
                                'spec_name'   => '',
                                'price'       => $v1['price'],
                                'photo'       => $v1['photo'],
                                'package'     => $v1['package_price'],
                                'sale_type'   => $v1['sale_type'],
                                'sale_sku'    => $v1['sale_sku'],
                                'sku_id'      => $v['product_id'].'-'.'0',
                                'num'         => $v['number'],
                                'signstr'     => $v['signstr'],
                                'str_obj'     => $products[$v['product_id']]['specification'],
                            );
                        }
                    }
                }
            }

            foreach ($cart as $k => $v) {
                $v['huodong_id'] = 0;
                $v['is_discount'] = 0;
                $v['oldprice'] = $v['price'];
                $v['quota'] = 0;
                $v['disctype'] = 0;
                $v['discval'] = 100;
                if($dp = $discount['products'][$v['product_id']]){
                    $v['is_discount'] = 1;
                    if($discount['discount_type']){
                        $dp['disc_price'] = $v['price'] - $dp['discount_value'];
                    }else{
                        $dp['disc_price'] = $v['price']*$dp['discount_value'];
                        $dp['discount_value'] = sprintf("%.1f",$dp['discount_value']*10);
                    }
                    $v['sale_sku'] = $dp['sale_sku'];
                    $v['sale_type'] = 1;
                    $v['price'] = max(0,sprintf("%.2f",$dp['disc_price']));
                    $v['disctype'] = $discount['discount_type'];
                    $v['discval'] = $dp['discount_value'];
                    $v['quota'] = $dp['quota'];
                    $v['huodong_id'] = $dp['huodong_id'];         
                }
                $cart[$k] = $v;
            }
            $_cart['cart'] = $cart;
            $_cart['shop_id'] = $shop_id;
            $_cart['title'] = $detail['title'];
            $_cart['discount'] = $discount;
            if($err1>=1||$err3>=1||$err5>=1){
                $msg = "购物车商品信息发生变化";
                $error = 211;
                $this->msgbox->set_data('data',$_cart);
                $this->msgbox->add($msg,$error);
            }elseif($err2>=1||$err4>=1){
                $msg = $pro_title."等商品库存不足";
                $error = 212;
                $this->msgbox->set_data('data',$_cart);
                $this->msgbox->add($msg,$error);
            }else{
                $this->msgbox->set_data('pdata',$_cart);
                $this->msgbox->add('success');
            }
        }
    }
	
	/*商家店内搜索页*/
    public function search($shop_id = null)
    {
        if(!$shop_id = (int)$shop_id){
           $this->msgbox->add('商家不存在',280);
        }else if(!$detail = K::M('waimai/waimai')->detail($shop_id)){
           $this->msgbox->add('商家不存在',281)->response();
        }else{
            $lng = (float)$this->request['UxLocation']['lng'];
            $lat = (float)$this->request['UxLocation']['lat'];
            $detail = K::M('waimai/waimai')->format_data($detail);
           /* $area_price = K::M('waimai/waimai')->get_shipping_fee($detail['area_polygon'], $lat, $lng);
            $detail['freight'] = $area_price['shipping_fee'];// 兼容旧版，重新赋值配送费
            $detail['min_amount'] = $area_price['min_price'];// 兼容旧版，重新赋值起送价*/
            $area_price = K::M('waimai/waimai')->get_shipping_fee($detail['area_polygon'], $lat, $lng);
            if($detail['pei_type']==0){
                $detail['freight'] = $area_price['shipping_fee'];// 兼容旧版，重新赋值配送费
                $detail['min_amount'] = $area_price['min_price'];// 兼容旧版，重新赋值起送价
            }else{
                //$fright_config = $this->system->config->get('fright');
                $jili = (int)K::M('helper/round')->juli($detail['lng'], $detail['lat'], $lng, $lat);  // 用户与商户的距离米
                $group = K::M("pei/group")->detail($detail['group_id']);
                //新增商家读取单独配置
                if($detail['is_separate']==0){
                    $detail['min_amount'] = $group['min_amount'];
                }
                if($detail['is_separate']==1&&$detail['config']){
                    $detail['freight'] =   K::M('waimai/waimai')->shipping_fee_by_type($detail['config'],$jili);
                }else{
                    $detail['freight'] =   K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($detail['group_id']),$jili);
                }
            }

            $discount = K::M('waimai/huodongdiscount')->get_discount($shop_id); //折扣商品处理
            $disc_pids = array();
            if($discount && $discount['products']){
                $disc_pids = array_keys($discount['products']);
                $newdetail = K::M('waimai/huodongdiscount')->get_newProducts($discount,array($detail));
                $detail = $newdetail[0] ? $newdetail[0] : array();
            }
            $this->pagedata['json_discount'] = json_encode($discount);
            $filter = array('shop_id'=>$detail['shop_id'],'closed'=>0,'is_onsale'=>1,'is_spec'=>0,'product_id'=>'NOTIN:'.implode(',',$disc_pids));

            $this->pagedata['items2'] = K::M('waimai/product')->items($filter,array('price'=>'asc'),1,10);
            $title = htmlspecialchars($this->GP('title'));
            $this->pagedata['yystat'] = $detail['yy_status'];
            $this->pagedata['title'] = $title;
            $this->pagedata['detail'] = $detail;
            $this->pagedata['shop_id'] = $shop_id;
            $must_count = K::M('waimai/product')->items(array('shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1,'is_must'=>1),array('product_id'=>"DESC"),1,100,$count);
            $must_ids = array();
            foreach($must_count as $k=>$v){
                $must_ids[] = $v['product_id'];
            }
            if($must_count){
                $this->pagedata['is_must'] = 1;
            }else{
                $this->pagedata['is_must'] = 0;
            }

            $this->pagedata['json_product'] = json_encode($must_ids);

            $this->tmpl = 'shop/search.html';
        }
    }    
}
