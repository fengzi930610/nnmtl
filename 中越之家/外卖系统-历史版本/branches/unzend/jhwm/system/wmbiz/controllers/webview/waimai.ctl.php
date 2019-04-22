<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/4
 * Time: 11:22
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Webview_Waimai extends Ctl{


    //外卖店铺 -- 创建-- 编辑
    public function index(){
        $link =K::M('helper/link')->mklink('webview/index',null,null,'wmbiz');
        header('location:'.$link);exit;
        $waimai = K::M('waimai/waimai')->detail($this->shop_id);
        $this->pagedata['waimai'] = $waimai;
        $filter = array();
        $filter['shop_id'] = $this->shop_id;
        $env = K::M('waimai/env')->items($filter,array(),1,5,$count);
        $this->pagedata['env'] = $env;
        if($waimai['cate_id']){
            $dcate = K::M('waimai/cate')->detail($waimai['cate_id']);
            if($dcate['parent_id'] != 0){
                $cat_id = $dcate['parent_id'];
                $cate_id = $waimai['cate_id'];
            }else{
                $cat_id = $waimai['cate_id'];
            }    
            $this->pagedata['cat_id'] = $cat_id;
            $this->pagedata['cate_id'] = $cate_id;
            $this->pagedata['cate_name'] = $dcate;
        }
        if($waimai['city_id']){
            $city = K::M('data/city')->detail($waimai['city_id']);
            $pro = K::M('data/province')->detail($city['province_id']);
            $this->pagedata['pro_name'] = $pro['province_name'].'-'.$city['city_name'];
        }
        if($waimai['area_id']){
          $this->pagedata['are_name'] = K::M('data/area')->detail($waimai['area_id']);
        }
        if($waimai['business_id']){
          $this->pagedata['business'] = K::M('data/business')->detail($waimai['business_id']);
        }
        if($data = $this->checksubmit('data')){
           if(!$data['title']){
               $this->msgbox->add('店铺名称不能为空',211);
           }else if(!$data['contact']){
               $this->msgbox->add('联系人不能为空',212);
           }else if(!$phone =K::M('verify/check')->phone($data['phone'])){
              $this->msgbox->add('服务电话格式不正确',213);
           }else if(!$data['cate_id']){
               $this->msgbox->add('请选择分类',214);
           }else if(!$data['city_id']){
              $this->msgbox->add('请选择区域',215);
           }else if(!$data['addr']){
               $this->msgbox->add('详细地址不能为空',216);
           }else if(!$data['lng']||!$data['lat']){
               $this->msgbox->add('请在地图上选择具体地址',217);
           }else {
               K::M('shop/shop')->update($this->shop_id,array('contact'=>$data['contact'],'phone'=>$data['phone'],'logo'=>$data['logo']));
               foreach ($env as $v){
                   K::M('waimai/env')->delete($v['photo_id']);
               }
               foreach ($data['env'] as $k=>$vol){
                   K::M('waimai/env')->create(array('shop_id'=>$this->shop_id,'photo'=>$vol));
               }
               unset($data['env']);

               if($waimai){
                 if(K::M('waimai/waimai')->update($this->shop_id,$data)){
                     $this->msgbox->add('修改资料成功');
                 }else{
                     $this->msgbox->add('修改资料失败',218);
                 }
               }else{
                   $data['shop_id'] = $this->shop_id;
                   $data['pei_type'] = 5;
                 if(K::M('waimai/waimai')->create($data)){
                   $this->msgbox->add('店铺资料提交成功');
                 }else {
                     $this->msgbox->add('店铺资料提交失败',219);
                 }
               }
               
               $this->msgbox->set_data('forward',$this->mklink('webview/verify:index'));
           }
            
        }else{
            if($this->GP('o_lng')){
                $this->pagedata['o_lng'] = $this->GP('o_lng');
            }
            if($this->GP('o_lat')){
                $this->pagedata['o_lat'] = $this->GP('o_lat');
            }
            if($this->GP('o_addr')){
                $this->pagedata['o_addr'] = $this->GP('o_addr');
            }
            $local_catch = array();
            $local_catch['logo'] = $waimai['logo']?$waimai['logo']:'';
            $local_catch['banner'] = $waimai['banner']?$waimai['banner']:'';
            $local_catch['env'] = array();
            foreach($env as $v){
                $local_catch['env'][] = $v['photo'];
            }
            $this->pagedata['local_catch'] = json_encode($local_catch);
            $this->pagedata['waimai_cate'] = K::M('waimai/cate')->fetch_all();
           // print_r(K::M('data/business')->fetch_all());die;
            $this->pagedata['all_business'] = K::M('data/business')->fetch_all();
            $this->addrlist();
            $this->tmpl = 'webview/waimai/index.html';
        }
    }
    //编辑地图
    public function edit_map(){
        $link =K::M('helper/link')->mklink('webview/index',null,null,'wmbiz');
        header('location:'.$link);exit;
       $waimai = K::M('waimai/waimai')->detail($this->shop_id);
        if(!$waimai){
            $waimai = array();
        }
        if($_GET['lng']&&$_GET['lat']){
            $this->pagedata['location'] = json_encode(array('lat'=>$_GET['lat'],'lng'=>$_GET['lng']));
        }
        $this->pagedata['waimai'] = $waimai;
        $this->tmpl = 'webview/waimai/map.html';


    }
   

    public function addrlist(){

        $provice = K::M('data/province')->fetch_all();
        $provice = array_values($provice);
        $city = K::M('data/city')->fetch_all();
        $area = K::M('data/area')->fetch_all();
        foreach ($city as $k4=>$v4){
            $city[$k4] = $this->check_fields($v4,'city_id,province_id,city_name,order_by,dateline');
        }
        foreach ($city as $k=>$v){
            $city[$k]['children'] = array();
            foreach ($area as $k1=>$v1){
                if($v['city_id']==$v1['city_id']){
                    $city[$k]['children'][] = $v1;
                }
            }
        }
        foreach ($provice as $k2=>$v2){
            $provice[$k2]['children'] = array();
            foreach ($city as $k3=>$v3){
                if($v2['province_id']==$v3['province_id']){
                    $provice[$k2]['children'][] =$v3;
                }
            }
        }
        $this->pagedata['json_format'] = json_encode($provice);
        $this->pagedata['format_area'] = $provice;
      

    }


}