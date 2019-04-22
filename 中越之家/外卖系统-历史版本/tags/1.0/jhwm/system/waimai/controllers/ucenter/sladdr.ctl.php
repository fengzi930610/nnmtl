<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 9:27
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Ucenter_Sladdr extends Ctl_Ucenter
{
    public function index($shop_id){
        $list = $filter = $m_addr = array();
        $filter['uid'] = $this->uid;
        // 根据用户取收货地址在商家配送范围的地址 add by zhuhongwei
        $detail = K::M('waimai/waimai')->detail((int)$shop_id);
        $addr_list = K::M('member/addr')->items(array('uid'=>$this->uid),null,1,50,$count_addr);//
        foreach($addr_list as $addr_k =>$addr_v){
            $addr_list[$addr_k]['juli'] = K::M('helper/round')->juli($detail['lng'],$detail['lat'],$addr_v['lng'],$addr_v['lat']);
            $addr_list[$addr_k]['is_in'] = 0;
        }

        if($detail['pei_type']==0){
            foreach($addr_list as $k=>$v){
                foreach ($detail['area_polygon']['polygon_point'] as $kkkk => $vvvv) {
                    if(K::M('helper/round')->in_or_out_polygon($vvvv, $v['lat'],$v['lng'])){
                        $addr_list[$k]['is_in'] = 1;
                    }
                }
            }
        }else{
            $group_detail = K::M('pei/group')->detail($detail['group_id']);
            foreach($addr_list as $k=>$v){
                if(K::M('helper/round')->in_or_out_polygon($group_detail['polygon_point'], $v['lat'],$v['lng'])){
                    $addr_list[$k]['is_in'] = 1;
                }
            }
        }

        uasort($addr_list, array($this, 'scope_sort'));

        foreach ($addr_list as $k => $item) {
            switch ($item['type']) {
                case 1:
                    $addr_list[$k]['title'] = '公司';
                    $addr_list[$k]['class'] = 'company';
                    break;
                case 2:
                    $addr_list[$k]['title'] = '家';
                    $addr_list[$k]['class'] = 'home';
                    break;
                case 3:
                    $addr_list[$k]['title'] = '学校';
                    $addr_list[$k]['class'] = 'school';
                    break;
                case 4:
                    $addr_list[$k]['title'] = '其他';
                    $addr_list[$k]['class'] = 'else';
                    break;
                default:
                    $addr_list[$k]['title'] = '其他';
                    $addr_list[$k]['class'] = 'else';
                    break;
            }
        }

        $this->pagedata['shop_id'] = $shop_id;
        $this->pagedata['addr'] = $addr_list;
        $this->tmpl = 'ucenter/sladdr.html';
    }

    protected function scope_sort($a, $b)
    {
        if ($a['is_in'] == $b['is_in']) {
            if ($a['juli'] == $b['juli']) {
                return 0;
            }else{
                return ($a['juli'] < $b['juli']) ? -1 : 1;
            }
        }
        return ($a['is_in'] > $b['is_in']) ? -1 : 1;
    }

    public function check_addr()
    {
        if ($_POST) {
            if (!$address = $this->GP('address')) {
                $this->msgbox->add('参数错误', 211);
            } elseif (!$shop_id = (int)$this->GP('shop_id')) {
                $this->msgbox->add('商家不存在', 212);
            } else if (!$detail = K::M('waimai/waimai')->detail($shop_id)) {
                $this->msgbox->add('商家不存在', 213);
            } else {
                if ($detail['pei_type'] == 0) {
                    if (!$area_price = K::M('waimai/waimai')->get_shipping_fee($detail['area_polygon'], $address['lat'], $address['lng'])) {
                        $this->msgbox->add('不在商家配送范围', 224);
                    }
                } else {
                    if (!$group_detail = K::M('pei/group')->detail($detail['group_id'])) {
                        $this->msgbox->add('不在配送范围', 225);
                    } else if (!K::M('helper/round')->in_or_out_polygon($group_detail['polygon_point'], $address['lat'], $address['lng'])) {
                        $this->msgbox->add('不在配送范围', 226);
                    }
                }
            }
        }
    }

    //新建外卖收货地址
    public function create($shop_id){
        if($this->checksubmit()){
            if(!$contact = K::M('member/addr')->check_contact($this->GP('contact'))) {
                $this->msgbox->add('联系人长度为2至16位字符!',214);
            }else if(!$mobile = K::M('verify/check')->mobile($this->GP('mobile'))){
                $this->msgbox->add('手机号码有误',215);
            }else if(!$house = $this->GP('house')) {
                $this->msgbox->add('详细地址不能为空',216);
            }else if(!$addr = $this->GP('addr')) {
                $this->msgbox->add('收货地址不合法',217);
            }else if(!$this->GP('lng') || !$this->GP('lat')){
                $this->msgbox->add('经纬度有误',218);
            }else if($addr_count = K::M('member/addr')->count(array('uid'=>$this->uid)) >= 10){
                $this->msgbox->add('抱歉，每个用户最多只能新增10个地址！',219);
            }else {
                $data = array();
                $data['uid'] = $this->uid;
                $data['contact'] = $contact;
                $data['mobile'] = $mobile;
                $data['addr'] = $addr;
                $data['house'] = $house;
                $data['type'] = $this->GP('type') ? $this->GP('type') : 4;
                $data['is_default'] = $this->GP('is_default') ? 1 : 0;
                $data['lng'] = $this->GP('lng');
                $data['lat'] = $this->GP('lat');
                if (!$addr_id = K::M('member/addr')->create($data)) {
                    $this->msgbox->add('添加地址错误',220);
                }
            }
        }else {
            if($addr = $this->GP('o_addr')){
                $this->pagedata['addr'] = $addr;
            }
            if($lat = $this->GP('o_lat')){
                $this->pagedata['lat'] = $lat;
            }
            if($lng = $this->GP('o_lng')){
                $this->pagedata['lng'] = $lng;
            }
            $this->pagedata['shop_id'] = (int)$shop_id;
            $this->tmpl = 'ucenter/sladdr_create.html';
        }
    }
    //删除外卖地址
    public function del($addr_id){
        if(!$addr_id){
            $this->msgbox->add('地址不存在！！',210);
        }else if(!$addr = K::M('member/addr')->detail($addr_id)){
            $this->msgbox->add('地址不存在！！！',211);
        }else if($this->uid != $addr['uid']){
            $this->msgbox->add('非法数据请求！',212);
        }else {
            if(!$re=K::M('member/addr')->delete($addr_id)){
                $this->msgbox->add('删除失败',213);
            }
        }
        $this->msgbox->set_data('data',$re);
        $this->msgbox->json();
    }

    //编辑地址
    public function edit($addr_id, $shop_id){
        if(!$addr_id){
            $this->msgbox->add('非法操作',221);
        }else if(!$deatil=K::M('member/addr')->detail($addr_id)){
            $this->msgbox->add('非法操作',222);
        }else if($this->checksubmit()){
            if(!$contact = K::M('member/addr')->check_contact($this->GP('contact'))) {
                $this->msgbox->add('联系人长度为2至16位字符',223);
            }else if(!$mobile = K::M('verify/check')->mobile($this->GP('mobile'))){
                $this->msgbox->add('手机号码有误',224);
            }else if(!$house = $this->GP('house')) {
                $this->msgbox->add('详细地址不能为空',225);
            }else if(!$addr = $this->GP('addr')) {
                $this->msgbox->add('收货地址不合法',226);
            }else if(!$this->GP('lng') || !$this->GP('lat')){
                $this->msgbox->add('经纬度有误',227);
            }else{
                $data = array();
                $data['uid'] = $this->uid;
                $data['contact'] = $contact;
                $data['mobile'] = $mobile;
                $data['addr'] = $addr;
                $data['house'] = $house;
                $data['type'] = $this->GP('type') ? $this->GP('type') : 4;
                $data['is_default'] = $this->GP('is_default') ? 1 : 0;
                $data['lng'] = $this->GP('lng');
                $data['lat'] = $this->GP('lat');
                if(!$addr_id = K::M('member/addr')->update($addr_id, $data)){
                    $this->msgbox->add('编辑失败',228);
                }
            }
        }else{
            if($addr = $this->GP('o_addr')){
                $deatil['addr'] = $addr;
            }
            if($lat = $this->GP('o_lat')){
                $deatil['lat'] = $lat;
            }
            if($lng = $this->GP('o_lng')){
                $deatil['lng'] = $lng;
            }
            $this->pagedata['addr'] = $deatil;
            $this->pagedata['shop_id'] = (int)$shop_id;
            $this->tmpl = "ucenter/sladdr_edit.html";
        }
    }



    // 距离排序辅助uasort()
    public function juli_order($a, $b)
    {
        if ($a['juli'] == $b['juli']) {
            return 0;
        }else{
            return ($a['juli'] < $b['juli']) ? -1 : 1;
        }

    }

}