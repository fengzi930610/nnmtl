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
Import::L('yilianyun/YLYSignAndUuidClient.php');
class Ctl_Order extends Ctl
{
    // 确认订单
    public function order($shop_id)
    {
        $this->check_login();
        // 获取用户经纬度坐标
        $lng = (float)$this->request['UxLocation']['lng'];
        $lat = (float)$this->request['UxLocation']['lat'];
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('商家不能为空',221)->response();
        }else if(!$detail = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('商家不存在',222)->response();
        }elseif ($detail['yy_status'] == 0 || $detail['yysj_status'] == 0) {// 手动闭店 或 打烊
            $this->msgbox->add('商家已经打烊不可下单',223)->response();
        }elseif (!($cart = $this->getmarketcart($shop_id)) || empty($cart[$shop_id])) {// 获取用户购物车信息判断是否点餐 使用cookie的购物车废除
            $this->msgbox->add('你还没有点餐呢',223)->response();
        }else{
            $is_ziti = (($this->GP('is_ziti')==1)&&$detail['can_zero_ziti']==1)?1:0;
            $detail = K::M('waimai/waimai')->format_data($detail);
            $m_addr = array();
            $area_price = K::M('waimai/waimai')->get_shipping_fee($detail['area_polygon'], $lat, $lng);

            $freight_stage = $area_price['shipping_fee']; // 这里兼容旧版，重新赋值配送费
            $tmp_detail = $detail;
            $detail['min_amount'] = $area_price['min_price'];// 这里兼容旧版，重新赋值起送价。
            if($detail['pei_type']==1||$detail['pei_type']==2){
                $group_detail = K::M('pei/group')->detail($detail['group_id']);
                //新增商家单独配置 20171024 叶超 --begin
                if($detail['is_separate']==0){
                    $detail['min_amount'] = $group_detail['min_amount'];
                }else{
                    $detail['min_amount'] = $tmp_detail['min_amount'];
                }
                //新增商家单独配置 20171024 叶超 --end
            }

            $addr_list = K::M('member/addr')->items(array('uid'=>$this->uid),null,1,50,$count_addr);//
            foreach($addr_list as $addr_k =>$addr_v){
                $addr_list[$addr_k]['juli'] = K::M('helper/round')->juli($detail['lng'],$detail['lat'],$addr_v['lng'],$addr_v['lat']);
            }
            uasort($addr_list, array($this, 'juli_order'));

            if($detail['pei_type']==0){
                foreach($addr_list as $k=>$v){
                    foreach ($detail['area_polygon']['polygon_point'] as $kkkk => $vvvv) {
                        if(K::M('helper/round')->in_or_out_polygon($vvvv, $v['lat'],$v['lng'])){
                            $addr_list[$k]['is_in'] = 1;
                            if ($m_addr && $v['is_default'] == 1) {
                                $m_addr = $v;
                                break;
                            }elseif (empty($m_addr)) {
                                $m_addr = $v;
                                break;
                            }
                        }
                    }
                }
            }else{
                foreach($addr_list as $k=>$v){
                    if(K::M('helper/round')->in_or_out_polygon($group_detail['polygon_point'], $v['lat'],$v['lng'])){
                        if ($m_addr && $v['is_default'] == 1) {
                            $m_addr = $v;
                            break;
                        }elseif (empty($m_addr)) {
                            $m_addr = $v;
                            break;
                        }
                    }
                }
            }

            if($detail['pei_type']==0){
                if($area_price = K::M('waimai/waimai')->get_shipping_fee($area_polygon, $m_addr['lat'], $m_addr['lng'])){//2017/03/31
                    $freight_stage = $area_price['shipping_fee'];
                    $detail['min_amount'] = $area_price['min_price'];
                }
            }else{
                if(!$juli= K::M('magic/baidu')->juli($detail['lng'], $detail['lat'], $m_addr['lng'], $m_addr['lat'])){
                    $juli = K::M('helper/round')->juli($detail['lng'], $detail['lat'],$m_addr['lng'], $m_addr['lat']);
                }
                if($detail['is_separate']==0){
                    $detail['min_amount'] = $group_detail['min_amount'];
                }

                if($detail['is_separate']==1&&$detail['config']){
                    $freight_stage = K::M('waimai/waimai')->shipping_fee_by_type($detail['config'],$juli);
                }else{
                    $freight_stage = K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($detail['group_id']),$juli);
                }
            }
            $freight_stage = $m_addr ? $freight_stage : 0;

            $product_number = $package_price = $product_price = $product_oldprice = 0;
            $products = "";

            foreach($cart as $k=>$v){
                if($k != $shop_id){
                    $this->msgbox->add('商品不是同一家商家的',202)->response();
                }else{
                    foreach($v as $kk=>$vv) {
                        $pk = $vv['product_id'].'-'.$vv['spec_id'].$vv['shuxin'];
                        $product_ids[$vv['product_id']] = $vv['product_id'];
                        $spec_ids[$vv['spec_id']] = $vv['spec_id'];
                        $product_numbers[$pk] = $vv['number'];
                        $product_number += $vv['number'];

                        $cart_product_list[$pk] = array('product_id'=>$vv['product_id'], 'number'=>$vv['number'], 'spec_id'=>$vv['spec_id'],"shuxin"=>$vv['shuxin']);
                    }
                }
            }

