<?php

/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/3/7

 * Time: 14:29

 * 全新的商城入驻

 */  

if(!defined('__CORE_DIR')){

    exit("Access Denied");

}

class Ctl_Newreg extends Ctl {

    //申请入驻第一步

    public function one(){

        $this->check_login();

        $env = K::M('waimai/env')->items(array('shop_id'=>$this->shop_id),array('photo_id'=>'asc'));

        if($data = $this->checksubmit('data')){

            if(!$data){

                $this->msgbox->add('非法提交',225);

            }else if(!$data['title']){

                $this->msgbox->add('商铺名不能为空',226);

            }/*else if(strlen($data['title'])>10){

                $this->msgbox->add('商铺名不能超过10个字符',227);

            }*/else if(!$mobile = K::M('verify/check')->phone($data['phone'])){

                $this->msgbox->add('服务电话错误',228);

            }else if(!$data['contact']){

                $this->msgbox->add('联系人不能为空',229);

            }else if(!(int)$data['cate_id']){

                $this->msgbox->add('分类不能为空',230);

            }else if(!$data['city_id']){

                $this->msgbox->add('请选择城市',231);

            }else if(!$data['addr']){

                $this->msgbox->add('详细地址不能为空',232);

            }else if(!$data['lng']||!$data['lat']){

                $this->msgbox->add('请在地图上选择详细地址',233);

            }else{

                //修改shop/shop

                $update_shop = array();

                //外卖信息

                $waimai_data = array();

                $update_shop['city_id'] = $data['city_id'];

                $update_shop['title'] = $data['title'];

                $update_shop['area_id'] = $data['area_id']?$data['area_id']:0;

                $update_shop['business_id'] = $data['business_id']?$data['business_id']:0;

                $update_shop['addr'] = $data['addr'];

                $update_shop['lng'] = $data['lng'];

                $update_shop['lat'] = $data['lat'];

                $update_shop['contact'] = $data['contact'];

                $update_shop['phone'] = $data['phone'];

                if($data['logo']){

                    $update_shop['logo'] = $data['logo'];

                    $waimai_data['logo'] = $data['logo'];

                }

                if($data['banner']){

                    $update_shop['banner'] = $data['banner'];

                    $waimai_data['banner'] = $data['banner'];

                }

                $waimai_data['title'] =$data['title'];

                $waimai_data['addr'] = $data['addr'];

                $waimai_data['contact'] = $data['contact'];

                $waimai_data['phone'] =$data['phone'];

                $waimai_data['city_id'] = $data['city_id'];

                $waimai_data['area_id'] = $data['area_id'];

                $waimai_data['business_id'] = $data['business_id'];

                $waimai_data['cate_id'] = $data['cate_id'];

                $waimai_data['lng'] = $data['lng'];

                $waimai_data['lat'] = $data['lat'];

                $waimai_data['pei_type'] = 5;

                $waimai_data['shop_id'] = $this->shop_id;

                $a = false;

                if($waimai = K::M('waimai/waimai')->detail($this->shop_id)){

                    unset($waimai_data['shop_id']);

                    $a= K::M('waimai/waimai')->update($this->shop_id,$waimai_data);

                }else{

                    $a= K::M('waimai/waimai')->create($waimai_data);

                }

                

                foreach ($env as $v){

                   K::M('waimai/env')->delete($v['photo_id']);

                }

                foreach ($data['env'] as $k=>$vol){

                   K::M('waimai/env')->create(array('shop_id'=>$this->shop_id,'photo'=>$vol));

                }

                if(K::M('shop/shop')->update($this->shop_id,$update_shop)&&$a){

                    $this->msgbox->add('商铺信息创建完成');

                }else{

                    $this->msgbox->add('商铺信息设置失败',234);

                }

            }

        }else{            

            $all_city = K::M('data/city')->fetch_all();

            //区

            $all_area = K::M('data/area')->fetch_all();

            //商圈

            $all_business = K::M('data/business')->fetch_all();



            //商城分类

            //$all_cate = K::M('waimai/cate')->fetch_all();
            $all_cate = K::M('waimai/cate')->select(array('parent_id'=>0));

            $this->pagedata['cate'] = $all_cate;

            $this->pagedata['all_city'] = $all_city;
            $this->pagedata['json_city']  = json_encode($all_city);

            $this->pagedata['all_area'] =  json_encode($all_area);

            $this->pagedata['all_business'] = json_encode($all_business);

            $this->pagedata['waimai'] = K::M('waimai/waimai')->detail($this->shop_id);

            $this->pagedata['verify'] = K::M('waimai/verify')->detail($this->shop_id);

            $this->pagedata['env'] = $env;

            $this->pagedata['countimg'] = count($env);

            $this->tmpl = 'newreg/reg_one.html';

        }

    }

    //申请入驻第二步

