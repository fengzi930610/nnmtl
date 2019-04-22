<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 16:53
 */
//导入乐外卖数据脚本
if(strtolower(php_sapi_name()) != 'cli'){
    exit('only run cli');
}
@ini_set("display_errors", "On");
@error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT ^ E_WARNING);;
//@error_reporting(E_ALL ^ E_NOTICE );
@set_time_limit(0);
@ini_set('memory_limit','1024M');
@ini_set('allow_url_fopen', 'On');
@date_default_timezone_set('Asia/Shanghai');
require(dirname(__DIR__).'/system/home/index.php');
$system = new Index('magic-shell');
$admin_id = 21553;
//z这里设置基础的配置信息 如有需要后期可以添加 城市ID  区域ID 配送站ID 配送方式
$config = array(
    'city_id'=>1,
    'area_id'=>1,
    'group_id'=>1,
    'pei_type'=>1

);

function  get_shop_info($admin_id,$shop_id){
    $parmas_shop = array(
       'admin_id'=>$admin_id,
        'lwm_appid'=>"dh129ahsd9898123gjhjfamnxoo1",
        'shop_id'=>$shop_id
    );
    $shop_url = "https://api.lewaimai.com/customer/common/shop/shop/info?ver=v2";
    $res1 =  K::M('net/http')->http($shop_url,$parmas_shop,'POST',array(
        ':authority:api.lewaimai.com',
        ':method:POST',
        ':path:/customer/common/page/food/choose?ver=v2',
        ':scheme:https',
        'accept:application/json, text/plain, */*',
        ' accept-encoding:gzip, deflate, br',
        'accept-language:zh-CN,zh;q=0.9',
        ' content-length:187',
        ' content-type:application/x-www-form-urlencoded',
        ' origin:https://wap.lewaimai.com',
        'referer:https://wap.lewaimai.com/h5/lwm/waimai/shop/196180?admin_id='.$admin_id,
        'user-agent:Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Mobile Safari/537.36'
    ));
    return json_decode($res1,true);
}

$parmas = array(
    'from_type'=>3,
    'lwm_appid'=>"dh129ahsd9898123gjhjfamnxoo1",
    'admin_id'=>$admin_id,
);

$first_url = "https://api.lewaimai.com/customer/common/shop/shop/list?ver=v2";
$json_result = K::M('net/http')->http($first_url,$parmas,'POST',array(
    ':authority:api.lewaimai.com',
    ':method:POST',
    ':path:/customer/common/page/food/choose?ver=v2',
    ':scheme:https',
    'accept:application/json, text/plain, */*',
    ' accept-encoding:gzip, deflate, br',
    'accept-language:zh-CN,zh;q=0.9',
    ' content-length:187',
    ' content-type:application/x-www-form-urlencoded',
    ' origin:https://wap.lewaimai.com',
    'referer:https://wap.lewaimai.com/h5/lwm/waimai/shop/196180?admin_id='.$admin_id,
    'user-agent:Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Mobile Safari/537.36'
));


$array_result = json_decode($json_result,true);
if($array_result['error_code']>0){
    exit('获取数据失败');
}




$shop_cate = $array_result['data']['shoptype'];


$shop_list =$array_result['data']['shoplist'];
foreach($shop_cate as $k=>$v){
    foreach ($shop_list as $kk=>$vv){
        if($v['id']==$vv['type_id']){
            $shop_cate[$k]['shop_list'][$vv['id']] =$vv;
        }
    }
}


$count = 1000;