            $must_count = K::M('waimai/product')->items(array('shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1,'is_must'=>1),array('product_id'=>"DESC"),1,100,$count_count);

            if($must_count){
                $must_ids = array();
                foreach($must_count as $kk22=>$vv22){
                    $must_ids[$vv22['product_id']] = $vv22['product_id'];
                }
                if(!array_intersect($must_ids,$product_ids)){
                    $this->msgbox->add('请选择必点商品',555)->response();
                }
            }

            $discount = $detail['discount'] ? $detail['discount'] : array();

            $product_list = K::M('waimai/product')->items_by_ids($product_ids);
            foreach ($product_list as $k=>$v){
                if($v['is_onsale']==0){
                    $this->msgbox->add($v['title'].'已下架',250)->response();
                }else{//新增折扣商品的信息 
                    $v['is_discount'] = 0;                  
                    if($dp = $discount['products'][$v['product_id']]){
                        $v['is_discount'] = 1;
                        $v['sale_sku'] = $dp['sale_sku'];
                        $v['sale_type'] = 1;                           
                    }
                    $product_list[$k] = $v;
                }
            }
            $spec_lists = K::M('waimai/productspec')->items_by_ids($spec_ids);
            foreach ($spec_lists as $k => $v) {//新增折扣商品的信息
                $v['is_discount'] = 0;
                if($dp = $discount['products'][$v['product_id']]){
                    $v['is_discount'] = 1;
                    $v['sale_sku'] = $dp['sale_sku'];
                    $v['sale_type'] = 1;
                }
                $spec_lists[$k] = $v;
            }

            $have_discount = 0;
            $product_price = $package_price = 0;
            foreach($cart_product_list as $pk=>$v){
                if(!$p = $product_list[$v['product_id']]){

                }else if($p['is_spec']){
                    $sp = $spec_lists[$v['spec_id']];
                    if(!$v['spec_id'] && $sp){

                        $this->msgbox->add('商品未选规格sku',231)->response();
                    }else if($sp['product_id'] != $v['product_id']){

                        $this->msgbox->add('选择规格与商品ID关联不符',232)->response();
                    }else if($sp['sale_type'] == 1 && ($sp['sale_sku'] < $product_numbers[$pk])){
                        $this->msgbox->add('商品库存不足',233)->response();
                    }else{
                        $product_lists[$pk]['title'] = $p['title'];
                        $product_lists[$pk]['spec_name'] = $sp['spec_name'];
                        $product_lists[$pk]['num'] = $product_numbers[$pk];
                        $product_lists[$pk]['price'] = $sp['price'];
                        $product_lists[$pk]['unit'] = $p['unit'];
                        $product_price += $sp['price']  * $product_numbers[$pk];       //商品总价
                        $package_price += $sp['package_price'] * $product_numbers[$pk]; //总打包费
                        $products .= $sp['product_id'].":".$product_numbers[$pk];
                        $products .= ":".$sp['spec_id'].'&'.$v['shuxin'].',';
                        $tmp_shuxin = "";
                        $tmp_arr = explode("-",$v['shuxin']);
                        foreach($tmp_arr as $kkk1=>$vvv2){
                            if($vvv2){
                                $tmp_v5 = explode('_',$vvv2);
                                $tmp_shuxin.="+".$tmp_v5[1];
                            }
                        }
                        $product_lists[$pk]['shuxin'] = $tmp_shuxin;/*?'['.str_replace('-','+',$v['shuxin']).']':"";*/

                        $product_lists[$pk]['product_id'] = $sp['product_id'];
                        if($p['is_discount'] && !$have_discount){  
                            $have_discount = 1;
                        }
                    }
                }else{
                    if($p['sale_type'] == 1 && ($p['sale_sku'] < $product_numbers[$pk])){
                        $this->msgbox->add('商品库存不足',211)->response();
                    }else{
                        $product_lists[$pk]['title'] = $p['title'];
                        $product_lists[$pk]['num'] = $product_numbers[$pk];
                        $product_lists[$pk]['price'] = $p['price'];
                        $product_lists[$pk]['unit'] = $p['unit'];
                        $product_price += $p['price']  * $product_numbers[$pk];       //商品总价
                        $package_price += $p['package_price'] * $product_numbers[$pk]; //总打包费
                        $products .= $v['product_id'].":".$product_numbers[$pk];
                        $products .= ':0&'.$v['shuxin'].',';
                        $tmp_shuxin = "";
                        $tmp_arr = explode("-",$v['shuxin']);
                        foreach($tmp_arr as $kkk1=>$vvv2){
                            if($vvv2){
                                $tmp_v5 = explode('_',$vvv2);
                                $tmp_shuxin.="+".$tmp_v5[1];
                            }
                        }
                        $product_lists[$pk]['shuxin'] = $tmp_shuxin /*?'['.str_replace('-','+',$v['shuxin']).']':""*/;

                        $product_lists[$pk]['product_id'] = $p['product_id'];  //判断是否含有折扣商品
                        if($p['is_discount'] && !$have_discount){
                            $have_discount = 1;
                        }
                    }
                }
            }

            //4.1首单共享处理
            $cfg_huodong = K::M('waimai/config')->gethuodongconfig();
            $cfg_true_huodong = $cfg_huodong ? $cfg_huodong : array(
                'hongbao'=>0,
                'first'=>0,
                'manjian'=>0,
                'youhui'=>0,
                'first_share'=>0
            );
            $first_youhui = $youhui_amount = $hongbao_amount = $coupon_amount = 0;
            if($detail['first']['type']==0){
                if($detail['first']['first_amount'] && !K::M('order/order')->count(array('uid'=>$this->uid, 'from'=>'waimai', 'order_status'=>'>=:0'))){
                    $first_youhui = $detail['first']['first_amount']; // 第一单享受首单优惠
                }else{
                    $first_youhui = 0; // 不是第一单不享受首单优惠.0
                }
            }else if($detail['first']['type']==1){
                if($detail['first']['first_amount'] && !K::M('order/order')->count(array('uid'=>$this->uid, 'shop_id'=>$shop_id, 'from'=>'waimai', 'order_status'=>'>=:0'))){
                    $first_youhui = $detail['first']['first_amount']; // 第一单享受首单优惠

                }else{
                    $first_youhui = 0; // 不是第一单不享受首单优惠.0
                }
            }

            if($first_youhui > 0 && !$cfg_true_huodong['first_share']){
                $discount = array();
            }
            
            $discount_amount = $product_discprice = 0;
            $newproducts = K::M('waimai/huodongdiscount')->get_newProducts($discount, $product_lists);
            $product_lists = K::M('waimai/order')->optimal_amount($discount, $newproducts);
            $discount_title = $discount['title'] ? $discount['title'] : '';
            if($have_discount && $discount){                
                foreach ($product_lists as $k => $v) {
                    $product_discprice += $v['prices'];
                }
                $discount_amount = $product_price-$product_discprice;
            }

            $products = rtrim($products,',');
            $this->pagedata['products'] = $products;

            if((($product_price+$package_price) < $detail['min_amount']) && $is_ziti==0){
               $this->msgbox->add('起送价没有达到配送要求',212)->response();
            }

            // 首单优惠
            $first_price = $yh_price = ($product_price + $package_price - $first_youhui);

            if(!$first_youhui || $cfg_true_huodong['first_share']){ //4.1首单优惠共享处理（不是首单||首单共享）
                if($have_discount){
                    $youhui_amount = $discount_amount;
                    $yh_price = $first_price - $discount_amount;
                }else{
                    if($youhui = K::M('waimai/huodongmj')->order_youhui($shop_id, $yh_price)){
                        $youhui_amount = $youhui['youhui_amount'];
                        $yh_price = $first_price - $youhui['youhui_amount'];  // 商品价格-首单优惠-满减优惠
                    }
                }
            }

            $coupon_price = $yh_price;
            $coupons = K::M('waimai/coupon')->get_coupons($this->uid,$shop_id,$yh_price);
            if($coupon = K::M('waimai/coupon')->get_coupon($this->uid,$shop_id,$yh_price)){
                $coupon_amount = $coupon['coupon_amount'];
                $coupon_price = $yh_price - $coupon['coupon_amount'];
                $yh_price = $yh_price - $coupon['coupon_amount'];
            }

            $hongbao_price = $yh_price;
            $hongbaos = K::M('hongbao/hongbao')->get_hongbaos($this->uid, $yh_price,'waimai');
            if($hongbao = K::M('hongbao/hongbao')->get_hongbao($this->uid, $yh_price,'waimai')){
                $hongbao_amount = $hongbao['amount'];
                $hongbao_price = $coupon_price - $hongbao['amount'];
                $yh_price = $coupon_price - $hongbao['amount'];
            }
            // 结算价格
            $total_price = $hongbao_price + $freight_stage ;
            // 总优惠
            $total_youhui = $first_youhui + $youhui_amount + $coupon_amount + $hongbao['amount'] + $discount_amount;
            // 送达时间选择列表

            //送达日期
            $day = $detail['yuyue_day']; //取的商家后台配置的数据--可预约天数
            if($day>0){
                $day_dates = K::M('waimai/order')->get_days($day, $detail['yy_weeks']);
            }else{
                $ch_week = array(0=>'日',1=>'一',2=>'二',3=>'三',4=>'四',5=>'五',6=>'六');
                $index = date('w',__TIME);
                $day_dates =array(array(
                    'date'=>date('Y-m-d'),
                    'day'=>'今天(周'.$ch_week[$index].")"
                )) ;
            }
            $this->pagedata['day_dates'] = $day_dates;
            if($detail['yuyue_day']>0){
                if($detail['pstime_type']){
                    $tmp_pei_time = $detail['yy_peitime'];
                }else{
                    $tmp_pei_time = $detail['ps_time']?$detail['ps_time']:$detail['yy_peitime'];
                }
                $set_time_date = K::M('waimai/order')->get_day_time('00:00','23:59',$tmp_pei_time);
                $this->pagedata['set_time_date'] = $set_time_date;
            }else{
                $this->pagedata['set_time_date'] = array();
            }

            if($member = K::M('member/member')->detail($this->uid)) {
                $this->pagedata['mymoney'] = $member['money'];
            }
        }
        if($total_price<=0){
            $total_price = 0.01;
        }
        
        $hongbao['count'] = count($hongbaos);
        $hongbao = $hongbaos[0]? $hongbaos[0]:array('hongbao_id'=>0);

        //4.0配送会员卡
        $peicards = $cards = array();
        $peicard_id = 0;
        $peicard_amount = 0;
        $have_peicard = 0;
        if($user_cards = K::M('peicard/member')->items(array('uid'=>$this->uid, 'ltime'=>'>=:'.__TIME))){
            $have_peicard = 1;
        }
        if($detail['pei_type'] == 1 && $is_ziti == 0 && $freight_stage){
            if($user_cards){
                $reduce = 0;
                $cids = array_keys($user_cards);
                $counts = K::M('peicard/log')->counts_group_by(array('uid'=>$this->uid, 'cid'=>$cids, 'day'=>date('Ymd')));
                K::M('system/logs')->log('aaa', array($user_cards, $counts));
                foreach ($user_cards as $k => $v) {
                    if($v['limits'] > $counts[$v['cid']]['count']){
                        $v['limits'] = $v['limits'] - $counts[$v['cid']]['count'];
                        $peicards[] = $this->filter_fields('cid,card_id,title,limits,reduce', $v);
                        if($peicard_id){
                            if(abs($freight_stage - $v['reduce']) < $reduce){
                                $reduce = abs($freight_stage - $v['reduce']);
                                $peicard_id = $v['cid'];
                                $peicard_amount = $v['reduce'] > $freight_stage ? $freight_stage : $v['reduce'];
                            }
                        }else{
                            $reduce = abs($freight_stage - $v['reduce']);
                            $peicard_id = $v['cid'];
                            $peicard_amount = $v['reduce'] > $freight_stage ? $freight_stage : $v['reduce'];
                        }
                    }
                }
            }else if($cards = K::M('peicard/card')->items(array('closed'=>0))){
                foreach ($cards as $k => $v) {
                    $cards[$k] = $this->filter_fields('card_id,title,days,limits,reduce,amount', $v);
                }
            }
        }

        //4.0换购商品（过滤掉已选择过的商品）
        $hg_pros = array();
        if($huangou = K::M('waimai/huodonghuangou')->get_huangou($shop_id, ($product_price+$package_price))){
            $pids = array_keys($huangou['products']);
            if($p_items = K::M('waimai/product')->items_by_ids($pids)){
                foreach ($p_items as $k => $v) {
                    if(!in_array($k, $product_ids)){
                        $v['photo'] = K::M('magic/upload')->geturl($v['photo'], true);
                        //$v = $this->filter_fields($this->_allow_product_fields, $v);
                        $hg_pros[] = K::M('waimai/huodonghuangou')->get_newProduct($huangou, $v);
                    }
                }
            }
        }

        //外卖3.8新增 自提无需起送价
        $this->pagedata['product_number'] = $product_number;  //
        $this->pagedata['total'] = $total_price + $total_youhui;  // 货到付款方式不享受所有优惠
        $this->pagedata['total_price'] = $total_price;  // 结算价格
        $this->pagedata['total_youhui'] = $total_youhui;
        $this->pagedata['coupon'] = $coupon;
        $this->pagedata['coupons'] = $coupons;
        $this->pagedata['hongbao'] = $hongbao;
        $this->pagedata['hongbaos'] = $hongbaos;
        $this->pagedata['product_price'] = $product_price;
        $this->pagedata['youhui'] = $youhui;
        $this->pagedata['yh_price'] = $yh_price;
        $this->pagedata['first_youhui'] = $first_youhui;
        $this->pagedata['package_price'] = $package_price;
        $this->pagedata['freight_stage'] = $freight_stage;
        $this->pagedata['product_list'] = array_values($product_lists);
        $this->pagedata['detail'] = $detail;
        $this->pagedata['maddr'] = $m_addr;
        $this->pagedata['is_ziti'] = $is_ziti;
        $this->pagedata['package_price_product_price'] = $product_price+$package_price;
        
        $this->pagedata['first_share'] = $cfg_true_huodong['first_share'];
        $this->pagedata['huodong_cfg'] = json_encode($cfg_true_huodong);

        $this->pagedata['discount_amount'] = $discount_amount;//新增折扣优惠
        $this->pagedata['have_discount'] = $have_discount;
        $this->pagedata['discount_title'] = $discount_title;

        //4.0
        $this->pagedata['shop_id'] = $shop_id;
        $this->pagedata['json_huangou'] = json_encode($this->filter_fields('title,quota,shop_id',$huangou));
        $this->pagedata['huangous'] = array_values($hg_pros);
        $this->pagedata['peicards'] = array_values($peicards);
        $this->pagedata['peicard_id'] = $peicard_id;
        $this->pagedata['peicard_amount'] = $peicard_amount;
        $this->pagedata['cards'] = array_values($cards);
        $this->pagedata['have_peicard'] = $have_peicard;

        $this->tmpl = 'order/order.html';
    }
    
