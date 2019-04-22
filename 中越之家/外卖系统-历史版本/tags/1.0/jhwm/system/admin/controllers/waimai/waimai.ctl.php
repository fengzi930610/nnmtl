<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Waimai_Waimai extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        $filter = array('audit'=>1,'closed'=>0,'verify_name'=>1); //verify_name仅作审核外卖商家筛选，不参与接口和其他逻辑判断，审核通过设为1
        if($SO = $this->GP('SO')) {
            $pager['SO'] = $SO;
            if ($SO['title']) {
                $filter['title'] = "LIKE:%" . $SO['title'] . "%";
            }
            if ($SO['contact']) {
                $filter['contact'] = "LIKE:%" . $SO['contact'] . "%";
            }
            if ($SO['phone']) {
                //$filter['phone'] = "LIKE:%" . $SO['phone'] . "%";
                $filter[':SQL'] = " ( o.phone LIKE '%".$SO['phone']."%' OR w.mobile LIKE '%".$SO['phone']."%')";
            }
            if ($SO['shop_id']) {
                $filter['shop_id'] = $SO['shop_id'];
            }
            if ($SO['group_id']) {
                $filter['group_id'] = $SO['group_id'];
            }
            if ($SO['yy_status'] == 1) {
                $filter['yy_status'] = 1;
            } else if ($SO['yy_status'] == 2) {
                $filter['yy_status'] = 0;
            }
            if ($SO['order_min'] > 0 && $SO['order_max'] > 0) {
                if ($SO['order_min'] > $SO['order_max']) {
                    $this->msgbox->add('请选择正确的订单区间', 202)->response();
                } else {
                    $filter['orders'] = $SO['order_min'] . '~' . $SO['order_max'];
                }
            }
            if ($SO['order_min'] > 0 && !$SO['order_max']) {
                $filter['orders'] = ">:" . $SO['order_min'];
            }
            if (!$SO['order_min'] && $SO['order_max'] > 0) {
                $filter['orders'] = "<:" . $SO['order_max'];
            }

            //4.0模糊查询
            if ($SO['keywords']) {
                $filter[':SQL'] = " (o.title LIKE '%".$SO['keywords']."%' OR o.phone LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        $shop_money = K::M('shop/shop')->sum(array('closed'=>0),'money');
        $cates = K::M('waimai/cate')->fetch_all();
        $group_ids = array();
        $shop_ids = array();
        if($items = K::M('waimai/waimai')->items_join_shop($filter, array('shop_id'=>'desc'), $page, $limit, $count)){
            foreach($items as $k=>$v){
                 $items[$k]['cate_name'] = $this->get_format_cate($v['cate_id'],$cates);
                 $group_ids[] = $v['group_id'];
                $shop_ids[] = $v['shop_id'];
            }
            $group_list = K::M('pei/group')->items_by_ids($group_ids);
            $shop_list = K::M('shop/shop')->items_by_ids($shop_ids);
            foreach($items as $kk=>$vv){
                $items[$kk]['group'] = $group_list[$vv['group_id']];
                $items[$kk]['shop_info'] = $shop_list[$vv['shop_id']];
            }

            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['total'] = $shop_money;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:waimai/waimai/items.html';
    }

    public function get_format_cate($acte_id,$arr){
        $name = '';
        if($arr[$acte_id]['parent_id']==0){
            $name = $arr[$acte_id]['title'];
        }else{
            $act_id = $arr[$acte_id]['parent_id'];
            if($arr[$act_id]){
                $name=$arr[$act_id]['title'].'-'.$arr[$acte_id]['title'];
                $level_3 = $arr[$act_id]['parent_id'];
            }
            if($arr[$level_3]){
                $name = $arr[$level_3]['title'].'-'.$arr[$act_id]['title'].'-'.$arr[$acte_id]['title'];
            }

        }
        return $name;
    }
    
    
    public function so()
    {
        $this->tmpl = 'admin:waimai/waimai/so.html';
    }
    
    public function detail($shop_id = null)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['waimai'] = $detail;
            $this->tmpl = 'admin:waimai/waimai/detail.html';
        }
    }
    
    /*public function create()
    {
        if($data = $this->checksubmit('data')){
            if($_FILES['data']){
                foreach($_FILES['data'] as $k=>$v){
                    foreach($v as $kk=>$vv){
                        $attachs[$kk][$k] = $vv;
                    }
                }
                $upload = K::M('magic/upload');
                foreach($attachs as $k=>$attach){
                    if($attach['error'] == UPLOAD_ERR_OK){
                        if($a = $upload->upload($attach, 'shop')){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            if($shop_id = K::M('waimai/waimai')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?waimai/waimai-index.html');
            } 
        }else{
            $this->pagedata['citys'] = K::M('data/city')->items();
            $this->pagedata['areas'] = K::M('data/area')->items(array('city_id'=>$detail['city_id']));
            $this->pagedata['busis'] = K::M('data/business')->items(array('area_id'=>$detail['area_id']));
           $this->tmpl = 'admin:waimai/waimai/create.html';
        }
    }*/

    public function create()
    { 
        
        if($data = $this->checksubmit('data')){
            if(!$data){
                $this->msgbox->add('非法提交',211);
            }else if(!$data['title']){
                $this->msgbox->add('商铺名不能为空',212);
            }else if(!$mobile = K::M('verify/check')->mobile($data['mobile'])){
                $this->msgbox->add('手机号码格式有误',213);
            }else if(K::M('shop/shop')->shop($mobile,'mobile')){
                $this->msgbox->add('手机号码已存在',213);
            }else if(!$passwd = K::M('verify/check')->passwd($data['passwd'])){
                $this->msgbox->add('密码格式有误',214);
            }else if(!$data['contact']){
                $this->msgbox->add('联系人不能为空',215);
            }else if(!$data['cate_id']){
                $this->msgbox->add('分类不能为空',216);
            }else if(!$data['city_id']){
                $this->msgbox->add('请选择城市',217);
            }else if(!$data['addr']){
                $this->msgbox->add('详细地址不能为空',218);
            }else if(!$data['lng']||!$data['lat']){
                $this->msgbox->add('请在地图上选择详细地址',219);
            }else{
                $shop_data = $waimai_data = array();

                $shop_data = array(
                    'mobile'=>$mobile,
                    'passwd'=>$passwd,
                    );

                if($data['cate_ids']){
                    asort($data['cate_ids']);
                }

                $waimai_data = array(
                    'title'=>$data['title'],
                    'addr'=>$data['addr'],
                    'is_new'=>$data['is_new'],
                    'contact'=>$data['contact'],
                    'phone'=>$mobile,
                    'city_id'=> $data['city_id'] ? $data['city_id'] : 0,
                    'area_id'=>$data['area_id'] ? $data['area_id'] : 0,
                    'business_id'=>$data['business_id'] ? $data['business_id'] : 0,
                    'cate_id'=>$data['cate_id'],
                    'lng'=>$data['lng'],
                    'lat'=>$data['lat'],
                    'cate_ids'=>$data['cate_ids'] ? ','.implode(',',$data['cate_ids']).',' : '',
                    'logo'=>$data['logo'] ? $data['logo'] : '',
                    'banner'=>$data['banner'] ? $data['banner'] : '',
                    'audit'=>1,
                    'verify_name'=>1,
                    'last_time'=>__TIME
                    );

                if($shop_id = K::M('shop/shop')->create($shop_data)){
                    $waimai_data['shop_id'] = $shop_id;
                    if(K::M('waimai/waimai')->create($waimai_data)){
                        if($data['env']){
                            foreach ($data['env'] as $k=>$vol){
                                K::M('waimai/env')->create(array('shop_id'=>$shop_id,'photo'=>$vol));
                            }
                        }
                        $up_data = array(
                            'title'=>$data['title'],
                            'addr'=>$data['addr'],
                            'contact'=>$data['contact'],
                            'phone'=>$mobile,
                            'city_id'=> $data['city_id'] ? $data['city_id'] : 0,
                            'area_id'=>$data['area_id'] ? $data['area_id'] : 0,
                            'business_id'=>$data['business_id'] ? $data['business_id'] : 0,
                            'lng'=>$data['lng'],
                            'lat'=>$data['lat'],
                            'logo'=>$data['logo'] ? $data['logo'] : '',
                            'banner'=>$data['banner'] ? $data['banner'] : '',
                            'audit'=>1,
                            );
                        K::M('shop/shop')->update($shop_id,$up_data);
                        $this->msgbox->add('商家创建成功！');
                        $this->msgbox->set_data('forward','?waimai/shop-one-'.$shop_id);
                    }else{
                        $this->msgbox->add('商家创建失败！',221);
                    }
                }else{
                    $this->msgbox->add('商家创建失败',220);
                }
            }

        }else{
            $all_city = K::M('data/city')->fetch_all();
            $all_area = K::M('data/area')->fetch_all();

            $cats = K::M('waimai/cate')->select(array('parent_id'=>0));
            $cates = K::M('waimai/cate')->select(array('parent_id'=>'>:0'));

            $this->pagedata['cats'] = array_values($cats);
            $this->pagedata['cates'] = array_values($cates);

            $this->pagedata['all_city'] = $all_city;
            $this->pagedata['all_area'] =  json_encode($all_area);

            $this->tmpl = 'admin:waimai/waimai/create.html';
        }
        
    }

    public function edit($shop_id=null)
    {
        if(!($shop_id = (int)$shop_id) && !($shop_id = $this->GP('shop_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            if($_FILES['data']){
                foreach($_FILES['data'] as $k=>$v){
                    foreach($v as $kk=>$vv){
                        $attachs[$kk][$k] = $vv;
                    }
                }
                $upload = K::M('magic/upload');
                foreach($attachs as $k=>$attach){
                    if($attach['error'] == UPLOAD_ERR_OK){
                        if($a = $upload->upload($attach, 'shop')){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            $data1 = array('mobile'=>$data['mobile']);
            if(isset($data['passwd'])){
                if($data['passwd'] != '******'){
                    $data1['passwd'] = $data['passwd'];
                }
            }
            unset($data['mobile']);
            unset($data['passwd']);
            if($data['cate_ids']){
                asort($data['cate_ids']);
                $data['cate_ids'] = ','.implode(',',$data['cate_ids']).',';
            }
            if(K::M('waimai/waimai')->update($shop_id, $data)){
                K::M('shop/shop')->update($shop_id,$data1);
                $this->msgbox->add('修改内容成功');
            }  
        }else{
            //一级分类
            $cats = K::M('waimai/cate')->select(array('parent_id'=>0));
            //二级分类
            $cates = K::M('waimai/cate')->select(array('parent_id'=>'>:0'));
            $site_open = K::M('waimai/config')->getsiteopen()?1:0;
            $this->pagedata['site_open'] = $site_open;
            $this->pagedata['cats'] = $cats;
            $this->pagedata['cates'] = $cates;
            $detail['cate_ids'] = explode(',', $detail['cate_ids']);
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:waimai/waimai/edit.html';
        }
    }
    public function audit($shop_id=null)
    {
        if($shop_id = (int)$shop_id){
            if(K::M('waimai/waimai')->batch($shop_id, array('audit'=>1))){
                K::M('shop/shop')->batch($shop_id, array('have_waimai'=>1));
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('shop_id')){
            if(K::M('waimai/waimai')->batch($ids, array('audit'=>1))){
                K::M('shop/shop')->batch($ids, array('have_waimai'=>1));
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($shop_id=null)
    {
        if($shop_id = (int)$shop_id){
            if(!$detail = K::M('waimai/waimai')->detail($shop_id)){
                $this->msgbox->add('你要关闭的商户不存在或已经删除', 211);
            }else{
                if(K::M('waimai/waimai')->delete($shop_id)&&K::M('waimai/product')->update_product($shop_id)){
                    $this->msgbox->add('关闭商户成功');
                }
            }
        }else if($ids = $this->GP('shop_id')){
            if(K::M('waimai/waimai')->delete($ids)&&K::M('waimai/product')->update_product($ids)){

                $this->msgbox->add('批量关闭商户成功');
            }
        }else{
            $this->msgbox->add('未指定要关闭的商户ID', 401);
        }
    }  
    
    public function manage($shop_id) 
    {
        K::M('shop/auth')->manager($shop_id);
        $url = $this->mklink('index',array(),array(),'wmbiz');
        header("Location:".$url);
    }



    
    
}