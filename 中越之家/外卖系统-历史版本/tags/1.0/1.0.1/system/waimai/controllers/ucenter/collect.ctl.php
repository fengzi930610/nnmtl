<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 16:21
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Ucenter_Collect extends Ctl_Ucenter {
    //收藏首页
    public function index(){
        $this->pagedata['uid'] = $this->MEMBER['uid'];
        $this->tmpl='waimai/collect/index.html';
        
    }
    //加载个人收藏
    public function loadcollect($page=1){
        $filter = array();
        $filter['uid'] = $this->MEMBER['uid'];
        $filter['type'] = 'waimai';
        $filter['status'] = 1;
        //所有评论
        $collect = K::M('member/collect')->items($filter,array('collect_id'=>'desc'),$page,10,$count);
        $shop_id = array();
        foreach ($collect as $v){
            $shop_id[] = $v['can_id'];
        }
        //商家列表
        $shop_list = K::M('waimai/waimai')->items_by_ids($shop_id);

        $round  = K::M('helper/round');
        $format = K::M('helper/format');
        $lng = $this->request['UxLocation']['lng'];
        $lat = $this->request['UxLocation']['lat'];
        if(!$lng||!$lat){
            $lng = trim($_COOKIE['lng']);
            $lat = trim($_COOKIE['lat']);
        }


        $shop_format = array();
        //格式化商家信息
        foreach ($shop_list as  $list){
            $list['juli'] = $round->juli($list['lng'],$list['lat'],$lng,$lat);
            $list['jili_label'] =$format->juli( $list['juli']);
            $list['score'] = ($list['score']/$list['comments']) ? round($list['score']/$list['comments'],1) : 0 ;
            $shop_format[] =  $list;
        }
        if($count < 10){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $group_ids = array();
        foreach ($shop_list as $kk=>$vv){
            $group_ids[$vv['group_id']] = $vv['group_id'];
        }
        $group_list = K::M('pei/group')->items_by_ids($group_ids);
        foreach ($shop_list as $kkk=>$vvv){
            $shop_list[$kkk]['group'] = $group_list[$vvv['group_id']]?$group_list[$vvv['group_id']]:array();
        }

        foreach ($shop_format as $k=>$v){
            if($v['pei_type']==0){
                $area_price = K::M('waimai/waimai')->get_shipping_fee($v['area_polygon'], $lat, $lng);
                $shop_format[$k]['freight'] = $area_price['shipping_fee'];// 兼容旧版，重新赋值配送费
                $shop_format[$k]['min_amount'] = $area_price['min_price'];// 兼容旧版，重新赋值起送价
            }else{
                if($v['is_separate']==1){
                    $shop_format[$k]['min_amount'] = $v['min_amount'];
                }

                if($v['is_separate']==1&&$v['config']){
                    $shop_format[$k]['freight'] = K::M('waimai/waimai')->shipping_fee_by_type($v['config'],$v['juli']);
                }else{
                    $shop_format[$k]['freight'] = K::M('waimai/waimai')->shipping_fee_by_type(K::M('waimai/config')->getfright($v['group_id']),$v['juli']);
                }
            }
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['collect'] = $shop_format;
        $this->tmpl = 'waimai/collect/loadcollect.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html',$html);
        $this->msgbox->json();
    }

    // 收藏
    public function collect($status, $type, $can_id)
    {
        $data = array();
        $type = $type;
        $status = (int) $status;
        $can_id = (int) $can_id;
        $detail = K::M('member/collect')->find(array('uid' => $this->uid, 'can_id' => $can_id, 'type' => $type));
        if($detail){
            if(K::M('member/collect')->update($detail['collect_id'], array('status' => $status, 'dateline' => __TIME))){
                if($status == 0){
                    $this->msgbox->add('取消收藏成功');
                }
                else{
                    $this->msgbox->add('收藏成功');
                }
            }
        } else{
            if($collect_id = K::M('member/collect')->create(array('uid' => $this->uid, 'type' => $type, 'can_id' => $can_id, 'status' => 1, 'dateline' => __TIME))){
                $this->msgbox->add('恭喜您，收藏成功');
            }
        }
    }







}