    /*
     * @param shop_id
     * @param addr_id
     * @param amount
     */
    public function preinfo()
    {
        $this->check_login();
        if(!$shop_id = (int)$this->GP('shop_id')){
            $this->msgbox->add(L('参数非法'), 221)->response();
        }else if(($total_price = (float)$this->GP('total_price'))<0){
            $this->msgbox->add(L('参数非法'), 221)->response();
        }else if(($product_price = (float)$this->GP('product_price'))<0){
            $this->msgbox->add(L('参数非法'), 221)->response();
        }else if(!$shop = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add(L('商家不存在'), 221)->response();
        }elseif(!in_array((int)$this->GP('is_ziti'), array(0, 1))) {// add by zhuhongwei 保证了用户端提交参数的合法性(如果用户修改此参数，则后面直接绕过配送费计算过程了)
            $this->msgbox->add(L('参数非法'), 221)->response();
        }elseif(!($cart = $this->getmarketcart($shop_id)) || empty($cart[$shop_id])) {// 获取用户购物车信息判断是否点餐 使用cookie的购物车废除
            $this->msgbox->add('你还没有点餐呢',223)->response();
        }else{
            $shop = K::M('waimai/waimai')->format_data($shop);
            //打包费
            $packprice = $this->GP('package');
            $addr_id = (int)$this->GP('addr_id');
            $hongbao_id = (int)$this->GP('hongbao_id');
            $coupon_id = (int)$this->GP('coupon_id');
            $is_ziti =  (int)$this->GP('is_ziti')==1?1:0;
            $online_pay = (int)$this->GP('online_pay');

            $discount_amount = $this->GP('discount_amount');

            $freight_stage = 0;
            $addr = $hongbao = $youhui = array();
            $first_youhui = $hongbao_amount = $delivery_amount = $youhui_amount = 0;
            $cfg_huodong = K::M('waimai/config')->gethuodongconfig();
            $cfg_true_huodong = $cfg_huodong?$cfg_huodong:array(
                'hongbao'=>0,
                'first'=>0,
                'manjian'=>0,
                'youhui'=>0,
                'first_share'=>0
            );

            $product_number = $package_price = $product_price = $product_oldprice = 0;
            $products = "";
            foreach($cart as $k=>$v){
                if($k != $shop_id){
                    $this->msgbox->add('商品不是同一家商家的',202)->response();
                }else{
                    foreach($v as $kk=>$vv) {
                        $pk = $vv['product_id'].'-'.$vv['spec_id'].$vv['shuxin'];
                        $product_ids[$vv['product_id']] = $vv['product_id'];
                        $spec_ids[$vv['spec_id']] = $vv['spec_id'];
                        $product_numbers[$pk] = $vv['number'];
                        $product_number += $vv['number'];
                        $cart_product_list[$pk] = array('product_id'=>$vv['product_id'], 'number'=>$vv['number'], 'spec_id'=>$vv['spec_id'],"shuxin"=>$vv['shuxin']);
                    }
                }
            }

            $must_count = K::M('waimai/product')->items(array('shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1,'is_must'=>1),array('product_id'=>"DESC"),1,100,$count_count);
            if($must_count){
                $must_ids = array();
                foreach($must_count as $kk22=>$vv22){
                    $must_ids[$vv22['product_id']] = $vv22['product_id'];
                }
                if(!array_intersect($must_ids,$product_ids)){
                    $this->msgbox->add('请选择必点商品',555)->response();
                }
            }

            $discount = $shop['discount'] ? $shop['discount'] : array();
            $product_list = K::M('waimai/product')->items_by_ids($product_ids);
            foreach ($product_list as $k=>$v){
                if($v['is_onsale']==0){
                    $this->msgbox->add($v['title'].'已下架',250)->response();
                }else{//新增折扣商品的信息 
                    $v['is_discount'] = 0;                  
                    if($dp = $discount['products'][$v['product_id']]){
                        $v['is_discount'] = 1;
                        $v['sale_sku'] = $dp['sale_sku'];
                        $v['sale_type'] = 1;                           
                    }
                    $product_list[$k] = $v;
                }
            }
            $spec_lists = K::M('waimai/productspec')->items_by_ids($spec_ids);
            foreach ($spec_lists as $k => $v) {//新增折扣商品的信息
                $v['is_discount'] = 0;
                if($dp = $discount['products'][$v['product_id']]){
                    $v['is_discount'] = 1;
                    $v['sale_sku'] = $dp['sale_sku'];
                    $v['sale_type'] = 1;
                }
                $spec_lists[$k] = $v;
            }

            $have_discount = 0;
            foreach($cart_product_list as $pk=>$v){
                if(!$p = $product_list[$v['product_id']]){

                }else if($p['is_spec']){
                    $sp = $spec_lists[$v['spec_id']];
                    if(!$v['spec_id'] && $sp){
                        $this->msgbox->add('商品未选规格sku',231)->response();
                    }else if($sp['product_id'] != $v['product_id']){
                        $this->msgbox->add('选择规格与商品ID关联不符',232)->response();
                    }else if($sp['sale_type'] == 1 && ($sp['sale_sku'] < $product_numbers[$pk])){
                        $this->msgbox->add('商品库存不足',233)->response();
                    }else{
                        $product_lists[$pk]['title'] = $p['title'];
                        $product_lists[$pk]['spec_name'] = $sp['spec_name'];
                        $product_lists[$pk]['num'] = $product_numbers[$pk];
                        $product_lists[$pk]['price'] = $sp['price'];
                        $product_lists[$pk]['unit'] = $p['unit'];
                        $product_price += $sp['price']  * $product_numbers[$pk];       //商品总价
                        $package_price += $sp['package_price'] * $product_numbers[$pk]; //总打包费
                        $products .= $sp['product_id'].":".$product_numbers[$pk];
                        $products .= ":".$sp['spec_id'].'&'.$v['shuxin'].',';
                        $tmp_shuxin = "";
                        $tmp_arr = explode("-",$v['shuxin']);
                        foreach($tmp_arr as $kkk1=>$vvv2){
                            if($vvv2){
                                $tmp_v5 = explode('_',$vvv2);
                                $tmp_shuxin.="+".$tmp_v5[1];
                            }
                        }
                        $product_lists[$pk]['shuxin'] = $tmp_shuxin;/*?'['.str_replace('-','+',$v['shuxin']).']':"";*/
                        $product_lists[$pk]['product_id'] = $sp['product_id'];
                        if($p['is_discount'] && !$have_discount){  
                            $have_discount = 1;
                        }
                    }
                }else{
                    if($p['sale_type'] == 1 && ($p['sale_sku'] < $product_numbers[$pk])){
                        $this->msgbox->add('商品库存不足',211)->response();
                    }else{
                        $product_lists[$pk]['title'] = $p['title'];
                        $product_lists[$pk]['num'] = $product_numbers[$pk];
                        $product_lists[$pk]['price'] = $p['price'];
                        $product_lists[$pk]['unit'] = $p['unit'];
                        $product_price += $p['price']  * $product_numbers[$pk];       //商品总价
                        $package_price += $p['package_price'] * $product_numbers[$pk]; //总打包费
                        $products .= $v['product_id'].":".$product_numbers[$pk];
                        $products .= ':0&'.$v['shuxin'].',';
                        $tmp_shuxin = "";
                        $tmp_arr = explode("-",$v['shuxin']);
                        foreach($tmp_arr as $kkk1=>$vvv2){
                            if($vvv2){
                                $tmp_v5 = explode('_',$vvv2);
                                $tmp_shuxin.="+".$tmp_v5[1];
                            }
                        }
                        $product_lists[$pk]['shuxin'] = $tmp_shuxin /*?'['.str_replace('-','+',$v['shuxin']).']':""*/;

                        $product_lists[$pk]['product_id'] = $p['product_id'];  //判断是否含有折扣商品
                        if($p['is_discount'] && !$have_discount){
                            $have_discount = 1;
                        }
                    }
                }
            }

            if($online_pay == 1 || ($shop['pei_type'] == 1 && $online_pay == 0 && $cfg_true_huodong['first'] == 1 && $is_ziti == 0)){
                if($shop['first']['type'] == 0){
                    if($shop['first']['first_amount'] && !$count_staff=K::M('order/order')->count(array( 'uid'=>$this->uid, 'from'=>'waimai','order_status'=>'>=:0'))){
                        $first_youhui = $shop['first']['first_amount'];
                    }
                }else if($shop['first']['type'] == 1){
                    if($shop['first']['first_amount'] && !$count_staff=K::M('order/order')->count(array('shop_id'=>$shop_id, 'uid'=>$this->uid, 'from'=>'waimai','order_status'=>'>=:0'))){
                        $first_youhui = $shop['first']['first_amount'];
                    }
                }
            }else{
                $first_youhui = 0;
            }

            $is_first = $this->GP('is_first');
            if($first_youhui > 0 && !$cfg_true_huodong['first_share']){
                if($is_first){
                    $discount = array();
                }else{
                    $first_youhui = 0;
                }                
            }

            $discount_amount = $product_discprice = 0;
            $newproducts = K::M('waimai/huodongdiscount')->get_newProducts($discount, $product_lists);
            $product_lists = K::M('waimai/order')->optimal_amount($discount, $newproducts);
            $discount_title = $discount['title'] ? $discount['title'] : '';
            if($have_discount && $discount){                
                foreach ($product_lists as $k => $v) {
                    $product_discprice += $v['prices'];
                }
                $discount_amount = $product_price-$product_discprice;
            }

            //修改  需要添加打包费
            //修改满减条件   2017-11-2 edit by 叶超
            if(!$first_youhui || $cfg_true_huodong['first_share'] || !$is_first){ //4.1首单优惠共享处理（不是首单||首单共享||不享受首单）
                if($have_discount){
                    $youhui_amount = $discount_amount;
                }else{
                    if($online_pay == 1 || (($shop['pei_type'] == 1 && $online_pay == 0 && $cfg_true_huodong['manjian'] == 1 && $is_ziti == 0))){
                        if($youhui = K::M('waimai/huodongmj')->order_youhui($shop_id, $product_price-$first_youhui+$packprice)){
                            $youhui_amount = $youhui['youhui_amount'];
                        }
                    }else{
                        $youhui_amount  = 0;
                    }
                }
            }
            
            if ($is_ziti == 0) {
                if((!$addr = K::M('member/addr')->detail($addr_id))||($addr['uid']!=$this->uid)){
                    $this->msgbox->add('请选择一个配送地址',998)->response();
                }
                if($shop['pei_type']==0){
                    if (!$area_price = K::M('waimai/waimai')->get_shipping_fee($shop['area_polygon'], $addr['lat'], $addr['lng'])) {
                        $this->msgbox->add('当前收货地址不在商家配送范围',210)->response();
                    }
                    $freight_stage = $area_price['shipping_fee'];
                }else{
                    if(!$group_detail = K::M('pei/group')->detail($shop['group_id'])){
                       $this->msgbox->add('商铺所属的配送在不存在或者已关闭',211)->response();
                    }
                    if(!K::M('helper/round')->in_or_out_polygon($group_detail['polygon_point'], $addr['lat'],$addr['lng'])){
                        $this->msgbox->add('当前收货地址不在商家配送范围',212)->response();
                    }

                    if(!$juli= K::M('magic/baidu')->juli($shop['lng'], $shop['lat'], $addr['lng'], $addr['lat'])){
                        $juli = K::M('helper/round')->juli($shop['lng'], $shop['lat'], $addr['lng'], $addr['lat']);
                    }

                    if($shop['is_separate']==1&&$shop['config']){
                        $freight_stage = K::M('waimai/waimai')->shipping_fee_by_type($shop['config'],$juli);
                    }else{
                        $freight_stage = K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($shop['group_id']),$juli);
                    }
                }
            }
            $filter = array('uid'=>$this->uid,'order_id'=>0,'shop_id'=>$shop_id,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME,);
            //添加打包费判断
            $filter['order_amount'] = '<=:' .($product_price-$first_youhui-$youhui_amount+$packprice);
            if($coupon_id == -1){
                $coupon_id = -1;
                $coupon = array();
            }elseif($coupon_id == 0){ //查出最符合条件优惠券
                //修改优惠券的使用条件 edit by 叶超  2017-11-2
                if($online_pay==1||(($shop['pei_type']==1&&$online_pay==0&&$cfg_true_huodong['youhui']==1&&$is_ziti==0))){
                    $meet_coupon = K::M('waimai/coupon')->find($filter,array('coupon_amount'=>'desc'));
                    $coupon_amount = $meet_coupon['coupon_amount'];
                    $coupon = $meet_coupon;
                    $coupon_id = $meet_coupon['coupon_id'];
                }else{
                    $coupon = array();
                    $coupon_id = 0;
                }
                //修改优惠券的使用条件 edit by 叶超  2017-11-2
            }elseif($coupon_id && !($coupon = K::M('waimai/coupon')->detail($coupon_id))){ //优惠券不存在
                $hongbao = array();
                $coupon_id = 0;
            }else if($coupon['uid'] != $this->uid||$coupon['shop_id'] != $shop_id||$coupon['ltime'] < __TIME||$coupon['stime'] > __TIME ||$coupon['order_id']>0){ //已使用,过期，不是自己的红包
                $coupon = array();
                $coupon_id = 0;
                //添加打包费
            }else if($coupon['order_amount'] > ($product_price-$first_youhui-$youhui_amount+$packprice)){ //未达到使用条件
                $coupon = array();
                $coupon_id = 0;
            }else{
                //修改 edit 叶超 2017-11-2
                if($online_pay==1||(($shop['pei_type']==1&&$online_pay==0&&$cfg_true_huodong['youhui']==1&&$is_ziti==0))){
                    $coupon_amount = $coupon['coupon_amount'];
                }else{
                    $coupon_amount = 0;
                }
                //修改 edit 叶超 2017-11-2
            }
            //修改 edit 叶超 2017-11-2
            if($online_pay==1||(($shop['pei_type']==1&&$online_pay==0&&$cfg_true_huodong['youhui']==1&&$is_ziti==0))){
                $coupon_count = K::M('waimai/coupon')->count($filter);
            }else{
                $coupon_count =0;
            }
            //修改 edit 叶超 2017-11-2            
            $filter2 = array('uid'=>$this->uid,'order_id'=>0);
            $filter2['ltime'] = '>:' . __TIME;
            //红包添加打包费判断
            $filter2['min_amount'] = '<=:' .($product_price-$first_youhui-$youhui_amount-$coupon_amount+$packprice);
            $min_amount = $product_price-$first_youhui-$youhui_amount-$coupon_amount+$packprice;
            //新增红包类型及时间判断
            $filter2['from'] = array('waimai','all');
            //$filter2[':SQL'] =  " ((limit_stime < ".__TIME." AND limit_ltime > ".__TIME.") OR ( limit_ltime <".__TIME.") OR (limit_ltime >".__TIME.") OR (limit_stime='' AND limit_ltime = ''))";
            if($hongbao_id == -1){
                $hongbao_id = -1;
                $hongbao = array();
            }elseif($hongbao_id == 0){ //查出最符合条件红包
                if($online_pay==1||(($shop['pei_type']==1&&$online_pay==0&&$cfg_true_huodong['hongbao']==1&&$is_ziti==0))){
                    //$meet_hongbao = K::M('hongbao/hongbao')->find($filter2,array('amount'=>'desc'));
                    $meet_hongbao = K::M('hongbao/hongbao')->get_hongbao($this->uid, $min_amount,'waimai');
                    $hongbao_amount = $meet_hongbao['amount'];
                    $hongbao = $meet_hongbao;
                    $hongbao_id = $meet_hongbao['hongbao_id'];
                }else{
                    $hongbao = array();
                    $hongbao_id = 0;
                }
            }elseif($hongbao_id && !($hongbao = K::M('hongbao/hongbao')->detail($hongbao_id))){ //红包不存在
                $hongbao = array();
                $hongbao_id = 0;
            }else if($hongbao['uid'] != $this->uid || $hongbao['ltime'] < __TIME || $hongbao['order_id']){ //已使用,过期，不是自己的红包
                $hongbao = array();
                $hongbao_id = 0;
                //添加打包费判断
            }else if($hongbao['min_amount'] > ($product_price-$first_youhui-$youhui_amount-$coupon_amount+$packprice)){ //未达到使用条件
                $hongbao = array();
                $hongbao_id = 0;
            }else if(!$res = K::M('hongbao/hongbao')->check_hongbao($hongbao,'waimai')){
                $hongbao = array();
                $hongbao_id = 0;
            }else{
                if($online_pay==1||(($shop['pei_type']==1&&$online_pay==0&&$cfg_true_huodong['hongbao']==1&&$is_ziti==0))){
                    $hongbao_amount = $hongbao['amount'];
                }else{
                    $hongbao_amount = 0;
                }
            }
            $hongbaos = K::M('hongbao/hongbao')->get_hongbaos($this->uid,$product_price-$first_youhui-$youhui_amount-$coupon_amount+$packprice,'waimai');
            if($online_pay==1||(($shop['pei_type']==1&&$online_pay==0&&$cfg_true_huodong['hongbao']==1&&$is_ziti==0))){
                $hongbao_count = count($hongbaos);
            }else{
                $hongbao_count =0;
            }

            $total_youhui = $first_youhui + $youhui_amount + $coupon_amount + $hongbao_amount;
            if($hongbaos[0]){
                if(!$res = K::M('hongbao/hongbao')->check_hongbao($hongbaos[0],'waimai')){
                    $hongbaos[0] = array(
                       'hongbao_id'=>0,
                    );
                }
            }

            //4.0超值换购
            $huangous = $hgpro_lists = array();
            $hgpro_price = $hgpro_package = $hgpro_price2 = $huangou_youhui = $hgpro_number = 0;
            if($huangou = K::M('waimai/huodonghuangou')->get_huangou($shop_id, ($product_price + $package_price))){
                $pids = array_keys($huangou['products']);
                if($hgpro_items = K::M('waimai/product')->items_by_ids($pids)){
                    foreach ($hgpro_items as $k => $v) {
                        if(!in_array($k, $product_ids)){
                            $v['photo'] = K::M('magic/upload')->geturl($v['photo'], true);
                            $v = $this->filter_fields($this->_allow_product_fields, $v);
                            $huangous[] = K::M('waimai/huodonghuangou')->get_newProduct($huangou, $v);
                        }
                    }
                }
            }else{
                $huangou = array();
            }           

            if($hg_cart = $this->GP['hg_products']){
                if(!$huangou){
                    $this->msgbox->add('超值换购活动不存在或已失效！',301)->response();
                }else{
                    $hg_pros = $pids = array();
                    $hg_cart = explode(',', $hg_cart);
                    foreach($hg_cart as $k=>$v){
                        $tmp = explode(':', $v);
                        if(!$hp = $huangou['products'][$tmp[0]]){
                            $this->msgbox->add('换购商品不存在或已删除！',302)->response();
                        }else if($hp['sale_sku'] < $tmp[1]){
                            $this->msgbox->add('换购商品库存不足！',303)->response();
                        }else if($hp['quota'] > 0 && $hp['quota'] < $tmp[1]){
                            $this->msgbox->add('换购商品超出限购数量！',304)->response();
                        }else{
                            $pids[$tmp[0]] = $tmp[0];
                            $hg_pros[] = array('product_id'=>$tmp[0], 'number'=>$tmp[1]);
                        }
                    }
                    //$hgpro_items = K::M('waimai/product')->items_by_ids($pids);
                    foreach ($hg_pros as $k => $v) {
                        if(!$hp = $hgpro_items[$v['product_id']]){
                            $this->msgbox->add('商品不存在或已删除！',305)->response();
                        }else if($hp['shop_id'] != $shop_id){
                            $this->msgbox->add('商品不是同一家商家的',306)->response();
                        }else{
                            $hp = $this->filter_fields($this->_allow_product_fields, $hp);
                            $hp['photo'] = K::M('magic/upload')->geturl($hp['photo'], true);
                            $hp['number'] = $v['number'];
                            $hgpro_lists[] = K::M('waimai/huodonghuangou')->get_newProduct($huangou, $hp);                           
                        }
                    }
                    //$hgpro_lists = K::M('waimai/huodonghuangou')->get_newProducts($huangou, $hgpro_lists);
                    foreach ($hgpro_lists as $k => $v) {
                        $hgpro_price += $v['oldprice']*$v['number'];
                        $hgpro_package += $v['package_price']*$v['number'];
                        $hgpro_price2 += $v['price']*$v['number'];
                        $hgpro_number += $v['number'];
                    }
                    $huangou_youhui = $hgpro_price - $hgpro_price2;
                }               
            }

            //4.0配送会员卡
            $peicard_id = (int)$this->GP('peicard_id'); //已有的会员卡ID
            $card_id = (int)$this->GP('pcard_id');       //需要购买的会员卡ID
            $peicards = $cards = array();
            $peicard_id2 = $card_id2 = $peicard_amount = $card_amount = 0;
            $have_peicard = 0;
            if($user_cards = K::M('peicard/member')->items(array('uid'=>$this->uid, 'ltime'=>'>=:'.__TIME))){
                $have_peicard = 1;
            }

            if($shop['pei_type'] == 1 && $is_ziti == 0 && $freight_stage){
                if($user_cards){
                    $cids = array_keys($user_cards);
                    $counts = K::M('peicard/log')->counts_group_by(array('uid'=>$this->uid, 'cid'=>$cids, 'day'=>date('Ymd')));                   
                    foreach ($user_cards as $k => $v) {
                        if($v['limits'] > $counts[$v['cid']]['count']){
                            $v['limits'] = $v['limits'] - $counts[$v['cid']]['count'];
                            $peicards[] = $this->filter_fields('cid,card_id,title,limits,reduce', $v);
                            if($peicard_id && ($peicard_id == $v['cid'])){
                                $peicard_id2 = $peicard_id;
                                $peicard_amount = $v['reduce'] > $freight_stage ? $freight_stage : $v['reduce'];
                            }
                        }
                    }
                }else if($cards = K::M('peicard/card')->items(array('closed'=>0))){
                    foreach ($cards as $k => $v) {                        
                        if($card_id && ($card_id == $v['card_id'])){
                            $card_id2 = $card_id;
                            $peicard_amount = $v['reduce'] > $freight_stage ? $freight_stage : $v['reduce'];
                            $card_amount = $v['amount'];
                        }
                        $cards[$k] = $this->filter_fields('card_id,title,days,limits,reduce,amount', $v);
                    }
                }
            }
            $total_youhui = $total_youhui + $peicard_amount;

            $data = array(
                'hongbao_id'=>$hongbaos[0]['hongbao_id']?$hongbaos[0]['hongbao_id']:0,
                'hongbao'=>$hongbaos[0]?$hongbaos[0]:array(),'coupon_id'=>$coupon_id,
                'coupon'=>$coupon,
                'coupon_count'=>$coupon_count,
                'addr_id'=>$addr_id, 'addr'=>$addr,
                'freight'=>$freight_stage,
                'youhui'=>$youhui,
                'first_youhui'=>$first_youhui,
                'youhui_amount'=>$youhui_amount,
                'hongbao_amount'=>$hongbao_amount,
                'hongbao_count'=>$hongbao_count,
                'hongbaos'=>$hongbaos,
                'total_youhui'=>$total_youhui,
                'peicard_amount'=>$peicard_amount,
                'card_amount'=>$card_amount,
                'pcard_id'=>$card_id2,
                'peicard_id'=>$peicard_id2,
                'cards'=>array_values($cards),
                'peicards'=>array_values($peicards),
                'product_list'=>array_values($product_lists),
                'discount_amount'=>$discount_amount,
                'discount_title'=>$discount_title
            );
            $this->msgbox->set_data('data', $data);
        }
    }
        
    // 创建订单
    public function ordercreate()
    {
        $this->check_login();        
        if(IS_AJAX){
            if($params = $this->checksubmit('params')){
                // 验证并拆分用户要求送达时间
                $pei_time = $pei_time_start =  $pei_time_last = 0;
                $opei_time = $params['pei_time'];
                if($opei_time){
                    $pei_time = strtotime($opei_time);
                }
                // 订单备注
                $note = $params['intro'];
                if(!$shop_id = (int) $params['shop_id']){
                    $this->msgbox->add('商家不能为空',221);
                }else if(!$shop_detail = K::M('waimai/waimai')->detail($shop_id)){
                    $this->msgbox->add('商家不存在',222);
                }else if($shop_detail['audit']!=1||$shop_detail['closed']!=0){
                    $this->msgbox->add('商家不存在或已删除',223);
                }else{
                    $shop_detail = K::M('waimai/waimai')->format_data($shop_detail);
                    if($shop_detail['yy_status'] == 0 || $shop_detail['yysj_status'] == 0){
                        $this->msgbox->add('商家已经打烊不可下单',224);
                    } else if(!$products = $params['products']){
                        $this->msgbox->add('您还没有订餐呢',226);
                    }elseif (!in_array((int)$params['pei_type'], array(0,1,2,3,4))) {// add by zhuhongwei 防止用户修改参数绕过验证
                        $this->msgbox->add('非法的参数提交',211);
                    }else if($params['pei_type'] == 3 && !$shop_detail['is_ziti']){
                        $this->msgbox->add('该商户不支持自提',226);
                    }else if($params['online_pay'] && !$shop_detail['online_pay']){
                        $this->msgbox->add('该商户不支持在线支付',226);
                    }else if(!$params['online_pay'] && !$shop_detail['is_daofu']){
                        $this->msgbox->add('该商户不支持货到付款',226);
                    }else if($params['pei_type'] != 3 && !($addr_id = (int)$params['addr_id'])){
                        $this->msgbox->add('请选择地址',227);
                    }else if($params['pei_type'] != 3 && !($addr_detail = K::M('member/addr')->detail($addr_id))){
                        $this->msgbox->add('地址不存在',228);
                    }else if(($addr_detail['uid'] != $this->uid)&&($params['pei_type']!=3)){
                        $this->msgbox->add('地址不存在',289);
                    }else if(!K::M('waimai/waimai')->check_pei_time($shop_id,$pei_time)){
                        $this->msgbox->add('配送时间不在商家营业时间内',318)->response();
                    }else{
                        if($params['pei_type']!=3){
                            if($shop_detail['pei_type'] == 0){
                                if(!$area_price = K::M('waimai/waimai')->get_shipping_fee($shop_detail['area_polygon'], $addr_detail['lat'], $addr_detail['lng'])){
                                    $this->msgbox->add('超出配送范围',319)->response();
                                }
                            }else if($shop_detail['pei_type'] == 1||$shop_detail['pei_type']==2){
                                if(!$group_detail = K::M('pei/group')->detail($shop_detail['group_id'])){
                                    $this->msgbox->add('商铺所属地配送站不存在或已关闭',320)->response();
                                }
                                if(!K::M('helper/round')->in_or_out_polygon($group_detail['polygon_point'], $addr_detail['lat'],$addr_detail['lng'])){
                                    $this->msgbox->add('超出配送范围',321)->response();
                                }

                                if($shop_detail['is_separate']==1){
                                    $area_price = array(
                                        'min_price'=>$shop_detail['min_amount']
                                    );
                                }else{
                                    $area_price = array(
                                        'min_price'=>$group_detail['min_amount']
                                    );
                                }
                            }
                        }else{
                            if($shop_detail['pei_type'] == 0){
                                $area_price = array(
                                    'min_price'=>0
                                );
                            }else {
                                if($shop_detail['is_separate']==1){
                                    $area_price = array(
                                        'min_price'=>$shop_detail['min_amount']
                                    );
                                }else{
                                    if(!$group_detail = K::M('pei/group')->detail($shop_detail['group_id'])){
                                        $group_detail = array();
                                    }
                                    $area_price = array(
                                        'min_price'=>$group_detail['min_amount']?$group_detail['min_amount']:0
                                    );
                                }
                            }
                        }
                        $data_order = $data_waimai = array();
                        // 验证订单商品信息
                        $products = explode(',',$products);
                        $shuxin = array();
                        foreach($products as $k=>$v){
                            $tmp = explode('&',$v);
                            $products[$k] =$tmp[0];
                            $shuxin[$k] = $tmp[1];
                        }
                        $product_ids = $spec_ids = $product_numbers = $product_specids = $order_product_list = array();
                        foreach ($products as $key => $val) {
                            if(preg_match('/^(\d+):(\d+):(\d+)$/', $val, $local)){
                                $pk = $local[1].'-'.$local[3].':'.$shuxin[$key];
                                $product_ids[$local[1]] = $local[1];
                                $spec_ids[$local[3]] = $local[3];
                                $product_numbers[$pk] = $local[2];
                                $cart_product_list[$pk] = array('product_id'=>$local[1], 'number'=>$local[2], 'spec_id'=>$local[3]);
                            }
                        }
                        $total_price = $product_price = $package_price = $product_number = $coupon_amount = $hongbao_amount = $first_youhui = $youhui_amount = $pei_amount  = $money = $amount = 0;
                        $product_list = K::M('waimai/product')->items_by_ids($product_ids);
                        $must_count = K::M('waimai/product')->items(array('shop_id'=>$shop_id,'closed'=>0,'is_onsale'=>1,'is_must'=>1),array('product_id'=>"DESC"),1,100,$count_count);

                        if($must_count){
                            $must_ids = array();
                            foreach($must_count as $kk22=>$vv22){
                                $must_ids[$vv22['product_id']] = $vv22['product_id'];
                            }
                            if(!array_intersect($must_ids,$product_ids)){
                                $this->msgbox->add('请选择必点商品',555)->response();
                            }
                        }

                        $discount = $shop_detail['discount'] ? $shop_detail['discount'] : array();//折扣商品
                        foreach ($product_list as $k=>$v){
                            if($v['is_onsale']==0){
                                $this->msgbox->add($v['title'].'已下架',250)->response();
                            }else{//新增折扣商品的信息 
                                $v['is_discount'] = 0;                  
                                if($dp = $discount['products'][$v['product_id']]){
                                    $v['is_discount'] = 1;
                                    $v['sale_sku'] = $dp['sale_sku'];
                                    $v['sale_type'] = 1;                           
                                }
                                $product_list[$k] = $v;
                            }
                        }
                        $spec_lists = K::M('waimai/productspec')->items_by_ids($spec_ids);
                        foreach ($spec_lists as $k => $v) {//新增折扣商品的信息
                            $v['is_discount'] = 0;
                            if($dp = $discount['products'][$v['product_id']]){
                                $v['is_discount'] = 1;
                                $v['sale_sku'] = $dp['sale_sku'];
                                $v['sale_type'] = 1;
                            }
                            $spec_lists[$k] = $v;
                        }

                        $have_discount = $product_price = $packprice = 0;
                        $product_lists = array();
                        foreach($cart_product_list as $pk=>$v){
                            $tmp_shuxin = explode(':',$pk);
                            $array_shuxin =   array_filter(explode('-',$tmp_shuxin[1]));
                            $data_shuxin = array();
                            if(!$tmp_shuxin_data = K::M('verify/check')->check_specification($product_list[$v['product_id']]['specification'],$tmp_shuxin[1])){
                                $this->msgbox->add('商品属性不存在',250)->response();
                            }
                            $data_shuxin[$pk] = $tmp_shuxin_data;
                            if(!$p = $product_list[$v['product_id']]){
                                //购物车的商品实际不存在
                            }if($p['shop_id'] != $shop_detail['shop_id']){
                                $this->msgbox->add('商品不是同一家商家的',230)->response();
                            }else if($p['is_spec']){
                                $sp = $spec_lists[$v['spec_id']];
                                $product_name = $p['title']."({$sp['spec_name']})";
                                if(!$v['spec_id'] && $sp){
                                    //商品未选规格sku
                                    $this->msgbox->add('商品未选规格',231)->response();
                                }else if($sp['product_id'] != $v['product_id']){
                                    //选择规格与商品ID关联不符
                                    $this->msgbox->add('选择规格与商品ID关联不符',232)->response();
                                }else if($p['sale_type'] == 1 && ($sp['sale_sku'] < $product_numbers[$pk])){
                                    $this->msgbox->add('商品【'.$product_name.'】库存不足',233)->response();
                                }else{
                                    $_pamount = ($sp['price'] + $sp['package_price']) * $product_numbers[$pk];
                                    $order_product_list[$pk] = array(
                                        'product_id'      => $v['product_id'],
                                        'spec_id'         => $v['spec_id'],
                                        'sale_type'       => $p['sale_type'],
                                        'product_number'  => $product_numbers[$pk],
                                        'product_name'    => $product_name,
                                        'product_price'   => $sp['price'],
                                        'package_price'   => $sp['package_price'],
                                        'amount'          => $_pamount,
                                        'unit'=>$p['unit'],
                                        'specification'   => is_array($data_shuxin[$pk])?serialize($data_shuxin[$pk]):"",
                                        'huodong_id'      => $discount ? $discount['huodong_id'] : 0,
                                        'huodong_title'   => $discount ? $discount['title'] : '',
                                        'product_photo'   => $p['photo'],
                                    );
                                    $product_number += $product_numbers[$pk];
                                    $product_price += $sp['price'] * $product_numbers[$pk];
                                    $package_price += $sp['package_price'] * $product_numbers[$pk];

                                    $product_lists[$pk]['title'] = $p['title']; //折扣商品
                                    $product_lists[$pk]['num'] = $product_numbers[$pk];
                                    $product_lists[$pk]['price'] = $sp['price'];
                                    $product_lists[$pk]['product_id'] = $v['product_id'];
                                    if($p['is_discount'] && !$have_discount){  
                                        $have_discount = 1;
                                    }
                                }
                            }else{
                                $product_name = $p['title']."({$sp['spec_name']})";
                                if($p['sale_type'] == 1 && ($p['sale_sku'] < $product_numbers[$pk])){
                                    $this->msgbox->add('商品【'.$product_name.'】库存不足',211)->response();
                                }else{
                                    $_pamount = ($p['price'] + $p['package_price']) * $product_numbers[$pk];
                                    $order_product_list[$pk] = array(
                                        'product_id'       => $v['product_id'],
                                        'spec_id'          => 0,
                                        'sale_type'        => $p['sale_type'],
                                        'product_number'   => $product_numbers[$pk],
                                        'product_name'     => $p['title'],
                                        'product_price'    => $p['price'],
                                        'package_price'    => $p['package_price'],
                                        'amount'           => $_pamount,
                                        'unit'=>$p['unit'],
                                        'specification'=>is_array($data_shuxin[$pk])?serialize($data_shuxin[$pk]):"",
                                        'huodong_id'      => $discount ? $discount['huodong_id'] : 0,
                                        'huodong_title'   => $discount ? $discount['title'] : '',
                                        'product_photo'   => $p['photo'],
                                    );
                                    $product_number += $product_numbers[$pk];
                                    $product_price +=$p['price'] * $product_numbers[$pk];
                                    $package_price +=$p['package_price'] * $product_numbers[$pk];

                                    $product_lists[$pk]['title'] = $p['title']; //折扣商品
                                    $product_lists[$pk]['num'] = $product_numbers[$pk];
                                    $product_lists[$pk]['price'] = $p['price'];
                                    $product_lists[$pk]['product_id'] = $v['product_id'];
                                    if($p['is_discount'] && !$have_discount){  
                                        $have_discount = 1;
                                    }
                                }
                            }
                        }


                        $cfg_huodong = K::M('waimai/config')->gethuodongconfig();
                        $cfg_true_huodong = $cfg_huodong?$cfg_huodong:array(
                            'hongbao'=>0,
                            'first'=>0,
                            'manjian'=>0,
                            'youhui'=>0,
                            'first_share'=>0
                        );

                        $first_order = $first_shop_order = 0;
                        if((K::M('order/order')->count(array( 'uid'=>$this->uid, 'from'=>'waimai', 'order_status'=>'>=:0')))==0){
                            $first_order = 1;
                        }
                        if((K::M('order/order')->count(array('shop_id'=>$shop_id, 'uid'=>$this->uid, 'from'=>'waimai', 'order_status'=>'>=:0')))==0){
                            $first_shop_order = 1;
                        }

                        if($params['online_pay']==1){
                            //$data_order['online_pay'] = 1;
                            if($shop_detail['first']['type']==0){
                                if($shop_detail['first']['first_amount'] &&$first_order==1){
                                    $first_youhui = $shop_detail['first']['first_amount'];
                                }else{
                                    $first_youhui = 0;
                                }
                            }else if($shop_detail['first']['type']==1){
                                if($shop_detail['first']['first_amount'] && $first_shop_order==1){
                                    $data_order['first_youhui'] = $first_youhui = $shop_detail['first']['first_amount'];
                                }else{
                                    $first_youhui = 0;
                                }
                            }
                        }else{
                            //$data_order['online_pay'] = 0;
                            if($shop_detail['pei_type']==1&&$cfg_true_huodong['first']==1&&$params['pei_type']!=3){
                                if($shop_detail['first']['type']==0){
                                    if($shop_detail['first']['first_amount'] &&$first_order==1){
                                        $first_youhui = $shop_detail['first']['first_amount'];
                                    }else{
                                        $first_youhui = 0;
                                    }
                                }else if($shop_detail['first']['type']==1){
                                    if($shop_detail['first']['first_amount'] && $first_shop_order==1){
                                        $first_youhui = $shop_detail['first']['first_amount'];
                                    }else{
                                        $first_youhui = 0;
                                    }
                                }
                            }
                        }

                        $is_first = (int)$params['is_first'];
                        if($first_youhui > 0 && !$cfg_true_huodong['first_share']){
                            if($is_first){
                                $discount = array();
                            }else{
                                $first_youhui = 0;
                            }
                        }

                        $discount_amount = $product_discprice = 0;
                        $newproducts = K::M('waimai/huodongdiscount')->get_newProducts($discount, $product_lists);
                        $product_lists = K::M('waimai/order')->optimal_amount($discount, $newproducts);//如果含有折扣商品，取折扣最优，计算总金额
                        if($have_discount && $discount){                    
                            foreach ($product_lists as $k => $v) {
                                $product_discprice += $v['prices'];
                            }
                            $discount_amount = $product_price-$product_discprice;
                        }

                        $freight = $area_price['shipping_fee'];// 兼容旧版，重新赋值配送费
                        $p_amount = $area_price['pei_amount'] ? $area_price['pei_amount']:0;// 第三方配送结算价格
                        $shop_detail['min_amount'] = $area_price['min_price'];// 兼容旧版，重新赋值起送价

                        if((($product_price+$package_price) < $shop_detail['min_amount'])&&!($shop_detail['can_zero_ziti']==1&&$params['pei_type']==3)){
                           $this->msgbox->add('起送价没有达到配送要求',212)->response();
                        }
                        $total_price = $product_price + $package_price + $freight;

                        $data_order = array(
                            'city_id'            => $shop_detail['city_id'],
                            'shop_id'            => $shop_id,
                            'staff_id'           => 0,
                            'uid'                => $this->uid,
                            'from'               => 'waimai',
                            'order_status'       => 0,
                            'total_price'        => $total_price,  
                            'product_number'     => $product_number,
                            'product_price'      => $product_price,
                            'package_price'      => $package_price,
                            'o_lat'              => $shop_detail['lat'],
                            'o_lng'              => $shop_detail['lng'],
                            'intro'               => $params['intro'],
                            'pay_status'         => 0,
                            'pei_time'           => $pei_time,
                            'order_from'         => (defined('IN_WEIXIN') ? 'weixin' : 'wap'),
                            'wx_openid'=>$this->MEMBER['wx_openid']?$this->MEMBER['wx_openid']:"",
                        );

                        $data_order['first_order'] = $first_order;
                        $data_order['first_shop_order'] = $first_shop_order;
                        $data_order['first_youhui'] = $first_youhui;
                        $data_order['online_pay'] = $params['online_pay'] ? 1 : 0;

                        if(defined('IN_WEIXIN') && defined('WX_OPENID')){
                            $data_order['wx_openid'] = WX_OPENID;
                        }else if($this->MEMBER['wx_openid']){
                             $data_order['wx_openid'] = $this->MEMBER['wx_openid'];
                         }else{
                             $data_order['wx_openid'] = '';
                         }

                        $jf_cfg = $this->system->config->get('jifen');
                        if($jf_cfg && in_array('waimai',$jf_cfg['jifen_module'])){
                            $data_order['jifen_status'] = 0;
                            $data_order['jifen_cfg'] = addslashes(serialize($jf_cfg));
                        }

                        if($params['pei_type'] == 3){
                            //$data_order['amount'] = $product_price+$package_price;
                            $data_order['amount'] = $product_price+$package_price-$discount_amount;
                            $data_order['freight'] = 0;
                            $data_order['contact'] = $this->MEMBER['nickname'];
                            $data_order['mobile'] = $this->MEMBER['mobile'];
                            $data_order['pei_type'] = 3;
                            $data_order['pei_amount'] = 0;
                            $data_order['total_price'] = $product_price+$package_price;
                        }else{
                            if($shop_detail['pei_type']==1||$shop_detail['pei_type']==2){
                                if(!$juli= K::M('magic/baidu')->juli($shop_detail['lng'], $shop_detail['lat'], $addr_detail['lng'], $addr_detail['lat'])){
                                    $juli = K::M('helper/round')->juli($shop_detail['lng'], $shop_detail['lat'], $addr_detail['lng'], $addr_detail['lat']);
                                }

                                if($shop_detail['is_separate']==1&&$shop_detail['config']){
                                    $freight = $p_amount = K::M('waimai/waimai')->shipping_fee_by_type($shop_detail['config'],$juli);
                                }else{
                                    $freight = $p_amount = K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($shop_detail['group_id']),$juli);
                                }

                                $data_order['group_id'] = $shop_detail['group_id'];
                            }
                            //$data_order['amount'] = $product_price+$package_price + $freight;
                            $data_order['freight'] = $freight;
                            $data_order['contact'] = $addr_detail['contact'];
                            $data_order['mobile'] = $addr_detail['mobile'];
                            $data_order['addr'] = $addr_detail['addr'];
                            $data_order['house'] = $addr_detail['house'];
                            $data_order['lng'] = $addr_detail['lng'];
                            $data_order['lat'] = $addr_detail['lat'];
                            $data_order['pei_type'] = $shop_detail['pei_type'];
                            $data_order['pei_amount'] = $p_amount;
                            $data_order['total_price'] = $product_price+$package_price+$freight;
                        }

                        //满减活动  2017-11-2 edit by 叶超  begin
                        if(!$first_youhui || $cfg_true_huodong['first_share'] || !$is_first){ //4.1首单优惠共享处理（不是首单||首单共享||不享受首单）
                            if($have_discount){  //有折扣不享受满减
                                $data_order['discount_youhui'] = $youhui_amount = $discount_amount;
                            }else{
                                if($params['online_pay']==1||($shop_detail['pei_type']==1&&$cfg_true_huodong['manjian']==1&&$params['pei_type']!=3)){
                                    if($youhui_detail = K::M('waimai/huodongmj')->order_youhui($shop_id, $product_price-$first_youhui+$package_price)){
                                        $data_order['order_youhui'] = $youhui_amount = $youhui_detail['youhui_amount'];
                                    }else{
                                        $data_order['order_youhui'] = $youhui_amount = 0;
                                    }
                                }
                            }                        
                            //满减活动  2017-11-2 edit by 叶超  end
                        }
                        
                        //优惠券 2017-11-2 edit by 叶超  begin
                        if($params['online_pay']==1||($shop_detail['pei_type']==1&&$cfg_true_huodong['youhui']==1&&$params['pei_type']!=3)){
                            if($coupon_id = (int)$params['coupon_id']){
                                if($coupon_id > 0){
                                    if(!$coupon_detail = K::M('waimai/coupon')->detail($coupon_id)){
                                        $this->msgbox->add('优惠券不存在',203)->response();
                                    }else if($coupon_detail['uid'] != $this->uid){
                                        $this->msgbox->add('优惠券信息不正确',204)->response();
                                    }else if($coupon_detail['shop_id'] != $shop_id){
                                        $this->msgbox->add('优惠券信息不正确',218)->response();
                                    }else if($coupon_detail['order_id']){
                                        $this->msgbox->add('该优惠券已经使用',205)->response();
                                    }else if($coupon_detail['ltime'] < __TIME){
                                        $this->msgbox->add('优惠券已过期不能使用',244)->response();
                                        //添加打包费
                                    }else if($coupon_detail['order_amount'] > ($product_price-$first_youhui-$youhui_amount+$package_price)){
                                        $this->msgbox->add('该优惠券不能使用',205)->response();
                                    }else{
                                        $data_order['coupon_id'] = $coupon_id;
                                        $data_order['coupon'] = $coupon_amount = $coupon_detail['coupon_amount'];
                                    }
                                }else{
                                    $data_order['coupon_id'] = 0;
                                    $data_order['coupon'] = $coupon_amount = 0;
                                }
                            }
                        }
                        //优惠券 2017-11-2 edit by 叶超  end
                        //红包 2017-11-2 edit by  叶超 begin
                        if($params['online_pay']==1||($shop_detail['pei_type']==1&&$cfg_true_huodong['hongbao']==1&&$params['pei_type']!=3)){
                            if($hongbao_id = (int)$params['hongbao_id']){
                                if($hongbao_id > 0){
                                    if(!$hongbao_detail = K::M('hongbao/hongbao')->detail($hongbao_id)){
                                        $this->msgbox->add('红包不存在',203)->response();
                                    }else if($hongbao_detail['uid'] != $this->uid){
                                        $this->msgbox->add('红包信息不正确',204)->response();
                                    }else if($hongbao_detail['order_id']){
                                        $this->msgbox->add('该红包已经使用',205)->response();
                                    }else if($hongbao_detail['ltime'] < __TIME){
                                        $this->msgbox->add('红包已过期不能使用',244)->response();
                                        //红包添加打包费
                                    }else if($hongbao_detail['min_amount'] > ($product_price-$first_youhui-$youhui_amount-$coupon_amount+$package_price)){
                                        $this->msgbox->add('该红包不能使用',205)->response();
                                    }else if(!K::M('hongbao/hongbao')->check_hongbao($hongbao_detail,'waimai')){
                                        $this->msgbox->add('该红包不能使用',255)->response();
                                    }
                                    else{
                                        $data_order['hongbao_id'] = $hongbao_id;
                                        $data_order['hongbao'] = $hongbao_amount = $hongbao_detail['amount'];
                                    }
                                }else{
                                    $data_order['hongbao_id'] = 0;
                                    $data_order['hongbao'] = $hongbao_amount = 0;
                                }
                            }
                        }
                        //红包 2017-11-2 edit by  叶超 end
                        if($params['online_pay']==1){
                            $amount = $product_price + $package_price - $youhui_amount - $first_youhui - $coupon_amount - $hongbao_amount;
                            if($amount<=0){
                                $amount = 0.01;
                            }
                            $amount = $amount + $freight;
                            $data_order['amount'] = $amount;
                        }
                        if(!$params['online_pay']) {
                            $params['online_pay'] = 0;
                        }
                      

                        //4.0超值换购-不参与满减等活动优惠计算
                        $hgpro_items = $hgpro_lists = array();
                        $hgpro_numbers = $hgpro_price = $hgpro_package = $hgpro_price2 = $huangou_youhui = 0;          
                        if($hg_cart = $this->get_hg_cart($shop_id)){
                            if(!$huangou = K::M('waimai/huodonghuangou')->get_huangou($shop_id, ($product_price+$package_price))){
                                $this->msgbox->add('超值换购活动不存在或已失效！',301)->response();
                            }else{
                                $hg_pros = $pids = array();
                                foreach($hg_cart as $k=>$v){
                                    if(!$hp = $huangou['products'][$v['product_id']]){
                                        $this->msgbox->add('换购商品不存在或已删除！',302)->response();
                                    }else if($hp['sale_sku'] < $v['number']){
                                        $this->msgbox->add('换购商品库存不足！',303)->response();
                                    }else if($hp['quota'] > 0 && $hp['quota'] < $v['number']){
                                        $this->msgbox->add('换购商品超出限购数量！',304)->response();
                                    }else{
                                        $pids[$v['product_id']] = $v['product_id'];
                                        $hg_pros[] = $v;
                                    }
                                }
                                $hgpro_items = K::M('waimai/product')->items_by_ids($pids);
                                foreach ($hg_pros as $k => $v) {
                                    if(!$hp = $hgpro_items[$v['product_id']]){
                                        $this->msgbox->add('商品不存在或已删除！',305)->response();
                                    }else if($hp['shop_id'] != $shop_id){
                                        $this->msgbox->add('商品不是同一家商家的',306)->response();
                                    }else{
                                        $hp['number'] = $v['number'];
                                        $hgpro_lists[] = $hp;                           
                                    }
                                }
                                $hgpro_lists = K::M('waimai/huodonghuangou')->get_newProducts($huangou, $hgpro_lists);
                                foreach ($hgpro_lists as $k => $v) {
                                    $hgpro_numbers += $v['number'];
                                    $hgpro_price += $v['oldprice']*$v['number'];
                                    $hgpro_package += $v['package_price']*$v['number'];
                                    $hgpro_price2 += $v['price']*$v['number'];
                                    $arr = array(
                                        'product_id'      => $v['product_id'],
                                        'spec_id'         => 0,
                                        'product_number'  => $v['number'],
                                        'product_name'    => $v['title'],
                                        'product_price'   => $v['oldprice'],
                                        'package_price'   => $v['package_price'],
                                        'amount'          => ($v['oldprice']+$v['package_price'])*$v['number'],
                                        'unit'            => $p['unit'],
                                        'specification'   => "",
                                        'huodong_id'      => 0,
                                        'huodong_title'   => '',
                                        'huangou_id'      => $huangou['huodong_id'],
                                        'basket_title'    => '',
                                        'product_photo'   => $v['photo']
                                    );
                                    $arr2 = array(
                                        'price'=>$v['price'],
                                        'oldprice'=>$v['oldprice'],
                                        'prices'=>$v['price']*$v['number'],
                                        'oldprices'=>$v['oldprice']*$v['number']
                                    );
                                    array_unshift($order_product_list, $arr);
                                    array_unshift($product_lists, $arr2);
                                }
                                $huangou_youhui = bcsub($hgpro_price, $hgpro_price2, 2);
                                $total_price = $total_price + $hgpro_price + $hgpro_package;
                                $amount = $amount + $hgpro_package + $hgpro_price2;
                                $product_number += $hgpro_numbers;
                                $product_price += $hgpro_price;
                                $package_price += $hgpro_package;
                                $data_order['huangou_youhui'] = $huangou_youhui;
                                $data_order['amount'] = $amount;
                                $data_order['total_price'] = $total_price;
                            }               
                        }

                        //4.0配送会员卡
                        $peicard_id = (int)$params['peicard_id']; //已有的会员卡ID
                        $card_id = (int)$params['pcard_id'];       //需要购买的会员卡ID
                        $peicard_amount = 0;
                        $data_order['peicard_youhui'] = 0;
                        $peicard_log = array();

                        if($shop_detail['pei_type'] == 1 && $params['pei_type'] != 3 && $data_order['freight']){
                            if($peicard_id > 0 ){
                                if(!$peicard = K::M('peicard/member')->detail($peicard_id)){
                                    $this->msgbox->add('会员卡信息有误', 401)->response();
                                }else if($peicard['uid'] != $this->uid){
                                    $this->msgbox->add('会员卡信息有误', 402)->response();
                                }else if($peicard['ltime'] < __TIME){
                                    $this->msgbox->add('会员卡已过期', 403)->response();
                                }else if($peicard['limits'] <= (K::M('peicard/log')->count(array('uid'=>$this->uid, 'cid'=>$peicard_id, 'day'=>date("Ymd"))))){
                                    $this->msgbox->add('会员卡单日次数已达上限', 404)->response();
                                }else{
                                    $peicard_youhui = ($freight - $peicard['reduce']) > 0 ? $peicard['reduce'] : $freight;
                                    $data_order['peicard_id'] = $peicard_id;
                                    $data_order['peicard_youhui'] = $peicard_youhui;
                                    $data_order['card_id'] = 0;
                                    $data_order['card_amount'] = 0;
                                    $data_order['amount'] = $amount - $peicard_youhui;
                                    $peicard_log = array(
                                        'uid'=>$this->uid,
                                        'cid'=>$peicard_id,
                                        'money'=>$peicard['reduce'],
                                        'day'=>date('Ymd'),
                                        'dateline'=>__TIME
                                    );
                                }
                            }else if($card_id > 0){
                                if(!$card = K::M('peicard/card')->detail($card_id)){
                                    $this->msgbox->add('会员卡信息有误', 405)->response();
                                }else{
                                    $peicard_youhui = ($freight - $card['reduce']) > 0 ? $card['reduce'] : $freight;
                                    $data_order['peicard_id'] = 0;
                                    $data_order['peicard_youhui'] = $peicard_youhui;
                                    $data_order['card_id'] = $card_id;
                                    $data_order['card_amount'] = $card['amount'];
                                    $data_order['amount'] = $amount - $peicard_youhui;
                                }
                            }
                        }

                        if($order_id = K::M('order/order')->create($data_order)) {
                            $data_waimai = array(
                                'order_id'         => $order_id,
                                'product_number'   => $product_number,
                                'product_price'    => $product_price,
                                'package_price'    => $package_price,
                                'freight'          => $freight,
                                );

                            $order = K::M('order/order')->detail($order_id);

                            //设置外卖订单的day_num
                            K::M('order/order')->set_order_day_num($order_id,$shop_id);

                            //有满减就创建数据
                            if($have_discount){  //有折扣不享受满减
                                $data_waimai['shop_amount'] = $discount_amount;
                                $data_waimai['roof_amount'] = 0;
                            }else{
                                if($youhui_detail){
                                    $data_waimai['shop_amount'] = $youhui_detail['shop_amount'];
                                    $data_waimai['roof_amount'] = $youhui_detail['roof_amount'];
                                }
                            }

                            //4.0换购优惠商户承担
                            if($huangou_youhui){
                                $data_waimai['shop_amount'] = $data_waimai['shop_amount'] + $huangou_youhui;
                            }
                            
                            //首单优惠   增加打包费判断 --2017-6-21
                            if($first_youhui){
                                if(($product_price+$package_price)<=$first_youhui){
                                    $first_bl = $shop_detail['first']['shop_amount']/($shop_detail['first']['shop_amount']+$shop_detail['first']['roof_amount']);
                                    $data_waimai['first_shop'] = number_format(($product_price+$package_price)* $first_bl,2,'.','');
                                    $data_waimai['first_roof'] = ($product_price+$package_price) - $data_waimai['first_shop'];
                                }else{
                                    $data_waimai['first_shop'] = $shop_detail['first']['shop_amount'];
                                    $data_waimai['first_roof'] = $shop_detail['first']['roof_amount']; 
                                }                               
                            }
                            $waimai_order_id = K::M('waimai/order')->create($data_waimai);
                            if($params['online_pay']==0 && $order['pei_type']==3) { 
                                //如果是自提单且选择了到付，直接生成消费码
                                K::M('waimai/order')->create_number($order_id);
                            }else if($order['online_pay']==1 && $order['pay_status']==0 && $order['pei_type']==3) { 

                                K::M('waimai/order')->update($order_id, array('spend_number'=>0,'spend_status'=>0));
                            }
                            //写入waimai_order_product表
                            foreach ($order_product_list as $k=>$val){
                                $val['order_id'] = $order_id;
                                $val['product_prices'] = $product_lists[$k]['prices'] ? $product_lists[$k]['prices'] : $val['product_price']*$val['product_number'];
                                $val['product_oldprices'] = $product_lists[$k]['oldprices'] ? $product_lists[$k]['oldprices'] : $val['product_price']*$val['product_number'];
                                K::M('waimai/orderproduct')->create($val);

                                //更新外卖商品库、销量
                                //修改库存处理
                                $_num = $val['product_number'];
                                /*if($val['spec_id']){
                                    $up_sale_count = array('sale_sku'=>'`sale_sku`-'.$_num, 'sale_count'=>'`sale_count`+'.$_num);
                                    $up_sale_count2 = array('sales'=>'`sales`+'.$_num,'sale_sku'=>'`sale_sku`-'.$_num, 'sale_count'=>'`sale_count`+'.$_num);
                                    K::M('waimai/productspec')->update($val['spec_id'], $up_sale_count, true);
                                    K::M('waimai/product')->update($val['product_id'], $up_sale_count2, true);
                                }else{
                                    $up_sale_count = array('sales'=>'`sales`+'.$_num,'sale_sku'=>'`sale_sku`-'.$_num, 'sale_count'=>'`sale_count`+'.$_num);
                                    K::M('waimai/product')->update($val['product_id'], $up_sale_count, true);
                                }

                                if($discount && $discount['products'][$val['product_id']]){
                                    $up_sale_count = array('sale_sku'=>'`sale_sku`-'.$_num, 'sale_count'=>'`sale_count`+'.$_num);
                                    K::M('waimai/discountproduct')->update($val['product_id'],$up_sale_count,true);
                                }*/

                                /*修改库存处理 20180615 by yufan*/
                                $up_prod_count = $up_spec_count = $up_disc_count = $up_hg_count = array();
                                if($prod = $product_list[$val['product_id']]){
                                    if($discount && $discount['products'][$val['product_id']]){
                                        $up_prod_count = array('sales'=>'`sales`+'.$_num, 'sale_count'=>'`sale_count`+'.$_num);
                                        if($val['spec_id'] && $sp = $spec_lists[$val['spec_id']]){
                                            $up_spec_count = array('sale_count'=>'`sale_count`+'.$_num);
                                        }
                                        //$up_disc_count = array('sale_sku'=>'`sale_sku`-'.$_num, 'sale_count'=>'`sale_count`+'.$_num);
                                        $up_disc_count = array('sale_count'=>'`sale_count`+'.$_num);
                                    }else{
                                        $up_prod_count = array('sales'=>'`sales`+'.$_num,'sale_sku'=>'`sale_sku`-'.$_num, 'sale_count'=>'`sale_count`+'.$_num);
                                        if($val['spec_id'] && $sp = $spec_lists[$val['spec_id']]){
                                            $up_spec_count = array('sale_count'=>'`sale_count`+'.$_num);
                                            if($sp['sale_type'] == 0){
                                                $up_spec_count = array('sale_sku'=>'`sale_sku`-'.$_num, 'sale_count'=>'`sale_count`+'.$_num);
                                            }                                        
                                        }else if($prod['sale_type'] == 0){                                            
                                            $up_prod_count = array('sales'=>'`sales`+'.$_num, 'sale_count'=>'`sale_count`+'.$_num);                                            
                                        }
                                    }
                                }

                                if($hgprod = $hgpro_items[$val['product_id']]){
                                    $up_prod_count = array('sales'=>'`sales`+'.$_num,'sale_sku'=>'`sale_sku`-'.$_num, 'sale_count'=>'`sale_count`+'.$_num);
                                    if($hgprod['sale_type'] == 0){
                                        $up_prod_count = array('sales'=>'`sales`+'.$_num, 'sale_count'=>'`sale_count`+'.$_num);
                                    }               
                                    $up_hg_count = array('sale_count'=>'`sale_count`+'.$_num, 'sale_sku'=>'`sale_sku`-'.$_num);
                                }

                                if($up_prod_count){
                                    K::M('waimai/product')->update($val['product_id'], $up_prod_count, true);
                                }
                                if($up_spec_count){
                                    K::M('waimai/productspec')->update($val['spec_id'], $up_spec_count, true);
                                }
                                if($up_disc_count){
                                    K::M('waimai/discountproduct')->update($val['product_id'],$up_disc_count,true);
                                }
                                if($up_hg_count){
                                    K::M('waimai/huangouproduct')->update($val['product_id'],$up_hg_count,true);
                                }
                            }

                            if($youhui_detail){
                                K::M('waimai/youhui')->update_count($youhui_detail['youhui_id'],'use_count',1);
                            }
                            if($coupon_detail){ //使用优惠券
                                K::M('waimai/coupon')->update($coupon_id, array('order_id'=>$order_id,'used_time'=>__TIME));
                            }
                            if($hongbao_detail){
                                K::M('hongbao/hongbao')->update($hongbao_id, array('order_id'=>$order_id,'used_time'=>__TIME,'used_ip'=>__IP));
                            }

                            //4.0生成配送会员卡使用记录
                            if($peicard_log){
                                $peicard_log['order_id'] = $order_id;
                                K::M('peicard/log')->create($peicard_log);
                            }
                    
                            K::M('order/log')->create(array('order_id'=>$order_id,'from'=>'member','log'=>'订单已提交','status'=>1));
                            K::M('shop/msg')->create(array('shop_id'=>$shop_id,'title'=>'订单已提交','content'=>'订单已提交','is_read'=>0,'type'=>1,'order_id'=>$order_id));
                            if($params['online_pay'] == 0){
                                $title = sprintf("您有新的外卖订单(单号：%s)", $order_id);
                                if($params['pei_type'] == 3){
                                    $content = sprintf("您有新的外卖自提订单");
                                }else{
                                    $content = sprintf("您有新的外卖订单(单号：%s)，客户%s(电话：%s)配送地址:%s", $order_id, $addr_detail['contact'], $addr_detail['mobile'], $addr_detail['addr']);
                                }

                                //4.1续-记录用户在当前商户的下单次数
                                K::M('order/order')->set_member_orders($order_id, $this->uid, $shop_id);

                                K::M('shop/shop')->send($shop_id,$title,$content,array('type'=>'newOrder','order_id'=>$order_id));
                            }
                            K::M('waimai/waimai')->update_count($shop_id, 'orders', 1);
                            K::M('member/member')->update_count($this->uid, 'orders', 1);
                            
                            if($order['online_pay']==0){
                                if($shop_detail['print_type']==1){
                                    if($print_list = K::M('shop/print')->items(array('shop_id'=>$shop_detail['shop_id']),array('plat_id'=>'desc'),1,10,$count)){
                                        foreach($print_list as $k=>$v){
                                            K::M('order/order')->yunprint($order_id,1,$v['plat_id']);
                                        }
                                    }
                                }
                            }

                            if($first_order){ //邀请好友
                                K::M('member/invite')->update($this->uid, array('status'=>1));
                            }

                            $this->msgbox->add('订单提交成功');
                            $this->msgbox->set_data('order_id',$order_id);
                            $this->msgbox->set_data('pay_status',$data_order['pay_status']);
                            $this->msgbox->set_data('online_pay',$params['online_pay']);
                        }else{
                            $this->msgbox->add('订单创建失败',211);
                        }
                    }
                }
            }
        }
    }

