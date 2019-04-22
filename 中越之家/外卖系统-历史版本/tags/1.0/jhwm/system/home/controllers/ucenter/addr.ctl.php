<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

/*收货地址*/
class Ctl_Ucenter_Addr extends Ctl_Ucenter
{
	// 收货地址列表
    public function index($from,$cate_id)
    {
        $check = $this->GP('check');
        if($check){
            $this->pagedata['check'] = $check;
        }
        $order = $this->GP('order'); $shop_id = $this->GP('shop_id');
        if($order && $shop_id){
            $this->pagedata['order'] = $order;
            $this->pagedata['shop_id'] = $shop_id;
        }
        $filter = array();
        $filter['uid'] = $this->uid;
        $page= max(intval($page), 1);
        $count = 0;
        if($list = K::M('member/addr')->items($filter, array('addr_id'=>'desc'), $page, $limit, $count)){
            $addrs = array();
            foreach($list as $k=>$val){
                $addrs[$k] = $this->filter_fields('addr_id,contact,mobile,addr,type,is_default,house,lng,lat', $val);
            }
        }
        $this->pagedata['addrs'] = $addrs;
        if($from){
            $this->pagedata['from'] = $from;
        }
        if($cate_id){
            $this->pagedata['cate_id'] = $cate_id;
        }
    	$this->tmpl = "ucenter/addr/index.html";
    }

    public function create($from,$cate_id)
    {
        if(!empty($_POST)){
            if(!$contact = K::M('member/addr')->check_contact($this->GP('contact'))) {
                $this->msgbox->add('联系人长度为2至16位字符',210);
            }else if(!$mobile = K::M('verify/check')->mobile($this->GP('mobile'))){
                $this->msgbox->add('手机号码有误',211);
            }else if(!$house = $this->GP('house')) {
                $this->msgbox->add('收货地址不能为空',212);
            }else if(!$addr = $this->GP('addr')) {
                $this->msgbox->add('收货详细地址合法',213);
            }else if(!$this->GP('lng') || !$this->GP('lat')){
                $this->msgbox->add('经纬度有误',213);
            }else if($addr_count = K::M('member/addr')->count(array('uid'=>$this->uid)) >= 10){
                $this->msgbox->add('抱歉，每个用户最多只能新增10个地址！',214);
            }else{
                $data = array();
                $data['uid'] = $this->uid;
                $data['contact'] = $contact;
                $data['mobile'] = $mobile;
                $data['addr'] = $addr;
                $data['house'] = $house;
                $data['type'] = $this->GP('type') ? $this->GP('type') : '';
                $data['is_default'] = $this->GP('is_default') ? 1 : 0;
                $data['lng'] = $this->GP('lng');
                $data['lat'] = $this->GP('lat');
                if($addr_id = K::M('member/addr')->create($data)){

                    $this->msgbox->add('新增地址成功');
                    if($from){
                        $this->msgbox->set_data("forward", $this->mklink('ucenter/addr:index',array($from,$cate_id)));
                    }else{
                        $this->msgbox->set_data("forward", $this->mklink('ucenter/addr:index',null));
                    }
                }else {
                    $this->msgbox->add('新增地址失败',215);
                }
            }
        }else{
			if($addr = $this->GP('o_addr')){
                $this->pagedata['addr'] = $addr;
            }
            if($lat = $this->GP('o_lat')){
                $this->pagedata['lat'] = $lat;
            }
            if($lng = $this->GP('o_lng')){
                $this->pagedata['lng'] = $lng;
            }
            if($from){
                $this->pagedata['from'] = $from;
            }
            if($cate_id){
                $this->pagedata['cate_id'] = $cate_id;
            }
           $this->tmpl = 'ucenter/addr/create.html';
        }
    }
    /*编辑地址*/
    public function edit($addr_id)
    {
        if(!$addr_id){
            $this->msgbox->add('非法操作',210);
        }else if(!$deatil=K::M('member/addr')->detail($addr_id)){
            $this->msgbox->add('非法操作',211);
        }else if(!empty($_POST)){
            if(!$contact = K::M('member/addr')->check_contact($this->GP('contact'))) {
                $this->msgbox->add('联系人长度为2至16位字符',210);
            }else if(!$mobile = K::M('verify/check')->mobile($this->GP('mobile'))){
                $this->msgbox->add('手机号码有误',211);
            }else if(!$house = $this->GP('house')) {
                $this->msgbox->add('收货地址不能为空',212);
            }else if(!$addr = $this->GP('addr')) {
                $this->msgbox->add('收货详细地址合法',213);
            }else if(!$this->GP('lng') || !$this->GP('lat')){
                $this->msgbox->add('经纬度有误',213);
            }else{
                $data = array();
                $data['uid'] = $this->uid;
                $data['contact'] = $contact;
                $data['mobile'] = $mobile;
                $data['addr'] = $addr;
                $data['house'] = $house;
                $data['type'] = $this->GP('type') ? $this->GP('type') : '';
                $data['is_default'] = $this->GP('is_default') ? 1 : 0;
                $data['lng'] = $this->GP('lng');
                $data['lat'] = $this->GP('lat');
                if($addr_id = K::M('member/addr')->update($addr_id, $data)){
                    $this->msgbox->add('编辑地址成功');
                    $this->msgbox->set_data("forward", $this->mklink('ucenter/addr:index',null));
                }else {
                    $this->msgbox->add('编辑地址失败',215);
                }
            }
        }else{
            $this->pagedata['detail'] = $deatil;
            $this->tmpl = "ucenter/addr/edit.html";
        }
    }

