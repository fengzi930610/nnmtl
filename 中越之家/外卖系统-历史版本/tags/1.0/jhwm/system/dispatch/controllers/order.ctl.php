<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/3/1
 * Time: 14:42
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Order extends Ctl {

    //已完成订单
    public function complete($page=1)
    {
        $page = max((int)$page,1);
        $limit = 20;

        $filter = array();
        $filter['group_id'] =$this->group_id;
        $filter['from'] = array('waimai','paotui');        
        $filter['order_status'] = array(4,8);
        
        $staff_ids = $shop_ids = array();
        if($SO = $this->GP('SO')){
            $this->pagedata['SO'] = $SO;
            if($SO['stime']&&$SO['ltime']){
                $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }else if(!$SO['stime']&&$SO['ltime']){
                $filter['dateline'] = '<:'.(strtotime($SO['ltime'])+86399);
            }else if($SO['stime']&&!$SO['ltime']){
                $filter['dateline'] = '>:'.strtotime($SO['stime']);
            }
            if($order_id = (int)$SO['order_id']){
                $filter['order_id'] = $order_id;
            }
            if($SO['title']){
                $waimai_count = K::M('waimai/waimai')->count(array('group_id'=>$this->group_id));
                $staff_count = K::M('staff/staff')->count(array('group_id'=>$this->group_id));
                $filter_staff = $filter_shop = array();
                $filter_staff[':OR'] = array(
                    'name'=>"LIKE:%".$SO['title'].'%',
                    'mobile'=>"LIKE:%".$SO['title'].'%'
                );
                $filter_shop[':OR'] = array(
                    'title'=>"LIKE:%".$SO['title'].'%',
                    'phone'=>"LIKE:%".$SO['title'].'%'
                );
                if($staff_list = K::M('staff/staff')->items($filter_staff,array('staff_id'=>'DESC'),1,$staff_count)){
                   foreach ($staff_list as $k=>$v){
                       $staff_ids[$v['staff_id']] = $v['staff_id'];
                   }
                }
                if($waimai_list = K::M('waimai/waimai')->items($filter_shop,array('shop_id'=>'DESC'),1,$waimai_count)){
                    foreach ($waimai_list as $k1=>$v1){
                        $shop_ids[$v1['shop_id']] = $v1['shop_id'];
                    }
                }
            }
        }

        if($SO['title'] && empty($shop_ids) && empty($staff_ids)){
            $items = array();
        }else{
            if(empty($shop_ids) && !empty($staff_ids)){
                $filter['staff_id'] = $staff_ids;                
            }else if(!empty($shop_ids) && empty($staff_ids)){
                $filter['shop_id'] = $shop_ids;
            }else if(!empty($shop_ids) && !empty($staff_ids)){
                //$filter[':OR'] = array('shop_id'=>$shop_ids,'staff_id'=>$staff_ids);
                $shop_ids_str = implode(',',$shop_ids);
                $staff_ids_str = implode(',',$staff_ids);
                $filter[':SQL'] = " ((`shop_id` IN (".$shop_ids_str.")) OR (`staff_id` IN (".$staff_ids_str.")))";
            }

            if($items = K::M('order/order')->items($filter,array('order_id'=>'DESC'),$page,$limit,$count)){
                $order_shop_ids = $order_staff_ids = $order_uids =$order_ids =  array();
                foreach ($items as $kk=>$vv){
                    $order_uids[$vv['uid']] = $vv['uid'];
                    $order_staff_ids[$vv['staff_id']] = $vv['staff_id'];
                    if($vv['shop_id']){
                        $order_shop_ids[$vv['shop_id']] = $vv['shop_id'];
                    }
                    $order_ids[$vv['order_id']] = $vv['order_id'];
                }

                $order_member_list = K::M('member/member')->items_by_ids($order_uids);
                $order_waimai_list = K::M('waimai/waimai')->items_by_ids($order_shop_ids);
                $order_staff_list  = K::M('staff/staff')->items_by_ids($order_staff_ids);
                //$order_time_list = K::M('order/time')->items_by_ids($order_ids);
                $order_time_out_list = K::M('staff/timeoutorder')->items(array('order_id'=>$order_ids));

                foreach ($items as  $k2=>$v2){
                    $items[$k2]['is_out'] = 0;
                    $items[$k2]['time_out_label'] = "未超时";
                    $items[$k2]['member'] = $order_member_list[$v2['uid']]?$order_member_list[$v2['uid']]:array();
                    $items[$k2]['waimai'] = $order_waimai_list[$v2['shop_id']]?$order_waimai_list[$v2['shop_id']]:array();
                    $items[$k2]['staff'] = $order_staff_list[$v2['staff_id']]?$order_staff_list[$v2['staff_id']]:array();
                    //$items[$k2]['time'] = $order_time_list[$v2['order_id']]?$order_time_list[$v2['order_id']]:array();
                    foreach ($order_time_out_list as $k3=>$v3){
                        if($v3['order_id']==$v2['order_id']){
                            $items[$k2]['is_out'] = 1;
                            $items[$k2]['time_out_label'] = K::M('helper/format')->format_Time($v3['complete_time']-$v3['timeout']);
                        }
                    }
                }
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('order/complete', array('{page}')), array('SO'=>$SO));
            }
        }
        
        //echo '<pre>';print_r($this->system->db->SQLLOG());die;
        
        $this->today_order();
        $this->common_data();

        if(!empty($SO) && is_array($SO)){
            $params = http_build_query(array("SO"=>$SO));
            $params = '?'.$params;
        }else{
            $params =  '';
        }
        $this->pagedata['query'] = $params;

        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;

        $this->tmpl = 'order/complete.html';

    }

    public function today_order()
    {
        $today_stime = strtotime(date('Y-m-d'));
        $filter_today = array();
        $filter_today['group_id'] =$this->group_id;
        $filter_today['from'] = array('waimai','paotui');
        $filter_today['order_status'] = array(4,8);
        $filter_today['dateline'] =$today_stime.'~'.($today_stime+86399);
        $today_count = K::M('order/order')->count($filter_today);
        $this->pagedata['today_count'] = $today_count;
    }

    public function common_data(){

        //所有的已完成的
        $filter_complete = array();
        $filter_complete['group_id'] = $this->group_id;
        $filter_complete['from'] = array('waimai','paotui');
        $filter_complete['order_status'] =array(4,8);
        $count_complete = K::M('order/order')->count($filter_complete);

        //配送中的
        $filter_pei = array();
        $filter_pei['group_id'] = $this->group_id;
        $filter_pei['staff_id'] = ">:0";
        $filter_pei[':SQL'] = "((`from`='waimai' AND (`pei_type`=1 AND `order_status` IN (2,3))) OR (`from`='paotui' AND `order_status` IN (1,3)))";
        $count_pei = K::M('order/order')->count($filter_pei);

        /*$filter_qu = $filter_song = array();
        $filter_qu['group_id'] = $this->group_id;
        $filter_qu['staff_id'] = ">:0";
        $filter_qu[':SQL'] = "((`from`='waimai' AND (`pei_type`=1 AND `order_status`=2)) OR (`from`='paotui' AND `order_status`=1))";

        $filter_song['group_id'] = $this->group_id;
        $filter_song['staff_id'] = '>:0';
        $filter_song['from'] = array('waimai','paotui');
        $filter_song['order_status'] = 3;

        $count_qu = K::M('order/order')->count($filter_qu);
        $count_song = K::M('order/order')->count($filter_song);*/

        //等待指派的
        $filter = array();
        $filter['group_id'] = $this->group_id;;
        $filter[':SQL'] = "((`from`='waimai' AND ((`pei_type`=1 AND `order_status`=2) OR (`order_status`=0 AND `pei_type`=2))) OR (`from`='paotui' AND `order_status`=0))";
        $filter['tmp_ltime'] = "<:".__TIME;
        $filter[':OR'] = array(
            'pay_status'=>1,
            'online_pay'=>0
        );
        $filter['refund_status'] = '<=:0';
        $filter['staff_id'] = 0;    // 0等待接单
        $count_pai = K::M('order/order')->count($filter);

        //异常订单
       /* $filter_yichang = array();
        $filter_yichang['group_id'] = $this->group_id;
        $filter_yichang['staff_id'] = '>:0';
        $filter_yichang[':OR'] = array(
            'order_status'=>-2,
            'refund_status'=>array(1,2,3)
        );
        $filter_yichang['order_status'] = "<:8";*/

        $filter_yichang = array();
        $filter_yichang['group_id'] =$this->group_id;
        $filter_yichang['from'] = array('waimai');
        $filter_yichang['staff_id'] = '>:0';
        $filter_yichang[':OR'] = array(
            'order_status'=>-2,
            'refund_status'=>array(1,2,3)
        );


        $count_yichang = K::M('order/order')->count($filter_yichang);

        $this->pagedata['count'] = array(
            'complete'=>$count_complete,
            'pei'=>$count_pei,
            'pai'=>$count_pai,
            'yichang'=>$count_yichang
        );
    }

    //@parmas
    //type 1:全部  2:待取餐 3:待送达

    public function waitorder($page=1,$type=1)
    {
        $page = max((int)$page,1);
        $limit = 20;

        $filter = array();
        $filter['group_id'] = $this->group_id;
        $filter['staff_id'] = ">:0";
        $filter_song['from'] = array('waimai','paotui');
        if($type==1){
            $filter['order_status'] = array(1,2,3);
            //$filter['from'] = array('waimai','paotui');
            $filter[':SQL'] = "((`from`='waimai' AND (`pei_type`=1 AND `order_status` IN (2,3))) OR (`from`='paotui' AND `order_status` IN (1,3)))";
        }else if($type==2){
            $filter['staff_id'] = '>:0';
            $filter[':SQL'] = "((`from`='waimai' AND (`pei_type`=1 AND `order_status`=2)) OR (`from`='paotui' AND `order_status`=1))";
        }else if($type==3){
            $filter['staff_id'] = '>:0';
            $filter['order_status'] = 3;
        }

        $staff_ids = $shop_ids = array();
        if($SO = $this->GP('SO')){
            $this->pagedata['SO'] = $SO;
            if($SO['stime']&&$SO['ltime']){
                $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }else if(!$SO['stime']&&$SO['ltime']){
                $filter['dateline'] = '<:'.(strtotime($SO['ltime'])+86399);
            }else if($SO['stime']&&!$SO['ltime']){
                $filter['dateline'] = '>:'.strtotime($SO['stime']);
            }
            if($order_id = (int)$SO['order_id']){
                $filter['order_id'] = $order_id;
            }
            if($SO['title']){
                $waimai_count = K::M('waimai/waimai')->count(array('group_id'=>$this->group_id));
                $staff_count = K::M('staff/staff')->count(array('group_id'=>$this->group_id));
                $filter_staff = $filter_shop = array();
                $filter_staff[':OR'] = array(
                    'name'=>"LIKE:%".$SO['title'].'%',
                    'mobile'=>"LIKE:%".$SO['title'].'%'
                );
                $filter_shop[':OR'] = array(
                    'title'=>"LIKE:%".$SO['title'].'%',
                    'phone'=>"LIKE:%".$SO['title'].'%'
                );
                if($staff_list = K::M('staff/staff')->items($filter_staff,array('staff_id'=>'DESC'),1,$staff_count)){
                    foreach ($staff_list as $k=>$v){
                        $staff_ids[$v['staff_id']] = $v['staff_id'];
                    }
                }
                if($waimai_list = K::M('waimai/waimai')->items($filter_shop,array('shop_id'=>'DESC'),1,$waimai_count)){
                    foreach ($waimai_list as $k1=>$v1){
                        $shop_ids[$v1['shop_id']] = $v1['shop_id'];
                    }
                }
            }
        }

        if($SO['title'] && empty($shop_ids) && empty($staff_ids)){
            $items = array();
        }else{
            if(empty($shop_ids) && !empty($staff_ids)){
                $filter['staff_id'] = $staff_ids;                
            }else if(!empty($shop_ids) && empty($staff_ids)){
                $filter['shop_id'] = $shop_ids;
            }else if(!empty($shop_ids) && !empty($staff_ids)){
                //$filter[':OR'] = array('shop_id'=>$shop_ids,'staff_id'=>$staff_ids);
                $shop_ids_str = implode(',',$shop_ids);
                $staff_ids_str = implode(',',$staff_ids);
                $filter[':SQL'] = " ((`shop_id` IN (".$shop_ids_str.")) OR (`staff_id` IN (".$staff_ids_str.")))";
            }
            if($items = K::M('order/order')->items($filter,array('order_id'=>'DESC'),$page,$limit,$count)){
                $order_shop_ids = $order_staff_ids = $order_uids =$order_ids =  array();
                foreach ($items as $kk=>$vv){
                    $order_uids[$vv['uid']] = $vv['uid'];
                    $order_staff_ids[$vv['staff_id']] = $vv['staff_id'];
                    if($vv['shop_id']){
                        $order_shop_ids[$vv['shop_id']] = $vv['shop_id'];
                    }
                    $order_ids[$vv['order_id']] = $vv['order_id'];
                }
                $order_member_list = K::M('member/member')->items_by_ids($order_uids);
                $order_waimai_list = K::M('waimai/waimai')->items_by_ids($order_shop_ids);
                $order_staff_list  = K::M('staff/staff')->items_by_ids($order_staff_ids);
                $order_time_list = K::M('order/time')->items_by_ids($order_ids);
                foreach ($items as  $k2=>$v2){
                    $items[$k2]['member'] = $order_member_list[$v2['uid']]?$order_member_list[$v2['uid']]:array();
                    $items[$k2]['waimai'] = $order_waimai_list[$v2['shop_id']]?$order_waimai_list[$v2['shop_id']]:array();
                    $items[$k2]['staff'] = $order_staff_list[$v2['staff_id']]?$order_staff_list[$v2['staff_id']]:array();
                    $items[$k2]['time'] = $order_time_list[$v2['order_id']]?$order_time_list[$v2['order_id']]:array();
                }
                foreach($items as $k3=>$v3){
                    $items[$k3]['is_out'] = 0;
                    if($v3['from']=='waimai'){
                        if($v3['expect_time']>0){
                            if($v3['expect_time']>__TIME){
                               $label = '距超时还剩'.K::M('helper/format')->format_Time($v3['expect_time'] - __TIME);
                            }else{
                                $items[$k3]['is_out'] = 1;
                                $label = '已超时'.K::M('helper/format')->format_Time(abs(__TIME - $v3['expect_time']));
                            }
                            $items[$k3]['expect_label'] = $label;

                        }else{
                           $items[$k3]['expect_label'] =  "--";
                        }
                    }else{
                        $items[$k3]['expect_label'] =  "--";
                    }

                }
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('order/waitorder', array('{page}',$type)), array('SO'=>$SO));
            }
        }

        if(!empty($SO) && is_array($SO)){
            $params = http_build_query(array("SO"=>$SO));
            $params = '?'.$params;
        }else{
            $params =  '';
        }

        $this->today_order();
        $this->common_data();

        $filter_qu = $filter_song = array();
        $filter_qu['group_id'] = $this->group_id;
        $filter_qu['staff_id'] = ">:0";
        $filter_qu[':SQL'] = "((`from`='waimai' AND (`pei_type`=1 AND `order_status`=2)) OR (`from`='paotui' AND `order_status`=1))";
        $filter_song['group_id'] = $this->group_id;
        $filter_song['staff_id'] = '>:0';
        $filter_song['from'] = array('waimai','paotui');
        $filter_song['order_status'] = 3;
        $count_qu = K::M('order/order')->count($filter_qu);
        $count_song = K::M('order/order')->count($filter_song);
        $this->pagedata['info'] = array(
            'qu'=>$count_qu,
            'song'=>$count_song
        );
        $this->pagedata['type'] = $type;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['query'] = $params;
        $this->pagedata['items'] = $items;
        $this->pagedata['time'] = __TIME;
        $this->tmpl = "order/waitorder.html";
    }

    public function paiorder()
    {
        $this->today_order();
        $this->common_data();
        $this->pagedata['detail'] = $this->group;
        $this->pagedata['polygon_point'] = $this->group['polygon_point']?json_encode($this->group['polygon_point']):json_encode(array());
        $this->pagedata['map_key'] = MAP_KEY;
        $this->pagedata['swoole'] = array(
            'is_swoole'=>IS_SWOOLE,
            'swoole_port'=>SWOOLE_PORT,
            'swoole_ip'=>SWOOLE_IP,
            'group_id'=>$this->group_id,
            'auto_shuaxin'=>AUTO_UPDATE
        );

        $this->tmpl = 'order/paiorder.html';
    }

    public function yichang($page=1)
    {
        $page = max((int)$page,1);
        $limit = 20;

        $filter = array();
        $filter['group_id'] =$this->group_id;
        $filter['from'] = array('waimai');
        $filter['staff_id'] = '>:0';       
        $filter[':OR'] = array(
            'order_status'=>-2,
            'refund_status'=>array(1,2,3)
        );
        
        $staff_ids = $shop_ids = array();
        if($SO = $this->GP('SO')){
            $this->pagedata['SO'] = $SO;
            if($SO['stime']&&$SO['ltime']){
                $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }else if(!$SO['stime']&&$SO['ltime']){
                $filter['dateline'] = '<:'.(strtotime($SO['ltime'])+86399);
            }else if($SO['stime']&&!$SO['ltime']){
                $filter['dateline'] = '>:'.strtotime($SO['stime']);
            }
            if($order_id = (int)$SO['order_id']){
                $filter['order_id'] = $order_id;
            }
            if($SO['title']){
                $waimai_count = K::M('waimai/waimai')->count(array('group_id'=>$this->group_id));
                $staff_count = K::M('staff/staff')->count(array('group_id'=>$this->group_id));
                $filter_staff = $filter_shop = array();
                $filter_staff[':OR'] = array(
                    'name'=>"LIKE:%".$SO['title'].'%',
                    'mobile'=>"LIKE:%".$SO['title'].'%'
                );
                $filter_shop[':OR'] = array(
                    'title'=>"LIKE:%".$SO['title'].'%',
                    'phone'=>"LIKE:%".$SO['title'].'%'
                );
                if($staff_list = K::M('staff/staff')->items($filter_staff,array('staff_id'=>'DESC'),1,$staff_count)){
                   foreach ($staff_list as $k=>$v){
                       $staff_ids[$v['staff_id']] = $v['staff_id'];
                   }
                }
                if($waimai_list = K::M('waimai/waimai')->items($filter_shop,array('shop_id'=>'DESC'),1,$waimai_count)){
                    foreach ($waimai_list as $k=>$v){
                        $shop_ids[$v['shop_id']] = $v['shop_id'];
                    }
                }
            }
        }

        if($SO['title'] && empty($shop_ids) && empty($staff_ids)){
            $items = array();
        }else{
            if(empty($shop_ids) && !empty($staff_ids)){
                $filter['staff_id'] = $staff_ids;                
            }else if(!empty($shop_ids) && empty($staff_ids)){
                $filter['shop_id'] = $shop_ids;
            }else if(!empty($shop_ids) && !empty($staff_ids)){
                //$filter[':OR'] = array('shop_id'=>$shop_ids,'staff_id'=>$staff_ids);
                $shop_ids_str = implode(',',$shop_ids);
                $staff_ids_str = implode(',',$staff_ids);
                $filter[':SQL'] = " ((`shop_id` IN (".$shop_ids_str.")) OR (`staff_id` IN (".$staff_ids_str.")))";
            }

            if($items = K::M('order/order')->items($filter,array('order_id'=>'DESC'),$page,$limit,$count)){
                $order_shop_ids = $order_staff_ids = $order_uids = $order_ids = array();
                foreach ($items as $k=>$v){
                    if($v['uid']){
                        $order_uids[$v['uid']] = $v['uid'];
                    }
                    if($v['staff_id']){
                        $order_staff_ids[$v['staff_id']] = $v['staff_id'];
                    }                   
                    if($v['shop_id']){
                        $order_shop_ids[$v['shop_id']] = $v['shop_id'];
                    }
                    $order_ids[$v['order_id']] = $v['order_id'];
                }

                $order_member_list = K::M('member/member')->items_by_ids($order_uids);
                $order_waimai_list = K::M('waimai/waimai')->items_by_ids($order_shop_ids);
                $order_staff_list  = K::M('staff/staff')->items_by_ids($order_staff_ids);
                $order_refund_list = K::M('waimai/order/refund')->items_by_ids($order_ids);

                foreach ($items as  $k=>$v){
                    $v['member'] = $order_member_list[$v['uid']] ? $order_member_list[$v['uid']] : array();
                    $v['waimai'] = $order_waimai_list[$v['shop_id']] ? $order_waimai_list[$v['shop_id']] : array();
                    $v['staff'] = $order_staff_list[$v['staff_id']] ? $order_staff_list[$v['staff_id']] : array();
                    $v['refund'] = $order_refund_list[$v['order_id']] ? $order_refund_list[$v['order_id']] : array();
                    $items[$k] = $v;
                }
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('order/yichang', array('{page}')), array('SO'=>$SO));
            }
        }
        $this->today_order();
        $this->common_data();
        
        if(!empty($SO) && is_array($SO)){
            $params = http_build_query(array("SO"=>$SO));
            $params = '?'.$params;
        }else{
            $params =  '';
        }
        $this->pagedata['query'] = $params;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = 'order/yichang.html';
    }

    public function detail($order_id=null)
    {
        if(!$order_id = (int)$order_id){
            $this->msgbox->add('参数有误！',211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已删除！',212);
        }else if($order['group_id'] != $this->group_id){
            $this->msgbox->add('您没有权限查看该订单',213);
        }else {
            if($order['o_lng']&&$order['o_lat']){
                $order['juli_label'] =K::M('helper/round')->juli_label($order['lng'],$order['lat'],$order['o_lng'],$order['o_lat']);
            }else{
                $order['juli_label'] ="就近购买";
            }

            $order['user'] = K::M('member/member')->detail($order['uid']);
            /*$order['logs'] = K::M('order/log')->items(array('order_id'=>$order_id),array('log_id'=>'DESC'));
            foreach ($order['logs'] as $log_k=>$log_v){
                if(!$log_v['log']){
                    unset($order['logs'][$log_k]);
                }
                $order['logs'][$log_k]['dateline'] = date('Y-m-d H:i:s',$log_v['dateline']);
            }*/
            
            $order_froms = array('weixin' => '微信', 'ios' => '苹果APP', 'android' => '安卓APP', 'wap' => 'wap端', 'www' => '网页端');
            $order['order_from'] = $order_froms[$order['order_from']];

            $payments = K::M('order/order')->get_payments();
            $order['pay_method'] = $payments[$order['pay_code']];

            $order['intro'] = $order['intro'] ? $order['intro'] : "---";
            $order['dateline'] = date('m-d H:i:s',$order['dateline']);

            if($order['staff_id']){
                $order['staff_info'] = K::M('staff/staff')->detail($order['staff_id']);
            }else{
                $order['staff_info'] = array();
            }

            $order['overtime_label'] = '未超时';
            $order['expect_time_label'] = '预计送达：----';
            $order['process'] = K::M('order/time')->detail($order_id);

            switch ($order['from']) {
                case 'waimai':
                    $w_order = K::M('waimai/order')->detail($order_id);
                    $detail = K::M('waimai/order')->get_label($order);
                    $detail['product_info'] = K::M('waimai/orderproduct')->items(array('order_id'=>$order['order_id']));
                    $detail['product_info'] = K::M('waimai/orderproduct')->get_basketProducts($detail['product_info']);  //4.0分篮处理
                    $detail['product_num'] = $w_order['product_number'] ? $w_order['product_number'] : 0;
                    $detail['shop_info'] = K::M('waimai/waimai')->detail($detail['shop_id']);

                    if($expect_time = $detail['expect_time']){
                        if(in_array($detail['order_status'],array(0,1,2,3))){
                            $t = ceil(($expect_time - __TIME)/60);
                            $label = '';
                            if($t == 0){
                                $t = -1;
                                $label = '已超时：  '.$t.'分钟';
                            }
                            if($t > 0){
                                $label = '距超时：  '.$t.'分钟';
                            }
                            if($t < 0){
                                $label = '已超时：  '.abs($t).'分钟';
                            }
                            $detail['overtime_label'] = $label; 
                        }else if(in_array($detail['order_status'],array(4,5,8))){
                            if($timeout = K::M('staff/timeoutorder')->find(array('order_id'=>$order_id))){
                                $difftime = ceil(($timeout['complete_time']-$timeout['timeout'])/60);
                                $detail['overtime_label'] = '超时'.$difftime.'分钟';
                            }
                        }                       
                        $detail['expect_time_label'] = '预计送达：'.date('Y-m-d H:i',$expect_time);
                    }                    
                    break;
                case 'paotui':
                    $p_order = K::M('paotui/order')->detail($order_id);                       
                    $detail = K::M('paotui/order')->get_msg($order);
                    $detail['from_type'] = $p_order['from'];
                    switch ($p_order['from']) {
                        case 'mai':
                            $detail['from_type_label'] = '帮我买';
                            break;
                        case 'song':
                            $detail['from_type_label'] = '帮我送';
                            break;
                        default:
                            $detail['from_type_label'] = '';
                            break;
                    }
                    $detail['product_info'] = array(
                        array(
                            'product_name'=>implode(' ',$p_order['product']),
                            'yuji_price'=>$p_order['yuji_price'],
                            'price'=>$p_order['price'],
                            'weight'=>$p_order['weight']
                            )
                        );
                    $detail['product_num'] = 1;
                    $detail['shop_info'] = array(
                        'title'=>$p_order['o_contact'] ? $p_order['o_contact'] : "----",
                        'addr'=>$p_order['o_addr'] ? $p_order['o_addr'] : "就近购买",
                        'phone'=>$p_order['o_mobile'] ? $p_order['o_mobile'] : "----"
                        );
                    $detail['pei_time_label'] = $order['pei_time'] ? date('Y-m-d H:i',$order['pei_time']) : "尽快送达";

                    $detail['expect_time_label'] = '期望送达：'.$detail['pei_time_label'];
                    
                    break;
                default:
                    $detail = array();
                    break;
            }
            $this->pagedata['order'] = $detail;
            //echo '<pre>';print_r($detail);die;
            $this->tmpl = 'order/detail.html';
        }        
    }

    public function juli_order($a,$b)
    {
        if ($a['juli'] == $b['juli']) {
            return 0;
        }else{
            return ($a['juli'] < $b['juli']) ? -1 : 1;
        }
    }

    public function get_lasttime(){
       $overtime = $this->group['overtime'] ? $this->group['overtime'] : 5;
       return $overtime*60;
    }

    public function gaipai($order_id=null)
    {
        if(!$order_id = (int)$order_id){
            $this->msgbox->add('参数有误！',211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已删除！',212);
        }else{
            $filter = $orderby = array();
            $filter['status'] = 1 ;
            $filter['from'] = 'paotui';
            $filter['group_id'] = $this->group_id;
            $filter['closed'] = 0;
            $filter['lastlogin'] = '>:0';

            $staff_ids = array();
            $staff_count = K::M('staff/staff')->count(array('group_id'=>$this->group_id));

            if($staff = K::M('staff/staff')->items($filter, $orderby, 1, $staff_count, $count)){
                if($order['staff_id']){
                    unset($staff[$order['staff_id']]);
                }                
                $staff_ids = array_keys($staff);                              
                //待取： 待送达：
                //-1:配送员弃单  0：未处理，1：已接单（跑腿订单已接单）  3：配送开始（跑腿服务中），4：配送完成（跑腿服务完成），8：订单完成
                $filter_no = $filter_yes = array('from'=>array('waimai','paotui'), 'staff_id'=>$staff_ids);
                $filter_no[':SQL'] = "((`from`='waimai' AND `order_status` in (2)) OR (`from`='paotui' AND `order_status` in (1)))";
                $filter_yes['order_status'] = 3;                
                $order_no = K::M('order/order')->items_group_by_staff($filter_no);//待取                
                $order_yes =  K::M('order/order')->items_group_by_staff($filter_yes);//待送

                foreach ($staff as $k=>$v){
                    $staff[$k]['juli'] = K::M('helper/round')->juli($order['o_lng'],$order['o_lat'],$v['lng'],$v['lat']);
                    $staff[$k]['juli_label'] = K::M('helper/round')->juli_label($order['lng'],$order['lat'],$v['lng'],$v['lat']);

                    $staff[$k]['dq_order'] = $order_yes[$v['staff_id']] ? $order_yes[$v['staff_id']]['orders'] : 0;
                    $staff[$k]['ds_order'] = $order_no[$v['staff_id']] ? $order_no[$v['staff_id']]['orders'] : 0;
                }
                uasort($staff, array($this, 'juli_order'));
            }else{
                $staff = array();
            }
            $this->pagedata['order_id'] = $order_id;
            $this->pagedata['levels'] = K::M('staff/level')->fetch_all();
            $this->pagedata['tj_items'] = array_slice($staff, 0, 5, true);
            $this->pagedata['items'] = $staff;
            $this->tmpl = 'order/gaipai.html';
        }
    }

    public function change_order_staff($order_id=null){
        if(!$order_id = (int)$order_id){
            $this->msgbox->add('参数有误！',211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在！',212);
        }else if($order['order_status'] == 4){
            $this->msgbox->add('该订单已完成配送',213);
        }else if($order['order_status'] == -1){
            $this->msgbox->add('该订单已取消',214);
        }else if($order['tmp_ltime'] !=0 && $order['tmp_ltime'] > __TIME){
            $this->msgbox->add('上次指派还未过期，请勿频繁指派',215);
        }else if($data = $this->checksubmit('data')){
            if(!$staff_id = (int)$data['staff_id']){
                $this->msgbox->add('配送员不存在！',216);
            }else if(!$staff = K::M('staff/staff')->detail($staff_id)){
                $this->msgbox->add('配送员不存在',217);
            }else if($staff['status'] == 0){
                $this->msgbox->add('该配送员休息中，不可改派！',218);
            }else if($staff_id == $order['staff_id']){
                $this->msgbox->add('不可指定相同的配送员！',219);
            }else if($staff['group_id'] != $this->group_id){
               $this->msgbox->add("指定的配送员不属于您的配送站！",220);
           }else{
                $type = $data['type'];
                $old_staff_id = $order['staff_id'];
                $old_staff_info  = K::M('staff/staff')->detail($order['staff_id']);
                //派单先把订单的staff_id 滞空 然后按照派单流程走
                if($order['from'] == 'waimai'){
                    $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
                    $order_status = 2;
                    $addr = $waimai['addr'];
                    if($type==1){
                        $true_staff_id = $staff_id;
                        $jd_time = __TIME;
                    }else{
                        $true_staff_id = 0;
                        $jd_time = 0;
                    }
                }else{
                    $paotui_order = K::M('paotui/order')->detail($order_id);
                    $addr = $paotui_order['o_addr'] ? $paotui_order['o_addr'] : "就近购买";
                    if($type==1){
                        $true_staff_id = $staff_id;
                        $order_status = 1;
                        $jd_time = __TIME;
                    }else{
                        $true_staff_id = 0;
                        $order_status = 0;
                        $jd_time = 0;
                    }
                }
                $last_time = __TIME+$this->get_lasttime();
                $up_data = array(
                    'staff_id'=>$true_staff_id,
                    'tmp_staff_id'=>$staff_id,
                    'tmp_ltime'=>$last_time,
                    'order_status'=>$order_status,
                    'jd_time'=>$jd_time
                    );
                if(K::M('order/order')->update($order_id,$up_data)){
                    $data_log = array();
                    $data_log['order_id'] = $order_id;
                    $data_log['status']  = 0;
                    $data_log['log'] = '订单ID('.$order_id.')从配送员'.$old_staff_info['name'].'ID('.$old_staff_id.')'.'调度到配送员'.$staff['name'].'ID('.$staff_id.')';
                    K::M('order/log')->create($data_log);

                    if($order['from'] == 'paotui' && ($other_order = K::M('other/order')->find(array('p_order_id'=>$order_id)))){
                        K::M('order/order')->update($other_order['order_id'],array('staff_id'=>$staff_id));
                        $data_log['order_id'] = $other_order['order_id'];
                        K::M('order/log')->create($data_log);
                    }

                    //老配送员--推送消息
                    $intro = '';
                    $liuyan = trim($data['message']);
                    if($liuyan){
                        $text = '取货地址:'.$addr.'备注:('.$liuyan.')';
                        $text1 = '您的订单ID('.$order_id.')已被调度,请注意处理。备注:('.$liuyan.')';
                        $intro = $liuyan;
                    }else{
                        $text = '取货地址:'.$addr;
                        $text1 = '您的订单ID('.$order_id.')已被调度,请注意处理';
                    }
                    if($old_staff_info){
                        K::M('staff/staff')->send($old_staff_id,'订单调度',$text1,array('type'=>'cancelOrder','order_id'=>$order_id));
                    }

                    if($type == 1){
                        K::M('staff/staff')->send($staff_id,'系统派单',$text,array('type'=>'newOrder','order_id'=>$order_id));
                        $accept = 1;
                    }else{
                        K::M('staff/staff')->send($staff_id,'系统派单',$text,array('type'=>'paiOrder','order_id'=>$order_id));
                        $accept = 0;
                    }
                    
                    $tongji_data = array(
                        'pai'=>1,
                        'accept'=>$accept,
                        'refuse'=>0,
                        'staff_id'=>$staff_id,
                    );
                    $paidan_log = array(
                        'order_id'=>$order_id,
                        'intro'=>$intro
                    );
                    K::M('staff/paidanlog')->create($paidan_log);
                    K::M('staff/paidan')->create($tongji_data);
                    $this->msgbox->add('指派成功');
                }else{
                    $this->msgbox->add('指派失败',208);
                }
            }
        }else{
            $this->msgbox->add('参数有误！',213);
        }
    }

    public function get_new_order(){
        $filter = array();
        $filter['group_id'] = $this->group_id;;
        $filter[':SQL'] = "((`from`='waimai' AND ((`pei_type`=1 AND `order_status`=2) OR (`order_status`=0 AND `pei_type`=2))) OR (`from`='paotui' AND `order_status`=0))";
        $filter['tmp_ltime'] = "<:".__TIME;
        $filter[':OR'] = array(
            'pay_status'=>1,
            'online_pay'=>0
        );
        $filter['refund_status'] = '<=:0';
        $filter['staff_id'] = 0;    // 0等待接单
        $count = K::M('order/order')->count($filter);
        $this->msgbox->set_data('data',array('count'=>$count));
        $this->msgbox->json();
    }

    public function waitordertable($page=1){
        $page = max((int)$page,1);
        $limit = 20;
        $filter = array();
        $filter['group_id'] = $this->group_id;
        $filter[':SQL'] = "((`from`='waimai' AND ((`pei_type`=1 AND `order_status`=2) OR (`order_status`=0 AND `pei_type`=2))) OR (`from`='paotui' AND `order_status`=0))";
        $filter['tmp_ltime'] = "<:".__TIME;
        $filter[':OR'] = array(
            'pay_status'=>1,
            'online_pay'=>0
        );
        $filter['refund_status'] = '<=:0';
        $filter['staff_id'] = 0;    // 0等待接单
        $shop_ids = array();
        $orderby = array('dateline' => 'ASC');
       /* if($title = $this->GP('title')){
            $waimai_count = K::M('waimai/waimai')->count(array('group_id'=>$this->group_id));
            $filter_shop = array();
            $filter_shop['group_id'] = $this->group_id;
            $filter_shop[':OR'] = array(
                'title'=>"LIKE:%".$title."%",
                'phone'=>"LIKE:%".$title."%"
            );
            $waimai_list = K::M('waimai/waimai')->items($filter_shop,array('shop_id'=>"DESC"),1,$waimai_count);
            foreach ($waimai_list as $k1=>$v1){
                $shop_ids[$v1['shop_id']] = $v1['shop_id'];
            }
            if($shop_ids){
                $filter['shop_id'] = $shop_ids;
            }
        }*/

        if($items = K::M('order/order')->items($filter,$orderby,$page,$limit,$count)){
            foreach ($items as $k=>$v){
                if($v['pei_time']>0){
                    $items[$k]['day_num'] = $v['day_num'].'[预]';
                }else if($v['pei_time']==0){
                    $items[$k]['day_num'] = $v['day_num'];
                }
                $items[$k]['show_time'] = date('m-d H:i:s',$v['dateline']);
                if($v['from']=='waimai'){
                    $items[$k]['expect_label'] =$v['expect_time']? date('m-d H:i',$v['expect_time']):"尽快送达";
                    $waimai_ids[$v['shop_id']] = $v['shop_id'];
                }else if($v['from']=='paotui'){
                    $items[$k]['expect_label']  = "尽快送达";
                    $items[$k]['o_lng'] = $v['o_lng']? $v['o_lng']:$v['lng'];
                    $items[$k]['o_lat'] = $v['o_lat']? $v['o_lat']:$v['lat'];
                    $paotui_ids[] = $v['order_id'];
                }
                if($v['from']=='waimai'){
                    if($v['expect_time']>0&&$v['expect_time']<__TIME){
                        $items[$k]['time_label'] = "已超时:".$this->formatTime(__TIME-$v['expect_time']);
                        $items[$k]['label_time'] = "预计:".date('m-d H:i',$v['expect_time']).'送达';
                    }else if($v['expect_time']>0&&$v['expect_time']>=__TIME){
                        $items[$k]['time_label'] = "距超时:".$this->formatTime($v['expect_time']-__TIME);
                        $items[$k]['label_time'] = "预计:".date('m-d H:i',$v['expect_time']).'送达';
                    }else{
                        $items[$k]['label_time'] = "--";
                        $items[$k]['time_label'] = "----";
                    }
                }else{
                    $items[$k]['label_time'] = "--";
                    $items[$k]['time_label'] = "----";
                }
            }
            $waimai_list = K::M('waimai/waimai')->items_by_ids($waimai_ids);
            foreach($items as $kk=>$vv){
                $items[$kk]['shop_addr'] = $waimai_list[$vv['shop_id']]['addr'];
            }
            $paotui_order_list = K::M('paotui/order')->items_by_ids($paotui_ids);
            foreach($items as $kkk=>$vvv){
                foreach($paotui_order_list as $key=>$val){
                    if($vvv['order_id']==$val['order_id']){
                        $items[$kkk]['shop_addr'] = $val['o_addr']?$val['o_addr']:"就近购买";
                    }
                }
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('order/waitordertable', array('{page}')), array('SO'=>$SO));
        }
        $this->today_order();
        $this->common_data();

        $filter = $orderby = array();
        $filter['status'] = 1 ;
        $filter['from'] = 'paotui';
        $filter['group_id'] = $this->group_id;
        $filter['closed'] = 0;
        $filter['lastlogin'] = '>:0';
        $staff_ids = array();
        $staff_count = K::M('staff/staff')->count(array('group_id'=>$this->group_id));
        if($staff = K::M('staff/staff')->items($filter, $orderby, 1, $staff_count, $count)){
            $staff_ids = array_keys($staff);
            //待取： 待送达：
            //-1:配送员弃单  0：未处理，1：已接单（跑腿订单已接单）  3：配送开始（跑腿服务中），4：配送完成（跑腿服务完成），8：订单完成
            $filter_no = $filter_yes = array('from'=>array('waimai','paotui'), 'staff_id'=>$staff_ids);
            $filter_no[':SQL'] = "((`from`='waimai' AND `order_status` in (2)) OR (`from`='paotui' AND `order_status` in (1)))";
            $filter_yes['order_status'] = 3;
            $order_no = K::M('order/order')->items_group_by_staff($filter_no);//待取
            $order_yes =  K::M('order/order')->items_group_by_staff($filter_yes);//待送
            foreach ($staff as $k=>$v){
                $staff[$k]['dq_order'] = $order_yes[$v['staff_id']] ? $order_yes[$v['staff_id']]['orders'] : 0;
                $staff[$k]['ds_order'] = $order_no[$v['staff_id']] ? $order_no[$v['staff_id']]['orders'] : 0;
                $staff[$k]['count_order'] = $staff[$k]['dq_order']+ $staff[$k]['ds_order'];
            }
            uasort($staff, array($this, 'count_order'));
        }else{
            $staff = array();
        }
        $this->pagedata['levels'] = K::M('staff/level')->fetch_all();
        $this->pagedata['tj_items'] = array_slice($staff, 0, 5, true);
        $this->pagedata['items'] = $staff;

        $this->pagedata['items_order'] =$items;
        $this->pagedata['pager'] =$pager;
        $this->tmpl = 'order/waitordertable.html';
    }

    public function formatTime($strtime)
    {
        $label = '';
        $minutes = intval($strtime/60);
        if($strtime<60){
            $label = $strtime.'秒';
        }else if($minutes < 60){
            $label = $minutes.'分钟';
        }else if($minutes < 60*24){
            $h = intval($minutes/60);
            $m = $minutes%60;
            $label = $h.'小时'.$m.'分钟';
        }else{
            $d = intval($minutes/(60*24));
            $h = intval(intval(($minutes%(60*24)))/60);
            $m = intval(intval(($minutes%(60*24)))%60);
            $label = $d.'天'.$h.'小时';
        }
        return $label;
    }

    public function ploygion($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',201);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if($order['group_id']!=$this->group_id){
            $this->msgbox->add('该订单不属于您的配送站',203);
        }else{
            $order['o_lng'] =  $order['o_lng']? $order['o_lng']:$order['lng'];
            $order['o_lat'] =  $order['o_lat']? $order['o_lat']:$order['lat'];
            $this->pagedata['point'] = json_encode($this->group['polygon_point']);
            $this->pagedata['group'] = $this->group;
            $this->pagedata['map_key'] = MAP_KEY;
            $this->pagedata['order'] = $order;
            $this->tmpl = "order/ploygion.html";
        }
    }

    public function count_order($a,$b)
    {
        if ($a['count_order'] == $b['count_order']) {
            return 0;
        }else{
            return ($a['count_order'] < $b['count_order']) ? -1 : 1;
        }
    }

}