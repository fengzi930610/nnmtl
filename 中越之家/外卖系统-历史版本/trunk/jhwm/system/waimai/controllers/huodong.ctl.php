<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/21
 * Time: 9:23
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Huodong extends Ctl 
{

    public function detail($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('该活动不存在',211);
        }elseif(!$detail = K::M('waimai/huodong')->detail($huodong_id)){
            $this->msgbox->add('该活动不存在',212);
        }elseif($detail['stime']>__TIME||$detail['ltime']<__TIME){
            $this->msgbox->add('该活动未开始或已结束',215);
        }else{
            $lng = (float)$this->request['UxLocation']['lng'];
            $lat = (float)$this->request['UxLocation']['lat'];
             if(!$lng && !$lat){
                 $lng = (float)$_COOKIE['lng'];
                 $lat = (float)$_COOKIE['lat'];
            }
            if($detail['tmpl'] == 'waimai/huodong/shop.html'){ //商家
                if($items = K::M('waimai/huodongitems')->items(array('huodong_id'=>$huodong_id,'type'=>1),array('item_id'=>'desc'),1,5000)){   
                    $shop_ids = array();
                    $group_ids = array();
                    foreach($items as $k=>$v){
                        $shop_ids[$v['can_id']] = $v['can_id'];
                    }
                    $shops_items = K::M('waimai/waimai')->items_by_ids($shop_ids);
                    foreach ($shops_items as $k22=>$v22){
                        $group_ids[$v22['group_id']] = $v22['group_id'];

                    }
                    $group_list = K::M('pei/group')->items_by_ids($group_ids);

                    $huodongs = K::M('waimai/waimai')->get_huodong($shop_ids); //商家活动

                    foreach($shops_items as $k=>$val) {
                        $val['juli'] = (int)K::M('helper/round')->juli($val['lng'], $val['lat'], $lng, $lat);  // 用户与商户的距离米
                        $val['juli_label'] = K::M('helper/format')->juli($val['juli']);
                        if($val['pei_type']==0){
                            if($area_price = K::M('waimai/waimai')->get_shipping_fee($val['area_polygon'], $lat, $lng)){// 根据用户选择地址取商家区域模板配置 add by zhuhongwei
                                $val['min_amount'] = $area_price['min_price'];
                                $val['freight'] = $area_price['shipping_fee'];
                            }
                        }else{
                            if($val['is_separate']==0){
                                $val['min_amount'] = $group_list[$val['group_id']]['min_amount'];
                            }
                            if(in_array($val['pei_type'],array(1,2))){
                                //单独读取商家配置 --  叶超 20171024 --begin
                                if($val['is_separate']==1&&$val['config']){
                                    $val['freight']= K::M('waimai/waimai')->shipping_fee_by_type($val['config'],$val['juli']);
                                }else{
                                    $val['freight']= K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($val['group_id']),$val['juli']);
                                }
                                //单独读取商家配置 --  叶超 20171024 --end
                                //外卖3.8 新增恶劣天气判断  叶超  2018 01 13 end
                            }
                        }
                        $val['avg_score'] = ($val['score']/$val['comments']) ? round($val['score']/$val['comments'],2) : 0 ;
                        if ($val['yysj_status'] == 1&&$val['yy_status']==1) {// 取序列化配置的营业时间
                            $val['yyst'] = 1;
                        }else{
                            $val['yyst'] = 0;
                        }

                        $val['huodong'] = $huodongs[$val['shop_id']] ? $huodongs[$val['shop_id']] : array(); //商家活动

                        $shops[$k] = $val;
                    }
                }else{
                    $items = array();
                }

                uasort($shops,array($this,'default_order'));
                /*foreach ($shops as $kk=>$vv){
                    $shops[$kk] =  K::M('waimai/waimai')->format_data($vv);
                }*/
                //echo '<pre>';print_r($shops);exit;
                $this->pagedata['shops'] = $shops;
            }elseif($detail['tmpl'] == 'waimai/huodong/product.html'){ //商品
                if($items = K::M('waimai/huodongitems')->items(array('huodong_id'=>$huodong_id,'type'=>2),array('item_id'=>'desc'),1,5000)){   
                    $product_ids = array();
                    foreach($items as $k=>$v){
                        $product_ids[$v['can_id']] = $v['can_id'];
                    }
                    $shop_ids = array();
                    $products = K::M('waimai/product')->items(array('product_id'=>$product_ids,'is_onsale'=>1),array(),1,9999,$count10);
                    foreach($products as $k=>$v){
                        $products[$k]['huodong'] = array();
                        $products[$k]['oldprice'] = $v['price'];
                        $shop_ids[$v['shop_id']] = $v['shop_id'];
                    }
                    $shops = K::M('waimai/waimai')->items_by_ids($shop_ids);
                    foreach ($shop_ids as $k=>$v){
                        $tmp =  K::M('waimai/huodongdiscount')->get_discount($v);
                        if($tmp){
                            foreach ($tmp['products'] as $k12=>$v12){
                                if($products[$k12]){
                                    $products[$k12]['huodong'] = $v12;
                                    $products[$k12]['sale_sku'] = $v12['sale_sku'];
                                    if($tmp['discount_type']){
                                        $products[$k12]['price'] = $products[$k12]['price'] - $v12['discount_value'];
                                    }else{
                                        $products[$k12]['price'] = $products[$k12]['price']*$v12['discount_value'];
                                    }
                                }
                            }
                        }
                    }
                    foreach($products as $k=>$v){
                        foreach($shops as $k1=>$v1){
                            if($v['shop_id'] == $v1['shop_id']){
                                $products[$k]['shop_title'] = $v1['title'];
                            }
                        }
                    }
                }else{
                    $items = array();
                }
                $this->pagedata['products'] = $products;
            }
            $this->pagedata['items'] = $items;
            $this->pagedata['detail'] = $detail;
            $this->tmpl = $detail['tmpl'];
        } 
    }

    protected function default_order($a, $b)
    {
        if($a['yyst'] == $b['yyst']){
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
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }

}