    public function two(){

        $this->check_login();

        if($data= $this->checksubmit('data')){

            if(!$data['id_name']){

                $this->msgbox->add('法人姓名不能为空',211);

            }/*else if(!$id_name = K::M('verify/check')->id_number($data['id_number'])){

                $this->msgbox->add('身份证号码不能为空',212);

            }*/else if(!$data['id_photo']){

                $this->msgbox->add('身份证身份证正面照不能为空',213);

            }else if(!$data['id_photo_f']){

                $this->msgbox->add('身份证身份证反面照不能为空',214);

            }else if(!$data['id_photo_s']){

                $this->msgbox->add('手持身份证正面照不能为空',215);

            }else{

                $data['cy_time'] = strtotime($data['cy_time']);

                $data['yz_time'] = strtotime($data['yz_time']);

                if(K::M('waimai/verify')->detail($this->shop_id)){

                    if(K::M('waimai/verify')->update($this->shop_id,$data)){

                        $this->msgbox->add('更新成功');

                        $this->msgbox->set_data('forward',$this->mklink('newreg/three'));

                    }else {

                        $this->msgbox->add('更新成功失败,请稍后再试',217);

                    }

                }else{

                    $data['shop_id'] = $this->shop_id;

                    if(K::M('waimai/verify')->create($data)){

                        $this->msgbox->add('提交成功');

                        $this->msgbox->set_data('forward',$this->mklink('newreg/three'));

                    }else{

                        $this->msgbox->add('提交失败，请稍后再试',218);

                    }

                }



            }



        }else{

            $this->pagedata['verify'] = K::M('waimai/verify')->detail($this->shop_id);

            $this->tmpl = 'newreg/reg_two.html';

        }



    }

    

    

    //申请入驻第三步

    public function three(){

        $this->check_login();

        $waimai = K::M('waimai/waimai')->detail($this->shop_id);

        if($data = $this->checksubmit('data')){

            $datas = array('pei_type'=>$data['pei_type']);

            if($data['pei_type'] == 1){

                $datas['online_pay'] = 1;

                $datas['is_dao'] = 0;

            }

            K::M('waimai/waimai')->update($this->shop_id,$datas,false);

            $this->msgbox->add('更新资料成功');

            $this->msgbox->set_data("forward", $this->mklink('newreg/four'));

        }else { 

            $this->pagedata['waimai'] = $waimai;

            $this->pagedata['verify'] = K::M('waimai/verify')->detail($this->shop_id);

            $this->tmpl = 'newreg/reg_three.html';

        }



    }

    

    

    

    //申请入驻第四步

    public function four(){

        $this->check_login();

        $account = K::M('shop/account')->detail($this->shop_id);

        if($data = $this->checksubmit('data')){

            if(!$data['account_type']){

                $this->msgbox->add('开户行不能为空',211);

            }else if(!$data['account_number']){

                $this->msgbox->add('账号或银行卡号不能为空',212);

            }else if(!$data['account_name']){

                $this->msgbox->add('开户姓名不能为空',213);

            }else{

                if($account){

                    if(K::M('shop/account')->update($this->shop_id,$data)){

                        K::M('waimai/verify')->update($this->shop_id,array('verify'=>5));

                        K::M('waimai/waimai')->update($this->shop_id,array('last_time'=>__TIME));

                        K::M('waimai/waimai')->update($this->shop_id,array('verify_name'=>0));// 重新更新状态为待审  = =# 设计多余

                        $this->msgbox->add('更新资料成功');

                    }else{

                        $this->msgbox->add('更新资料失败，请稍后再试',215);

                    }

                }else{

                    $data['shop_id'] = $this->shop_id;

                    if(K::M('shop/account')->create($data,true)){

                        K::M('waimai/verify')->update($this->shop_id,array('verify'=>5));

                        K::M('waimai/waimai')->update($this->shop_id,array('last_time'=>__TIME));

                        K::M('waimai/waimai')->update($this->shop_id,array('verify_name'=>0));// 重新更新状态为待审  = =# 设计多余

                        $this->msgbox->add('更新资料成功');

                    }else{

                        $this->msgbox->add('更新资料失败，请稍后再试',216);

                    }



                }

            }

        }else {

           $this->pagedata['verify'] = K::M('waimai/verify')->detail($this->shop_id);

            $this->pagedata['account'] = $account;

            $this->tmpl = 'newreg/reg_four.html';

        }



    }



    //异步上传文件

    public function uploadimg()

    {

        $thumbs = array();

        if($attach = $_FILES['logo']){

            $thumbs = array('photo'=>'160X160');

        }elseif($attach = $_FILES['huanjing']){

            $thumbs = array('photo'=>'800', 'thumb'=>'300X300');

        }elseif($attach = $_FILES['image']){

            $thumbs = array('photo'=>'800');

        }

        if($data = K::M('magic/upload')->upload($attach, 'image', null, $thumbs)){

            $this->msgbox->set_data('data', array('photo'=>$data['photo']));

        }else{

            $this->msgbox->add('上传图片失败', 501);

        }

        $this->msgbox->json();

    }

    //入驻首页

    public function index(){

        $this->check_login();

        $waimai = K::M('waimai/waimai')->detail($this->shop_id);

        $verify = K::M('waimai/verify')->detail($this->shop_id);

        $account = K::M('shop/account')->detail($this->shop_id);

        $cate =  K::M('waimai/cate')->detail($waimai['cate_id']);

        $env = K::M('waimai/env')->items(array('shop_id'=>$this->shop_id),array('photo_id'=>'asc'));

        $this->pagedata['env'] = $env;

        $this->pagedata['cate'] = $cate;

        $this->pagedata['waimai'] = $waimai;

        $this->pagedata['verify'] = $verify;

        $this->pagedata['account'] = $account;

        $this->tmpl = 'newreg/index.html';



    }

























}