    /**
     * market_确认订单-添加备注
     */
    public function remark($shop_id)
    {
        $notes = K::M('order/order')->get_note();
        $this->pagedata['notes'] = $notes;
        $this->pagedata['shop_id'] = (int)$shop_id;
        $this->tmpl = 'order/remark.html';
    }

    // 获取用户购物车信息
    public function getmarketcart($shop_id)
    {
        if(!$cart = json_decode(urldecode($_COOKIE['KT-ECart']), true)){
            $cookie_str = str_replace('\"','"',$_COOKIE['KT-ECart']);
            $cart = json_decode($cookie_str,true);

        }
        //$cart_shop_id = array_keys($cart);
        //if($shop_id != $cart_shop_id[0]) {
        //    $this->msgbox->add('非法操作',211);
        //}else {
            $cart_goods = explode(',',$cart[$shop_id]);
            $shuxing = array();
            foreach($cart_goods as $k=>$v){
                $arr = explode("&",$v);
                $cart_goods[$k] = $arr[0];
                $shuxing[$k]  = $arr[1];

            }
            foreach ($cart_goods as $key => $val) {
                if(preg_match('/^(\d+)-(\d+):(\d+)$/', $val, $local)){

                    //$pk = $local[1].'-'.$local[2];
                    $cart_product_list[] = array(
                        'product_id'=>$local[1], 
                        'number'=>$local[3], 
                        'spec_id'=>$local[2],
                        'shuxin'=>$shuxing[$key]?$shuxing[$key]:"",
                        );
                }
            }
            $items[$shop_id] = $cart_product_list;
            return $items;
        //}   
    }

