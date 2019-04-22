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
class Ctl_Product extends Ctl
{
    public function extend_index(){
        //显示搜索框--叶超
        //print_r($scates);die;
        if($title = $this->GP('title')){
            $pager['title'] = $title;
        }
        if($order = $this->GP('order')){
            $pager['order'] = $order;
        }
        if($area_id = $this->GP('area_id')){
            $pager['area_id'] = $area_id;
        }
        if($area_id = $this->GP('area_id')){
            $pager['area_id'] = $area_id;
        }
        if($biz_id = $this->GP('biz_id')){
            $pager['biz_id'] = $biz_id;
        }
        $pager['order'] = 'j';
        $this->pagedata['pager'] = $pager;
        $this->pagedata['cates'] = K::M('waimai/cate')->tree();
        $areas = K::M('data/area')->items(array('city_id'=>$this->city_id));
        $bizs = K::M('data/business')->items(array('city_id'=>$this->city_id));
        foreach($areas as $k=>$v){
            foreach($bizs as $k1=>$v1){
                if($v['area_id'] == $v1['area_id']){
                    $areas[$k]['bizs'][] = $v1;
                }
            }
        }
        $this->pagedata['areas'] = $areas;
        $cfg = $this->system->config->get('hotwaimai');
        $cfg = str_replace('，', ',', $cfg['hotwaimai']);
        $this->pagedata['hotwaimai'] = explode(',', $cfg);
        $this->tmpl = 'product/indexjuli.html';
    }
    /* 商家商品列表 */
    public function index($a=0)
    {
        //显示搜索框--叶超
        if($a){
           $this->pagedata['search'] =1;
        }
        if($cat_id = (int)$this->GP('cat_id')){
            if($res = K::M('waimai/cate')->detail($cat_id)){
                if($r = K::M('waimai/cate')->detail($res['parent_id'])){
                    if($r['parent_id'] == 0){ //表示点击的是二级分类
                        $cate_id = $res['parent_id'];
                        $scate_id = $res['cate_id'];
                    }else{
                        $cate_id = $r['parent_id'];
                        $scate_id = $r['cate_id'];
                    }
                }
                $this->pagedata['scate_id'] = $scate_id;
                if($p = K::M('waimai/cate')->detail($cate_id)){
                    $scates = K::M('waimai/cate')->items(array('parent_id'=>$p['cate_id']),array('cate_id'=>'asc'));
                    //print_r($scates);die;
                    $cates = K::M('waimai/cate')->fetch_all();
                    foreach($scates as $k=>$v){
                        foreach($cates as $k1=>$v1){
                            if($v['cate_id'] == $v1['parent_id']){
                                $scates[$k]['children'][] = $v1;
                            }
                        } 
                    }
                    foreach($scates as $k=>$v){
                        $scates[$k]['count'] = count($v['children']);
                    }
                    $this->pagedata['scates'] = $scates;
                }
            }
            $pager['cat_id'] = $cat_id;
        }elseif($cid = (int)$this->GP('cid')){
            $scate_id = 0;
            $this->pagedata['scate_id'] = $scate_id;
            $this->pagedata['cid'] = $cid;
            $scates = K::M('waimai/cate')->items(array('parent_id'=>$cid),array('cate_id'=>'asc'));
            $cates = K::M('waimai/cate')->fetch_all();
            foreach($scates as $k=>$v){
                foreach($cates as $k1=>$v1){
                    if($v['cate_id'] == $v1['parent_id']){
                        $scates[$k]['children'][] = $v1;
                    }
                } 
            }
            $_count = 0;
            foreach($scates as $k=>$v){
                $scates[$k]['count'] = count($v['children']);
                $_count += count($v['children']);
            }
            $this->pagedata['count'] = $_count; 
            $this->pagedata['scates'] = $scates;
        }
        //print_r($scates);die;
        if($title = $this->GP('title')){
            $pager['title'] = $title;
        }
        if($order = $this->GP('order')){
            $pager['order'] = $order;
        }
        if($area_id = $this->GP('area_id')){
            $pager['area_id'] = $area_id;
        }
        if($area_id = $this->GP('area_id')){
            $pager['area_id'] = $area_id;
        }
        if($biz_id = $this->GP('biz_id')){
            $pager['biz_id'] = $biz_id;
        }
        $this->pagedata['pager'] = $pager;
        $this->pagedata['cates'] = K::M('waimai/cate')->tree();
        $areas = K::M('data/area')->items(array('city_id'=>$this->city_id));
        $bizs = K::M('data/business')->items(array('city_id'=>$this->city_id));
        foreach($areas as $k=>$v){
            foreach($bizs as $k1=>$v1){
                if($v['area_id'] == $v1['area_id']){
                    $areas[$k]['bizs'][] = $v1;
                }
            }
        }
        $this->pagedata['areas'] = $areas;
        $cfg = $this->system->config->get('hotwaimai');
        $cfg = str_replace('，', ',', $cfg['hotwaimai']);
        $array = explode(',', $cfg);
        foreach($array as $k=>$v){
            if(!$v){
                unset($array[$k]);
            }
        }
        $this->pagedata['hotwaimai'] = $array;
        $this->tmpl = 'product/index.html';
    }
    
