<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28
 * Time: 10:48
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Staff extends Ctl {

    public function detail($staff_id){
        $this->check_login();
        if(!$staff_id){
          $this->msgbox->add('配送员不存在',201);
        }else if(!$staff  = K::M('staff/staff')->detail($staff_id)){
            $this->msgbox->add('配送员不存在',202);
        }else{
            $all_distance = K::M('staff/distance')->sum(array('staff_id'=>$staff_id),'distance');
            $today_distance = K::M('staff/distance')->find(array('staff_id'=>$staff_id,'day'=>date('Ymd')));
            $all_pei_day = K::M('staff/distance')->count(array('staff_id'=>$staff_id));
            $all_order = K::M('order/order')->count(array('staff_id'=>$staff_id,'from'=>array('paotui','waimai'),'order_status'=>array(4,8)));
            $avg_distance = number_format($all_distance/$all_pei_day,1,".",'');
            $avg_order = number_format($all_order/$all_pei_day,1,'.','');
            $comment_count = K::M('staff/comment')->count(array('staff_id'=>$staff_id));
            $comment_score = K::M('staff/comment')->sum(array('staff_id'=>$staff_id),'score');
            $avg_score = number_format($comment_score/$comment_count, 1, '.', '');
            if($avg_score>5){
                $avg_score = 5.0;
            }
            if($avg_score<0){
                $avg_score = 1.0;
            }
            $data_comment = array(
                '5'=>K::M('staff/comment')->count(array('staff_id'=>$staff_id,'score'=>5)),
                '4'=>K::M('staff/comment')->count(array('staff_id'=>$staff_id,'score'=>4)),
                '3'=>K::M('staff/comment')->count(array('staff_id'=>$staff_id,'score'=>3)),
                '2'=>K::M('staff/comment')->count(array('staff_id'=>$staff_id,'score'=>2)),
                '1'=>K::M('staff/comment')->count(array('staff_id'=>$staff_id,'score'=>1)),
            );
            $this->pagedata['in_app_client'] = IN_APP_CLIENT;
            $this->pagedata['comment'] = $data_comment;
            $this->pagedata['data'] =array(
                'all_diatance'=>$all_distance>0?number_format($all_distance/1000,2):0.00,
                'today_distance'=>$today_distance['distance']>0?number_format($today_distance['distance']/1000,2):0.00,
                'all_order'=>$all_order,
                'avg_order'=>$avg_order,
                'avg_distance'=>$avg_distance>0?number_format($avg_distance/1000,2):0.00,
                'score'=>$avg_score,
                'staff'=>$staff,
                'count_reward'=>K::M('order/order')->count(array('staff_id'=>$staff_id,'pay_status'=>1,'order_status'=>8,'from'=>'reward'))
            );
            $this->pagedata['order_id'] = $this->GP('order_id')?$this->GP('order_id'):0;
            $this->tmpl = "staff/index.html";

        }

    }

    public function ordercreate(){
        $this->check_login();
        if($data = $this->checksubmit('data')){
           if((float)$data['money']<0){
               $this->msgbox->add('打赏金额不正确',201);
           }else if(!$staff_id = $data['staff_id']){
               $this->msgbox->add('未指定需要打赏的配送员',202);
           }else if(!$staff = K::M('staff/staff')->detail($staff_id)){
               $this->msgbox->add('指定打赏的配送员不存在',203);
           }else{
               $data_order = array();
               $data_order['uid'] = $this->uid;
               $data_order['from'] = 'reward';
               $data_order['online_pay'] = 1;
               $data_order['amount'] = $data['money'];
               $data_order['day'] = date('Ymd');
               $data_order['staff_id'] = $data['staff_id'];
               if($order_id = K::M('order/order')->create($data_order)){
                   $this->msgbox->set('data',array('order_id'=>$order_id,'amount'=> $data['money']));
               }else{
                   $this->msgbox->add('出错了，请稍后再试',204);
               }
              // $data['staff_id'] =
            //   uid from  online_pay
           }
        }
    }

    public function reward($staff_id){
        $this->pagedata['staff_id'] = $staff_id;
        $this->pagedata['order_id'] = $this->GP('order_id')?$this->GP('order_id'):0;
        $this->pagedata['in_app_client'] = $this->GP('in_app_client') ? $this->GP('in_app_client') : false;
        $this->tmpl = "staff/reward.html";
    }

    public function load_reward($page,$staff_id){
        $page = max((int)$page,1);
        $limit = 10;
        if($items = K::M('order/order')->items(array('staff_id'=>$staff_id,'from'=>'reward','order_status'=>8,'pay_status'=>1),array('order_id'=>'DESC'),$page,$limit,$count)){
            $uids = array();
            foreach ($items as $k=>$v){
                $uids[$v['uid']] = $v['uid'];
            }
            $member_list = K::M('member/member')->items_by_ids($uids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['member'] = $member_list[$vv['uid']];
            }
        }
        if($count <= $limit){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['items'] = $items;
        $this->tmpl = 'staff/load_reward.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();

    }












}