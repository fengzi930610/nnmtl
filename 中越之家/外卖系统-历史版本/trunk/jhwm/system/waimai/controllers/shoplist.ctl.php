<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/5
 * Time: 11:23
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shoplist extends Ctl {


    public function index($cate_id){
       if(!$cate_id){
           $cate_id = '';
       }
       $cat_id = '';
       $this->pagedata['cates'] = K::M('waimai/cate')->tree();
       if($cate = K::M('waimai/cate')->detail($cate_id)){
         if($cate['parent_id']>0){
             $cate_id=$cate['parent_id'];
             $cat_id = $cate['cate_id'];
         }
       }



       $this->pagedata['cate_id'] = $cate_id;
       $this->pagedata['cat_id'] = $cat_id;

        $title = strip_tags(trim($this->GP('title')));

        if(!$title || $title === "")
        {
            $mainCate = K::M('waimai/cate')->detail($cate_id);
            if($mainCate)
                $title = trim($mainCate['title']);
        }

        if($title === "")
            $title = "店铺列表";

        $this->pagedata['title'] = $title;
        
        $tmpType = (int)$this->GP("_st");
        if($tmpType !== 0 || ((int)$cat_id>0 && (int)$cate_id>0))
            $this->tmpl = "shoplist/index2.html";
        else
        {
            $theme = K::M('adv/themes')->getTheme();
            $this->pagedata['theme_config'] = $theme['config'] ? $theme['config'] : NULL;
            $this->tmpl = 'shoplist/index.html';
        }
    }
    //参数
    //@parmas cat_id 一级分类
    //@parmas cate_id 二级分类
    //@parmas pei_type 配送方式 1：自己送 2:平台送
    //@parmas orderby 排序 --智能排序(距离):juli  --评分:pingfen 销量:sales 起送价:minprice 最早送达:song
    //@parmas youhui 满减:mj 满反:mf 优惠券:yhj 首单:sd
    //@parmas ts  免配送费:mian 0元起送:zero
    public function loaditems($page){

        $page = max((int)$page,1);
        $limit = 10;
        $filter = array('closed'=>0,'audit'=>1,'verify_name'=>1);
        //获取商家分类选择
        if($cat_id = (int)$this->GP('cat_id')){
           $res = K::M('waimai/cate')->getChildren($cat_id);
           $filter['cate_id'] = $res;
        }
        if($cate_id = (int)$this->GP('cate_id')){
            unset($filter['cate_id']);
            $filter['cate_ids'] = 'LIKE:%,'.$cate_id.',%';
        }
        if($pet = $this->GP('pei_type')){
            if($pet==1){
                //自己送
                $filter['pei_type']=0;
            }else if($pet==2){
                //平台送
                $filter['pei_type']=1;
            }
        }
        if($title = htmlspecialchars(trim($this->GP('title')))){
            $filter['title'] = "LIKE:%".$title."%";
        }
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


        //还有  评分 和 距离
        //选择优惠信息
        // --满减 mj --满反 mf 优惠券 yhj --首单 sd
        if($youhui = $this->GP('youhui')){
            if($youhui=='sd'){
                $filter['hd_first_ltime'] = ">:".__TIME;
            }else if($youhui=='mj'){
                $filter['hd_mj_ltime'] = ">:".__TIME;
            }else if($youhui=='yhj'){
                $filter['hd_coupon_ltime'] = '>:'.__TIME;
            }else if($youhui=='mf'){
                $filter['hd_mf_ltime'] = ">:".__TIME;
            }
        }

        //商家特色
        //ts
        if($tese = $this->GP('ts')){

            if($tese=='mian'){
                $filter['freight'] = 0;
            }else if($tese=='zero'){
                $filter['min_amount'] = 0;
            }
        }
        // order 排序
        // 智能排序  juli   评分  pingfen  销量 sales 起送价 minprice 最早送达 song
        $order_by = array();
        $orderby = $this->GP('orderby');
        if($page <= 50 && $waimai_items = K::M('waimai/waimai')->items($filter, array(), 1, 500, $count)) {
            $_waimai_items =  $shop_ids = array();
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
                        if($tese){
                            if($tese=='mian'&&$val['freight']==0){
                                $_waimai_items[$k] = $val;
                            }else if($tese=='zero'&& $val['min_amount']==0){
                                $_waimai_items[$k] = $val;
                            }
                        }else{
                            $_waimai_items[$k] = $val;
                        }
                    }
                }else {
                    if(K::M('helper/round')->in_or_out_polygon($group_list[$val['group_id']]['polygon_point'],$lat,$lng)){
                        if($val['is_separate']==0){
                            $val['min_amount'] = $group_list[$val['group_id']]['min_amount'];
                        }
                        //$val['min_amount'] = $group_list[$val['group_id']]['min_amount'];
                        unset($val['area_polygon']);
                        if($tese){
                            if($tese=='mian'&&$val['freight']==0){
                                $_waimai_items[$k] = $val;
                            }else if($tese=='zero'&& $val['min_amount']==0){
                                $_waimai_items[$k] = $val;
                            }
                        }else{
                            $_waimai_items[$k] = $val;
                        }
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
            $items=$_waimai_items;

            if($orderby == 'default'||!$orderby) {
                uasort($items, array(K::M('waimai/orderby'), 'default_order'));
            }
            if($orderby == 'juli') {
                uasort($items, array(K::M('waimai/orderby'), 'juli_order'));
            }
            if($orderby == 'pingfen') {
                uasort($items, array(K::M('waimai/orderby'), 'score_order'));
            }
            if($orderby == 'minprice') {
                uasort($items, array(K::M('waimai/orderby'), 'price_order'));
            }
            if($orderby == 'sales') {
                uasort($items, array(K::M('waimai/orderby'), 'sales_order'));
            }
            if($orderby == 'song') {
                uasort($items, array(K::M('waimai/orderby'), 'ptime_order'));
            }
            $items_wai = array_slice($items,($page-1)*10,10,true);  // 每次取10条记录，偏移量为$page-1
        }else{
            $items_wai = array();
        }

        //3.8新增 是否显示  热销商品   2018 01 15 begin
        if(K::M('waimai/config')->get_hot_show()){
            foreach($items_wai as $k=>$v){
                $items_wai[$k]['products'] = K::M('waimai/product')->items(array('shop_id'=>$v['shop_id'],'is_hot'=>1,'closed'=>0,'is_onsale'=>1),null,1,4);
            }
        }
        //3.8新增 是否显示  热销商品   2018 01 15 end
        $count_num = count($items_wai);
        if($count_num <= 10){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        foreach ($items_wai as $kk1=>$vv1){
            $items_wai[$kk1] = K::M('waimai/waimai')->format_data($vv1);
        }
        foreach($items_wai as $k=>$v){
           // $items_wai[$k] = K::M('waimai/waimai')->format_data($v);
            if(in_array($v['pei_type'],array(1,2))){
                    //单独读取商家配置 --  叶超 20171024 --begin
                    if($v['is_separate']==1&&$v['config']){
                        $items_wai[$k]['freight'] = K::M('waimai/waimai')->shipping_fee_by_type($v['config'],$v['juli']);
                    }else{
                        $items_wai[$k]['freight'] = K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($v['group_id']),$v['juli']);
                    }
                    //单独读取商家配置 --  叶超 20171024 --end

                //外卖3.8 新增恶劣天气判断  叶超  2018 01 13 end

            }
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items_wai;
        $this->tmpl = 'product/loadshops.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();

    }

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

    public function loadshops($page=1){

        $page = max((int)$page,1);
        $limit = 10;
        $filter = array('closed'=>0,'audit'=>1,'verify_name'=>1);

        //检测country_code筛选参数
        if($country_code = trim($this->GP("country_code")))
        {
            $country_code = strtolower($country_code);
            if(strlen($country_code) === 2)
                $filter['country_code'] = $country_code;
        }

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

        //2019-02-25 添加 判断城市，如果城市存在，则使用城市参与筛选
        $cityName = trim($_COOKIE['KT-UxCityName']);
        if(!empty($cityName))
        {
            $cityRow = K::M('data/city')->find(['city_name_vn'=>$cityName]);
            if($cityRow)
                $filter['city_id'] = (int)$cityRow['city_id'];
        }
        //======================================================

        $site_config = K::M('system/config')->get('site');
        if($lat&&$lng){
            if($site_config && $site_config['show_shop'] == 2){ //show_shop:0显示所有商户 1显示配送范围内的 2显示距离范围内
                $pei_range = $site_config['pei_range'] ? $site_config['pei_range'] : 5;
                $squares = K::M('helper/round')->returnSquarePoint($lng, $lat,$pei_range);
                $_lat = array($squares['left-bottom']['lat'], $squares['right-top']['lat']);
                $_lng = array($squares['left-bottom']['lng'], $squares['right-top']['lng']);
                asort($_lat);
                asort($_lng);
                $filter['lat'] = $_lat[0].'~'.$_lat[1];
                $filter['lng'] = $_lng[0].'~'.$_lng[1];
            }
        }

        //获取商家分类选择
        if($cat_id = (int)$this->GP('cat_id')){
           $res = K::M('waimai/cate')->getChildren($cat_id);
           $filter['cate_id'] = $res;
        }
        if($cate_id = (int)$this->GP('cate_id')){
            unset($filter['cate_id']);
            $filter['cate_ids'] = 'LIKE:%,'.$cate_id.',%';
        }
        if($pet = $this->GP('pei_type')){
            if($pet==1){               
                $filter['pei_type']=0;//自己送
            }else if($pet==2){                
                $filter['pei_type']=1;//平台送
            }
        }

        //
        if($youhui = $this->GP('youhui')){
            if($youhui == 'sd'){
                $filter['hd_first_ltime'] = ">:".__TIME;
            }else if($youhui == 'mj'){
                $filter['hd_mj_ltime'] = ">:".__TIME;
            }else if($youhui == 'yhj'){
                $filter['hd_coupon_ltime'] = '>:'.__TIME;
            }else if($youhui == 'mf'){
                $filter['hd_mf_ltime'] = ">:".__TIME;
            }
        }

        //商家特色
        if($tese = $this->GP('ts')){
            if($tese == 'mian'){
                $filter['freight'] = 0;
            }else if($tese == 'zero'){
                $filter['min_amount'] = 0;
            }else if($tese == 'is_new'){
                $filter['is_new'] = 1;
            }else if($tese == 'online_pay'){
                $filter['online_pay'] = 1;
            }
        }
        // order 排序
        // 智能排序  juli   评分  pingfen  销量 sales 起送价 minprice 最早送达 song
        $order_by = array();
        $orderby = $this->GP('orderby');
        if($page <= 50 && $waimai_items = K::M('waimai/waimai')->items($filter, array(), 1, 500, $count)) {
            $_waimai_items =  $shop_ids = array();
            $group_ids = array();
            foreach($waimai_items as $kk1=>$vv1){
                if(in_array($vv1['pei_type'],array(1,2))){
                    $group_ids[$vv1['group_id']] =$vv1['group_id'];
                }

                /*4.0用户浏览过和买过排序*/
                $shop_ids[$vv1['shop_id']] = $vv1['shop_id'];
            }
            $group_list = K::M('pei/group')->items_by_ids($group_ids);
            foreach($waimai_items as $k=>$v) {
                if($v['yysj_status'] == 1 && $v['yy_status'] == 1){
                    $v['yyst'] = 1;
                }else{
                    $v['yyst'] = 0;
                }

                if($v['pei_type']==0){
                    if ($area_price = K::M('waimai/waimai')->get_shipping_fee($v['area_polygon'], $lat, $lng)) {// 配送范围内
                        $v['min_amount'] = $area_price['min_price'];
                        $v['freight'] = $area_price['shipping_fee'];                            
                    }else{
                        $max_freight = K::M('waimai/waimai')->get_min_data($v['area_polygon'], false);
                        $v['min_amount'] = $max['min_price'];
                        $v['freight'] = $max['shipping_fee'];
                    }
                    if($site_config && $site_config['show_shop'] == 1 && !$area_price){
                        continue;
                    }
                }else{
                    //新增商家单独配置 20171024 --begin
                    if($v['is_separate']==0){
                        $v['min_amount'] = $group_list[$v['group_id']]['min_amount'];
                    }
                    $juli = K::M('helper/round')->juli($v['lng'],$v['lat'], $lng, $lat);
                    if($v['is_separate']==1 && $v['config']){
                        $v['freight'] = K::M('waimai/waimai')->shipping_fee_by_type($v['config'], $juli);
                    }else{
                        $v['freight'] = K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($v['group_id']), $juli);
                    }
                    $in_polygon = K::M('helper/round')->in_or_out_polygon($group_list[$v['group_id']]['polygon_point'],$lat,$lng);
                    if($site_config && $site_config['show_shop'] == 1 && !$in_polygon){
                         continue;                                                      
                    }
                }
                unset($v['area_polygon']);
                if($tese){
                    if($tese == 'mian' && $v['freight'] == 0){
                        $_waimai_items[$k] = $v;
                    }else if($tese == 'zero' && $v['min_amount'] == 0){
                        $_waimai_items[$k] = $v;
                    }else{
                        $_waimai_items[$k] = $v;
                    }
                }else{
                    $_waimai_items[$k] = $v;
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
                    $val['is_refund'] = 1;

                    $_waimai_items[$k] = $val; 
                }
            }
            $_items = $_waimai_items;

            if($orderby == 'default') {
                uasort($_items, array(K::M('waimai/orderby'), 'default_order'));
            }
            if($orderby == 'juli'||!$orderby) {
                uasort($_items, array(K::M('waimai/orderby'), 'juli_order'));
            }
            if($orderby == 'pingfen') {
                uasort($_items, array(K::M('waimai/orderby'), 'score_order'));
            }
            if($orderby == 'minprice') {
                uasort($_items, array(K::M('waimai/orderby'), 'price_order'));
            }
            if($orderby == 'sales') {
                uasort($_items, array(K::M('waimai/orderby'), 'sales_order'));
            }
            if($orderby == 'song') {
                uasort($_items, array(K::M('waimai/orderby'), 'ptime_order'));
            }

            //2019-01-14 无论什么情况，一次只加载10个！
            $items = array_slice($_items, ($page-1)*$limit, $limit, true);

            //$items = array_slice($_items,($page-1)*10,10,true);  // 每次取10条记录，偏移量为$page-1
            // if(($pagelimit = (int)$this->GP('pagelimit')) && ($page == 1)){
            //    $items = array_slice($_items, 0, $limit*$pagelimit, true);              
                /*if($page==1){
                    $items = array_slice($_items,0,10*$scrollPage,true);
                }else{
                    $items = array_slice($_items,($page-2)*10+$scrollPage*10,10,true);
                }*/
            // }else{
            //    $items = array_slice($_items, ($page-1)*$limit, $limit, true);  // 每次取10条记录，偏移量为$page-1
            // }

        }else{
            $items = array();
        }

        //$this->msgbox->set_data('pagelimit', array('count'=>count($items), 'items'=>array_values($items), 'pagelimit'=>$pagelimit));

        //echo '<pre>';print_r($items);die;

        //3.8新增 是否显示  热销商品   2018 01 15 begin
        /*if(K::M('waimai/config')->get_hot_show()){
            foreach($items_wai as $k=>$v){
                $items_wai[$k]['products'] = K::M('waimai/product')->items(array('shop_id'=>$v['shop_id'],'is_hot'=>1,'closed'=>0,'is_onsale'=>1),null,1,4);
            }
        }*/
        //3.8新增 是否显示  热销商品   2018 01 15 end
                
        $shop_ids = array();        
        foreach($items as $k=>$v){
            $shop_ids[$v['shop_id']] = $v['shop_id'];
            $v['logo'] = $v['logo'];
            $v['products'] = array();
            $v['huodong'] = array();
            $items[$k] = $v;
        }

        $is_index = $this->GP('index') ? $this->GP('index') : false;
        $show_huodong = true;
        if($is_index){
            $show_huodong = K::M('adv/themes')->show_huodong();
        }

        if($show_huodong){
            $huodong = K::M('waimai/waimai')->get_huodong($shop_ids);
            foreach($items as $k=>$v){                    
                $items[$k]['huodong'] = (array)$huodong[$k];
            }
        }

        $count_num = count($items);
        if($count_num <= 9){
            $loadst = 0;
        }else{
            $loadst = 1;
        }

        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = 'shoplist/loadshops.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }

    public function search($search=0)
    { 
        if($search){
            $this->pagedata['search'] = 1;
        }       
        if($title = $this->GP('title')){
            $this->pagedata['title'] = $title;
        }

        $cfg = $this->system->config->get('hotwaimai');
        $cfg = str_replace('，', ',', $cfg['hotwaimai']);
        $this->pagedata['hotwaimai'] = explode(',', $cfg);

        $this->tmpl = 'shoplist/search.html';
    }

    public function loadsearchs($page=1)
    {        
        $filter_shop = array();
        $title = $this->GP('title');
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

        $page = max((int)$page, 1);
        if($lng && $lat){
            $filter = $pager = array();
            $filter['audit'] = 1;
            $filter['closed'] = 0;
            $filter['verify_name'] = 1;
            //使用此函数计算得到结果后，带入sql查询。
            $site_config = K::M('system/config')->get('site');

            if($site_config && $site_config['show_shop'] == 2){ //show_shop:0显示所有商户 1显示配送范围内的 2显示距离范围内
                $pei_range = $site_config['pei_range'] ? $site_config['pei_range'] : 5;
                $squares = K::M('helper/round')->returnSquarePoint($lng, $lat,$pei_range);
                $_lat = array($squares['left-bottom']['lat'], $squares['right-top']['lat']);
                $_lng = array($squares['left-bottom']['lng'], $squares['right-top']['lng']);
                asort($_lat);
                asort($_lng);
                $filter['lat'] = $_lat[0].'~'.$_lat[1];
                $filter['lng'] = $_lng[0].'~'.$_lng[1];
            }

            //$filter['yy_status'] = 1;// 取手动营业中的
            $orderby = array('orders'=>'desc');

            $filter[':SQL'] = " (w.`closed`=0 AND w.`is_onsale`=1)";
            if($title){
                $filter[':SQL'] .= " AND (o.`title` LIKE '%".$title."%' OR w.`title` LIKE '%".$title."%')";
            }
            if($waimai_items = K::M('waimai/waimai')->items_join_product($filter, $orderby, 1, 500, $count)){
                $shop_ids = $group_ids = array();
                foreach ($waimai_items as $k => $v) {
                    if(in_array($v['pei_type'],array(1,2))){
                        $group_ids[$v['group_id']] =$v['group_id'];
                    }
                    if ($v['yysj_status'] == 1&&$v['yy_status']==1) {// 取序列化配置的营业时间
                        $v['yyst'] = 1;
                    }else{
                        $v['yyst'] = 0;
                    }
                    $v['juli'] = K::M('helper/round')->juli($v['lng'], $v['lat'], $lng, $lat);
                    $waimai_items[$k] = $v;
                }

                $group_list = K::M('pei/group')->items_by_ids($group_ids);
                foreach ($waimai_items as $k => $v) {
                    if($v['pei_type'] == 0){
                        if($area_price = K::M('waimai/waimai')->get_shipping_fee($v['area_polygon'], $lat, $lng)){
                            $v['min_amount'] = $area_price['min_price'];
                            $v['freight'] = $area_price['shipping_fee'];
                            unset($v['area_polygon']);
                            $waimai_items[$k] = $v;
                        }
                        if($site_config && $site_config['show_shop'] == 1 && !$area_price){
                            unset($waimai_items[$k]);
                        }

                    }else{
                        if($res = K::M('helper/round')->in_or_out_polygon($group_list[$v['group_id']]['polygon_point'], $lat, $lng)){                            
                            //新增商家单独配置 20171024 --begin
                            if($v['is_separate']==0){
                                $v['min_amount'] = $group_list[$v['group_id']]['min_amount'];
                            }
                            //$juli = K::M('helper/round')->juli($v['lng'],$v['lat'], $lng, $lat);

                            if($v['is_separate'] == 1 && $v['config']){
                                $v['freight'] = K::M('waimai/waimai')->shipping_fee_by_type($v['config'],$v['juli']);
                            }else{
                                $v['freight'] = K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($v['group_id']),$juli);
                            }
                            //新增商家单独配置 20171024 --end
                            unset($v['area_polygon']);
                            $waimai_items[$k] = $v;
                        }

                        if($site_config && $site_config['show_shop'] == 1 && !$res){
                            unset($waimai_items[$k]);
                        }
                    }
                }

                if($waimai_items){
                    uasort($waimai_items, array(K::M('waimai/orderby'), 'default_order_juli'));
                    $items = array_slice($waimai_items, ($page-1)*10, 10, true);
                    foreach ($items as $k => $v) {
                        $shop_ids[$v['shop_id']] = $v['shop_id'];
                       
                        $v['avg_score'] = ($v['score']/$v['comments']) ? round($v['score']/$v['comments'],1) : 0 ;
                        $v['avg_score'] = min(max(0, $v['avg_score']), 5);
                        $v['juli_label'] = K::M('helper/format')->juli($v['juli']);
                        $v['products'] = array();
                        $v['huodong'] = array();
                        $v['title'] = str_replace($title, htmlspecialchars("<span class='light'>".$title."</span>"), $v['title']);
                        $v['title'] = htmlspecialchars_decode($v['title']);
                        $items[$k] = $this->filter_fields('shop_id,title,logo,orders,min_amount,freight,pei_type,huodong,is_new,yyst,avg_score,juli,juli_label,products,huodong,tips_label', $v);
                    }

                    $p_filter = array('closed'=>0, 'is_onsale'=>1, 'shop_id'=>$shop_ids);
                    $p_orderby = array('sales'=>'desc', 'good'=>'desc', 'product_id'=>'desc');
                    if($title){
                        $p_filter['title'] = 'LIKE:%'.$title.'%';
                    }
                    if($product_lists = K::M('waimai/product')->select($p_filter, $p_orderby)){
                        foreach ($product_lists as $k => $v) {
                            if($items[$v['shop_id']]){
                               
                                $v['title'] = str_replace($title, htmlspecialchars("<span class='light'>".$title."</span>"), $v['title']);
                                $v['title'] = htmlspecialchars_decode($v['title']);
                                $items[$v['shop_id']]['products'][] = $this->filter_fields('product_id,shop_id,title,photo,price,sales,good', $v);
                            }
                        }
                    }

                    $huodong = K::M('waimai/waimai')->new_get_huodong($shop_ids);
                    foreach ($huodong as $k => $v) {
                        if($items[$k]){
                            $items[$k]['huodong'] = $v;
                        }
                    }                   
                }else{
                    $items = array();
                }
            }else{
                $items = array();
            }           
        }else{
            $items = array();
        }

        $count_num = count($items);
        if($count_num <= 10){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        //echo '<pre>';print_r($items);die;
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = 'shoplist/loadsearchs.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }

}