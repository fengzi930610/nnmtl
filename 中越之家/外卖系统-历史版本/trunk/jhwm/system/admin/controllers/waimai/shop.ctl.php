<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Waimai_Shop extends Ctl
{
    public function one($shop_id)
    { //店铺信息
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('商户ID不存在', 211);
        }elseif(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('该商户不存在', 212);
        }else{
            $env = K::M('waimai/env')->items(array('shop_id'=>$shop_id),array('photo_id'=>'asc'));
            if($data = $this->checksubmit('data')){
                if(!$data){
                    $this->msgbox->add('非法提交',225);
                }else if(!$data['title']){
                    $this->msgbox->add('商铺名不能为空',226);
                }else if(!$mobile = K::M('verify/check')->phone($data['phone'])){
                    $this->msgbox->add('服务电话错误',228);
                }else if(!$data['contact']){
                    $this->msgbox->add('联系人不能为空',229);
                }else if(!$data['cate_id']){
                    $this->msgbox->add('分类不能为空',230);
                }else if(!$data['city_id']){
                    $this->msgbox->add('请选择城市',231);
                }else if(!$data['addr']){
                    $this->msgbox->add('详细地址不能为空',232);
                }else if(!$data['lng']||!$data['lat']){
                    $this->msgbox->add('请在地图上选择详细地址',233);
                }else if(!$denglu_mobile = K::M('verify/check')->vietnamMobile($data['mobile'])){
                    $this->msgbox->add('登录手机号码错误',234);
                }else if(!$data['passwd']){
                    $this->msgbox->add('密码不能为空',235);
                }else if((int)$data['warn_sku'] < 0){
                    $this->msgbox->add('库存预警值设置有误',236);
                }else if(isset($data['freight_calc_type']) && ((int)$data['freight_calc_type']<-2 || (int)$data['freight_calc_type']>2)) {
                    $this->msgbox->add('运费计算模式错误',238);
                //禁用店铺类型
                // }else if(!$data['country_code'] || ($data['country_code'] = strtolower(trim($data['country_code'])))==="" || !in_array($data['country_code'], ['cn','vn'])){
                //     $this->msgbox->add('请选择店铺类型',237);
                }else{
                    $data['country_code'] = "";//禁用店铺类型，强制为未类
                    $shop = K::M('shop/shop')->detail($shop_id);
                    if($shop['mobile']!=$denglu_mobile){
                        K::M('shop/shop')->update_mobile($shop_id,$denglu_mobile,true);//2019-01-27 修改 使用强制更新模式
                    }
                    //修改shop/shop
                    $update_shop = array();
                    //外卖信息
                    $waimai_data = array();
                    $update_shop['city_id'] = $data['city_id']?$data['city_id']:0;
                    $update_shop['title'] = $data['title'];
                    $update_shop['area_id'] = $data['area_id']?$data['area_id']:0;
                    $update_shop['business_id'] = $data['business_id']?$data['business_id']:0;
                    $update_shop['addr'] = $data['addr'];
                    $update_shop['lng'] = $data['lng'];
                    $update_shop['lat'] = $data['lat'];
                    $update_shop['contact'] = $data['contact'];
                    $update_shop['phone'] = $data['phone'];
                    if($data['passwd'] != '******'){
                        $update_shop['passwd'] = $data['passwd'];
                    }

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
                    $waimai_data['is_new'] = $data['is_new'];
                    $waimai_data['contact'] = $data['contact'];
                    $waimai_data['phone'] =$data['phone'];
                    $waimai_data['city_id'] = $data['city_id']?$data['city_id']:0;
                    $waimai_data['area_id'] = $data['area_id']?$data['area_id']:0;
                    $waimai_data['business_id'] = $data['business_id'];
                    $waimai_data['cate_id'] = $data['cate_id'];
                    $waimai_data['lng'] = $data['lng'];
                    $waimai_data['lat'] = $data['lat'];
                    $waimai_data['shop_id'] = $shop_id;
                    $waimai_data['info'] = $data['info'];
                    $waimai_data['delcare'] = $data['delcare'];
                    $waimai_data['orderby'] = $data['orderby'];
                    //$waimai_data['country_code'] = $data['country_code'];

                    //20190121添加，运费计算类型
                    if(isset($data['freight_calc_type']))
                        $waimai_data['freight_calc_type'] = (int)$data['freight_calc_type'];

                    $waimai_data['warn_sku'] = (int)$data['warn_sku'];
                    
                    if($data['cate_ids']){
                        asort($data['cate_ids']);
                        $waimai_data['cate_ids'] = ','.implode(',',$data['cate_ids']).',';
                    }

                    if(K::M('waimai/waimai')->update($shop_id,$waimai_data)){
                        foreach ($env as $v){ //先删后存
                            K::M('waimai/env')->delete($v['photo_id']);
                        }
                        foreach ($data['env'] as $k=>$vol){
                            K::M('waimai/env')->create(array('shop_id'=>$shop_id,'photo'=>$vol));
                        }
                        if(K::M('shop/shop')->update($shop_id,$update_shop)){
                            $this->msgbox->add('店铺信息设置完成');
                        }else{
                            $this->msgbox->add('店铺信息设置失败',222);
                        }

                    }else{
                        $this->msgbox->add('店铺信息设置失败',234);
                    }
                }

            }else{
                $all_city = K::M('data/city')->fetch_all();
                //区
                $all_area = K::M('data/area')->fetch_all();
                //商圈
                $all_business = K::M('data/business')->fetch_all();

                //一级分类
                $cats = K::M('waimai/cate')->select(array('parent_id'=>0));
                //二级分类
                $cates = K::M('waimai/cate')->select(array('parent_id'=>'>:0'));
                $this->pagedata['cats'] = $cats;
                $this->pagedata['cates'] = $cates;
                $this->pagedata['shop'] = K::M('shop/shop')->detail($shop_id);

                $this->pagedata['all_city'] = $all_city;
                $this->pagedata['all_area'] =  json_encode($all_area);
                $this->pagedata['all_business'] = json_encode($all_business);
                $this->pagedata['env'] = $env;
                $this->pagedata['countimg'] = count($env);
                $this->pagedata['shop_id'] = $shop_id;
                $waimai['cate_ids'] = explode(',', $waimai['cate_ids']);
                $this->pagedata['waimai'] = $waimai;

                //2019-01-27 添加 将系统中所有的手机号输出到页面上，以用JS进行手机号重复检测，方便进行提示，
                //                之所以这样做，是因为系统添加后台接口，要将接口数据添加到jh_system_module数据表，并刷新权限数据，接口才可以使用，单纯为了一个手机号检测而如此大动干戈，目前没有必要
                $phones = [];
                $_count = 0;
                $shops = K::M('waimai/waimai')->items([],NULL,1,999999,$_count);
                if($shops)
                {
                    foreach($shops as &$shop)
                    {
                        if($shop['phone'] === $this->pagedata['shop']['phone'])
                            continue;
                        $shop['phone'] = trim($shop['phone']);
                        if($shop['phone'] !== "")
                            $phones['ph'.$shop['phone']] = $shop['phone'];
                        unset($shop);
                    }
                }
                $this->pagedata['phones'] = ",".implode(",", $phones).",";

                $this->tmpl = 'admin:waimai/shop/one.html';
            }
        }
    }

    public function two($shop_id)
    { //资质信息
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('商户ID不存在', 211);
        }elseif(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('该商户不存在', 212);
        }else{
            if($data= $this->checksubmit('data')){
                if(!$data['id_name']){
                    $this->msgbox->add('法人姓名不能为空',211);
                }else if(!$data['id_photo']){
                    $this->msgbox->add('身份证身份证正面照不能为空',213);
                }else if(!$data['id_photo_f']){
                    $this->msgbox->add('身份证身份证反面照不能为空',214);
                }else if(!$data['id_photo_s']){
                    $this->msgbox->add('手持身份证正面照不能为空',215);
                }else if(!$data['yz_photo']){
                    $this->msgbox->add('营业执照图片不能为空',217);
                }else if(!$data['yz_name']){
                    $this->msgbox->add('营业执照名称不能为空',219);
                }else if(!$data['yz_number']){
                    $this->msgbox->add('营业执照注册号不能为空',220);
                }else if(!$data['yz_addr']){
                    $this->msgbox->add('营业执照所在地不能为空',221);
                }else{
                    $data['cy_time'] = strtotime($data['cy_time']);
                    $data['yz_time'] = strtotime($data['yz_time']);
                    if(K::M('waimai/verify')->detail($shop_id)){
                        if(K::M('waimai/verify')->update($shop_id,$data)){
                            $this->msgbox->add('操作成功');
                        }else {
                            $this->msgbox->add('操作失败,请稍后再试',217);
                        }
                    }else{
                        $data['shop_id'] = $shop_id;
                        if(K::M('waimai/verify')->create($data)){
                            $this->msgbox->add('操作成功');
                        }else{
                            $this->msgbox->add('操作失败,请稍后再试',218);
                        }
                    }
                }
            }else{

                $this->pagedata['waimai'] = $waimai;
                $verify = K::M('waimai/verify')->detail($shop_id);
                $verify['cy_time'] =  $verify['cy_time']?date('Y-m-d ',$verify['cy_time']):"";
                $verify['yz_time'] =  $verify['yz_time']?date('Y-m-d ',$verify['yz_time']):"";
                $this->pagedata['verify'] = $verify;
                $this->pagedata['shop_id'] = $shop_id;
                $this->tmpl = 'admin:waimai/shop/two.html';
            }
        }


    }

    public function three($shop_id)
    {   //配送信息
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('商户ID不存在', 211);
        }elseif(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('该商户不存在', 212);
        }else{
            if($data = $this->checksubmit('data')){
                
               /* if($data['pei_type'] == 1){
                    $datas['online_pay'] = 1;
                    $datas['is_daofu'] = 0;
                }else{
                    $datas['online_pay'] = 1;
                    $datas['is_daofu'] = 0;
                }*/
                /*if($data['pei_type']==1&&!$data['group_id']){
                    $this->msgbox->add('请选择配送站',213)->response();
                }
                if($data['group_id']){
                    $datas['group_id'] = $data['group_id'];
                }
                if($data['pei_type']==0){
                    $datas['group_id'] = 0;
                }*/
                /*$datas = array('pei_type'=>$data['pei_type']);
                if(!$data['group_id']){
                    $this->msgbox->add('请选择配送站',213)->response();
                }
                $datas['group_id'] = $data['group_id'];

                K::M('waimai/waimai')->update($shop_id,$datas,false);
                $this->msgbox->add('操作成功');*/

                
                if(!$data['group_id']){
                    $this->msgbox->add('请选择配送站',213)->response();
                }
                $datas = array('pei_type'=>$data['pei_type'], 'group_id'=>$data['group_id']);
                if($data['group_id'] && $data['pei_type']==1){
                    if(!$waimai['is_separate']){
                        $mindata = K::M('waimai/waimai')->get_min_data_by_group($data['group_id']);
                        $datas['min_amount'] = $mindata['min_price'];
                        $datas['freight'] = $mindata['shipping_fee'];
                    }else{

                    }
                }
                if($data['pei_type']==0){
                    $mindata = K::M('waimai/waimai')->get_min_data($waimai['area_polygon']);
                    $datas['min_amount'] = $mindata['min_price'];
                    $datas['freight'] = $mindata['shipping_fee'];
                }
                
                $datas['group_id'] = $data['group_id'];
                K::M('waimai/waimai')->update($shop_id,$datas,false);
                $this->msgbox->add('操作成功');
            }else {
                $this->pagedata['waimai'] = $waimai;

                $filter = array();
                $filter['city_id'] = $waimai['city_id'];
                $filter['closed'] = 0;
                $waimai['group_info'] = K::M('pei/group')->find($filter);
                $group_list = K::M('pei/group')->items($filter);

                $this->pagedata['group_list'] = $group_list;

                //2019-01-21添加，取出店铺的配置参数 2019-01-24 作废
                // $useShopId = (int)$shop_id;
                // if((int)$waimai['freight_calc_type'] <= 0)
                //     $useShopId = 0;
                // $fccConfigList = [];
                // if((int)$waimai['freight_calc_type'] !== 0)
                // {
                //     $fccConfigList = K::M('waimai/freightcalcconfig')->get_data_list($useShopId,(int)$waimai['freight_calc_type']);
                //     if(!$fccConfigList)
                //         $fccConfigList = [];
                // }
                // foreach($fccConfigList as $key => &$cfgItem)
                // {
                //     $fccConfigList[$key]['distance'] = (int)$cfgItem['distance']/1000;
                //     $fccConfigList[$key]['fee'] = (float)$cfgItem['fee'];
                //     unset($cfgItem);
                // }
                // $this->pagedata['fcc_config_list'] = $fccConfigList;
                //====================================

                //2019-01-24 新增，使用新的配置数据格式
                $useShopId = (int)$shop_id;
                if((int)$waimai['freight_calc_type'] <= 0)
                    $useShopId = 0;
                $fccConfig = [];
                if((int)$waimai['freight_calc_type'] !== 0)
                {
                    $fccConfig = K::M('waimai/freightcalcconfig')->get_data($useShopId,(int)$waimai['freight_calc_type']);
                    if(!$fccConfig)
                        $fccConfig = [];
                }
                if($useShopId>0 && !$fccConfig)
                {
                    if(abs((int)$waimai['freight_calc_type']) === 1)
                    {
                        $fccConfig = [
                            'start_distance' => 0,
                            'start_fee' => 0,
                            'distance_base' => 1,
                            'fee_base' => 0,
                        ];
                    }
                    else if(abs((int)$waimai['freight_calc_type']) === 2)
                    {
                        $fccConfig = [
                            'distance_range' => 0,
                            'in_distance_base' => 1,
                            'in_fee_base' => 0,
                            'out_distance_base' => 1,
                            'out_fee_base' => 0,
                        ];
                    }
                }
                else
                {
                    $distKeys = [
                        'start_distance',
                        'distance_base',
                        'distance_range',
                        'in_distance_base',
                        'out_distance_base'
                    ];
                    foreach($fccConfig as $ck => &$cv)
                    {
                        if(in_array($ck, $distKeys))
                            $fccConfig[$ck] = (float)$cv/1000;
                    }
                }
                $this->pagedata['fcc_config'] = $fccConfig;
                //==========================================

                $this->pagedata['shop_id'] = $shop_id;
                $this->tmpl = 'admin:waimai/shop/three.html';
            }
        }
    }

    public function four($shop_id){   //账户信息
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('商户ID不存在', 211);
        }elseif(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('该商户不存在', 212);
        }else{
            $account = K::M('shop/account')->detail($shop_id);
            if($data = $this->checksubmit('data')){
                if(!$data['account_type']){
                    $this->msgbox->add('开户行不能为空',211);
                }else if(!$data['account_number']){
                    $this->msgbox->add('账号或银行卡号不能为空',212);
                }else if(!$data['account_name']){
                    $this->msgbox->add('开户姓名不能为空',213);
                }else if(!($waimai_bl = $this->GP('waimai_bl'))<0){
                    $this->msgbox->add('佣金比例错误',214);
                }else if($waimai_bl < 0||$waimai_bl>100){
                    $this->msgbox->add('佣金比例应在0-100之间',215);
                }else if(!in_array($data['jisuan_type'],array(0,1,2))){
                    $this->msgbox->add('结算方式错误',217);
                }else if($data['is_ztsp']==1&&($data['zt_bl']<0||$data['zt_bl']>100)){
                    $this->msgbox->add('请填写正确的自提单结算比例',218);
                }else{
                    $jisuan_type = $data['jiesuan_type'];
                    unset($data['jiesuan_type']);
                    $is_ztsp = $data['is_ztsp'];
                    $zt_bl = $data['zt_bl'];
                    unset($data['is_ztsp']);
                    unset($data['zt_bl']);


                    if($account){
                        if(K::M('shop/account')->update($shop_id,$data)){
                            K::M('waimai/waimai')->update($shop_id,array('waimai_bl'=>$waimai_bl,'jiesuan_type'=>$jisuan_type,'is_ztsp'=>$is_ztsp,'zt_bl'=>$zt_bl));
                            $this->msgbox->add('更新资料成功');
                        }else{
                            $this->msgbox->add('更新资料失败，请稍后再试',215);
                        }
                    }else{
                        $data['shop_id'] = $shop_id;
                        if(K::M('shop/account')->create($data,true)){
                            K::M('waimai/waimai')->update($shop_id,array('waimai_bl'=>$waimai_bl,'jiesuan_type'=>$jisuan_type,'is_ztsp'=>$is_ztsp,'zt_bl'=>$zt_bl));
                            $this->msgbox->add('操作成功');
                        }else{
                            $this->msgbox->add('操作失败，请稍后再试',216);
                        }

                    }



                }
            }else {
                $this->pagedata['waimai'] = $waimai;
                $this->pagedata['account'] = $account;
                $this->pagedata['shop_id'] = $shop_id;
                $this->tmpl = 'admin:waimai/shop/four.html';
            }
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

    public function set_print($shop_id){


        if(!$shop_id){
            $this->msgbox->add('商户不存在',201);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('商家不存在或者被关闭',202);
        }else if($this->checksubmit()){
            $filter = array();
            $filter['shop_id'] = $shop_id;
            $print = K::M('shop/print')->items($filter);
            $ids = array();
            foreach($print as $k=>$v){
                $ids[] = $v['plat_id'];
            }
            K::M('shop/print')->delete($ids);
            if($data = $this->checksubmit('data')){
                $print_cfg = $this->system->config->get('print');
                foreach($data as $v){
                   if($v['machine_code']&&$v['mkey']){
                       $true_data= array(
                           'machine_code'=>$v['machine_code'],
                           'mkey'=>$v['mkey'],
                           'shop_id'=>$shop_id,
                           'title'=>$v['title'],
                           'from'=>$print_cfg['from'],
                           'partner'=>$print_cfg['partner'],
                           'apikey'=>$print_cfg['apikey'],
                           'num'=>1,
                           'status'=>1
                       );
                       if(!$print = K::M('printer/common')->load()){
                           $this->msgbox->add('加载模型错误',206)->response();
                       }else if(!$print->addAndOpenPrint($v['machine_code'],$v['mkey'])){
                           $this->msgbox->add('同步打印机错误',205)->response();
                       }else if(!K::M('shop/print')->create($true_data)){
                           $this->msgbox->add('添加失败',203)->response();
                       }
                       /*if(!K::M('printer/ylyun')->addAndOpenPrint($v['machine_code'],$v['mkey'])){
                           $this->msgbox->add('同步打印机失败',205)->response();
                       }
                       if(!K::M('shop/print')->create($true_data)){
                           $this->msgbox->add('添加失败',203)->response();
                       }*/
                   }
                }
                $this->msgbox->add('添加成功');
            }

        }else{
            $this->pagedata['shop_id'] =$shop_id;
            $this->pagedata['waimai'] = K::M('waimai/waimai')->detail($shop_id);
            $filter = array();
            $filter['shop_id'] = $shop_id;
            $print = K::M('shop/print')->items($filter);
            $i=100;
            foreach($print as $k=>$v){
                $print[$k]['i'] = $i;
                $i++;
            }
            $this->pagedata['print'] = $print;
            $this->tmpl = 'admin:waimai/shop/print.html';
        }

       /* if(!$shop_id){
            $this->msgbox->add('商户ID不存在',201);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('外卖商家不存在',202);
        }else if($data = $this->checksubmit('data')){
           if(!$data['machine_code']){
               $this->msgbox->add('终端号不能为空',203);
           }else if(!$data['mkey']){
               $this->msgbox->add('终端密钥',204);
           }
            $inse_data = array();
            $inse_data['machine_code'] = $data['machine_code'];
            $inse_data['mkey'] = $data['mkey'];
            $print_cfg = $this->system->config->get('print');
            $inse_data['shop_id'] = $shop_id;
            $inse_data['title'] = $print_cfg['title'];
            $inse_data['from'] = $print_cfg['from'];
            $inse_data['partner'] = $print_cfg['partner'];
            $inse_data['apikey'] = $print_cfg['apikey'];
            $inse_data['num'] = 1;
            $inse_data['status'] = 1;
            if(K::M('shop/print')->create($inse_data)){
               $this->msgbox->add('设置打印机成功');
            }else{
                $this->msgbox->add('设置打印机失败',205);
            }

        }else{


        }*/

    }

    public function import($shop_id){
        if($data = $this->checksubmit('data')){
            if(!$ele_shop_id = $data['local_shop_id']){
                $this->msgbox->add('请输入商铺id',203);
            }else if(!$shop_id){
                $this->msgbox->add('商铺不存在',204);
            }else if(K::M('import/import')->import_ele($ele_shop_id,$shop_id)){
                $this->msgbox->add('导入信息成功');
            }else{
                $this->msgbox->add('导入失败',205);
            }

        }else{
            if(!$shop_id){
                $this->msgbox->add('商家不存在',201);
            }else if(!$waiami = K::M('waimai/waimai')->detail($shop_id)){
                $this->msgbox->add('商家不存在',202);
            }else{
                $this->pagedata['shop_id'] =$shop_id;
                $this->pagedata['waimai'] = $waiami;
                $this->tmpl = "admin:waimai/shop/import.html";
            }
        }

    }

    public function separate($shop_id){
        if($data = $this->checksubmit('data')){
             if(!$shop_id = $data['shop_id']){
                 $this->msgbox->add('未指定需要修改的商户',204);
             }else if(!$waimai_shop = K::M('waimai/waimai')->detail($shop_id)){
                 $this->msgbox->add('商户不存在',205);
             }else if($waimai_shop['pei_type']==0){
                 $this->msgbox->add('该商家是自己配送，不可修改',206);
             }else if(!in_array($data['is_separate'],array(1,0))){
                 $this->msgbox->add('非法数据请求',207);
             }else if((float)$data['min_amount']<0){
                 $this->msgbox->add('起送价不正确',208);
             }else{
                 $config = $this->checksubmit('config');
                 $config_format = K::M('helper/format')->overturn($config);
                 foreach ($config_format as $k=>$v){
                     if(!$v['fkm']){
                         unset($config_format);
                         continue;
                     }
                     $config_format[$k]['fm'] = $v['fm']?$v['fm']:0;
                 }
                 if($config_format){
                     $sort = array();
                     foreach ($config_format as $k=>$v){
                         $sort[] = $v['fm'];
                     }
                     sort($sort);
                     K::M('waimai/waimai')->update($shop_id,array('freight'=>$sort[0]));
                 }

                 $config_format = $config_format?serialize($config_format):"";

                  if(K::M('waimai/waimai')->update($shop_id,array('is_separate'=>$data['is_separate'],'min_amount'=>(float)$data['min_amount'],'config'=>$config_format))){
                         $this->msgbox->add('修改成功');
                  }else{
                         $this->msgbox->add('修改失败',209);
                   }
             }

        }else{
            if(!$shop_id){
                $this->msgbox->add('商铺不存在',201);
            }else if(!$detail = K::M('waimai/waimai')->detail($shop_id)){
                $this->msgbox->add('商铺不存在',202);
            }else if($detail['pei_type']==0){
                $this->msgbox->add('该商铺是商家配送，请去商家后台配置',203);
            }else{
                if($detail['pei_type']==1){
                    $group = K::M('pei/group')->detail($detail['group_id']);
                    if($detail['is_separate']==0){
                        $detail['min_amount'] = $group['min_amount'];
                    }
                }
                $this->pagedata['shop_id'] =$shop_id;
                $this->pagedata['waimai'] = $detail;
                $this->tmpl = "admin:waimai/shop/separate.html";
            }

        }


    }



    public function importInit($shop_id,$ele_shop_id,$type,$admin_id=0)
    {

        if($type==1){
            if(!$datas = K::M('import/import')->getEleCates($ele_shop_id)){
                $datas = array();
            }
        }else{
            if(!$datas = K::M('import/lewaimai')->get_lewaimai_cate($admin_id,$ele_shop_id)){
                $datas = array();
            }

        }
        $this->pagedata['cates'] = $datas;
        $this->pagedata['type'] = $type;
        $this->pagedata['shop_id'] = $shop_id;
        $this->pagedata['ele_shop_id'] = $ele_shop_id;
        $this->pagedata['admin_id'] = $admin_id;

        $this->tmpl = 'admin:waimai/shop/importInit.html';        
    }

    public function importDo($shop_id, $ele_shop_id)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('商户有误！',211);
        }else if(!$ele_shop_id){
            $this->msgbox->add('饿了么商户id有误！',212);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('商户不存在或已删除！',212);
        }else if(!$cate_id = (int)$this->GP('cate_id')){
            $this->msgbox->add('请选择导入分类！',214);
        }else{
            $importSku = (int)$this->GP('importSku');
            $importImg = (int)$this->GP('importImg');
            if($pcount = K::M('import/import')->importDataByCates($shop_id, $cate_id, $importSku, $importImg)){
                $this->msgbox->add('导入成功！');
                $this->msgbox->set_data('data',array('pcount'=>$pcount));
            }else{
                $this->msgbox->add('导入失败！',215);
            }            
        }
    }

    public function importcate($shop_id)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('商户有误！',211);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('商户不存在或已删除！',212);
        }else if(!$cate_title = $this->GP('cate_title')){
            $this->msgbox->add('请选择导入分类！',214);
        }else{
            $cate_data = array(
                'parent_id'=>0,
                'shop_id'=>$shop_id,
                'title'=>$cate_title,
                'icon'=>'',
                'orderby'=>50,
                'dateline'=>__TIME,
                );
            if($cate_id = K::M('waimai/productcate')->create($cate_data)){
                $this->msgbox->add('分类导入成功！');
                $this->msgbox->set_data('data',$cate_id);
            }else{
                $this->msgbox->add('分类导入失败！',215);
            }            
        }
    }

    public function importproduct($shop_id)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('商户有误！',211);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('商户不存在或已删除！',212);
        }else if(!$cate_id = (int)$this->GP('cate_id')){
            $this->msgbox->add('请选择导入分类！',214);
        }else if(!$cate = K::M('waimai/productcate')->detail($cate_id)){
            $this->msgbox->add('导入的分类不存在！',215);
        }else if(!$product = $this->GP('product')){
            $this->msgbox->add('导入的商品不存在！',216);
        }else{
            $importSku = (int)$this->GP('importSku');
            $importImg = (int)$this->GP('importImg');
            if($importImg){
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
            } 
                     
        }
    }

    //设置商铺营业时间
    public function setbusiness($shop_id){

        if(!$shop_id){
            $this->msgbox->add('未指定商铺',201);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('指定的商户不存在',202);
        }else if($data = $this->checksubmit('data')){
            if(!in_array($data['pay_type'],array(1,2,3))){
                $this->msgbox->add('非法的配送方式',201);
            }else if(!in_array($data['yy_status'],array(1,0))){
                $this->msgbox->add('非法的营业状态',202);
            }else if(!in_array($data['is_ziti'],array(0,1))){
                $this->msgbox->add('非法的自提状态',203);
            }else if((count($data['stime'])<=0)||(count($data['ltime'])<=0)){
                $this->msgbox->add('请设置营业时间段',204);
            }else if(!array_intersect($data['yy_weeks'],array(0,1,2,3,4,5,6))){
                $this->msgbox->add('请选择营业日',205);
            }else if(!in_array($data['pstime_type'],array(0,1))){
                $this->msgbox->add('非法的可配送时间',206);
            }else if($data['pstime_type']==0&&(!$data['ps_stime']||!$data['ps_ltime'])){
                $this->msgbox->add('请填写可配送时间段',207);
            }else if (!in_array($data['yuyue_day'], array(0,1,2,3,4,5,6,7))) {// 1：当天 2：明天  .... 最大到6天内
                $this->msgbox->add('非法的数据提交',217);
            } else{


                $update_data = array();
                if ($data['pay_type'] == 1) {// 全部支持
                    $update_data['online_pay'] = $update_data['is_daofu'] = 1;
                }elseif ($data['pay_type'] == 2) {// 仅支持货到付款
                    $update_data['online_pay'] = 0;
                    $update_data['is_daofu'] = 1;
                }elseif ($data['pay_type'] == 3) {// 仅支持在线支付
                    $update_data['online_pay'] = 1;
                    $update_data['is_daofu'] = 0;
                }
                if($data['is_ziti']==0){
                    $update_data['zero_ziti']=0;
                }else if($data['is_ziti']==1){
                    if(!$data['zero_ziti']){
                        $update_data['zero_ziti'] = 0;
                    }else{
                        $update_data['zero_ziti'] = 1;
                    }
                }

                $yy_peitime = array();
                foreach ($data['stime'] as $k=>$v){
                    //检测两个时间是否检查
                    if($data['stime'][$k]&&$data['ltime'][$k]){
                        if(!preg_match('/^\d{1,2}\:\d{2}$/i', $v)){
                            $this->msgbox->add('开始时间格式不对',219)->response();
                        }else if((strpos($data['ltime'][$k],'次日') === false) && (!preg_match('/^\d{1,2}\:\d{2}$/i', $data['ltime'][$k]))){
                            $this->msgbox->add('结束时间格式不对',220)->response();
                        }else if(!preg_match('/^\d{1,2}\:\d{2}$/i', trim(str_replace('次日','',$data['ltime'][$k])))){
                            $this->msgbox->add('结束时间格式不对',220)->response();
                        }else{
                            $yy_peitime[] = array(
                                'stime'=>$data['stime'][$k],
                                'ltime'=>$data['ltime'][$k]
                            );
                        }
                    }
                }
                $update_data['yy_peitime'] = serialize($yy_peitime);
                $ps_peitime = array();
                foreach ($data['ps_stime'] as $k=>$v){
                    //检测两个时间是否检查
                    if($data['ps_stime'][$k]&&$data['ps_ltime'][$k]){
                        /*if((!preg_match('/^\d{1,2}\:\d{2}$/i', $v))||(!preg_match('/^\d{1,2}\:\d{2}$/i', $ps['ltime'][$k]))){
                            $this->msgbox->add('开始时间或者结束时间格式不对',219)->response();
                        }*/
                        if(!preg_match('/^\d{1,2}\:\d{2}$/i', $v)){
                            $this->msgbox->add('开始时间格式不对',219)->response();
                        }else if((strpos($data['ps_ltime'][$k],'次日') === false) && (!preg_match('/^\d{1,2}\:\d{2}$/i', $data['ps_ltime'][$k]))){
                            $this->msgbox->add('结束时间格式不对',220)->response();
                        }else if(!preg_match('/^\d{1,2}\:\d{2}$/i', trim(str_replace('次日','',$data['ps_ltime'][$k])))){
                            $this->msgbox->add('结束时间格式不对',220)->response();
                        }else{
                            $ps_peitime[] = array(
                                'stime'=>$data['ps_stime'][$k],
                                'ltime'=>$data['ps_ltime'][$k]
                            );
                        }
                    }
                }
                $update_data['ps_time'] = serialize($ps_peitime);

                //v3.6 营业时间按周选择
                $update_data['yy_weeks'] = implode(',', $data['yy_weeks']);
                $update_data['yy_status'] =$data['yy_status'];
                $update_data['is_ziti'] = $data['is_ziti'];
                $update_data['pstime_type'] = $data['pstime_type'];
                $update_data['yuyue_day'] = $data['yuyue_day'];
                if(K::M('waimai/waimai')->update($shop_id, $update_data)){
                    $this->msgbox->add('修改成功！');
                }else{
                    $this->msgbox->add('修改失败！',301);
                }

            }

        }else{
           /* echo '<pre>';
            print_r($waimai);exit;*/
            $this->pagedata['shop_id'] =$shop_id;
            $this->pagedata['waimai'] = $waimai;
            $this->tmpl = "admin:waimai/shop/setbusiness.html";
        }

    }


    public function comment($shop_id,$page){
        if(!$shop_id){
            $this->msgbox->add('未指定需要查看的商家',201);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('指定的商家不存在或已删除',202);
        }else{
            $page = max((int)$page,1);
            $limit = 50;
            $filter = array();
            $filter['shop_id'] =$shop_id;
            if($SO = $this->GP('SO')){
                if($SO['order_id']){
                    $filter['order_id'] = $SO['order_id'];
                }
                if($SO['uid']){
                    $filter['uid'] = $SO['uid'];
                }
                if($SO['stime']&&$SO['ltime']){
                    $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
                }
                if(!$SO['stime']&&$SO['ltime']){
                    $filter['dateline'] = ">:".strtotime($SO['stime']);
                }
                if($SO['stime']&&!$SO['ltime']){
                    $filter['dateline'] = "<:".(strtotime($SO['ltime'])+86399);
                }
                if (isset($SO['score'])) {
                    switch ($SO['score']) {// 满意程度
                        case '1':
                            //$filter['score_avg'] = "3.01~5";// 满意
                            $filter['score'] = "3.01~5";// 满意
                            break;
                        case '2':
                            //$filter['score_avg'] = "0~2.99";// 不满意
                            $filter['score'] = "0~2.99";// 不满意
                            break;
                        case '3':
                            $filter['score'] = "3";// 一般
                            //$filter['score_avg'] = "3";// 一般
                            break;

                    }
                }
                if (isset($SO['score_peisong'])) {
                    switch ($SO['score_peisong']) {// 满意程度
                        case '1':
                            //$filter['score_avg'] = "3.01~5";// 满意
                            $filter['score_peisong'] = "3.01~5";// 满意
                            break;
                        case '2':
                            //$filter['score_avg'] = "0~2.99";// 不满意
                            $filter['score_peisong'] = "0~2.99";// 不满意
                            break;
                        case '3':
                            $filter['score_peisong'] = "3";// 一般
                            //$filter['score_avg'] = "3";// 一般
                            break;

                    }
                }
            }
            if($items = K::M('waimai/comment')->items($filter, array("comment_id"=>"DESC"), $page, $limit, $count)){
                $uids = $comment_ids = array();
                foreach ($items as $k=>$v){
                    $uids[$v['uid']] = $v['uid'];
                   // $comment_ids[$v['comment_id']] = $v['comment_id'];
                }
                $member_list = K::M('member/member')->items_by_ids($uids);
                foreach ($items as $kk=>$vv){
                    $items[$kk]['member'] = $member_list[$vv['uid']]?$member_list[$vv['uid']]:array();
                    $items[$kk]['content'] = K::M('content/string')->sub($vv['content']);
                }
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink("waimai/shop:comment", array($shop_id,'{page}')), array('SO'=>$SO));
            }
            $this->pagedata['shop_id'] = $shop_id;
            $this->pagedata['waimai'] = $waimai;
            $this->pagedata['pagers'] = $pager;
            $this->pagedata['items'] = $items;
            $this->tmpl ="admin:waimai/shop/comment.html";





        }





    }

    public function comment_so($shop_id){
        $this->pagedata['shop_id'] = $shop_id;
        $this->tmpl = 'admin:waimai/shop/comment_so.html';
    }


    //删除评论
    public function comment_delete($comment_id){
        if($comment_ids = $this->GP('comment_id')){
           foreach ($comment_ids as $k=>$v){
               if(!K::M('waimai/comment')->detail($v)){
                   $this->msgbox->add('指定的评论不存在或已删除',202)->response();
               }
           }
           if(K::M('waimai/comment')->delete($comment_ids)){
               $this->msgbox->add('删除成功');
           }else{
               $this->msgbox->add('删除失败',203);
           }

        }else if(!$comment_id){
            $this->msgbox->add('未指定需要删除的评论',201);
        }else if(!$comment = K::M('waimai/comment')->detail($comment_id)){
            $this->msgbox->add('指定的评论不存在或已删除',202);
        }else if(K::M('waimai/comment')->delete($comment_id)){
            $this->msgbox->add('删除成功');
        }else{
            $this->msgbox->add('删除失败',203);
        }

    }


    public function tixian($shop_id,$page){
        if(!$shop_id){
            $this->msgbox->add('商家不存在',201);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('查看的商家不存在或已删除',203);
        }else{
            $page = max((int)$page,1);
            $limit = 50;
            $filter = array();
            $filter['shop_id'] = $shop_id;
            if($so = $this->checksubmit('SO')){
                $this->pagedata['so'] = $so;
                if($so['stime']&&!$so['ltime']){
                    $filter['dateline'] = '>:'.strtotime($so['stime']);
                }
                if(!$so['stime']&&$so['ltime']){
                    $filter['dateline'] = '<:'.strtotime($so['ltime']);
                }
                if($so['stime']&&$so['ltime']){
                    $filter['dateline'] = strtotime($so['stime']).'~'.strtotime($so['ltime']);
                }
            }
            if($items = K::M('shop/log')->items($filter,array('log_id'=>'DESC'),$page,$limit,$count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('waimai/shop:tixian', array($shop_id,'{page}')), array('SO' => $so));
            }else{
                $items = array();
            }
            $this->pagedata['shop_id'] = $shop_id;
            $this->pagedata['waimai'] = $waimai;
            $this->pagedata['shop'] = K::M('shop/shop')->detail($shop_id);
            $this->pagedata['pager'] = $pager;
            $this->pagedata['items'] = $items;
            $this->tmpl = 'admin:waimai/shop/tixian.html';
        }

    }


}