    // 获取用户购物车信息
    public function get_hg_cart($shop_id)
    {
        if(!$cart = json_decode(urldecode($_COOKIE['KT-ECart_hg']), true)){
            $cookie_str = str_replace('\"','"',$_COOKIE['KT-ECart_hg']);
            $cart = json_decode($cookie_str,true);
        }
        
        $cart_goods = explode(',',$cart[$shop_id]);

        foreach ($cart_goods as $key => $val) {
            if(preg_match('/^(\d+):(\d+)$/', $val, $local)){
                $cart_product_list[] = array(
                    'product_id'=>$local[1], 
                    'number'=>$local[2], 
                    );
            }
        }
        $items = $cart_product_list ? $cart_product_list : array();
        return $items;  
    }
    
    // 获取用户购物车信息 by locastroge
    public function getcart($cart_goods)
    {
        //$cart_goods = explode(',',$cart);
        foreach ($cart_goods as $key => $val) {
            if(preg_match('/^(\d+)-(\d+)$/', $key, $local)){
                //$pk = $local[1].'-'.$local[2];
                $cart_product_list[$key] = array(
                    'product_id'=>$local[1], 
                    'number'=>$val['num'], 
                    'spec_id'=>$local[2],
                    );
            }
        }
        $items = $cart_product_list;
        return $items;
    }
 