    public function loaditems($page=1)
    { 
        $filter_shop = array();  
        $lng = (float)$this->request['UxLocation']['lng'];
        $lat = (float)$this->request['UxLocation']['lat'];
        if(!$lng || !$lat){
            $lng = trim($_COOKIE['lng']);
            $lat = trim($_COOKIE['lat']);
        }
        if(!$lng || !$lat){
            $uxlocal = $this->GP('uxlocal');
            $uxlocals = explode(',',$uxlocal);
            $lng = $uxlocals[0];
            $lat = $uxlocals[1];
        }
        if($lat && $lng){
            $site_config = K::M('system/config')->get('site');
            $pei_range = $site_config['pei_range']?$site_config['pei_range']:5;
            $squares = K::M('helper/round')->returnSquarePoint($lng, $lat,$pei_range);
            $_lat = array($squares['left-bottom']['lat'], $squares['right-top']['lat']);
            $_lng = array($squares['left-bottom']['lng'], $squares['right-top']['lng']);
            asort($_lat);
            asort($_lng);
            $filter_shop['lat'] = $_lat[0].'~'.$_lat[1];
            $filter_shop['lng'] = $_lng[0].'~'.$_lng[1];
        }

        $shop_ids = array();
        if($waimai_items = K::M('waimai/waimai')->items($filter_shop, array(), 1, 500, $count_shop)){            
            foreach($waimai_items as $k=>$v){
                $shop_ids[$v['shop_id']] = $v['shop_id'];
            }
        }
        $this->pagedata['shops'] = $waimai_items ? $waimai_items : array();

        //商品列表
        $filter = array('is_onsale'=>1,'closed'=>0,'shop_id'=>$shop_ids);
        //print_r($filter);die;
        if($title = strip_tags(trim($this->GP('title')))){
            $filter['title'] = "LIKE:%".$title."%";
        }
        if($cat_id = (int)$this->GP('cat_id')){
            $res = K::M('waimai/cate')->getChildren($cat_id);
            //print_r($res);die;
            $filter['cat_id'] = $res;
        }
        //地区商圈
        $area_id = (int)$this->GP('area_id');
        $biz_id = (int) $this->GP('biz_id');
        if($area_id&&$biz_id){
            if($biz = K::M('data/business')->find(array('business_id'=>$biz_id))){
                if($biz['area_id'] != $area_id){
                    $this->msgbox->add('城市商圈不正确!',213);
                }
            }
            $filter['area_id'] = $area_id;
            $filter['business_id'] = $biz_id;
        }elseif($area_id && !$biz_id){
            if(!$area = K::M('data/area')->find(array('area_id'=>$area_id))){
                $this->msgbox->add('地区不存在!',214);
            }
            $filter['area_id'] = $area_id;
        }
        $this->pagedata['area_id'] = $area_id;
        $this->pagedata['biz_id'] = $biz_id;
       
        $page = max((int)$page, 1);
        $limit = 10;
        $orderby = array();
        if($order = $this->GP('order')){
            switch($order){
                case 'sales':
                $orderby['sales'] = 'desc';break;
                case 'price':
                $orderby['price'] = 'asc';break;
                default:
                $orderby = array('sales'=>'desc','price'=>'asc');    
            }
        }

        if(!$items = K::M('waimai/product')->items($filter,$orderby,$page, $limit, $count)){
            $items = array();
        }
        /*$shop_ids = array();
        foreach($items as $k=>$v){
            $shop_ids[$v['shop_id']] = $v['shop_id'];
        }
        $this->pagedata['shops'] = K::M('waimai/waimai')->items_by_ids($shop_ids);*/

        $count_num = K::M('waimai/product')->count($filter);
        if($count_num <= $limit){
            $loadst = 0; 
        }else{
            $loadst = 1; 
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = 'product/loaditems.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }
   
    public function get_cart_num(){
        if(!$cart = json_decode(urldecode($_COOKIE['KT-ECart']), true)){
            $cookie_str = str_replace('\"','"',$_COOKIE['KT-ECart']);
            $cart = json_decode($cookie_str,true);
        }
        foreach($cart as $k=>$shop_cart){
            $cart_goods = explode(',',$shop_cart);
            foreach ($cart_goods as $key => $val) {
                if(preg_match('/^(\d+)-(\d+):(\d+)$/', $val, $local)){
                    $items[$k] += $local[3];
                }
            }
        }
        return $items;
    }

    public function loadshops($page=1)
    { //商家

        $filter = array('closed'=>0,'audit'=>1,'verify_name'=>1);
        if($this->city_id){
            $filter['city_id']=$this->city_id;
        }
        if($title = strip_tags(trim($this->GP('title')))){
            $filter['title'] = "LIKE:%".$title."%";
        }
        if($cat_id = (int)$this->GP('cat_id')){
            $res = K::M('waimai/cate')->getChildren($cat_id);
            $filter['cate_id'] = $res;
        }
        
        //地区商圈
        $area_id = (int)$this->GP('area_id');
        $biz_id = (int) $this->GP('biz_id');
        if($area_id&&$biz_id){
            if($biz = K::M('data/business')->find(array('business_id'=>$biz_id))){
                if($biz['area_id'] != $area_id){
                    $this->msgbox->add('城市商圈不正确!',213);
                }
            }
            $filter['area_id'] = $area_id;
            $filter['business_id'] = $biz_id;
        }elseif($area_id && !$biz_id){
            if(!$area = K::M('data/area')->find(array('area_id'=>$area_id))){
                $this->msgbox->add('地区不存在!',214);
            }
            $filter['area_id'] = $area_id;
        }
        $this->pagedata['area_id'] = $area_id;
        $this->pagedata['biz_id'] = $biz_id;
        
        $lng = (float)$this->request['UxLocation']['lng'];
        $lat = (float)$this->request['UxLocation']['lat'];
        if(!$lng||!$lat){
            $lng = trim($_COOKIE['lng']);
            $lat = trim($_COOKIE['lat']);
        }
		if(!$lng||!$lat){
            $uxlocal = $this->GP('uxlocal');
            $uxlocals = explode(',',$uxlocal);
            $lng = $uxlocals[0];
            $lat = $uxlocals[1];
        }
        if($lat&&$lng){
            $site_config = K::M('system/config')->get('site');
            $pei_range = $site_config['pei_range']?$site_config['pei_range']:5;
            $squares = K::M('helper/round')->returnSquarePoint($lng, $lat,$pei_range);
            $_lat = array($squares['left-bottom']['lat'], $squares['right-top']['lat']);
            $_lng = array($squares['left-bottom']['lng'], $squares['right-top']['lng']);
            asort($_lat);
            asort($_lng);
            $filter['lat'] = $_lat[0].'~'.$_lat[1];
            $filter['lng'] = $_lng[0].'~'.$_lng[1];
        }
        $page = max((int)$page, 1);
        $limit = 500;
        $orderby = array();
        $order = $this->GP('order');
        /*if(!$items = K::M('waimai/waimai')->items($filter,$orderby,$page, $limit, $count)){
            $items = array();
        }*/
        
        if($page <= 50 && $waimai_items = K::M('waimai/waimai')->items($filter, array(), 1, $limit, $count)) {
            $shop_ids = array();
            $group_ids = array();
            foreach($waimai_items as $kk1=>$vv1){
                if(in_array($vv1['pei_type'],array(1,2))){
                    $group_ids[$vv1['group_id']] =$vv1['group_id'];
                }

                /*4.0用户浏览过和买过排序*/
                $shop_ids[$vv1['shop_id']] = $vv1['shop_id'];
            }
            $group_list = K::M('pei/group')->items_by_ids($group_ids);
            foreach($waimai_items as $k=>$val) {
                if($val['yysj_status']== 1&&$val['yy_status']==1){
                    $val['yyst'] = 1;
                }else{
                    $val['yyst'] = 0;
                }

                if($val['pei_type']==0){
                    if ($area_price = K::M('waimai/waimai')->get_shipping_fee($val['area_polygon'], $lat, $lng)) {// 配送范围内
                        $val['min_amount'] = $area_price['min_price'];
                        $val['freight'] = $area_price['shipping_fee'];
                        unset($val['area_polygon']);
                        $_waimai_items[$k] = $val;
                    }
                }else{
                    if(K::M('helper/round')->in_or_out_polygon($group_list[$val['group_id']]['polygon_point'],$lat,$lng)){
                        //新增商家单独配置功能 --读取配置 2017-10-24 add by 叶超 --begin
                        if($val['is_separate']==0){
                            $val['min_amount'] = $group_list[$val['group_id']]['min_amount'];
                        }
                        //新增商家单独配置功能 --读取配置 2017-10-24 add by 叶超 --end
                        unset($val['area_polygon']);
                        $_waimai_items[$k] = $val;
                    }
                }               
            }

            /*4.0用户浏览过和买过排序*/
            if($this->uid && $shop_ids){
                $viewed_items = K::M('waimai/views')->group_by_shop_id(array('shop_id'=>$shop_ids, 'uid'=>$this->uid));
                $bought_items = K::M('order/order')->group_sum_by_shop_id(array('shop_id'=>$shop_ids, 'uid'=>$this->uid, 'order_status'=>8));
            }else{
                $viewed_items = array();
                $bought_items = array();
            }

            if($_waimai_items){
                foreach($_waimai_items as $k=>$val){
                    $val['juli'] = (int)K::M('helper/round')->juli($val['lng'], $val['lat'], $lng, $lat);  // 用户与商户的距离米
                    $val['juli_label'] = K::M('helper/format')->juli($val['juli']);
                    $val['avg_score'] = ($val['score']/$val['comments']) ? round($val['score']/$val['comments'],2) : 0 ;
                    if($val['avg_score']<0){
                        $val['avg_score'] = 0;
                    }else if($val['avg_score']>=5){
                        $val['avg_score'] = 5;
                    }

                    /*4.0用户浏览过和买过排序*/
                    $val['viewed'] = $val['bought'] = 0;
                    if($viewed_items && ($viewed = $viewed_items[$val['shop_id']])){
                        $val['viewed'] = $viewed['views'];
                    }
                    if($bought_items && ($bought = $bought_items[$val['shop_id']])){
                        $val['bought'] = $bought['orders'];
                    }

                    $_waimai_items[$k] = $val; 
                }
            }
                        
            $items = $_waimai_items;
            if($order == 'd'|| !$order) {
                uasort($items, array(K::M('waimai/orderby'), 'default_order'));   
            }  
            if($order == 'j') {
                uasort($items, array(K::M('waimai/orderby'), 'juli_order'));   
            }    
            if($order == 'f') {
                uasort($items, array(K::M('waimai/orderby'), 'score_order'));   
            }  
            if($order == 'q') {
                uasort($items, array(K::M('waimai/orderby'), 'price_order'));   
            }
            if($order == 's') {
                uasort($items, array(K::M('waimai/orderby'), 'sales_order'));   
            }
            if($order == 'p') {
                uasort($items, array(K::M('waimai/orderby'), 'ptime_order'));   
            }
            $items = array_slice( $items, ($page-1)*50, 50, true);  // 每次取10条记录，偏移量为$page-1
        }else{
            $items = array();
        }
        //echo '<pre>';print_r($items);die;
        //print_r($this->system->db->SQLLOG());die;
        
        //根据配置  列表是否显示 热销商品 叶超  2018 01 15
        if(K::M('waimai/config')->get_hot_show()){
            foreach($items as $k=>$v){
                $items[$k]['products'] = K::M('waimai/product')->items(array('shop_id'=>$v['shop_id'],'is_hot'=>1,'closed'=>0,'is_onsale'=>1),null,1,4);
            }
        }
        
        $count_num = count($items);
        if($count_num <= 50){
            $loadst = 0; 
        }else{
            $loadst = 1; 
        }
        $shop_carts = $this->get_cart_num();
        foreach($items as $k=>$v){
            $items[$k] = K::M('waimai/waimai')->format_data($v);
            $items[$k]['cart_num'] = (int)$shop_carts[$v['shop_id']];
            if(in_array($v['pei_type'],array(1,2))){
                //单独读取商家配置 --  叶超 20171024 --begin
                if($v['is_separate']==1&&$v['config']){
                    $items[$k]['freight'] = K::M('waimai/waimai')->shipping_fee_by_type($v['config'],$v['juli']);
                }else{
                    $items[$k]['freight'] = K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($v['group_id']),$v['juli']);
                }
                //单独读取商家配置 --  叶超 20171024 --end
            }
        }

        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = 'product/loadshops.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }

    /* 商品详情 */
    public function detail($product_id)
    {
        $lng = (float)$this->request['UxLocation']['lng'];
        $lat = (float)$this->request['UxLocation']['lat'];
        if(!$product_id = (int)$product_id){
            $this->msgbox->add('该商品不存在',211);
        }else if(!$detail=K::M('waimai/product')->detail($product_id)){
            $this->msgbox->add('该商品不存在',212);
        }else if(!$shop_detail = K::M('waimai/waimai')->detail($detail['shop_id'])){
            $this->msgbox->add('非法访问',213);
        }/*elseif(!$area_price = K::M('waimai/waimai')->get_shipping_fee($shop_detail['area_polygon'], $lat, $lng)){// 根据用户选择地址取商家区域模板配置 add by zhuhongwei
            $this->msgbox->add('超出配送范围',211);
        }*/else{
            $area_price = K::M('waimai/waimai')->get_shipping_fee($shop_detail['area_polygon'], $lat, $lng);
            $shop_detail2 = K::M('shop/shop')->detail($detail['shop_id']);
            $shop_detail['tel'] = $shop_detail2['mobile'];
            $waimai_shop_tmp = $shop_detail;
            if($spec_list=K::M('waimai/productspec')->items(array('product_id'=>$product_id))){                
                foreach($spec_list as $k=>$v) {
                    $spec_list[$k]['package_price'] = $detail['package_price'];
                    $spec_list[$k]['sale_type'] = $detail['sale_type'];
                    if(empty($v['spec_photo'])){
                        $spec_list[$k]['spec_photo'] = $detail['photo'];
                    }
                }               
            }
            $shop_detail['freight'] = $area_price['shipping_fee'];// 兼容旧版，重新赋值配送费
            $shop_detail['min_amount'] = $area_price['min_price'];// 兼容旧版，重新赋值起送价
            //新增商家单独配置功能 --读取配置 2017-10-24 add by 叶超 --begin
            if($shop_detail['pei_type']==1){
                if($shop_detail['is_separate']==1){
                    $shop_detail['min_amount'] =$waimai_shop_tmp['min_amount'];
                }else{
                    $group_detail = K::M('pei/group')->detail($shop_detail['group_id']);
                    $shop_detail['min_amount'] = $group_detail['min_amount'];
                }
            }
            //新增商家单独配置功能 --读取配置 2017-10-24 add by 叶超 --end

            $discount = K::M('waimai/huodongdiscount')->get_discount($detail['shop_id']); //折扣商品处理
            $disc_pids = array();
            if($discount && $discount['products']){
                $disc_pids = array_keys($discount['products']);                
            }
            $newdetail = K::M('waimai/huodongdiscount')->get_newProducts($discount,array($detail));
            $detail = $newdetail[0] ? $newdetail[0] : array();

            $this->pagedata['json_discount'] = json_encode($discount);
            $filter = array('shop_id'=>$detail['shop_id'],'closed'=>0,'is_onsale'=>1,'price'=>"<=:".$shop_detail['min_amount'],'is_spec'=>0,'product_id'=>'NOTIN:'.implode(',',$disc_pids));
            
            //$filter = array('shop_id'=>$detail['shop_id'],'closed'=>0,'is_onsale'=>1,'price'=>"<=:".$shop_detail['min_amount'],'is_spec'=>0);
            $this->pagedata['items2'] = K::M('waimai/product')->items($filter,array('price'=>'asc'));
            $detail['data_spec'] =  K::M('utility/json')->encode(array_values($detail['specification']));
            $detail['is_shuxing'] =$detail['specification']?1:0;
            $detail['pcate_id'] =  implode(',',$detail['cate_ids']);

            $this->pagedata['detail'] = $detail;
            $this->pagedata['shop'] = $shop_detail;
            $this->pagedata['spec_list'] = $spec_list;
            $must_count = K::M('waimai/product')->items(array('shop_id'=>$detail['shop_id'],'closed'=>0,'is_onsale'=>1,'is_must'=>1),array('product_id'=>"DESC"),1,100,$count);
            $must_ids = array();
            foreach($must_count as $k=>$v){
                $must_ids[] = $v['product_id'];
            }
            if($must_count){
                $this->pagedata['is_must'] = 1;
            }else{
                $this->pagedata['is_must'] = 0;
            }
            $this->pagedata['shop_id'] = $detail['shop_id'];
            $this->pagedata['json_product'] = json_encode($must_ids);
            //echo '<pre>';print_r($detail);die;
            $this->tmpl = 'product/detail.html';
        }
    }
    
    // 默认排序辅助uasort()
    protected function default_order($a, $b)
    {
        if($a['yyst'] == $b['yyst']){
            if ($a['juli'] == $b['juli']) {
                if ($a['orderby'] == $b['orderby']) {
                    if($a['orders'] == $b['orders']){
                        return 0;
                    }else{
                        return ($a['orders'] > $b['orders']) ? -1 : 1;
                    }
                }else{
                    return ($a['orderby'] < $b['orderby']) ? -1 : 1;
                }
            }else{
                return ($a['juli'] < $b['juli']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }
    
    // 距离排序辅助uasort()
    protected function juli_order($a, $b)
    {
        if($a['yyst'] == $b['yyst']){
            if ($a['juli'] == $b['juli']) {
                return 0;
            }else{
                return ($a['juli'] < $b['juli']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }
    
    //评分排序
    protected function score_order($a, $b)
    {
        if($a['yyst'] == $b['yyst']){
            if ($a['avg_score'] == $b['avg_score']) {
                return 0;
            }else{
                return ($a['avg_score'] > $b['avg_score']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }
    
    //销量排序
    protected function sales_order($a, $b)
    {
        if($a['yyst'] == $b['yyst']){
            if ($a['orders'] == $b['orders']) {
                return 0;
            }else{
                return ($a['orders'] > $b['orders']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }
    
    // 起送价排序uasort()
    protected function price_order($a, $b)
    {
        if($a['yyst'] == $b['yyst']){
            if ($a['min_amount'] == $b['min_amount']) {
                return 0;
            }else{
                return ($a['min_amount'] < $b['min_amount']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }
    
     // 送达排序uasort()
    protected function ptime_order($a, $b)
    {
        if($a['yyst'] == $b['yyst']){
            if ($a['pei_time'] == $b['pei_time']) {
                return 0;
            }else{
                return ($a['pei_time'] < $b['pei_time']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }
    
}