    public function delete($addr_id)
    {
        if(!$detail = K::M('member/addr')->detail($addr_id)) {
           $this->msgbox->add("你要修改的地址不存在或已经删除".$addr_id,210);
        }else if($detail['uid'] != $this->uid){
            $this->msgbox->add("非法的数据请求",211);
        }else {
            if(K::M('member/addr')->delete($addr_id)){
                $this->msgbox->add('删除成功');
            }else {
                $this->msgbox->add('删除失败',212);
            }
        }
    }

    public function add_map($addr_id)
    {
        $location = array();
        $ip_location = $this->ip_location();
        $location['addr_id'] = $addr_id;
        if($addr_id) {
            if($addr = K::M('member/addr')->detail($addr_id)) {
                $location['lng'] = $addr['lng'];
                $location['lat'] = $addr['lat'];
            }
            $this->pagedata['addr_id'] = $addr_id;
        }

        $this->pagedata['location'] = $location;
    	$this->tmpl = "ucenter/addr/map.html";
    }

    public function set_default()
    {
        $addr_id = (int)$this->GP('addr_id');
        if(!$detail = K::M('member/addr')->detail($addr_id)) {
            $this->msgbox->add('你要设置的地址不存在',210);
        }else if($detail['uid'] != $this->uid) {
            $this->msgbox->add("非法的数据请求",211);
        }else {
            if(K::M('member/addr')->set_default($this->uid,$addr_id)) {
                $this->msgbox->add('默认地址设置成功');
            }else {
                $this->msgbox->add('设置默认地址失败',212);
            }
        }
    }
    // 根据IP地址获取当前位置的经纬度
    public function ip_location()
    {
        $locaiton = array();
        // 获得会员最近一次登录的IP
        if($loginip = K::M('member/member')->detail($this->uid)) {
            $getIp = $loginip['loginip'];
        }
        $content = file_get_contents("http://api.map.baidu.com/location/ip?ak=7b92b3afff29988b6d4dbf9a00698ed8&ip={$getIp}&coor=bd09ll");
        if(empty($content)) {
            $this->msgbox->add('位置获取失败',212);
        }else {
            $json = json_decode($content);
            $location['lng']= $json->{'content'}->{'point'}->{'x'};
            $location['lat']= $json->{'content'}->{'point'}->{'y'};
            $location['city']= $json->{'content'}->{'address_detail'}->{'city'};
            return $location;
        }
    }
}
