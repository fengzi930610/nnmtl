<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/3/9
 * Time: 10:35
 */
if(strtolower(php_sapi_name()) != 'cli'){
    exit('only run cli');
}
@ini_set("display_errors", "On");
@error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT ^ E_WARNING);;
@set_time_limit(0);
@ini_set('memory_limit','128M');
@ini_set('allow_url_fopen', 'On');
@date_default_timezone_set('Asia/Shanghai');
require(dirname(__DIR__).'/system/home/index.php');
$system = new Index('magic-shell');

$server = new swoole_websocket_server("0.0.0.0", SWOOLE_PORT);
$server->set(array(
    'worker_num'  => 8,
    'daemonize'   => 1, //是否作为守护进程,此配置一般配合log_file使用
    'max_request' => 1000,
    'dispatch_mode' => 2,
    'debug_mode' => 1,
    'log_file'    => './swoole.log',
    'heartbeat_check_interval' => 60,
    'heartbeat_idle_time' => 120, //默认是heartbeat_check_interval的2倍,超过此设置客户端没有回应则强制断开链接
));

$server->on('open', function (swoole_websocket_server $server, $request) {
    //$server->push($request->fd , json_encode(array('data'=>array(),'message'=>'success','error'=>0)));//循环广播
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    $data = json_decode($frame->data,true);
    $fd = $frame->fd;
    //绑定事件
    $orders = loadorder($data['group_id']);
    $staff = loadstaff($data['group_id']);
    $load_data = array(
        'order'=>$orders,
        'staff'=>$staff
    );
    $server->push($fd, json_encode(array('data'=>$load_data,'message'=>'success','error'=>0)));
    unset($data);
    unset($fd);
    unset($orders);
    unset($staff);
    unset($load_data);



});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
   


});


 function loadorder($group_id){
    $filter = array();
    $filter['group_id'] = $group_id;
    $filter[':SQL'] = "((`from`='waimai' AND ((`pei_type`=1 AND `order_status`=2) OR (`order_status`=0 AND `pei_type`=2))) OR (`from`='paotui' AND `order_status`=0))";
    $filter['tmp_ltime'] = "<:".time();
    $filter[':OR'] = array(
        'pay_status'=>1,
        'online_pay'=>0
    );
    $filter['refund_status'] = '<=:0';
    $filter['staff_id'] = 0;    // 0等待接单
    $shop_ids = array();
    $orderby = array('dateline' => 'ASC');
    if($items = K::M('order/order')->items($filter,$orderby,1,500,$count)){
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
                if($v['expect_time']>0&&$v['expect_time']<time()){
                    $items[$k]['time_label'] = "已超时:".formatTime(time()-$v['expect_time']);
                    $items[$k]['label_time'] = "预计:".date('m-d H:i',$v['expect_time']).'送达';
                }else if($v['expect_time']>0&&$v['expect_time']>=time()){
                    $items[$k]['time_label'] = "距超时:".formatTime($v['expect_time']-time());
                    $items[$k]['label_time'] = "预计:".date('m-d H:i',$v['expect_time']).'送达';
                }else{
                    $items[$k]['label_time'] = "";
                    $items[$k]['time_label'] = "----";
                }
            }else{
                $items[$k]['label_time'] = "";
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


    }

    return array_values($items);


}


 function formatTime($strtime)
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


 function loadstaff($group_id)
 {
     $filter = array();
     $filter['status'] = 1;
     $filter['from'] = 'paotui';
     $filter['group_id'] = $group_id;
     $filter['closed'] = 0;
     $filter['lastlogin'] = '>:0';
     $staff_ids = array();
     $limit_order = -1;
     $level_ids = array();
     $staff_count = K::M('staff/staff')->count(array('group_id' => $group_id));
     if ($staff = K::M('staff/staff')->items($filter, null, 1, $staff_count, $count)) {
         foreach ($staff as $k => $v) {
             $location = K::M('helper/date')->bd_decrypt($v['lng'], $v['lat']);
             $staff[$k]['lng'] = $location['gg_lon'];
             $staff[$k]['lat'] = $location['gg_lat'];
             $staff_ids[] = $v['staff_id'];
             $level_ids[$v['level_id']] = $v['level_id'];
         }
         $level_list = K::M('staff/level')->items_by_ids($level_ids);
         //待取： 待送达：
         //  -1:配送员弃单   0：未处理，1：已接单（跑腿订单已接单）  3：配送开始（跑腿服务中），  4：配送完成（跑腿服务完成），8：订单完成
         $filter_no = $filter_yes = array();
         $filter_no['from'] = array('waimai', 'paotui');
         $filter_no['staff_id'] = $filter_yes['staff_id'] = $staff_ids;
         //$filter_no['order_status'] = array(1,2);
         $filter_no[':SQL'] = "((`from`='waimai' AND `order_status` in (2)) OR (`from`='paotui' AND `order_status` in (1)))";
         $filter_yes['order_status'] = 3;
         $filter_yes['from'] = array('waimai', 'paotui');

         //配送员代取
         $order_no = K::M('order/order')->items_group_by_staff($filter_no);
         //待送达：
         $order_yes = K::M('order/order')->items_group_by_staff($filter_yes);
         foreach ($staff as $k => $v) {
             $staff[$k]['level_name'] = $level_list[$v['level_id']]['title'] ? $level_list[$v['level_id']]['title'] : "--";
             if ($order_yes[$v['staff_id']]) {
                 $staff[$k]['ds_order'] = $order_yes[$v['staff_id']]['orders'];
             } else {
                 $staff[$k]['ds_order'] = 0;
             }
             if ($order_no[$v['staff_id']]) {
                 $staff[$k]['dq_order'] = $order_no[$v['staff_id']]['orders'];
             } else {
                 $staff[$k]['dq_order'] = 0;
             }

             if ($limit_order == 0) {
                 if (($staff[$k]['dq_order'] + $staff[$k]['ds_order']) > 0) {
                     unset($staff[$k]);
                 }
             } else if ($limit_order == 1) {
                 if (($staff[$k]['dq_order'] + $staff[$k]['ds_order']) >= 2) {
                     unset($staff[$k]);
                 }
             } else if ($limit_order == 3) {
                 if (($staff[$k]['dq_order'] + $staff[$k]['ds_order']) >= 4) {
                     unset($staff[$k]);
                 }
             } else if ($limit_order == 5) {
                 if (($staff[$k]['dq_order'] + $staff[$k]['ds_order']) >= 6) {
                     unset($staff[$k]);
                 }
             } else if ($limit_order == 7) {
                 if (($staff[$k]['dq_order'] + $staff[$k]['ds_order']) >= 8) {
                     unset($staff[$k]);
                 }
             }


         }


     } else {
         $staff = array();
     }

     return array_values($staff);
 }





$server->start();


