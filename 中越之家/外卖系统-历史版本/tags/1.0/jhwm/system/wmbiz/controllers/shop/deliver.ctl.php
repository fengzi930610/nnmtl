<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shop_Deliver extends Ctl
{
    protected $_allow_fields = 'area_1,area_2,area_3,area_4,area_5,area_6,area_7,area_8,area_9,area_10';

    public function index()
    {

        $points = array();
        if ($polygon_point = $this->waimai_shop['area_polygon']['polygon_point']) {
            foreach ($polygon_point as $k => $v) {
                foreach ($v as $kk => $vv) {
                    $points[] = array("lng" => $vv['lng'], "lat"=>$vv['lat']);
                }
            }
        }
        if($this->waimai_shop['pei_type']==1&&$this->waimai_shop['group_id']){
            $group = K::M('pei/group')->detail($this->waimai_shop['group_id']);
            $province = K::M('data/province')->detail($group['province_id']);
            $city = K::M('data/city')->detail($group['city_id']);
            $group['province'] = $province;
            $group['city'] = $city;
            if($this->waimai_shop['is_separate']==1){
                $group['min_amount'] =  $this->waimai_shop['min_amount'];
            }
            $this->pagedata['group'] = $group;
            $points = $group['polygon_point']?$group['polygon_point']:array();
            $this->pagedata['json_point']['area_1'] =  $points;
            $this->pagedata['group_point'] = json_encode($points);



        }
		$this->pagedata['detail'] = $this->waimai_shop;
        $this->pagedata['points'] = json_encode($points);
        $this->pagedata['shop_point'] = json_encode($this->waimai_shop['area_polygon']['polygon_point']);
       /* echo '<pre>';
        print_r( $this->waimai_shop['area_polygon']['polygon_point']);exit;*/
    	$this->tmpl = 'shop/deliver/gaode.html';
    }

    /*public function edit()
    {
    	if($data = $this->checksubmit('data')){
            if(!$data = $this->check_fields($data,'min_amount,fkm,fm')){
                $this->msgbox->add('非法的数据提交', 211);
            }elseif ($this->waimai_shop['pei_type'] != 0) {// 越权判断，商户只有自己配送类型才允许修改配置
            	$this->msgbox->add('当前配送设置不可编辑', 212);
            }elseif ($data['min_amount'] < 0) {
            	$this->msgbox->add('起送金额不能小于0', 213);
            }elseif (!isset($data['fkm']) || !$fkm_list = K::M('waimai/waimai')->check_deliver_fkm($data['fkm'])) {// 验证商家配送设置提交字段（公里数数组是否有空数据）
            	$this->msgbox->add('公里数不能为空', 214);
            }else{
            	$data['min_amount'] = (float)$min_amount;
            	$freight_stage = array();
            	foreach ($fkm_list as $k => $v) {
            		$freight_stage[$k]['fkm'] = $v;
            		$fm = (float) $data['fm'][$k];// 类型转换
            		$freight_stage[$k]['fm'] = !empty($fm) ? $fm : 0;
            	}
            	$data['freight_stage'] = !empty($freight_stage) ? serialize($freight_stage) : array();
                K::M('waimai/waimai')->update($this->shop_id, $data);
                K::M('waimai/waimai')->update_pei_distance($this->shop_id, $fkm_list);
                $this->msgbox->add('配送设置成功');
                $this->msgbox->set_data('forward', $this->mklink('shop/deliver:index'));
            }
        }else{
        	if ($this->waimai_shop['pei_type'] != 0) {
        		$this->msgbox->add('当前配送设置不可编辑', 211);
        	}else{
        		$this->pagedata['detail'] = $this->waimai_shop;
            	$this->tmpl = 'shop/deliver/edit.html';
        	}
        }
    }*/

    public function edit_save()
    {
        if ($_POST) {

            if ($this->waimai_shop['pei_type'] != 0) {// 0:自己送，1:跑腿送,  2:代购(仅仅外卖), 3:用户自提单
                $this->msgbox->add('当前的配送方式不允许设置配送区域',211);
            }elseif (!$this->GP('polygon_point') || !$this->GP('area_price')) {
                $this->msgbox->add('请添加一个配送模板',212);
            }elseif (!$polygon_point = $this->check_fields($this->GP('polygon_point'), $this->_allow_fields)) {
                $this->msgbox->add('非法的数据提交',213);
            }elseif (!$this->check_fields($this->GP('area_price'), $this->_allow_fields)) {
                $this->msgbox->add('非法的数据提交',214);
            }elseif (!$area_price = K::M('waimai/waimai')->check_field_shipping_fee($this->GP('area_price'))) {
                $this->msgbox->add('金额不能是负数',215);
            }else{

                $data['polygon_point'] = $polygon_point;
                $data['area_price'] = $area_price;
                $waimai = K::M('waimai/waimai')->detail($this->shop_id);
                //查询外卖的配送方式 分别处理
                if($waimai['pei_type']==0){
                    //自己送
                    K::M('waimai/waimai')->after_set_min_zero($this->shop_id,$area_price);
                }else{
                   //平台送
                    $fright_config = $this->system->config->get('fright');
                    $min_price_arr = array();
                    foreach($fright_config as $v){
                        $min_price_arr[] =  $v['fm'];
                    }
                    sort($min_price_arr);
                    foreach($data['area_price'] as $k=>$v){
                        $data['area_price'][$k]['shipping_fee'] = 0;
                    }
                    K::M('waimai/waimai')->after_set_min_zero($this->shop_id,$area_price,array('shipping_fee'=>$min_price_arr[0]));
                }
                $area_polygon = serialize($data);
                if (K::M('waimai/waimai')->update($this->shop_id, array('area_polygon'=>$area_polygon))) {
                    $this->msgbox->add('保存成功！');
                }else{
                    $this->msgbox->add('保存失败！');
                }
            }
        }
    }
}