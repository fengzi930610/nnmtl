<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Huodong_Discount extends Ctl
{
    public function create()
    {
        if(K::M('waimai/huodongdiscount')->count(array('shop_id'=>$this->shop_id, 'closed'=>0))){
            $this->msgbox->add('已有活动，只有撤销后才能创建', 211);
        }else if($data = $this->checksubmit('data')){
            /*$quota = (int)$data['quota'];
            if(!$title = $data['title']){
                $this->msgbox->add('请填写折扣活动标题！',212);
            }else if(!($stime = $data['stime']) || !($ltime = $data['ltime'])){
                $this->msgbox->add('请选择活动时间！',213);
            }else if(strtotime($stime) > strtotime($ltime)){
                $this->msgbox->add('活动时间设置有误！',214);
            }else if(!($period_weeks = $data['period_weeks']) || !($period_times = $data['period_times'])){
                $this->msgbox->add('请选择活动周期！',215);
            }else if($period_times['stime'] && !($period_times_stime = K::M('helper/format')->checkTime($period_times['stime']))){
                $this->msgbox->add('活动周期开始时间设置有误！',216);
            }else if($period_times['ltime'] && !($period_times_ltime = K::M('helper/format')->checkTime($period_times['ltime']))){
                $this->msgbox->add('活动周期结束时间设置有误！',217);
            }else if($quota < 0){
                $this->msgbox->add('活动限购设置有误！',218);
            }else if(!$products = $this->checksubmit('products')){
                $this->msgbox->add('请选择活动商品！',219);
            }else if(count($products['product_id']) > 10){
                $this->msgbox->add('最多可以选择10个商品！',220);
            }else{

                $discount_type = $data['discount_type'];//0,打折 1,减价
                $pids = $products['product_id'];                
                $pitems = K::M('waimai/product')->items_by_ids($pids);
                $discount_data = $p_ids = $p_datas = array();

                foreach ($pids as $k => $v) {
                    if(($pro = $pitems[$v]) && ($discount_value = $products['discount_value'][$k]) && ($sale_sku = $products['sale_sku'][$k])){
                        if($pro['is_onsale'] && !$pro['closed']){
                            if($discount_type){
                                $discount_value = round($discount_value*100,0);
                                if($discount_value <= $pro['price']*100){
                                    $p_datas[$v] = array(
                                        'product_id'=>$v,
                                        'discount_value'=>$discount_value,
                                        'sale_sku'=>$sale_sku,
                                        'sale_count'=>0,
                                        );
                                    $p_ids[] = $v;
                                }
                            }else{
                                $discount_value = round($discount_value*10,0);
                                if($discount_value >=0 && $discount_value < 100){
                                    $p_datas[$v] = array(
                                        'product_id'=>$v,
                                        'discount_value'=>$discount_value,
                                        'sale_sku'=>$sale_sku,
                                        'sale_count'=>0,
                                        );
                                    $p_ids[] = $v;
                                }
                            }
                        }
                    }
                }

                if(count($p_datas) < count($pids)){
                    $this->msgbox->add('商品设置有误！',221)->response();
                }

                $period_times['stime'] = $period_times_stime ? $period_times_stime : '00:00';
                $period_times['ltime'] = $period_times_ltime ? $period_times_ltime : '23:59';
                $discount_data = array(
                    'shop_id'=>$this->shop_id,
                    'title'=>$title,
                    'stime'=>strtotime($stime),
                    'ltime'=>strtotime($ltime)+83599,
                    'period_type'=>1,
                    'period_weeks'=>implode(',',$period_weeks),
                    'period_times'=>serialize($period_times),
                    'quota'=>$quota,
                    'discount_type'=>$discount_type,
                    //'products'=>serialize($p_datas),
                    'products'=>implode(',',$p_ids),
                    'audit'=>1,
                    'dateline'=>__TIME,
                    'clientip'=>__IP,
                    );

                $type = (int)$this->GP('type') ? (int)$this->GP('type') : 1;
                $forward = $this->mklink('huodong/shop');
                if($type == 2){
                     $forward = $this->mklink('huodong/discount/create');
                }

                if($huodong_id = K::M('waimai/huodongdiscount')->create($discount_data)){
                    K::M('waimai/discountproduct')->insertAll($p_datas,$huodong_id);
                    K::M('waimai/waimai')->update_huodong_ltime($this->shop_id,'discount');//audit=1执行
                    $this->msgbox->add('活动创建成功！');
                    $this->msgbox->set_data('forward',$forward);
                }else{
                    $this->msgbox->add('活动创建失败！',222);
                }*/
            if(!$discount_data = $this->checkdata($data)){
                $this->msgbox->add('参数错误！',214);
            }else if(!$products = $this->checksubmit('products')){
                $this->msgbox->add('请选择活动商品！',215);
            }else if(!$p_datas = $this->checkproducts($products,$data['discount_type'])){
                $this->msgbox->add('活动商品设置有误！',215);
            }else if(count($p_datas) < count($products['product_id'])){
                $this->msgbox->add('活动商品设置有误！',215);
            }else{
                $pids = array_keys($p_datas);
                $discount_data['products'] = implode(',',$pids);
                $discount_data['shop_id'] = $this->shop_id;
                
                $type = (int)$this->GP('type') ? (int)$this->GP('type') : 1;
                $forward = $this->mklink('huodong/shop');
                if($type == 2){
                     $forward = $this->mklink('huodong/discount/create');
                }

                if($huodong_id = K::M('waimai/huodongdiscount')->create($discount_data)){
                    K::M('waimai/discountproduct')->insertAll($p_datas,$huodong_id);
                    K::M('waimai/waimai')->update_huodong_ltime($this->shop_id,'discount');//audit=1执行
                    $this->msgbox->add('活动创建成功！');
                    $this->msgbox->set_data('forward',$forward);
                }else{
                    $this->msgbox->add('活动创建失败！',222);
                }
            }
        }else{
            $cates = K::M('waimai/productcate')->select(array('shop_id'=>$this->shop_id,'parent_id'=>0));
            $this->pagedata['cates'] = $cates;
            $this->tmpl = 'huodong/discount/create.html';
        }       
    }

    public function detail($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('参数有误！',211);
        }else if(!$detail = K::M('waimai/huodongdiscount')->detail($huodong_id,true)){
            $this->msgbox->add('活动不存在或已被删除！',212);
        }else{
            $pros = K::M('waimai/product')->items_by_ids($detail['products']);
            $disc_pros = K::M('waimai/discountproduct')->items(array('huodong_id'=>$huodong_id,'product_id'=>$detail['products']),array('product_id'=>'desc'));
            $products = array();
            if($pros && $disc_pros){               
                foreach ($disc_pros as $k => $v) {
                    if($p = $pros[$v['product_id']]){                       
                        if($detail['discount_type']){
                            $disc_price = $p['price'] - $v['discount_value'];
                        }else{
                            $disc_price = $p['price']*$v['discount_value'];
                            $v['discount_value'] = sprintf("%.1f",$v['discount_value']*10);
                        }
                        $val = array_merge($p,$v);
                        $val['disc_price'] = $disc_price;
                        $products[$v['product_id']] = $val;
                    }
                }
            }
            $this->pagedata['products'] = $products;
            $this->pagedata['detail'] = $detail;
            $this->pagedata['huodong_id'] = $huodong_id;
            $this->tmpl = 'huodong/discount/detail.html';
        }           
    }

    public function history($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 1;
        if($items = K::M('waimai/huodongdiscount')->items($filter, array('huodong_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'huodong/discount/index.html';
    }

    public function close($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('参数有误！',211);
        }elseif(!$detail = K::M('waimai/huodongdiscount')->detail($huodong_id)){
            $this->msgbox->add('撤销的活动不存在或已撤销！',212);
        }else{
            if(K::M('waimai/huodongdiscount')->update($huodong_id,array('closed'=>1))){
                if(!K::M('waimai/waimai')->update_huodong_ltime($this->shop_id,'discount')){
                    K::M('waimai/huodongdiscount')->update($huodong_id,array('closed'=>0));
                    $this->msgbox->add('撤销失败!!',214);
                }else{
                    $this->msgbox->add('撤销成功!');
                    $this->msgbox->set_data('forward',$this->mklink('huodong/shop'));
                }
            }else{
                $this->msgbox->add('撤销失败!',215);
            }
        }
    }

    public function product($huodong_id=0, $cate_id, $page=1)
    {
        
        $filter = $orderby = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 0;
        $filter['is_onsale'] = 1;
        $filter['is_must'] = 0;
        $filter['is_tuijian'] = 0;

        $cates = K::M('waimai/productcate')->select(array('shop_id'=>$this->shop_id,'parent_id'=>0));
        $cate = current($cates);
        $cate_id = $cate_id ? $cate_id : $cate['cate_id'];
        if($cate_id = (int)$cate_id){
            $cate_ids = K::M('waimai/productcate')->getChildren($cate_id, true);
                $filter[':OR'] = array(
                    'cate_ids'=>'LIKE:%,'.$cate_id.',%',
                    'cate_id'=>$cate_ids,
                    );
        }
        if($SO = $this->GP('SO')){
            if($title = $SO['title']){
                $filter['title'] = 'LIKE:%'.$title.'%';
                unset($filter[':OR']);
            }
        }
        $orderby = array('orderby'=>'asc','product_id'=>'desc');

        $disc_pros = array();
        if($huodong_id = (int)$huodong_id){
            if($huodong = K::M('waimai/huodongdiscount')->detail($huodong_id)){
                $disc_pros = K::M('waimai/discountproduct')->items(array('huodong_id'=>$huodong_id));
            }            
        }

        if($items = K::M('waimai/product')->items($filter, $orderby, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('huodong/discount/product', array($huodong_id,$cate_id, '{page}')),array('SO'=>$SO));
        }

        $this->pagedata['huodong_id'] = $huodong_id;
        $this->pagedata['disc_products'] = $disc_pros;
        //$this->pagedata['cate_id'] = $cate_id;
        //$this->pagedata['cates'] = $cates;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'huodong/discount/product.html';
    }

    public function edit($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('参数有误！',211);
        }else if(!$detail = K::M('waimai/huodongdiscount')->detail($huodong_id,true)){
            $this->msgbox->add('活动不存在或已被删除！',212);
        }else if($detail['stime'] <= __TIME){
            $this->msgbox->add('活动已经开始，不能修改！',213);
        }else if($data = $this->checksubmit('data')){
            if(!$discount_data = $this->checkdata($data)){
                $this->msgbox->add('参数错误！',214);
            }else if(!$products = $this->checksubmit('products')){
                $this->msgbox->add('请选择活动商品！',215);
            }else if(!$p_datas = $this->checkproducts($products,$data['discount_type'])){
                $this->msgbox->add('活动商品设置有误！',215);
            }else if(count($p_datas) < count($products['product_id'])){
                $this->msgbox->add('活动商品设置有误！',215);
            }else{
                $pids = array_keys($p_datas);
                $discount_data['products'] = implode(',',$pids);
                
                $type = (int)$this->GP('type') ? (int)$this->GP('type') : 1;
                $forward = $this->mklink('huodong/shop');
                if($type == 2){
                     $forward = $this->mklink('huodong/discount/edit',array($huodong_id));
                }

                if(K::M('waimai/huodongdiscount')->update($huodong_id,$discount_data)){
                    K::M('waimai/discountproduct')->insertAll($p_datas,$huodong_id);
                    K::M('waimai/waimai')->update_huodong_ltime($this->shop_id,'discount');//audit=1执行
                    $this->msgbox->add('活动修改成功！');
                    $this->msgbox->set_data('forward',$forward);
                }else{
                    $this->msgbox->add('活动修改失败！',222);
                }
            }
        }else{
            $pros = K::M('waimai/product')->items_by_ids($detail['products']);
            $disc_pros = K::M('waimai/discountproduct')->items(array('huodong_id'=>$huodong_id,'product_id'=>$detail['products']),array('product_id'=>'desc'));
            $products = array();
            if($pros && $disc_pros){               
                foreach ($disc_pros as $k => $v) {
                    if($p = $pros[$v['product_id']]){                       
                        if($detail['discount_type']){
                            $disc_price = $p['price'] - $v['discount_value'];
                        }else{
                            $disc_price = $p['price']*$v['discount_value'];
                            $v['discount_value'] = sprintf("%.1f",$v['discount_value']*10);
                        }
                        unset($p['intro']);
                        $val = array_merge($p,$v);
                        $val['disc_price'] = $disc_price;
                        $products[$v['product_id']] = $val;
                    }
                }
            }
            $this->pagedata['products'] = $products;
            $this->pagedata['detail'] = $detail;
            $this->pagedata['huodong_id'] = $huodong_id;
            $cates = K::M('waimai/productcate')->select(array('shop_id'=>$this->shop_id,'parent_id'=>0));
            $this->pagedata['cates'] = $cates;

            $this->tmpl = 'huodong/discount/edit.html';
        }       
        
    }

    public function checkdata($data)
    {
        $quota = (int)$data['quota'];
        if(!$title = $data['title']){
            $this->msgbox->add('请填写折扣活动标题！',212)->response();
        }else if(!($stime = $data['stime']) || !($ltime = $data['ltime'])){
            $this->msgbox->add('请选择活动时间！',213)->response();
        }else if(strtotime($stime) > strtotime($ltime)){
            $this->msgbox->add('活动时间设置有误！',214)->response();
        }else if(!($period_weeks = $data['period_weeks']) || !($period_times = $data['period_times'])){
            $this->msgbox->add('请选择活动周期！',215)->response();
        }else if($period_times['stime'] && !($period_times_stime = K::M('helper/format')->checkTime($period_times['stime']))){
            $this->msgbox->add('活动周期开始时间设置有误！',216)->response();
        }else if($period_times['ltime'] && !($period_times_ltime = K::M('helper/format')->checkTime($period_times['ltime']))){
            $this->msgbox->add('活动周期结束时间设置有误！',217)->response();
        }else if($quota < 0){
            $this->msgbox->add('活动限购设置有误！',218)->response();
        }else{

            $period_times['stime'] = $period_times_stime ? $period_times_stime : '00:00';
            $period_times['ltime'] = $period_times_ltime ? $period_times_ltime : '23:59';
            $discount_data = array(
                'title'=>$title,
                'stime'=>strtotime($stime),
                'ltime'=>strtotime($ltime)+83599,
                'period_type'=>1,
                'period_weeks'=>implode(',',$period_weeks),
                'period_times'=>serialize($period_times),
                'quota'=>$quota,
                'discount_type'=>(int)$data['discount_type'],
                //'products'=>serialize($p_datas),
                //'products'=>implode(',',$p_ids),
                'audit'=>1,
                'dateline'=>__TIME,
                'clientip'=>__IP,
                );
            return $discount_data;
        }
    }

    public function checkproducts($products,$discount_type=0)
    {
        $discount_type = (int)$discount_type;
        $pids = $products['product_id'];                
        $pitems = K::M('waimai/product')->items_by_ids($pids);
        $discount_data = $p_datas = array();
        foreach ($pids as $k => $v) {
            if(($pro = $pitems[$v]) && ($discount_value = $products['discount_value'][$k]) && ($sale_sku = $products['sale_sku'][$k])){
                if($pro['is_onsale'] && !$pro['closed']){
                    if($discount_type){
                        $discount_value = round($discount_value*100,0);
                        if($discount_value <= $pro['price']*100){
                            $p_datas[$v] = array(
                                'product_id'=>$v,
                                'discount_value'=>$discount_value,
                                'sale_sku'=>$sale_sku,
                                'sale_count'=>0,
                                );
                        }
                    }else{
                        $discount_value = round($discount_value*10,0);
                        if($discount_value >=0 && $discount_value < 100){
                            $p_datas[$v] = array(
                                'product_id'=>$v,
                                'discount_value'=>$discount_value,
                                'sale_sku'=>$sale_sku,
                                'sale_count'=>0,
                                );
                        }
                    }
                }
            }
        }
        return $p_datas;
    }

}