<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/23
 * Time: 15:09
 */
if(!defined('__CORE_DIR')){

    exit("Access Denied");

}
class Mdl_Import_Import extends Model {

    //导入饿了吗数据
    public function import_ele($shop_id,$local_shop_id){
        @set_time_limit(0);
        @ini_set('memory_limit','128M');
        $url = "https://mainsite-restapi.ele.me/shopping/v2/menu?restaurant_id=";
        if(!$shop_id||!$local_shop_id){
            return false;
        }else if(!$waimai = K::M('waimai/waimai')->detail($local_shop_id)){
            return false;
        } else{
          $send_url = $url.$shop_id;
            $headers = array(
                'Host:mainsite-restapi.ele.me',
                'Origin:https://h5.ele.me',
                'Referer:https://h5.ele.me/shop/',
                'User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
	            'X-Shard:shopid='.$shop_id.';'
            );
          $result = K::M('net/http')->http($send_url, array(), 'get', $headers);
          if(!$array = json_decode($result,true)){
             return false;
          }else{
             $tmp_array  = array();
             foreach($array as $k=>$v){
                 if($v['name']!="热销"&&$v['name']!="优惠"){
                     $tmp_array[$k] = 0;
                     $waimai_product_cate = array();
                     $waimai_product_cate['parent_id'] = 0;
                     $waimai_product_cate['shop_id'] = $local_shop_id;
                     $waimai_product_cate['title'] = $v['name'];
                     $waimai_product_cate['icon'] = '';
                     $waimai_product_cate['orderby']= 0;
                     $waimai_product_cate['dateline'] = __TIME;
                     if($cate_id = K::M('waimai/productcate')->create($waimai_product_cate)){
                         $tmp_array[$k] = $cate_id;
                     }
                 }
             }
             foreach($array as $kk=>$vv){
                 $tmp_data = array();
                 foreach($vv['foods'] as $kkk=>$vvv){
                     $tmp_data['shop_id'] = $local_shop_id;
                     $tmp_data['area_id'] =$waimai['area_id'];
                     $tmp_data['business_id'] = $waimai['business_id'];
                     $tmp_data['cat_id'] = $waimai['cate_id'];
                     $tmp_data['cate_id'] = $tmp_array[$kk];
                     $tmp_data['title'] = $vvv['name'];
                     $tmp_data['photo'] = $this->get_ele_img($vvv['image_path']);
                     if($vvv['attrs']){
                         $specification = array();
                         foreach($vvv['attrs'] as $k1=>$v1){
                             $specification[$k1]['key'] = $v1['name'];
                             foreach($v1['values'] as $kk1=>$vv1){
                                 $specification[$k1]['val'][] = $vv1;
                             }
                         }
                         $tmp_data['specification'] = serialize($specification);
                     }else{
                         $tmp_data['specification'] = "";
                     }
                     if(count($vvv['specfoods'])==1){
                         $tmp_data['is_spec'] = 0;
                     }else{
                         $tmp_data['is_spec'] = 1;
                     }
                     $tmp_data['price'] = $vvv['specfoods'][0]['price'];
                     $tmp_data['package_price'] =  $vvv['specfoods'][0]['packing_fee'];
                     $tmp_data['sales'] = 0;
                     $tmp_data['sale_type'] = 0;
                     $tmp_data['sale_sku'] = 0;
                     $tmp_data['sale_count'] = 0;
                     $tmp_data['intro'] = $vvv['description'];
                     $tmp_data['orderby']= 50;
                     $tmp_data['closed'] = 0;
                     $tmp_data['dateline'] = __TIME;
                     $tmp_data['is_onsale'] = 1;
                     $tmp_data['good'] = 0;
                     $tmp_data['bad'] = 0;
                     $tmp_data['unit'] = '份';
                     $product_id = K::M("waimai/product")->create($tmp_data);
                     if($tmp_data['is_spec']==1){
                         $tmp_spec = array();
                         foreach($vvv['specfoods'] as $key_spec =>$val_spec){
                             $tmp_spec['product_id'] = $product_id;
                             $tmp_spec['price'] = $val_spec['price'];
                             $tmp_spec['package_price'] = $val_spec['packing_fee'];
                             $tmp_spec['spec_name'] = $val_spec['specs'][0]['value'];
                             $tmp_spec['spec_photo'] = "";
                             $tmp_spec['sale_sku'] = 0;
                             $tmp_spec['sale_count'] = 0;
                             K::M('waimai/productspec')->create($tmp_spec);

                         }
                     }
                 }

             }

          }

        }
       return  true;

    }

