<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Huodong_Huangou extends Ctl
{
    public function create()
    {
        if(K::M('waimai/huodonghuangou')->count(array('shop_id'=>$this->shop_id, 'closed'=>0))){
            $this->msgbox->add('已有活动，只有撤销后才能创建', 211);
        }else if($data = $this->checksubmit('data')){
            if(!$huangou_data = $this->checkdata($data)){
                $this->msgbox->add('参数错误！',214);
            }else if(!$products = $this->checksubmit('products')){
                $this->msgbox->add('请选择活动商品！',215);
            }else if(!$p_datas = $this->checkproducts($products)){
                $this->msgbox->add('活动商品设置有误！',215);
            }else if(count($p_datas) < count($products['product_id'])){
                $this->msgbox->add('活动商品设置有误！',215);
            }else{
                $pids = array_keys($p_datas);
                $huangou_data['products'] = implode(',',$pids);
                $huangou_data['shop_id'] = $this->shop_id;

                if($huodong_id = K::M('waimai/huodonghuangou')->create($huangou_data)){
                    K::M('waimai/huangouproduct')->insertAll($p_datas,$huodong_id);
                    $this->msgbox->add('活动创建成功！');
                    $this->msgbox->set_data('forward', $this->mklink('huodong/shop'));
                }else{
                    $this->msgbox->add('活动创建失败！',222);
                }
            }
        }else{
            $cates = K::M('waimai/productcate')->select(array('shop_id'=>$this->shop_id,'parent_id'=>0));
            $this->pagedata['cates'] = $cates;
            $this->tmpl = 'huodong/huangou/create.html';
        }       
    }

    public function detail($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('参数有误！',211);
        }else if(!$detail = K::M('waimai/huodonghuangou')->detail($huodong_id,true)){
            $this->msgbox->add('活动不存在或已被删除！',212);
        }else{
            $pros = K::M('waimai/product')->items_by_ids($detail['products']);
            $disc_pros = K::M('waimai/huangouproduct')->items(array('huodong_id'=>$huodong_id,'product_id'=>$detail['products']),array('product_id'=>'desc'));
            $products = array();
            if($pros && $disc_pros){               
                foreach ($disc_pros as $k => $v) {
                    if($p = $pros[$v['product_id']]){                                               
                        $disc_price = $p['price'] - $v['discount_value'];                        
                        $val = array_merge($p,$v);
                        $val['disc_price'] = $disc_price;
                        $products[$v['product_id']] = $val;
                    }
                }
            }
            $this->pagedata['products'] = $products;
            $this->pagedata['detail'] = $detail;
            $this->pagedata['huodong_id'] = $huodong_id;
            $this->tmpl = 'huodong/huangou/detail.html';
        }           
    }

    public function history($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 1;
        if($items = K::M('waimai/huodonghuangou')->items($filter, array('huodong_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'huodong/huangou/index.html';
    }

    public function close($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('参数有误！',211);
        }elseif(!$detail = K::M('waimai/huodonghuangou')->detail($huodong_id)){
            $this->msgbox->add('撤销的活动不存在或已撤销！',212);
        }else{
            if(K::M('waimai/huodonghuangou')->update($huodong_id,array('closed'=>1))){                
                $this->msgbox->add('撤销成功!');
                $this->msgbox->set_data('forward',$this->mklink('huodong/shop'));                
            }else{
                $this->msgbox->add('撤销失败!',215);
            }
        }
    }

    public function product($cate_id=0, $page=1)
    {
        
        $filter = $orderby = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 0;
        $filter['is_onsale'] = 1;
        $filter['is_must'] = 0;
        $filter['is_spec'] = 0;

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

        if($items = K::M('waimai/product')->items($filter, $orderby, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('huodong/huangou/product', array($huodong_id,$cate_id, '{page}')),array('SO'=>$SO));
        }

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'huodong/huangou/product.html';
    }

    public function edit($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('参数有误！',211);
        }else if(!$detail = K::M('waimai/huodonghuangou')->detail($huodong_id,true)){
            $this->msgbox->add('活动不存在或已被删除！',212);
        }else if($detail['stime'] <= __TIME){
            $this->msgbox->add('活动已经开始，不能修改！',213);
        }else if($data = $this->checksubmit('data')){
            if(!$huangou_data = $this->checkdata($data)){
                $this->msgbox->add('参数错误！',214);
            }else if(!$products = $this->checksubmit('products')){
                $this->msgbox->add('请选择活动商品！',215);
            }else if(!$p_datas = $this->checkproducts($products)){
                $this->msgbox->add('活动商品设置有误！',215);
            }else if(count($p_datas) < count($products['product_id'])){
                $this->msgbox->add('活动商品设置有误！',215);
            }else{
                $pids = array_keys($p_datas);
                $huangou_data['products'] = implode(',',$pids);
                
                $type = (int)$this->GP('type') ? (int)$this->GP('type') : 1;
                $forward = $this->mklink('huodong/shop');
                if($type == 2){
                     $forward = $this->mklink('huodong/discount/edit',array($huodong_id));
                }

                if(K::M('waimai/huodonghuangou')->update($huodong_id,$huangou_data)){
                    K::M('waimai/huangouproduct')->insertAll($p_datas,$huodong_id);
                    K::M('waimai/waimai')->update_huodong_ltime($this->shop_id,'discount');//audit=1执行
                    $this->msgbox->add('活动修改成功！');
                    $this->msgbox->set_data('forward',$forward);
                }else{
                    $this->msgbox->add('活动修改失败！',222);
                }
            }
        }else{
            $pros = K::M('waimai/product')->items_by_ids($detail['products']);
            $disc_pros = K::M('waimai/huangouproduct')->items(array('huodong_id'=>$huodong_id,'product_id'=>$detail['products']),array('product_id'=>'desc'));
            $products = array();
            if($pros && $disc_pros){               
                foreach ($disc_pros as $k => $v) {
                    if($p = $pros[$v['product_id']]){                                              
                        $disc_price = $p['price'] - $v['discount_value'];                        
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

            $this->tmpl = 'huodong/huangou/edit.html';
        }       
        
    }

    public function checkdata($data)
    {
        $quota = (int)$data['quota'];
        $order_amount = (float)$data['order_amount'];
        if(!$title = $data['title']){
            $this->msgbox->add('请填写活动标题！',212)->response();
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
        }else if(!$order_amount || $order_amount <= 0){
            $this->msgbox->add('订单金额设置有误！',219)->response();
        }else{

            $period_times['stime'] = $period_times_stime ? $period_times_stime : '00:00';
            $period_times['ltime'] = $period_times_ltime ? $period_times_ltime : '23:59';
            $huangou_data = array(
                'title'=>$title,
                'stime'=>strtotime($stime),
                'ltime'=>strtotime($ltime)+83599,
                'order_amount'=>$order_amount,
                'period_weeks'=>implode(',',$period_weeks),
                'period_times'=>serialize($period_times),
                'quota'=>$quota,
                'audit'=>1,
                'dateline'=>__TIME,
                'clientip'=>__IP,
                );
            return $huangou_data;
        }
    }

    public function checkproducts($products,$discount_type=0)
    {
        $discount_type = (int)$discount_type;
        $pids = $products['product_id'];                
        $pitems = K::M('waimai/product')->items_by_ids($pids);
        $huangou_data = $p_datas = array();
        foreach ($pids as $k => $v) {
            if(($pro = $pitems[$v]) && ($discount_value = $products['discount_value'][$k]) && ($sale_sku = $products['sale_sku'][$k])){
                if($pro['is_onsale'] && !$pro['closed']){
                    $discount_value = round($discount_value*100,0);
                    if($discount_value <= $pro['price']*100){
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
        return $p_datas;
    }
}