    // 再来一单
    public function onemore()
    {
        $this->check_login();
        $cart = array();
        $order_id = $this->GP('order_id');
        if(!$order_id) {
            $this->msgbox->add('订单不存在',211);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add('订单不存在',212);
        }else if($order['uid'] != $this->uid) {
            $this->msgbox->add('非法操作',213);
        }else { 
            if($order_product = K::M('waimai/orderproduct')->items(array('order_id'=>$order_id))) {
                foreach($order_product as $key=>$val) {
                    if($val['huangou_id']){
                        continue;
                    }
                    $pk = $val['product_id'].'-'.$val['spec_id'];
                    $product_ids[$val['product_id']] = $val['product_id'];
                    $spec_ids[$val['spec_id']] = $val['spec_id'];
                    $product_numbers[$pk] = $val['product_number'];
                    $cart_product_list[$pk] = array('product_id'=>$val['product_id'], 'number'=>$val['product_number'], 'spec_id'=>$val['spec_id']);
                }
                $product_list = K::M('waimai/product')->items_by_ids($product_ids);
                $spec_lists = K::M('waimai/productspec')->items_by_ids($spec_ids);

                foreach($cart_product_list as $pk=>$v){
                    if(!$p = $product_list[$v['product_id']]){
                        
                    }else if($p['is_spec']){
                        $sp = $spec_lists[$v['spec_id']];
                        $order_product_list[$pk] = array(
                            'product_id'      => $v['product_id'],
                            'title'           => $p['title'],
                            'spec_name'       => $sp['spec_name'],
                            'price'           => $sp['price'],
                            'package'         => $p['package_price'],
                            'sale_type'       => $p['sale_type'],
                            'sale_sku'        => $sp['sale_sku'],
                            'product_number'  => $product_numbers[$pk]
                        );
                    }else{

                        $order_product_list[$pk] = array(
                            'product_id'      => $v['product_id'],
                            'title'           => $p['title'],
                            'spec_name'       => '',
                            'price'           => $p['price'],
                            'package'         => $p['package_price'],
                            'sale_type'       => $p['sale_type'],
                            'sale_sku'        => $p['sale_sku'],
                            'product_number'  => $product_numbers[$pk]
                        );
                    }
                }   
            }
            $this->msgbox->add('success');
            $this->msgbox->set_data('product_list', $order_product_list);
        }
    }