    public function import_meituan(){

    }

    public function get_ele_img($img_path)
    {
       $img_array = array(
        'bmp',
        'jpg',
        'png',
        'jpeg',
        'gif'
       );
        $ext = "";
        foreach($img_array as $v){
            if(strpos($img_path,$v)){
                $ext = $v;
                break;
            }
        }

        $url = "https://fuss10.elemecdn.com/".$img_path.".".$ext;
        if($res1 = $this->getImg($url)) {
            return $res1;
        }
        $str1 = substr($img_path,0,1);
        $str2 = substr($img_path,1,2);
        $str_tmp = substr_replace($img_path,'',0,3);
        $img_path = $str1."/".$str2.'/'.$str_tmp;
        $url = "https://fuss10.elemecdn.com/".$img_path.".".$ext;
        if($res2 = $this->getImg($url)) {
            return $res2;
        }
        return "";


    }

    public function getImg($img_url)
    {

        //去除URL连接上面可能的引号
       /* $ym = date('Ym');
        static $cfg = null;
        if($cfg===null){
            $cfg = K::$system->config->get('attach');
        }
        $fname = date('Ymd_').strtoupper(md5(microtime().uniqid())).".png";
        $dir = $cfg['attachdir'].'photo'.DIRECTORY_SEPARATOR.$ym.DIRECTORY_SEPARATOR;
        $file = $dir.$fname;
        file_put_contents($file,file_get_contents($img_url));*/
         $file =   K::M('magic/upload')->upload_by_data(file_get_contents($img_url),"data");
        return $file['photo']?$file['photo']:"";

    }

    public function getEleData($shop_id)
    {
        @set_time_limit(0);
        @ini_set('memory_limit','128M');
        $url = "https://mainsite-restapi.ele.me/shopping/v2/menu?restaurant_id=";
        if(!$shop_id){
            return false;
        }else{
            $send_url = $url.$shop_id;
            $headers = array(
                'Host:mainsite-restapi.ele.me',
                'Origin:https://h5.ele.me',
                'Referer:https://h5.ele.me/shop/',
                'User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
                'X-Shard:shopid='.$shop_id.';'
            );

            $_pre_cache_key = 'eledata-'.$shop_id;
            if(($result = K::M('cache/cache')->get($_pre_cache_key)) === false){
                $json_result = K::M('net/http')->http($send_url, array(), 'get', $headers);
                if(!$result = json_decode($json_result,true)){
                    return false;
                }
                K::M('cache/cache')->set($_pre_cache_key, $result);
            }            
            return $result;
        } 
    }

    public function getEleCate($shop_id)
    {
        if(!$shop_id){
            return false;
        }else if(!$data = $this->getEleData($shop_id)){
            return false;
        }else{
            $cates = array();
            foreach($data as $k=>$v){
                if($v['id'] > 0 && $v['type'] == 1){
                    $cate = array();
                    $cate['title'] = $v['name'];
                    $cate['ele_cate_id'] = $v['id'];
                    $cates[$v['id']] = $cate;
                }
            }
            return $cates;
        }
    }

    public function getEleProduct($shop_id)
    {
        if(!$shop_id){
            return false;
        }else if(!$data = $this->getEleData($shop_id)){
            return false;
        }else{
            $products = array();
            foreach($data as $k=>$v){
                if($v['id'] > 0 && $v['type'] == 1){
                    foreach ($v['foods'] as $kk => $vv) {
                        $attrs = $specs = array();
                        if($vv['attrs']){                             
                             foreach($vv['attrs'] as $k1=>$v1){
                                $attr = array();
                                $attr['key'] = $v1['name'];
                                foreach($v1['values'] as $kk1=>$vv1){
                                    $attr['val'][] = $vv1;
                                }
                                $attrs[] = $attr;
                            }                            
                        }
                        $is_spec = $vv['specifications'] ? 1 : 0;
        
                        if($is_spec){
                            foreach ($vv['specfoods'] as $k2 => $v2) {
                                $spec = array();
                                $spec['price'] = $v2['price'];
                                $spec['package_price'] = $v2['packing_fee'];
                                $spec['spec_name'] = $v2['specs'][0]['value'];
                                $spec['sale_sku'] = $v2['stock'];
                                $specs[] = $spec;
                            }
                        }

                        $product = array(
                            'title'=>$vv['name'],
                            'ele_cate_id'=>$vv['category_id'],
                            'photo'=>$vv['image_path'],
                            'price'=>$vv['specfoods'][0]['price'],
                            'package_price' =>  $vv['specfoods'][0]['packing_fee'],
                            'specification'=>$attrs ? serialize($attrs) : '',
                            'sale_sku'=>$vv['specfoods'][0]['stock'],
                            'intro' => $vv['description'],
                            'is_spec'=>$is_spec,
                            'specs'=>$specs
                            );
                        $products[] = $product;
                    }
                }
            }
            return $products;
        }
    }

