<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Product_Product extends Ctl
{

    
    /*public function index($cate_id, $page=1)
    {
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 0;
        
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($cate_id2 = (int)$SO['cate_id2']){
                $filter['cate_id'] = $cate_id2;
            }
            $cate_id = (int)$SO['cate_id'];
        
            if(!$cate_id2&&$cate_id){
                $filter['cate_id'] = $cate_id;
            }
            $is_onsale = $SO['is_onsale'];
            if($is_onsale>0){
                if($is_onsale == 1){
                    $filter['is_onsale'] = 1;
                }else{
                    $filter['is_onsale'] = 0;
                }
            }
            if($title = $SO['title']){
                $filter['title'] = "LIKE:%".$title."%";
            }
        }
        if($items = K::M('waimai/product')->items($filter, array('is_hot'=>'desc','orderby'=>'asc','product_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('product/product/index', array('{page}')),array('SO'=>$SO));
        }
        $cate_ids = $product_ids = array();
        foreach($items as $k=>$v){
            $product_ids[$v['product_id']] = $v['product_id'];
            $cate_ids[$v['cate_id']] = $v['cate_id'];
        }
        $specs = K::M('waimai/productspec')->items(array('product_id'=>$product_ids));
        foreach($items as $k=>$v){
            foreach($specs as $k1=>$v1){
                if($v1['product_id'] == $v['product_id']){
                    $items[$k]['spec_list'][] = $v1;
                }
            }
        }
        $this->pagedata['cates'] = K::M('waimai/productcate')->items_by_ids($cate_ids);
        $this->pagedata['pcates'] = K::M('waimai/productcate')->items(array('shop_id'=>$this->shop_id,'parent_id'=>0));
        $scates = K::M('waimai/productcate')->items(array('shop_id'=>$this->shop_id));
        $this->pagedata['scates'] = json_encode($scates);
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['shop'] = K::M('shop/shop')->detail($this->shop_id);
        $this->tmpl = 'product/product/index.html';
    }*/

    public function index()
    {
        $pcates = K::M('waimai/productcate')->items(array('shop_id'=>$this->shop_id,'parent_id'=>0));
        $pcates = array_values($pcates);
        //array_unshift($pcates, array('cate_id'=>'hot','title'=>'热销'));
        $discount = K::M('waimai/huodongdiscount')->get_discount($this->shop_id);
        if($discount && $discount['products']){
            array_unshift($pcates,array('cate_id'=>'discount','shop_id'=>$this->shop_id,'title'=>'优惠'));
        }
        $hot_count = K::M('waimai/product')->count(array('shop_id'=>$this->shop_id,'closed'=>0,'is_onsale'=>1,'product_id'=>$this->waimai_shop['hot']));
        $must_count = K::M('waimai/product')->count(array('shop_id'=>$this->shop_id,'closed'=>0,'is_onsale'=>1,'is_must'=>1));
        $this->pagedata['hot_count'] = $hot_count;
        $this->pagedata['must_count'] = $must_count;
        $this->pagedata['pcates'] = $pcates;
        $this->pagedata['shop'] = K::M('shop/shop')->detail($this->shop_id);
        $this->tmpl = 'product/product/index.html';
    }

    public function items($cate_id, $page=1)
    {
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 0;
        
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            $is_onsale = $SO['is_onsale'];
            if($is_onsale>0){
                if($is_onsale == 1){
                    $filter['is_onsale'] = 1;
                }else{
                    $filter['is_onsale'] = 0;
                }
            }
            if($title = $SO['title']){
                $filter['title'] = "LIKE:%".$title."%";
            }
        }
        $pcates = K::M('waimai/productcate')->items(array('shop_id'=>$this->shop_id,'parent_id'=>0));
        $pcates = array_values($pcates);
        
        $discount = K::M('waimai/huodongdiscount')->get_discount($this->shop_id);//折扣

        if($cate_id == 'hot'){
            $filter['is_hot'] = 1;
        }else if($cate_id=='must'){
            $filter['is_must'] = 1;
        }else if($cate_id=='discount'){
            
            $pids = array();
            if($discount && $discount['products']){
                $pids = array_keys($discount['products']);
            }
            $filter['product_id'] = $pids;
        }else{
            if($cate_id = (int)$cate_id){
                //$cate_id = $pcates[0]['cate_id'];
                $cate_ids = K::M('waimai/productcate')->getChildren($cate_id, true);
                $filter[':OR'] = array(
                    'cate_ids'=>'LIKE:%,'.$cate_id.',%',
                    'cate_id'=>$cate_ids,
                    );
            }
            
        }
        if($items = K::M('waimai/product')->items($filter, array('is_hot'=>'desc','orderby'=>'asc','product_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('product/product/items', array($cate_id, '{page}')),array('SO'=>$SO));
        }
        $cate_ids = $product_ids = array();
        foreach($items as $k=>$v){
            $product_ids[$v['product_id']] = $v['product_id'];
            $cate_ids[$v['cate_id']] = $v['cate_id'];

            $v['is_discount'] = 0;
            if($discount && ($p = $discount['products'][$v['product_id']])){
                $v['is_discount'] = 1;
                $v['sale_type'] = 1;
                $v['sale_sku'] = $p['sale_sku'];
            }
            $items[$k] = $v;
        }
        $specs = K::M('waimai/productspec')->items(array('product_id'=>$product_ids),array(),1,500);
        foreach($items as $k=>$v){
            foreach($specs as $k1=>$v1){
                if($v1['product_id'] == $v['product_id']){
                    $items[$k]['spec_list'][] = $v1;
                }
            }
        }
        $this->pagedata['cates'] = K::M('waimai/productcate')->items_by_ids($cate_ids);
        $this->pagedata['pcates'] = $pcates;
        $scates = K::M('waimai/productcate')->items(array('shop_id'=>$this->shop_id));
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['shop'] = K::M('shop/shop')->detail($this->shop_id);

        //2019-01-26 添加 输出是否为虚拟商家及商品附加费，以在列表中对附加费进行显示
        $this->pagedata['is_custom_shop'] = K::M('waimai/waimai')->is_custom_mgr_shop($this->shop_id);
        $this->pagedata['goods_addone_config'] = K::M('waimai/freightcalcconfig')->get_goods_addone_cfg();
        //=================================================
        $this->tmpl = 'product/product/items.html';
    }
    
    
    //增加库存
    public function stock_add()
    {
        $stock_add = $this->GP('stock_num');
        if ($stock_add > 0) {
            if (!$ids = $this->GP('product_id')) {
                $this->msgbox->add('请选择产品', 210);
            } else {
                $filter = array(
                    'shop_id' => $this->shop['shop_id'],
                    'product_id' => $ids,
                );
                if ($arr_product = K::M('waimai/product')->items($filter)) {
                    foreach ($arr_product as $v) {
                        $arr_product = K::M('waimai/product')->update_count($v['product_id'], 'sale_sku', $stock_add);
                    }
                }
            }

        }
    }

    public function stock_num($product_id=null)
    {//批量改库存
        $stock_num = (int)$this->GP('stock_num');
        if($product_id = (int)$product_id){
            if(!$detail = K::M('waimai/product')->detail($product_id)){
                $this->msgbox->add('你要操作的商品不存在或已经删除', 211);
            }else if($detail['shop_id'] != $this->shop_id){
                $this->msgbox->add('非法操作', 213);
            }else{
                if($stock_num == -1){
                    $up_data = array('sale_sku'=>0, 'sale_type'=>0);
                }else{
                    $up_data = array('sale_sku'=>$stock_num, 'sale_type'=>1);
                }
                if(!$detail['is_spec']){                    
                    if(K::M('waimai/product')->batch($product_id, $up_data)){
                        $this->msgbox->add(L('商品库存修改成功'));
                    }
                }else{
                    $specs = K::M('waimai/productspec')->items(array('product_id'=>$product_id));
                    $spec_ids = array();
                    $nums = 0;
                    foreach($specs as $k=>$v){
                        $spec_ids[$v['spec_id']] = $v['spec_id'];
                        $nums += $stock_num;
                    }

                    if($stock_num == -1){
                        $pup_data = array('sale_sku'=>0, 'sale_type'=>0);
                    }else{
                        $pup_data = array('sale_sku'=>$nums, 'sale_type'=>1);
                    }
                    K::M('waimai/productspec')->batch($spec_ids, $up_data);
                    K::M('waimai/product')->batch($product_id, $pup_data);

                    $this->msgbox->add('商品库存修改成功');
                }
                
            }
        }else if($ids = $this->GP('product_id')){
            foreach($ids as $pid){
                $detail = K::M('waimai/product')->detail($pid);
                if($stock_num == -1){
                    $up_data = array('sale_sku'=>0, 'sale_type'=>0);
                }else{
                    $up_data = array('sale_sku'=>$stock_num, 'sale_type'=>1);
                }
                if(!$detail['is_spec']){
                    K::M('waimai/product')->batch($pid, $up_data);
                }else{
                    $specs = K::M('waimai/productspec')->items(array('product_id'=>$pid));
                    $spec_ids = array();
                    $nums = 0;
                    foreach($specs as $k=>$v){
                        $spec_ids[$v['spec_id']] = $v['spec_id'];
                        if($stock_num == -1){
                            $nums = -1;
                        }else{
                            $nums += $stock_num;
                        }
                    }

                    if($stock_num == -1){
                        $pup_data = array('sale_sku'=>0, 'sale_type'=>0);
                    }else{
                        $pup_data = array('sale_sku'=>$nums, 'sale_type'=>1);
                    }

                    K::M('waimai/productspec')->batch($spec_ids, $up_data);
                    K::M('waimai/product')->batch($pid, $pup_data);
                } 
            }
            $this->msgbox->add('批量修改商品库存成功');
        }else{
            $this->msgbox->add('未指定要修改的商品', 401);
        }
    }
    
    
    public function onsale_open($product_id=null)
    {//上架
        if($product_id = (int)$product_id){
            if(!$detail = K::M('waimai/product')->detail($product_id)){
                $this->msgbox->add('你要上架的商品不存在或已经删除', 211);
            }else if($detail['shop_id'] != $this->shop_id){
                $this->msgbox->add('非法操作', 213);
            }else if($detail['is_onsale'] ==1){
                $this->msgbox->add('该商品已经上架', 214);
            }else{
                if(K::M('waimai/product')->batch($product_id,array('is_onsale'=>1))){
                    $this->msgbox->add('商品上架成功');
                }
            }
        }else if($ids = $this->GP('product_id')){
            if(K::M('waimai/product')->batch($ids,array('is_onsale'=>1))){
                $this->msgbox->add('批量上架商品成功');
            }
        }else{
            $this->msgbox->add('未指定要上架的商品', 401);
        }
    }
    
    public function onsale_close($product_id=null)
    {//下架
        if($product_id = (int)$product_id){
            if(!$detail = K::M('waimai/product')->detail($product_id)){
                $this->msgbox->add('你要下架的商品不存在或已经删除', 211);
            }else if($detail['shop_id'] != $this->shop_id){
                $this->msgbox->add('非法操作', 213);
            }else if($detail['is_onsale'] ==0){
                $this->msgbox->add('该商品已经下架', 214);
            }else if($detail['is_must']==1){
                $this->msgbox->add('必点商品无法下架',215);
            }else{
                if(K::M('waimai/product')->batch($product_id,array('is_onsale'=>0))){
                    $this->msgbox->add('商品下架成功');
                }
            }
        }else if($ids = $this->GP('product_id')){
            if(K::M('waimai/product')->batch($ids,array('is_onsale'=>0))){
                $this->msgbox->add('批量下架商品成功');
            }
        }else{
            $this->msgbox->add('未指定要下架的商品', 401);
        }
    }
    

    public function skunotice($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 0;
        $filter['is_onsale'] = 1;
        $filter['sale_sku'] = '<:15';
        if($items = K::M('waimai/product')->items($filter, array('product_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }
        $cate_ids = array();
        foreach($items as $k=>$v){
            $cate_ids[$v['cate_id']] = $v['cate_id'];
        }
        $this->pagedata['cates'] = K::M('waimai/productcate')->items_by_ids($cate_ids);
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['shop'] = K::M('shop/shop')->detail($this->shop_id);
        $this->tmpl = 'biz/waimai/product/skunotice.html';
    } 
    
    public function open($product_id=null)
    {
        if(!($product_id = (int)$product_id) && !($product_id = $this->GP('product_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('waimai/product')->detail($product_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($detail['shop_id'] != $this->shop_id){
            $this->msgbox->add('非法操作', 213);
        }else{
            if($detail['is_onsale'] == 0){
                $open = 1;
            }else{
                $open = 0;
            }
            $up = K::M('waimai/product')->update($product_id,array('is_onsale'=>$open));
            if($up){
                $this->msgbox->add('操作成功!');
            }else{
                $this->msgbox->add('操作失败!',300);
            }
        }        
    }
        
    public function set_hot($product_id,$st){
        if(!$product_id = (int)$product_id){
            $this->msgbox->add('请选择要设置商品!',211);
        }elseif(!$detail = K::M('waimai/product')->detail($product_id)){
            $this->msgbox->add('请选择要设置商品!',212);
        }elseif($detail['shop_id'] != $this->shop_id){
            $this->msgbox->add('你没有权限操作该商品',213);
        }else{
            if($st == 1){//设为热销
                if($detail['is_hot'] == 1){
                    $this->msgbox->add('该商品已经是热销商品!',214);
                }else{
                    if(4 <= $count = K::M('waimai/product')->count(array('is_onsale'=>1,'closed'=>0,'shop_id'=>$this->shop_id,'is_hot'=>1))){
                        $this->msgbox->add('热销商品个数已达到上限!',215);
                    }else{
                        if(K::M('waimai/product')->update($product_id,array('is_hot'=>1))){
                            $hot_products = K::M('waimai/product')->items(array('is_onsale'=>1,'closed'=>0,'shop_id'=>$this->shop_id,'is_hot'=>1));
                            $product_ids = array();
                            foreach($hot_products as $k=>$v){
                                $product_ids[] = $v['product_id'];
                            }
                            K::M('waimai/waimai')->update($this->shop_id,array('hot'=>serialize($product_ids)));
                            $this->msgbox->add('设置成功!');
                        }
                    }
                }
            }else{//取消热销
                if($detail['is_hot'] == 0){
                    $this->msgbox->add('该商品不是热销商品!',216);
                }else{
                    if(K::M('waimai/product')->update($product_id,array('is_hot'=>0))){
                        $hot_products = K::M('waimai/product')->items(array('is_onsale'=>1,'closed'=>0,'shop_id'=>$this->shop_id,'is_hot'=>1));
                        $product_ids = array();
                        foreach($hot_products as $k=>$v){
                            $product_ids[] = $v['product_id'];
                        }
                        K::M('waimai/waimai')->update($this->shop_id,array('hot'=>serialize($product_ids)));
                        $this->msgbox->add('设置成功!');
                    }
                }
            }
        }
    }

    public function set_must($product_id,$st){
        if(!$product_id){
            $this->msgbox->add('请选择需要设置的商品!',201);
        }else if(!$product = K::M('waimai/product')->detail($product_id)){
            $this->msgbox->add('选择的商品不存在',202);
        }else if($product['shop_id']!=$this->shop_id){
            $this->msgbox->add('没有权限操作该商品',203);
        }else{
            if($st==1){
                $must_count = K::M('waimai/product')->count(array('shop_id'=>$this->shop_id,'closed'=>0,'is_onsale'=>1,'is_must'=>1));
                if($product['is_must']==1){
                    $this->msgbox->add('该商品已经是必点商品',204);
                }/*else if($product['is_onsale']==0){
                  $this->msgbox->add('必点商品必须为上架状态',205);
                }*//*else if($must_count>0){
                  $this->msgbox->add('已经设置必点商品，请先取消再设置',206);
                }*/else{
                    if(K::M('waimai/product')->update($product_id,array('is_must'=>1))){
                        $this->msgbox->add('设置成功');
                    }else{
                        $this->msgbox->add('设置失败',207);
                    }
                }
            }else{
                if($product['is_must']==0){
                    $this->msgbox->add('该商品不是必点商品',208);
                }else {
                    if(K::M('waimai/product')->update($product_id,array('is_must'=>0))){
                        $this->msgbox->add('设置成功');
                    }else{
                        $this->msgbox->add('设置失败',209);
                    }
                }
            }
        }
    }

    //添加商品
    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($_FILES['data']){
                foreach($_FILES['data']['name']['photo'] as $k=>$v){
                    $attachs[$k] = array(
                        'name' => $v,
                        'type' => $_FILES['data']['type']['photo'][$k],
                        'tmp_name' => $_FILES['data']['tmp_name']['photo'][$k],
                        'error' => $_FILES['data']['error']['photo'][$k],
                        'size' => $_FILES['data']['size']['photo'][$k]
                    );
                }
                $upload = K::M('magic/upload');
                foreach($attachs as $k=>$attach){
                    if($attach['error'] == UPLOAD_ERR_OK){
                        if($a = $upload->upload($attach, 'wmproduct')){
                            if($k == 0){
                                $data['photo'] = $a['photo'];
                            }else{
                                $photos[$k] = $a['photo'];
                            }
                        }
                    }
                }
            }
            /*if(!$data['cate_id'][1]&&!$data['cate_id'][2]){
                $this->msgbox->add('请选择分类', 211)->response();
            }else if(!$data['unit']){
                $this->msgbox->add("请选择商品单位",212)->response();
            }else if($data['cate_id'][2]){
                $data['cate_id'] = $data['cate_id'][2];
            }else{
                $data['cate_id'] = $data['cate_id'][1];
            }*/
            if(!$data['cate_ids']){
                $this->msgbox->add('请选择分类', 211)->response();
            }else if(!$data['unit']){
                $this->msgbox->add("请选择商品单位",212)->response();
            }else{
                asort($data['cate_ids']);                
                $data['cate_id'] = $data['cate_ids'][0];
                $data['cate_ids'] = ','.implode(',',$data['cate_ids']).',';
            }
            /*if(!$data['cat_id'][1]&&!$data['cat_id'][2]&&!$data['cat_id'][3]){
                $this->msgbox->add('请选择系统分类', 212)->response();
            }elseif($data['cat_id'][3]){
                $data['cat_id'] = $data['cat_id'][3];
            }elseif($data['cat_id'][2]){
                $data['cat_id'] = $data['cat_id'][2];
            }else{
                $data['cat_id'] = $data['cat_id'][1];
            }*/
            $datas = $this->checksubmit('datas');
            //print_r($datas);die;
            if(!$datas){
                $data['is_spec'] = 0;
                if($data['sale_sku']==-1){
                    $data['sale_type'] = 0;
                }else{
                    $data['sale_type'] = 1;
                }
            }else{
                $data['sale_type'] = 1;
                $data['is_spec'] = 1;
                $current_data = current($datas);
                $data['price'] = $current_data['price'];
                $data['package_price'] = $current_data['package_price'];
                $data['sale_sku'] = $current_data['sale_sku'];
                foreach($datas as $k=>$v){
                    if(!$v['spec_name']){
                        $this->msgbox->add('规格名称不能为空', 213)->response();
                    }
                    $datas[$k]['spec_photo'] = $photos[$k];
                }
            }
            $data['shop_id'] = $this->shop_id;
            if(!$type = (int)$this->GP('type')){
                $type = 1;
            }
            $data['area_id'] = $this->waimai_shop['area_id'];
            $data['business_id'] = $this->waimai_shop['business_id'];
            //specification 规格
            $specification = array();
            if($data['key']&&$data['val']){
                array_unique($data['key']);
                foreach($data['key'] as $k=>$v){
                    $specification[$k]['key'] = $v;
                    foreach($data['val'] as $kk=>$vv){
                        if($k == $kk){
                            $specification[$k]['val'] = $vv;
                        }
                    }
                }
            }
            foreach($specification as $kkk=>$vvv){
                if(!$vvv['val']){
                    unset($specification[$kkk]);
                }
            }

            $data['specification'] = $specification?serialize($specification):"";

            if($product_id = K::M('waimai/product')->create($data)){                
                if($data['is_spec'] == 1){
                    $count_kucun = 0;
                    $minprice = 0;
                    $minpackage = 0;
                    $sale_sku = 1;
                    foreach($datas as $k=>$v){
                        $v['product_id'] = $product_id;
                        if($v['sale_sku']==-1){
                            $v['sale_type'] =0;
                            $sale_sku = 0;//v3.6.0
                        }else{
                            $v['sale_type'] =1;
                        }

                        //取规格最小金额
                        if(!$minprice){
                            $minprice = $v['price'];
                            $minpackage = $v['package_price'];
                        }else{
                            if($minprice > $v['price']){
                                $minprice = $v['price'];
                                $minpackage = $v['package_price'];
                            }
                        }

                        K::M('waimai/productspec')->create($v);
                        $count_kucun = $count_kucun+$v['sale_sku'];
                    }
                    //K::M('waimai/product')->update($product_id,array('sale_sku'=>$count_kucun));
                    K::M('waimai/product')->update($product_id,array('sale_sku'=>$count_kucun, 'price'=>$minprice, 'package_price'=>$minpackage, 'sale_type'=>$sale_type));
                }
                $this->msgbox->add('添加内容成功');
                if($type == 1){
                    $this->msgbox->set_data('forward', $this->mklink('product/product/index'));
                }else{
                    $this->msgbox->set_data('forward', $this->mklink('product/product/create'));
                }
            } 
        }else{
           $this->pagedata['shop_id'] = $this->shop_id;  
           $this->pagedata['shop'] = K::M('shop/shop')->detail($this->shop_id);
           $this->pagedata['pcates'] = K::M('waimai/productcate')->items(array('shop_id'=>$this->shop_id,'parent_id'=>0));//商家分类
           $this->pagedata['xcates'] = K::M('waimai/cate')->items(array('parent_id'=>0));//系统分类
           $scates =  K::M('waimai/productcate')->items(array('shop_id'=>$this->shop_id)); //商家分类
           $xscates =  K::M('waimai/cate')->fetch_all(); //系统分类
           $this->pagedata['unit_list']  = K::M('data/unit')->unit_list();//商品单位
           $this->pagedata['json_scates'] = json_encode($scates);
           $this->pagedata['json_xcates'] = json_encode($xscates);
           $this->tmpl = 'product/product/create.html';
        }   
    }

    public function edit($product_id=null)
    {
        if(!($product_id = (int)$product_id) && !($product_id = $this->GP('product_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('waimai/product')->detail($product_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($detail['shop_id'] != $this->shop_id){
            $this->msgbox->add('非法操作', 213);
        }else if($data = $this->checksubmit('data')){
            if($_FILES['data']){
                foreach($_FILES['data']['name']['photo'] as $k=>$v){
                    $attachs[$k] = array(
                        'name' => $v,
                        'type' => $_FILES['data']['type']['photo'][$k],
                        'tmp_name' => $_FILES['data']['tmp_name']['photo'][$k],
                        'error' => $_FILES['data']['error']['photo'][$k],
                        'size' => $_FILES['data']['size']['photo'][$k]
                    );
                }
                $upload = K::M('magic/upload');
                foreach($attachs as $k=>$attach){
                    if($attach['error'] == UPLOAD_ERR_OK){
                        if($a = $upload->upload($attach, 'wmproduct')){
                            if($k == 0){
                                $data['photo'] = $a['photo'];
                            }else{
                                $photos[$k] = $a['photo'];
                            }
                        }
                    }
                }
            }
            /*if(!$data['cate_id'][1]&&!$data['cate_id'][2]){
                $this->msgbox->add('请选择分类', 211)->response();
            }else if(!$data['unit']){
                $this->msgbox->add('请选择商品单位',212);
            }elseif($data['cate_id'][2]){
                $data['cate_id'] = $data['cate_id'][2];
            }else{
                $data['cate_id'] = $data['cate_id'][1];
            }*/
            if(!$data['cate_ids']){
                $this->msgbox->add('请选择分类', 211)->response();
            }else if(!$data['unit']){
                $this->msgbox->add("请选择商品单位",212)->response();
            }else{
                asort($data['cate_ids']);                
                $data['cate_id'] = $data['cate_ids'][0];
                $data['cate_ids'] = ','.implode(',',$data['cate_ids']).',';
            }
            /*if(!$data['cat_id'][1]&&!$data['cat_id'][2]&&!$data['cat_id'][3]){
                $this->msgbox->add('请选择系统分类', 212)->response();
            }elseif($data['cat_id'][3]){
                $data['cat_id'] = $data['cat_id'][3];
            }elseif($data['cat_id'][2]){
                $data['cat_id'] = $data['cat_id'][2];
            }else{
                $data['cat_id'] = $data['cat_id'][1];
            }*/
            $datas = $this->checksubmit('datas');
            //print_r($datas);die;
            if(!$datas){
                $data['is_spec'] = 0;
                if($data['sale_sku']==-1){
                    $data['sale_type'] = 0;
                }else{
                    $data['sale_type'] = 1;
                }
            }else{
                $data['sale_type'] = 1;
                $data['is_spec'] = 1;
                $current_data = current($datas);
                $data['price'] = $current_data['price'];
                $data['package_price'] = $current_data['package_price'];
                $data['sale_sku'] = $current_data['sale_sku'];
                foreach($datas as $k=>$v){
                    if(!$v['spec_name']){
                        $this->msgbox->add('规格名称不能为空', 213)->response();
                    }
                    $datas[$k]['spec_photo'] = $photos[$k];
                }
            }
            $specification = array();
            if($data['key']&&$data['val']){
                array_unique($data['key']);
                foreach($data['key'] as $k=>$v){
                    $specification[$k]['key'] = $v;
                    foreach($data['val'] as $kk=>$vv){
                        if($k == $kk){
                            $specification[$k]['val'] = $vv;
                        }
                    }
                }
            }
            foreach($specification as $kkk=>$vvv){
                if(!$vvv['val']){
                    unset($specification[$kkk]);
                }
            }
            $data['specification'] = $specification?serialize($specification):"";
            if(K::M('waimai/product')->update($product_id, $data)){
                //清空规格，重新添加
                $specs = K::M('waimai/productspec')->items(array('product_id'=>$product_id));
                foreach($specs as $k=>$v){
                    K::M('waimai/productspec')->delete($v['spec_id']);
                }
                if($data['is_spec'] == 1){
                    $count_kucun = 0;
                    $minprice = 0;
                    $minpackage = 0;
                    $sale_type = 1;
                    foreach($datas as $k=>$v){
                        $v['product_id'] = $product_id;
                        if($v['sale_sku']==-1){
                            $v['sale_type'] =0;
                            $sale_type = 0;//v3.6.0
                        }else{
                            $v['sale_type'] =1;
                        }

                        //取规格最小金额
                        if(!$minprice){
                            $minprice = $v['price'];
                            $minpackage = $v['package_price'];
                        }else{
                            if($minprice > $v['price']){
                                $minprice = $v['price'];
                                $minpackage = $v['package_price'];
                            }
                        }

                        K::M('waimai/productspec')->create($v);
                        $count_kucun=$count_kucun+$v['sale_sku'];
                    }
                    //K::M('waimai/product')->update($product_id,array('sale_sku'=>$count_kucun));
                    K::M('waimai/product')->update($product_id,array('sale_sku'=>$count_kucun, 'price'=>$minprice, 'package_price'=>$minpackage, 'sale_type'=>$sale_type));
                }
                $this->msgbox->add('修改内容成功');
                $this->msgbox->set_data('forward', $this->mklink('product/product/index'));
            }  
        }else{
            $this->pagedata['detail'] = $detail;

            $scate2 = K::M('waimai/productcate')->detail($detail['cate_id']);
            $scate1 = K::M('waimai/productcate')->detail($scate2['parent_id']);
            if($scate1){
                $cate_id = $scate1['cate_id'];
                $cate_id2 = $scate2['cate_id'];
            }else{
                $cate_id = $scate2['cate_id'];
                $cate_id2 = 0;
            }
            $this->pagedata['cate_id'] = $cate_id;
            $this->pagedata['cate_id2'] = $cate_id2;
            
            if($detail['cat_id']){
                $xcate3 = K::M('waimai/cate')->detail($detail['cat_id']);
                //print_r($xcate3);die;
                if($xcate3['parent_id']){
                    $xcate2 = K::M('waimai/cate')->detail($xcate3['parent_id']);
                    if($xcate2['parent_id']){
                        $xcate = K::M('waimai/cate')->detail($xcate2['parent_id']);
                        $cat_id = $xcate['cate_id'];
                        $cat_id2 = $xcate2['cate_id'];
                        $cat_id3 = $xcate3['cate_id'];
                    }else{
                        $cat_id2 = $detail['cat_id'];
                        $cat_id = $xcate2['cate_id'];
                        $cat_id3 = 0;
                    }
                }else{
                    $cat_id = $detail['cat_id'];
                    $cat_id2 = 0;
                    $cat_id3 = 0;
                }
            }
            $this->pagedata['cat_id'] = $cat_id;
            $this->pagedata['cat_id2'] = $cat_id2;
            $this->pagedata['cat_id3'] = $cat_id3;
            
            $this->pagedata['shop_id'] = $this->shop_id;  
            $this->pagedata['shop'] = K::M('shop/shop')->detail($this->shop_id);
            $this->pagedata['pcates'] = K::M('waimai/productcate')->items(array('shop_id'=>$this->shop_id,'parent_id'=>0));//商家分类
            $this->pagedata['xcates'] = K::M('waimai/cate')->items(array('parent_id'=>0));//系统分类
            $scates =  K::M('waimai/productcate')->items(array('shop_id'=>$this->shop_id)); //商家分类
            $xscates =  K::M('waimai/cate')->fetch_all(); //系统分类
            $this->pagedata['json_scates'] = json_encode($scates);
            $this->pagedata['json_xcates'] = json_encode($xscates);
            $this->pagedata['unit_list']  = K::M('data/unit')->unit_list();//商品单位
            //规格列表
            $spec_lists = K::M('waimai/productspec')->items(array('product_id'=>$product_id),array('spec_id'=>'asc'));
            $this->pagedata['spec_lists'] = $spec_lists;
            $this->pagedata['num'] = count($spec_lists);
            $this->tmpl = 'product/product/edit.html';
        }       
    }

    public function delete($product_id=null)
    {
        if($product_id = (int)$product_id){
            if(!$detail = K::M('waimai/product')->detail($product_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else if($detail['shop_id'] != $this->shop_id){
                $this->msgbox->add('非法操作', 213);
            }else{
                if(K::M('waimai/product')->batch($product_id,array('closed'=>1))){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('product_id')){
            if(K::M('waimai/product')->batch($ids,array('closed'=>1))){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
    
    public function specs($product_id=null) 
    { 
        $this->check_waiwami();
        $product_id = (int)$product_id;
        if(!$pro = K::M('waimai/product')->detail($product_id)) {
            $this->msgbox->add('商品不存在',210);
        }else if($pro['shop_id'] != $this->shop_id) {
            $this->msgbox->add('非法操作', 213);
        }else {
            if($data = $this->checksubmit()) {
                if(!$data = $this->check_fields($data, 'spec_name,price,package_price,sale_sku,spec_photo')){
                    $this->msgbox->add('非法的数据提交', 211);
                }else{
                    if($data1 = $this->checksubmit('data1')) {
                        if($spec_list = K::M('waimai/productspec')->items(array('product_id'=>$product_id))){
                            foreach($data1 as $k=>$v){
                                if($sp = $spec_list[$k]){
                                    $a = array();
                                    if($v['spec_name']!=$sp['spec_name']){
                                        $a['spec_name'] = $v['spec_name'];
                                    } 
                                    if($v['price']!=$sp['price']){
                                        $a['price'] = $v['price'];
                                    }
                                    if($v['package_price']!=$sp['package_price']){
                                        $a['package_price'] = $v['package_price'];
                                    }
                                    if($v['sale_sku']!=$sp['sale_sku']){
                                        $a['sale_sku'] = $v['sale_sku'];
                                    }
                                    if($v['spec_photo']!=$sp['spec_photo']){
                                        $a['spec_photo'] = $v['spec_photo'];
                                    }
                                    if($a){
                                        K::M('waimai/productspec')->update($k, $a);
                                    }   
                                }
                            }
                        }
                    }
                    if($data2 = $this->checksubmit('data2')) {
                        foreach($data2 as $k=>$v){
                            $v['product_id'] = $product_id;
                            K::M('waimai/productspec')->create($v);
                        } 
                        K::M('waimai/product')->update_spec($product_id);
                    }
                    $this->msgbox->add('规格设置成功'); 
                }
            }else {
                $filter = array('product_id'=>$product_id);
                if($items = K::M('waimai/productspec')->items($filter, null, $page, $limit, $count)){
                    $pager['count'] = $count;
                    $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($product_id, '{page}')));
                    $this->pagedata['items'] = $items;
                    K::M('waimai/product')->update($product_id,array('is_spec'=>1));
                }else {
                    K::M('waimai/product')->update($product_id,array('is_spec'=>0));
                }
                $this->pagedata['pager'] = $pager;

                $this->pagedata['product_id'] = $product_id;
                $this->tmpl = 'biz/waimai/product/specs.html';
            } 
        }
    }

    public function specs_save()
    {

    }

    public function specs_del($spec_id, $product_id)
    {
        $this->check_waiwami();
        if($spec_id = (int)$spec_id){
            if(!$detail = K::M('waimai/productspec')->detail($spec_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else if($detail['product_id'] != $product_id){
                $this->msgbox->add('非法操作', 213);
            }else{
                if(K::M('waimai/productspec')->delete($spec_id)){
                    if(!$res = K::M('waimai/productspec')->find(array('product_id'=>$product_id))){
                        K::M('waimai/product')->update($product_id, array('is_spec'=>0));
                    }
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }

    public function edit_sort(){
        if($data = $this->checksubmit('sort_data')){
            $edit_true = true;
            foreach ($data as $v){
                $arr = explode('-', $v);
                if(!$arr[0]){
                    $this->msgbox->add('数据错误',241)->response();
                }else if(!$arr[1]||$arr[1]<0){
                    $this->msgbox->add('排序不能为空或者小于0',242)->response();
                }
                if(!K::M('waimai/product')->update($arr[0],array('orderby'=>$arr[1]))){
                    $edit_true = false;
                }
            }
            if($edit_true){
                $this->msgbox->add('批量修改排序成功');
            }else{
                $this->msgbox->add('批量修改排序失败，请稍后再试',243);
            }
        }else{
            $this->msgbox->add('请选择需要修改的数据',240);
        }
    }

    public function ajax_spec()
    {       
        if($parent_id = $this->checksubmit('data')){
            $spec = array();
            if($items = K::M('waimai/productspec')->items(array('product_id'=>$parent_id))){
                $spec = array_values($items);
            }
            $this->msgbox->set_data('data',$spec);
            $this->msgbox->json();
        }
    }

    public function ajax_spec_edit()
    {
        $product_id = $this->GP('product_id');
        $data = $this->GP('data');
        if(!$data&&$product_id){
            if(K::M('waimai/product')->update($product_id,array('is_spec'=>0))){
                $this->msgbox->add('修改成功');
            }else{
             $this->msgbox->add('修改参数失败，请稍后再试',242);
            }
            $spec = K::M('waimai/productspec')->items(array('product_id'=>$product_id));
            $ids = array();
            foreach ($spec as $v){
                $ids[] = $v['spec_id'];
            }
            K::M('waimai/productspec')->delete($ids);
        }else if($data&&$product_id){
            $insert_data = array();
            foreach ($data as $v){
                $arr_item = explode('/',$v);
                if(!$arr_item[0]){
                    $this->msgbox->add('规格名称不能为空',243)->response();
                }
                if(!(int)$arr_item[1]<0){
                    $this->msgbox->add('价格设置错误',244)->response();
                }
                
                $insert_data[] = array(
                    'product_id'=>$product_id,
                    'price'=>$arr_item[1],
                    'spec_name'=>$arr_item[0],
                    'package_price'=>$arr_item[2],
                    'sale_sku'=>$arr_item[3],
                    'sale_type'=> $arr_item[3] == -1 ? 0 : 1
                );
            }
            $spec = K::M('waimai/productspec')->items(array('product_id'=>$product_id));
            $ids = array();
            foreach ($spec as $v){
                $ids[] = $v['spec_id'];
            }
            K::M('waimai/productspec')->delete($ids);
            $insert = true;
            foreach ($insert_data as $v){
                if(!K::M('waimai/productspec')->create($v)){
                    $insert = false;
                }
            }
            if($insert){
                K::M('waimai/product')->update($product_id,array('is_spec'=>1));
                $this->msgbox->add('修改成功');
            }else{
                $this->msgbox->add('修改失败，请稍后再试',245);
            }

        }else{
            $this->msgbox->add('参数错误',241);
        }
    }


    public function set_tuijian($product_id, $st)
    {
        if(!$product_id){
            $this->msgbox->add('请选择需要设置的商品!',201);
        }else if(!$product = K::M('waimai/product')->detail($product_id)){
            $this->msgbox->add('选择的商品不存在',202);
        }else if($product['shop_id'] != $this->shop_id){
            $this->msgbox->add('没有权限操作该商品',203);
        }else{
            if($st == 1){
                if($product['is_tuijian'] == 1){
                    $this->msgbox->add('该商品已经是推荐商品',204);
                }else{
                    if(K::M('waimai/product')->update($product_id, array('is_tuijian'=>1))){
                        $this->msgbox->add('设置成功');
                    }else{
                        $this->msgbox->add('设置失败',205);
                    }
                }
            }else{
                if($product['is_tuijian'] == 0){
                    $this->msgbox->add('该商品不是推荐商品',206);
                }else {
                    if(K::M('waimai/product')->update($product_id, array('is_tuijian'=>0))){
                        $this->msgbox->add('设置成功');
                    }else{
                        $this->msgbox->add('设置失败',207);
                    }
                }
            }
        }
    }

    public function warnsku($page=1)
    {
        $filter = $orderby = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 0;
        $filter['is_onsale'] = 1;
        
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($title = $SO['title']){
                $filter['title'] = "LIKE:%".$title."%";
            }
        }

        $items = array();
        $warn_sku = $this->waimai_shop['warn_sku'];
        if($products = K::M('waimai/product')->select($filter)){          
            $spids = $specs = array();            
            foreach ($products as $k => $v) {
                if($v['is_spec']){
                    $spids[$v['product_id']] = $v['product_id'];
                }else if($v['sale_type'] == 1 && $v['sale_sku'] <= $warn_sku){
                    $items[$v['product_id']] = $v;
                }
            }
            if(!empty($spids) && $specs = K::M('waimai/productspec')->select(array('product_id'=>$spids, 'sale_type'=>1, 'sale_sku'=>'<=:'.$warn_sku))){
                $spec_list = array();
                foreach ($specs as $k => $v) {                    
                    if($p = $products[$v['product_id']]){
                        $spec_list[$v['product_id']][] = $v;
                        $items[$v['product_id']] = $p;
                    }
                }

                foreach ($spec_list as $k => $v) {
                    if($item = $items[$k]){
                        $items[$k]['spec_list'] = $v;
                    }
                }
            }

            foreach ($items as $k => $v) {
                $v['spec_list'] = array();
                if($specs = $spec_list[$v['product_id']]){
                    $v['spec_list'] = $specs;
                }
                $v['json_data'] = json_encode($v);
                $items[$k] = $v;
            }
        }
        krsort($items);
        $this->pagedata['items'] = $items;
        $this->tmpl = 'product/product/warnsku.html';
    }

    public function ajax_sku()
    {
        if($p_data = $this->checksubmit('product')){
            if($p_data['sale_sku'] == -1){
                $p_updata = array('sale_sku'=>0, 'sale_type'=>0);
            }else{
                $p_updata = array('sale_sku'=>(int)$p_data['sale_sku'], 'sale_type'=>1);
            }
            if(K::M('waimai/product')->update($p_data['product_id'], $p_updata)){
                $this->msgbox->add('库存修改成功');
            }else{
                $this->msgbox->add('库存修改失败', 211);
            }           
        }else if($s_data = $this->checksubmit('spec')){
            foreach ($s_data as $k => $v) {
                if($v['sale_sku'] == -1){
                    $s_updata = array('sale_sku'=>0, 'sale_type'=>0);
                }else{
                    $s_updata = array('sale_sku'=>(int)$v['sale_sku'], 'sale_type'=>1);
                }
                if(!K::M('waimai/productspec')->update($v['spec_id'], $s_updata)){
                    $this->msgbox->add('库存修改失败', 212)->response();
                }
            }
            $this->msgbox->add('库存修改成功');
        }else{
            $this->msgbox->add('请选择需要修改的数据',240);
        }
    }

}
