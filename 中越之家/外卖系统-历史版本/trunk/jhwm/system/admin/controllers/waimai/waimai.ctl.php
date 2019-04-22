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
            }else if(!$mobile = K::M('verify/check')->vietnamMobile($data['mobile'])){
                $this->msgbox->add('手机号码格式有误',213);
            // }else if(K::M('shop/shop')->shop($mobile,'mobile')){
            //     $this->msgbox->add('手机号码已存在',213);
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
            }else if(isset($data['freight_calc_type']) && ((int)$data['freight_calc_type']<-2 || (int)$data['freight_calc_type']>2)) {
                $this->msgbox->add('运费计算模式错误',223);
            //禁用店铺类型
            // }else if(!$data['country_code'] || ($data['country_code'] = strtolower(trim($data['country_code'])))==="" || !in_array($data['country_code'], ['cn','vn'])){
            //     $this->msgbox->add("请选择店铺类型",222);
            }else{
                $data['country_code'] = ""; //禁用店铺类型，强制为未类
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
                    'last_time'=>__TIME,
                    //'country_code' => $data['country_code']

                    //20190121添加，运费计算类型
                    'freight_calc_type' => isset($data['freight_calc_type'])?(int)$data['freight_calc_type']:0
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

            //2019-01-26 添加 将系统中所有的手机号输出到页面上，以用JS进行手机号重复检测，方便进行提示，
            //                之所以这样做，是因为系统添加后台接口，要将接口数据添加到jh_system_module数据表，并刷新权限数据，接口才可以使用，单纯为了一个手机号检测而如此大动干戈，目前没有必要
            $phones = [];
            $_count = 0;
            $shops = K::M('waimai/waimai')->items([],NULL,1,999999,$_count);
            if($shops)
            {
                foreach($shops as &$shop)
                {
                    $shop['phone'] = trim($shop['phone']);
                    if($shop['phone'] !== "")
                        $phones['ph'.$shop['phone']] = $shop['phone'];
                    unset($shop);
                }
            }
            $this->pagedata['phones'] = ",".implode(",", $phones).",";

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
        $home = "";
        if($this->GP("_sr"))
        {
            $subRouter = trim($this->GP("_sr"));
            if($subRouter !== "")
            {
                $args = [];
                if($this->GP("_args"))
                    $args = explode(",", trim($this->GP("_args")));
                $home = $this->mklink($subRouter,$args,[],"wmbiz");
                if(!$home)
                    $home = "";
            }
        }
        $url = $this->mklink('index',array(),$home?['home'=>$home]:[],'wmbiz');
        header("Location:".$url);
    }

    //2019-01-21新增，保存运费计算参数配置数据的接口 JSON
    //2019-01-26备注：此接口原意思全称：save freight calculate config（保存运费计算配置），
    //               因管理需要，此接口名称不改，但作用是用于保存所有在fcc_mgr()接口页面中的配置参数的接口，
    //               称为“保存费用计算参数配置”
    public function save_fcc()
    {
        $errStr = "";
        try
        {
            if($this->GP("opt"))
            {
                $data = $this->GP("data");
                if(!$data || !is_array($data))
                    throw new \Exception("未指定数据或数据格式错误", 1);
                $opt = trim($this->GP("opt"));
                switch($opt)
                {
                    case 'addone':
                        //2019-01-27 修改 不直接使用数值
                        // $value = (float)$data['addone'];
                        // if($value < 0)
                        //     throw new \Exception("参数错误", 1);
                        // K::M('waimai/freightcalcconfig')->set_addone($value);

                        //2019-01-27 修改 使用新的参数格式
                        if(!K::M('waimai/freightcalcconfig')->set_addone($data))
                            throw new \Exception("写入配置数据失败", 1);
                        break;
                    case 'goods_addone':
                        if(!K::M('waimai/freightcalcconfig')->set_goods_addone($data))
                            throw new \Exception("写入配置数据失败", 1);
                        break;
                    default:
                        throw new \Exception("无此操作", 1);
                }
            }
            else
            {
                $shopId = (int)$this->GP('shop_id');
                if($shopId < 0)
                    throw new \Exception("未指定店铺", 1);
                
                $type = 0;
                if($shopId > 0)
                {
                    $shop = K::M('waimai/waimai')->detail($shopId);
                    if(!$shop)
                        throw new \Exception("找不到店铺数据", 1);
                    $type = (int)$shop['freight_calc_type'];
                    if($type <= 0)
                        throw new \Exception("该店铺不可设置配送费参数，<br>请修改运费计算类型后再执行操作", 1);
                }
                else
                {
                    $type = (int)$this->GP('type');
                    if($type >= 0)
                        throw new \Exception("未指定数据类型", 1);
                }

                $data = $this->GP("data");
                if(!$data || !is_array($data) || !K::M('waimai/freightcalcconfig')->check_data_format($type,$data))
                    throw new \Exception("未指定数据或数据格式错误", 1);
                
                $distKeys = [
                    'start_distance',
                    'distance_base',
                    'distance_range',
                    'in_distance_base',
                    'out_distance_base'
                ];
                foreach($data as $ck => &$cv)
                {
                    if(in_array($ck, $distKeys))
                        $data[$ck] = (int)((float)$cv*1000);
                }

                if(!K::M('waimai/freightcalcconfig')->save($shopId,$type,$data))
                    throw new \Exception("更新数据失败", 1);
            }
            



            /*2019-01-24 作废
            if(!$data || (!is_array($data) && $data !== "empty"))
                throw new \Exception("未指定数据或数据格式错误", 1);
            if($data === "empty")
                $data = [];
            $saveData = [];
            foreach($data as &$item)
            {
                if(!isset($item['distance']) || (!is_numeric($item['distance']) || !is_string($item['distance'])) ||
                    !isset($item['fee']) || (!is_numeric($item['fee']) || !is_string($item['fee']) || (float)$item['fee']<0)
                ) throw new \Exception("数据格式错误", 1);
                $distance = (int)((float)$item['distance']*1000);
                if($distance <= 0)
                    throw new \Exception("数据格式错误", 1);
                $saveData[$distance] = [
                    'distance' => $distance,
                    'fee' => (float)$item['fee']
                ];
                unset($item);
            }
            $saveData = array_merge($saveData);
            $type = 0;
            if($shopId > 0)
            {
                $shop = K::M('waimai/waimai')->detail($shopId);
                if(!$shop)
                    throw new \Exception("找不到店铺数据", 1);
                $type = (int)$shop['freight_calc_type'];
                if($type <= 0)
                    throw new \Exception("该店铺不可设置配送费参数，<br>请修改运费计算类型后再执行操作", 1);
            }
            else
            {
                $type = (int)$this->GP('type');
                if($type >= 0)
                    throw new \Exception("未指定数据类型", 1);
            }
            if(!K::M('waimai/freightcalcconfig')->save($shopId,$type,$saveData))
                throw new \Exception("更新数据失败", 1);
            */
        }catch(\Exception $e) { $errStr = $e->getMessage(); }
        if($errStr === "")
            $this->msgbox->add("操作成功",0);
        else
            $this->msgbox->add($errStr,1);
        $this->msgbox->json();
        exit;
    }

    //2019-01-21 新增，系统运费计算参数设置页面
    //2019-01-26备注：此接口原意思全称：freight calculate config manager（运费计算配置管理器），
    //               因管理需要，此接口名称不改，但作用是展示所有与商家费用计算相关的配置参数，
    //               这些参数也统一是文件格式进行保存，称为“费用计算参数配置管理”，
    //               比如今天（2019-01-26）添加的商品附加费这个参数，不能算运费，但属于商家相费用计算参数之一，属于虚拟商家的费用计算范畴。
    //               同理：模型文件/system/models/waimai/freightcalcconfig.mdl.php虽然名称不改，但作用已变。

    public function fcc_mgr()
    {
        /*2019-01-24 作废
        $_count = 0;
        //外卖参数列表
        $wmParams = K::M('waimai/freightcalcconfig')->items(['shop_id'=>0,'type'=>-1],'distance ASC',1,999999,$_count);
        //同城送参数列表
        $tcsParams = K::M('waimai/freightcalcconfig')->items(['shop_id'=>0,'type'=>-2],'distance ASC',1,999999,$_count);
        foreach($wmParams as $key => &$value)
        {
            $wmParams[$key]['distance'] = (float)$value['distance']/1000;
            $wmParams[$key]['fee'] = (float)$value['fee'];
            unset($value);
        }
        foreach($tcsParams as $key => &$value)
        {
            $tcsParams[$key]['distance'] = (float)$value['distance']/1000;
            $tcsParams[$key]['fee'] = (float)$value['fee'];
            unset($value);
        }
        */

        //2019-01-24 修改
        $wmParams = K::M('waimai/freightcalcconfig')->get_data(0,-1);
        $tcsParams = K::M('waimai/freightcalcconfig')->get_data(0,-2);
        if(!$wmParams)
        {
            $wmParams = [
                'start_distance' => 0,
                'start_fee' => 0,
                'distance_base' => 1,
                'fee_base' => 0,
            ];
        }
        else
        {
            $wmParams['start_distance'] = (float)$wmParams['start_distance']/1000;
            $wmParams['distance_base'] = (float)$wmParams['distance_base']/1000;
        }

        //2019-01-25 同城送也使用与外卖一样的参数及运算方式
        if(!$tcsParams)
        {
            $tcsParams = [
                'start_distance' => 0,
                'start_fee' => 0,
                'distance_base' => 1,
                'fee_base' => 0,
            ];
        }
        else
        {
            $tcsParams['start_distance'] = (float)$tcsParams['start_distance']/1000;
            $tcsParams['distance_base'] = (float)$tcsParams['distance_base']/1000;
        }
        //---以下暂不使用---
        // if(!$tcsParams)
        // {
        //     $tcsParams = [
        //         'distance_range' => 0,
        //         'in_distance_base' => 1,
        //         'in_fee_base' => 0,
        //         'out_distance_base' => 1,
        //         'out_fee_base' => 0,
        //     ];
        // }
        // else
        // {
        //     $tcsParams['distance_range'] = (float)$tcsParams['distance_range']/1000;
        //     $tcsParams['in_distance_base'] = (float)$tcsParams['in_distance_base']/1000;
        //     $tcsParams['out_distance_base'] = (float)$tcsParams['out_distance_base']/1000;
        // }
        //=================================

        $excRates = K::M('system/config')->get('exchange_rate');
        $fccAddone = K::M('waimai/freightcalcconfig')->get_addone_cfg();

        $this->pagedata['exc_retes'] = $excRates;
        $this->pagedata['fcc_addone'] = $fccAddone;
        
        //2019-01-26 添加 商品附加费数据
        $goodsAddone = K::M('waimai/freightcalcconfig')->get_goods_addone_cfg();
        $this->pagedata['goods_addone'] = $goodsAddone;

        $this->pagedata['wm_params'] = $wmParams;
        $this->pagedata['tcs_params'] = $tcsParams;
        $this->tmpl = 'admin:waimai/waimai/fcc_mgr.html';
    }
}