    public function importByCates($ele_shop_id, $shop_id, $cates, $importSku=0, $importImg=1)
    {
        if(!$ele_shop_id || !($shop_id = (int)$shop_id)){
            return false;
        }else if(empty($cates)){
            return false;
        }else if(!$eleCates = $this->getEleCate($ele_shop_id)){
            return false;
        }else if(!$eleProducts = $this->getEleProduct($ele_shop_id)){
            return false;
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            return false;
        }else{
            $cate_ids = $specs_data = array();
            foreach ($cates as $k => $v) {
                if($a = $eleCates[$v]){
                    $cate_data = array(
                        'parent_id'=>0,
                        'shop_id'=>$shop_id,
                        'title'=>$a['title'],
                        'icon'=>'',
                        'orderby'=>50,
                        'dateline'=>__TIME,
                        );
                    if($cate_id = K::M('waimai/productcate')->create($cate_data)){
                        $cate_ids[$v] = $cate_id;
                    }
                }
            }
            foreach ($eleProducts as $k => $v) {
                if($cate_id = $cate_ids[$v['ele_cate_id']]){
                    if($importImg){
                        $v['photo'] = $this->get_ele_img($v['photo']);
                    }else{
                        $v['photo'] = '';
                    }
                    $product_data = array(
                        'shop_id'=>$shop_id,
                        'area_id'=>$waimai['area_id'],
                        'business_id'=>$waimai['business_id'],
                        'cat_id'=>$waimai['cate_id'],
                        'cate_id'=>$cate_id,
                        'title'=>$v['title'],
                        'intro'=>$v['intro'],
                        'photo'=>$v['photo'],
                        'price'=>$v['price'],
                        'package_price'=>$v['package_price'],
                        'is_spec'=>$v['is_spec'],
                        'specification'=>$v['specification'],
                        'sale_sku'=>$importSku ? $v['sale_sku'] : 0,
                        'sales'=>0,
                        'sale_type'=>0,
                        'sale_count'=>0,                        
                        'orderby'=>50,
                        'closed'=>0,
                        'is_onsale'=>1,
                        'good'=>0,
                        'bad'=>0,
                        'unit'=>'份',
                        'dateline'=>__TIME,
                        'cate_ids'=>','.$cate_id.','
                        );
                    if($product_id = K::M("waimai/product")->create($product_data)){
                        if($product_data['is_spec'] && $v['specs']){
                            $specs_data[$product_id] = $v['specs'];
                        }
                    }
                }
            }

            if($specs_data){               
                foreach($specs_data as $k=>$v){
                    foreach($v as $kk=>$vv){
                        $spec_data = $vv;
                        $spec_data['product_id'] = $k;
                        $spec_data['sale_sku'] = $importSku ? $vv['sale_sku'] : 0;
                        $spec_data['spec_photo'] = '';
                        $spec_data['sale_count'] = 0;
                        K::M('waimai/productspec')->create($spec_data);   
                    }                     
                }
            }
            return true;                             
        }
    }