foreach ($shop_cate as $insert_key=>$insert_val){
     $data_cate = array();
     $data_cate['parent_id'] = 0;
     $data_cate['title'] =$insert_val['name'];
     $data_cate['icon']  = "";
     $data_cate['photo'] = "";
     $data_cate['orderby'] = 50;
     $data_cate['dateline'] = time();

     if($cate_id = K::M('waimai/cate')->create($data_cate)){
        foreach ($insert_val['shop_list'] as $shop_key=>$shop_v){
            $shop_info = array();
            $ext = sprintf("%'.08d\n", $count);
            $shop_info['cate_id'] = $cate_id;
            $shop_info['city_id'] = $config['city_id'];
            $shop_info['title'] = $shop_v['shopname'];
            $shop_info['contact']=$shop_v['shopname'];
            $shop_info['mobile'] = '138'.$ext;
            $shop_info['passwd'] = md5(123456);
            $shop_info['audit'] = 1;
            if($shop_id = K::M('shop/shop')->create($shop_info,true)){
                $shop_detail = get_shop_info($admin_id,$shop_v['id']);
                $waimai = array();
                $waimai['shop_id'] = $shop_id;
                $waimai['city_id'] = $config['city_id'];
                $waimai['area_id'] = $config['area_id'];
                $waimai['business_id'] = 0;
                $waimai['cate_id'] = $cate_id;
                $waimai['contact'] = $shop_v['shopname'];
                $waimai['title'] = $shop_v['shopname'];
                $waimai['logo'] = K::M('import/lewaimai')->get_lewaimai_img($shop_v['shopimage']);
                $waimai['banner'] =  $waimai['logo'];
                $waimai['addr'] = $shop_detail['data']['shop']['shopaddress']?$shop_detail['data']['shop']['shopaddress']:"";
                $waimai['views'] = 0;
                $waimai['orders'] = 0;
                $waimai['comments'] = 1;
                $waimai['praise_num'] = 0;
                $waimai['score'] = 5;
                $waimai['score_peisong'] = 5;
                $waimai['first_amount'] = 0;
                $waimai['min_amount'] = 0;
                $waimai['freight'] = 0;
                $waimai['freight_stage'] = "";
                $waimai['pei_amount'] = 0;
                $waimai['pei_distance'] = 0;
                $waimai['pei_type'] = $config['pei_type'];
                $waimai['pei_time'] = "30";
                $waimai['yy_status'] = 0;
                $waimai['yy_stime'] = "09:00";
                $waimai['yy_ltime'] = "20:00";
                $waimai['yy_xiuxi'] = "";
                $waimai['is_new'] = 0;
                $waimai['online_pay'] = 1;
                $waimai['info'] = $shop_detail['data']['shop']['notice']?$shop_detail['data']['shop']['notice']:"";
                $waimai['delcare'] = $shop_detail['data']['shop']['shop_notice']?$shop_detail['data']['shop']['shop_notice']:"";
                $waimai['pmid'] = "";
                $waimai['last_time'] = time();
                $waimai['verify_name'] = 1;
                $waimai['audit'] = 1;
                $waimai['closed'] = 0;
                $waimai['clientip'] = "";
                $waimai['dateline'] = time();
                $waimai['tmpl_type'] = "waimai";
                $waimai['phone'] = $shop_detail['data']['shop']['orderphone']?$shop_detail['data']['shop']['orderphone']:'138'.$ext;
                $waimai['is_daofu'] = 0;
                $waimai['is_ziti'] = 0;
                $waimai['lat'] = $shop_detail['data']['shop']['coordinate_x']?$shop_detail['data']['shop']['coordinate_x']:0;
                $waimai['lng'] = $shop_detail['data']['shop']['coordinate_y']? $shop_detail['data']['shop']['coordinate_y']:0;
                $waimai['orderby'] = 50;
                $waimai['yuyue_day'] = 1;
                $business_hour = $shop_detail['data']['shop']['business_hours'];
                $yy_peitime = array();
                foreach ($business_hour as $kss=>$vss){
                     $stime = explode(':',$vss['start']);
                     unset($stime[2]);
                     $start_time = implode(':',$stime);
                    $ltime = explode(':',$vss['stop']);
                    unset($ltime[2]);
                    $ltime_time = implode(':',$ltime);
                    $yy_peitime[] = array(
                        'stime'=>$start_time,
                        'ltime'=>$ltime_time
                    );

                }
                $waimai['yy_peitime'] = serialize($yy_peitime);
                $waimai['area_polygon'] = "";
                $waimai['waimai_bl'] = 0;
                $waimai['hot'] = "";
                $waimai['hd_first_ltime'] = 0;
                $waimai['hd_coupon_ltime'] = 0;
                $waimai['hd_mf_ltime'] = 0;
                $waimai['hd_mj_ltime'] = 0;
                $waimai['group_id'] = $config['group_id'];
                $waimai['print_type'] = 0;
                $waimai['cate_ids'] = "";
                $waimai['config'] = "";
                $waimai['is_separate'] = 0;
                $waimai['ps_time'] = serialize($yy_peitime);
                $waimai['refund_order'] = 0;
                $waimai['pstime_type'] = 0;
                $yy_weeks = $shop_detail['data']['shop']['weeks'];
                $week_arr = explode(',',$yy_weeks);
                $waimai_yy_week = array(0,1,2,3,4,5,6);
                if(!in_array(1,$week_arr)){
                    unset($waimai_yy_week[1]);
                }
                if(!in_array(2,$week_arr)){
                    unset($waimai_yy_week[2]);
                }
                if(!in_array(3,$week_arr)){
                    unset($waimai_yy_week[3]);
                }
                if(!in_array(4,$week_arr)){
                    unset($waimai_yy_week[4]);
                }
                if(!in_array(5,$week_arr)){
                    unset($waimai_yy_week[5]);
                }
                if(!in_array(6,$week_arr)){
                    unset($waimai_yy_week[6]);
                }
                if(!in_array(7,$week_arr)){
                    unset($waimai_yy_week[0]);
                }
                $waimai['yy_weeks'] = implode(',',$waimai_yy_week);
                $waimai['jiesuan_type'] = 0;
                $waimai['is_ztsp'] = 0;
                $waimai['zt_bl'] = 0;
                //导入waimai 商铺
                if(K::M('waimai/waimai')->create($waimai,true)){
                    echo "waimai_success \r\n";
                    //开始导入外卖商品
                    $product_info = K::M('import/lewaimai')->get_lewaimai_cate($admin_id,$shop_v['id']);
                    foreach ($product_info as $k_1=>$v_2){
                        $waimai_product_cate = array();
                        $waimai_product_cate['parent_id'] = 0;
                        $waimai_product_cate['shop_id'] = $shop_id;
                        $waimai_product_cate['title'] = $v_2['title'];
                        $waimai_product_cate['icon'] = "";
                        $waimai_product_cate['orderby'] = 50;
                        $waimai_product_cate['type'] = 'waimai';
                        $waimai_product_cate['dateline'] = time();
                        $waimai_product_cate['settime'] = "";
                        $waimai_product_cate['show_type'] = 0;
                        if($product_cate_id = K::M('waimai/productcate')->create($waimai_product_cate,true)){
                            echo "waimai_product_cate_success \r\n";
                            foreach ($v_2['products'] as $product_k=>$product_v){
                                $photo = K::M('import/lewaimai')->get_lewaimai_img($product_v['photo']);
                                $product_data = array(
                                    'shop_id'=>$shop_id,
                                    'area_id'=>$config['area_id'],
                                    'business_id'=>0,
                                    'cat_id'=>$cate_id,
                                    'cate_id'=>$product_cate_id,
                                    'title'=>$product_v['title'],
                                    'intro'=>$product_v['intro'],
                                    'photo'=>$photo,
                                    'price'=>$product_v['price'],
                                    'package_price'=>$product_v['package_price'],
                                    'is_spec'=>$product_v['is_spec'],
                                    'specification'=>$product_v['specification'],
                                    'sale_sku'=>0,
                                    'sales'=>0,
                                    'sale_type'=>0,
                                    'sale_count'=>0,
                                    'orderby'=>50,
                                    'closed'=>0,
                                    'is_onsale'=>1,
                                    'good'=>0,
                                    'bad'=>0,
                                    'unit'=>$product_v['unit']?$product_v['unit']:"份",
                                    'dateline'=>__TIME,
                                    'cate_ids'=>','.$product_cate_id.','
                                );
                                if($waimai_product_id = K::M('waimai/product')->create($product_data,true)){
                                    echo "waimai_product_product_success \r\n";
                                    if($product_data['is_spec'] && $product_v['specs']){
                                        foreach($product_v['specs'] as $k=>$v){
                                            $spec_data = $v;
                                            $spec_data['product_id'] = $waimai_product_id;
                                            $spec_data['sale_sku'] = 0;
                                            $spec_data['spec_photo'] = '';
                                            $spec_data['sale_count'] = 0;
                                            $spec_data['sale_type'] = 0;
                                            K::M('waimai/productspec')->create($spec_data,true);
                                        }
                                    }

                                }

                            }
                               /* if($importImg){
                                    if($a = K::M('import/lewaimai')->get_lewaimai_img($product['photo'])){
                                        $photo = $a;
                                    }else if($b = K::M('import/import')->get_ele_img($product['photo'])){
                                        $photo = $b;
                                    }else{
                                        $photo = '';
                                    }
                                }

                            $product_data = array(
                                'shop_id'=>$shop_id,
                                'area_id'=>$waimai['area_id'],
                                'business_id'=>$waimai['business_id'],
                                'cat_id'=>$waimai['cate_id'],
                                'cate_id'=>$cate_id,
                                'title'=>$product['title'],
                                'intro'=>$product['intro'],
                                'photo'=>$photo,
                                'price'=>$product['price'],
                                'package_price'=>$product['package_price'],
                                'is_spec'=>$product['is_spec'],
                                'specification'=>$product['specification'],
                                'sale_sku'=>$importSku ? $product['sale_sku'] : 0,
                                'sales'=>0,
                                'sale_type'=>0,
                                'sale_count'=>0,
                                'orderby'=>50,
                                'closed'=>0,
                                'is_onsale'=>1,
                                'good'=>0,
                                'bad'=>0,
                                'unit'=>$product['unit']?$product['unit']:"份",
                                'dateline'=>__TIME,
                                'cate_ids'=>','.$cate_id.','
                            );

                            if($product_id = K::M("waimai/product")->create($product_data)){
                                if($product_data['is_spec'] && $product['specs']){
                                    foreach($product['specs'] as $k=>$v){
                                        $spec_data = $v;
                                        $spec_data['product_id'] = $product_id;
                                        $spec_data['sale_sku'] = $importSku ? $v['sale_sku'] : 0;
                                        $spec_data['spec_photo'] = '';
                                        $spec_data['sale_count'] = 0;
                                        K::M('waimai/productspec')->create($spec_data);
                                    }
                                }
                                $this->msgbox->add('商品导入成功！');
                                $this->msgbox->set_data('data',$product_id);
                            }else{
                                $this->msgbox->add('商品导入失败！',217);
                            }*/

                        }


                    }
                    //


                }

                echo $count."->"."\r\n";

            }
            $count++;

        }

     }

}

$filter = array();
$filter['price'] = 0;
$items  = K::M('waimai/productspec')->items($filter,array('spec_id'=>"desc"),1,100000,$count);
foreach ($items as $k=>$v){
    if($product_detail = K::M('waimai/product')->detail($v['product_id'])){
        $specification = $product_detail['specification'];

        if($specification){
            $specification[0]['val'][]=$v['spec_name'];
        }else{
            $specification = array(array('key'=>"属性",'val'=>array($v['spec_name'])));
        }

        K::M('waimai/product')->update($v['product_id'],array('specification'=>serialize($specification)));
        K::M('waimai/productspec')->delete($v['spec_id']);
        echo $v['product_id']."\n";




    }

}
//导入乐外卖数据 有bug 导入完成后请执行这条sql 语句
/*update jh_waimai_product set `is_spec` = 0
 where `is_spec` = 1 and product_id not in (select product_id from jh
_waimai_product_spec);*/

exit('success');