    // 用户确认送达
    public function finish($order_id)
    {
        $this->check_login();
        if(!$order_id = (int)$order_id) {
            $this->msgbox->add('订单不存在',210);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add('订单不存在',211);
        }else if(!in_array($order['order_status'], array(3,4))) {
            $this->msgbox->add('订单不可确认送达',212);
        }else if($order['uid'] != $this->uid) {
            $this->msgbox->add('非法操作',213);
        }else if(K::M('order/order')->confirm($order_id,$order,'member')){
            K::M('order/order')->send_member('订单已完成','您的订单于 '.date('Y-m-d H:i:s',__TIME).' 已完成',$order);
            $this->msgbox->add('订单确认成功');

        }else{
            $this->msgbox->add('订单确认失败',214);
        } 
    }

    // 根据用户收货地址与商家的距离计算出配送费
    public function getfreight()
    {
        $this->check_login();
        if($addr_id = (int)$this->GP('addr_id')) {
            $addr = K::M('member/addr')->detail($addr_id);
        }
        if($shop_id = (int)$this->GP('shop_id')) {
            $shop = K::M('waimai/waimai')->detail($shop_id);
        }

        if(isset($addr) && isset($shop)){
            //计算出对应的配送费
            $juli = K::M('helper/round')->juli($addr['lng'],$addr['lat'],$shop['lng'],$shop['lat']);
            $juli = ceil($juli/1000);
            $_freight = array();
            $_max_freight = array('fkm'=>0, 'fm'=>0);
            foreach($shop['freight_stage'] as $k=>$v){
                if($juli <= $v['fkm']){
                    if($_freight && $_freight['fkm'] > $v['fkm']){
                        $_freight = $v;
                    }else if(empty($_freight)){
                        $_freight = $v;
                    }
                }
                if($v['fkm'] > $_max_freight['fkm']){
                    $_max_freight = $v;
                }
            }
           
            $data['freight_stage'] = $_freight['fm'] ? $_freight['fm'] : $_max_freight['fm'];
            //计算出对应的配送费结束
        }else{
            $data['freight_stage'] = 0;
        }
        $this->msgbox->set_data('freight', $data['freight_stage']);
    }