    public function getEleCates($shop_id)
    {
        if(!$shop_id){
            return false;
        }else if(!$data = $this->getEleData($shop_id)){
            return false;
        }else{
            $cates = array();
            foreach($data as $k=>$v){
                if($v['id'] > 0 && $v['type'] == 1){
                    $cate = array();
                    $cate['title'] = $v['name'];
                    $cate['ele_cate_id'] = $v['id'];
                    $cate['products'] = array();

                    foreach ($v['foods'] as $kk => $vv) {
                        $attrs = $specs = array();
                        if($vv['attrs']){                             
                             foreach($vv['attrs'] as $k1=>$v1){
                                $attr = array();
                                $attr['key'] = $v1['name'];
                                foreach($v1['values'] as $kk1=>$vv1){
                                    $attr['val'][] = $vv1;
                                }
                                $attrs[] = $attr;
                            }                            
                        }
                        $is_spec = $vv['specifications'] ? 1 : 0;
        
                        if($is_spec){
                            foreach ($vv['specfoods'] as $k2 => $v2) {
                                $spec = array();
                                $spec['price'] = $v2['price'];
                                $spec['package_price'] = $v2['packing_fee'];
                                $spec['spec_name'] = $v2['specs'][0]['value'];
                                $spec['sale_sku'] = $v2['stock'];
                                $specs[] = $spec;
                            }
                        }

                        $product = array(
                            'title'=>$vv['name'],
                            'ele_cate_id'=>$vv['category_id'],
                            'photo'=>$vv['image_path'],
                            'price'=>$vv['specfoods'][0]['price'],
                            'package_price' =>  $vv['specfoods'][0]['packing_fee'],
                            'specification'=>$attrs ? serialize($attrs) : '',
                            'sale_sku'=>$vv['specfoods'][0]['stock'],
                            'intro' => $vv['description'],
                            'is_spec'=>$is_spec,
                            'specs'=>$specs
                            );
                        $cate['products'][] = $product;
                    }
                    //K::M('cache/cache')->set('elecate-'.$v['id'], $cate);
                    $cates[$v['id']] = $cate;
                }
            }
            return $cates;
        }
    }

    public function importDataByCates($shop_id, $cate_id, $importSku=1, $importImg=1)
    {
        if(!($shop_id = (int)$shop_id)){
            return false;
        }else if(!$cate_id = (int)$cate_id){
            return false;
        }else if(!$cate = K::M('cache/cache')->get('elecate-'.$cate_id)){
            return false;
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            return false;
        }else{
            $specs_data = array();
            $pcount = 0;
                
            $cate_data = array(
                'parent_id'=>0,
                'shop_id'=>$shop_id,
                'title'=>$cate['title'],
                'icon'=>'',
                'orderby'=>50,
                'dateline'=>__TIME,
                );
            if($cateid = K::M('waimai/productcate')->create($cate_data)){
                foreach ($cate['products'] as $k => $v) {                            
                    if($importImg){
                        $v['photo'] = $this->get_ele_img($v['photo']);
                    }else{
                        $v['photo'] = '';
                    }
                    $product_data = array(
                        'shop_id'=>$shop_id,
                        'area_id'=>$waimai['area_id'],
                        'business_id'=>$waimai['business_id'],
                        'cat_id'=>$waimai['cate_id'],
                        'cate_id'=>$cateid,
                        'title'=>$v['title'],
                        'intro'=>$v['intro'],
                        'photo'=>$v['photo'],
                        'price'=>$v['price'],
                        'package_price'=>$v['package_price'],
                        'is_spec'=>$v['is_spec'],
                        'specification'=>$v['specification'],
                        'sale_sku'=>$importSku ? $v['sale_sku'] : 0,
                        'sales'=>0,
                        'sale_type'=>0,
                        'sale_count'=>0,                        
                        'orderby'=>50,
                        'closed'=>0,
                        'is_onsale'=>1,
                        'good'=>0,
                        'bad'=>0,
                        'unit'=>'份',
                        'dateline'=>__TIME,
                        'cate_ids'=>','.$cateid.','
                        );
                    if($product_id = K::M("waimai/product")->create($product_data)){
                        if($product_data['is_spec'] && $v['specs']){
                            $specs_data[$product_id] = $v['specs'];                            
                        }
                        $pcount++;
                    }                    
                }

                if($specs_data){               
                    foreach($specs_data as $k1=>$v1){
                        foreach($v1 as $k2=>$v2){
                            $spec_data = $v2;
                            $spec_data['product_id'] = $k1;
                            $spec_data['sale_sku'] = $importSku ? $v2['sale_sku'] : 0;
                            $spec_data['spec_photo'] = '';
                            $spec_data['sale_count'] = 0;
                            K::M('waimai/productspec')->create($spec_data);   
                        }                     
                    }
                }
            }            
            return $pcount;                             
        }
    }
}