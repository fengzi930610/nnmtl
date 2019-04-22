<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/25
 * Time: 18:05
 */
if(!defined('__CORE_DIR')){

    exit("Access Denied");

}
class Mdl_Import_Lewaimai extends Model {

    public function get_lewaimai_data($admin_id,$shop_id){
        if(!$admin_id||!$shop_id){
            return false;
        }else{

            $cache_key = "lewaimai_".$admin_id.'_'.$shop_id;
            if(($result = K::M('cache/cache')->get($cache_key)) === false){
                @set_time_limit(0);
                @ini_set('memory_limit','512M');
                $first_url = "https://api.lewaimai.com/customer/common/page/food/choose?ver=v2";
                $parmas = array();
                $parmas['food_type'] = 1;
                $parmas['shop_id'] = $shop_id;
                $parmas['from_type']=1;
                $parmas['admin_id'] = $admin_id;
                $parmas['lwm_appid'] = 'dh129ahsd9898123gjhjfamnxoo1';
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
                if(!$result = json_decode($json_result,true)){
                    return false;
                }else if($result['error_code']!=0){
                    return false;
                }
                K::M('cache/cache')->set($cache_key, $result);

            }
            return $result;
        }


    }


    public function get_lewaimai_cate_food($admin_id,$shop_id,$type_id){
        $page = 1;
        $parmas = array();
        $parmas['shop_id'] = $shop_id;
        $parmas['from_type'] = 1;
        $parmas['type_id'] = $type_id;
        $parmas['page'] = 1;
        $parmas['admin_id'] = $admin_id;
        $parmas['lwm_appid'] = "dh129ahsd9898123gjhjfamnxoo1";
        $first_url = "https://api.lewaimai.com/customer/common/page/food/getFoodByPage?ver=v2";
        $return_data = array();
        while($page<20){
            $parmas['page']  = $page;
            $json_result = K::M('net/http')->http($first_url,$parmas,'POST');
            if(!$result = json_decode($json_result,true)){
                break;
            }else if($result['error_code']!=0){
                break;
            }else if(!$result['data']['foodlist']){
                break;
            }else{
                foreach ($result['data']['foodlist'] as $kk=>$vv){
                    $return_data[] = $vv;
                }

            }

            $page++;
        }
        return $return_data;


    }



    public function get_lewaimai_cate($admin_id,$shop_id){
        if(!$admin_id||!$shop_id){
            return false;
        }else{
            $result = $this->get_lewaimai_data($admin_id,$shop_id);
            if(!$result){
                return false;
            }else{
                $cate = array();
                foreach($result['data']['foodtype'] as $k=>$v){
                    $cate[$v['id']] = array(
                        'ele_cate_id'=>$v['id'],
                        'title'=>$v['name']
                    );
                }
                foreach ($cate as $k1=>$v1){
                    $result = $this->get_lewaimai_cate_food($admin_id,$shop_id,$v1['ele_cate_id']);
                    foreach ($result as $k2=>$vv){
                        $product = array();
                        $product['specs'] = array();
                        $product['is_spec'] = 0;
                        $price = $vv['price'];
                        if($vv['nature']){
                            $product['is_spec'] = 1;
                            foreach($vv['nature'] as $spec_key => $spec_val){
                                foreach($spec_val['data'] as $ks_k=>$vs_v){
                                    $spec = array();
                                    $spec['spec_name'] = $vs_v['naturevalue'];
                                    $spec['package_price'] = $vv['dabao_money'];
                                    $spec['price'] = $vs_v['price']+$vv['price'];
                                    $spec['sale_sku'] = 0;
                                    $spec['sale_type'] = 0;
                                    $product['specs'][] = $spec;
                                    if($price==0&&$spec['price']!=0){
                                        $price =  $spec['price'] ;
                                    }
                                }
                            }
                        }
                        $product['title'] = $vv['name'];
                        $product['ele_cate_id'] = $vv['type_id'];
                        $product['photo'] =$vv['img'];
                        $product['price'] = $vv['price']>0?$vv['price']:$price;
                        $product['package_price'] = $vv['dabao_money'];
                        $product['specification'] = '';
                        $product['sale_sku'] =$vv['stock'];
                        $product['intro'] = $vv['descript'];
                        $product['unit'] = $vv['unit'];
                        $cate[$k1]['products'][] = $product;

                    }

                }
                foreach ($cate as $k12=>$v12){
                    if(!$v12['products']){
                        unset($cate[$k12]);
                    }

                }



                return $cate;
            }

        }

    }

    public function get_lewaimai_img($src){

            $file =   K::M('magic/upload')->upload_by_data(file_get_contents($src),"data");
            return $file['photo']?$file['photo']:"";



    }


}