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
class Ctl_Ucenter_Addr extends Ctl_Ucenter{
    //外卖地址首页
    public function index(){
        $list = $filter = $m_addr = array();
        $filter['uid'] = $this->MEMBER['uid'];
        $addr = K::M('member/addr')->items($filter);
        foreach ($addr as $k => $item){
            switch ($item['type']){
                case 1:
                    $item['title'] = '家';
                    $item['class'] = 'home';
                    break;
                case 2:
                    $item['title'] = '公司';
                    $item['class'] = 'company';
                    break;
                case 3:
                    $item['title'] = '学校';
                    $item['class'] = 'school';
                    break;
                case 4:
                    $item['title'] = '其他';
                    $item['class'] = 'else';
                    break;
                default:
                    $item['title'] = '其他';
                    $item['class'] = 'else';
                    break;
            }
            $list[]= $item;
        }
        $this->pagedata['addr'] = $list;
        $this->tmpl = 'ucenter/addr.html';
    }

    //新建外卖收货地址
    public function create(){

        if($this->checksubmit()){
            if(!$contact = K::M('member/addr')->check_contact($this->GP('contact'))) {
                $this->msgbox->add('联系人长度为2至16位字符',214);
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
            $this->tmpl = 'ucenter/addr_create.html';
        }
    }
    //删除外卖地址
    public function del($addr_id){

        if(!$addr_id){
            $this->msgbox->add('地址不存在！',210);
        }else if(!$addr = K::M('member/addr')->detail($addr_id)){
            $this->msgbox->add('地址不存在！',211);
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
    //地图添加地址
    public function add_map($addr_id)
    {
        $location = array();
        $location['addr_id'] = $addr_id;
        if($addr_id) {
            if($addr = K::M('member/addr')->detail($addr_id)) {
                $location['lng'] =$addr['lng'];
                $location['lat'] = $addr['lat'];
            }
            $this->pagedata['addr_id'] = $addr_id;
        }else {
            $location['lng'] =$this->request['UxLocation']['lng'];
            $location['lat'] = $this->request['UxLocation']['lat'];
        }
        $this->pagedata['map_key'] = MAP_KEY;
        $this->pagedata['location'] = $location;
        $this->tmpl = "ucenter/gaodemap.html";
    }
    //编辑地址
    public function edit($addr_id){
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

            $this->tmpl = "ucenter/addr_edit.html";
        }
        $this->msgbox->set_data('forward',$this->mklink('waimai/ucenter/addr:addrlist'));



    }



    
}