    public function timeZiti() 
    {
        $time = date('H:i', __TIME+1800);
        $this->msgbox->set_data('time',$time);
    }

    public function yilianyunorder(){
        $data = $_REQUEST;
        if($order_id = $data['order_id']){
            if(!$order_id = (int)$order_id){
                $this->msgbox->add(L('订单不存在'), 211);
            }else if(!$order = K::M('order/order')->find(array('print_id'=>$order_id))){
                $this->msgbox->add(L('订单不存在或已被删除'), 212);
            }else if($order['from'] != 'waimai'){
                $this->msgbox->add(L('非法操作'), 214);
            }else if($order['order_status']!=0){
                $this->msgbox->add('当前订单状态不可接单',220);
            }else if($order['pei_type'] == 2){
                $this->msgbox->add(L('代购订单不可接单'), 216);
            }else if(($order['online_pay']==1&&$order['pay_status']==0)){
                $this->msgbox->add(L('订单未支付不可接单'), 217);
            }else if((int)$order['order_status'] !== 0){
                $this->msgbox->add(L('订单状态不可接单'), 218);
            }else{
                $sign = YLYSignAndUuidClient::GetSign($data['push_time']);
                if($sign==$data['sign']){
                    if($data['state']==1){
                        if(K::M('order/order')->update($order['order_id'], array('order_status'=>2,'lasttime'=>__TIME))){
                            K::M('order/time')->update($order_id,array('shop_jiedan_time'=>__TIME));
                            echo json_encode(array('data'=>"OK"));
                            exit;
                        }
                    }else{
                        K::M('order/order')->cancel($order['order_id']);
                        echo json_encode(array('data'=>"OK"));
                        exit;
                    }
                }
                echo json_encode(array('data'=>"OK"));
                exit;
            }
        }else{
            echo json_encode(array('data'=>"OK"));
            exit;
        }
    }

    public function xprintorder(){
        
       $data = $_REQUEST;
        if($order_id = $data['dingdanID']){
            if(!$order_id = (int)$order_id){
               print_r("OK");exit;
            }else if(!$order = K::M('order/order')->detail($order_id)){
                print_r("OK");exit;
            }else if($order['from'] != 'waimai'){
                print_r("OK");exit;
            }else if($order['order_status']!=0){
                print_r("OK");exit;
            }else if($order['pei_type'] == 2){
               print_r("OK");exit;
            }else if(($order['online_pay']==1&&$order['pay_status']==0)){
                print_r("OK");exit;
            }else if((int)$order['order_status'] !== 0){
               print_r("OK");exit;
            }else if(!$waimai = K::M('waimai/waimai')->detail($order['shop_id'])){
              print_r("OK");exit;
            }else if((int)$waimai['print_type'] !=1 ){
               print_r("OK");exit;
            }else{
                if(K::M('order/order')->update($order['order_id'], array('order_status'=>2,'lasttime'=>__TIME))){
                   print_r("OK");exit;
                }
               print_r("OK");exit;
            }
        }else{
            print_r("OK");exit;
        }
    }

    // 距离排序辅助uasort()
    public function juli_order($a, $b)
    {
        if ($a['juli'] == $b['juli']) {
            return 0;
        }else{
            return ($a['juli'] < $b['juli']) ? -1 : 1;
        }
    }

}
