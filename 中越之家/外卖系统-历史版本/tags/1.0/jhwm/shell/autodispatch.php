<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/4/12
 * Time: 17:47
 */
//此脚本为自动派单脚本
//派单规则
//1:配送员在取货地址范围内 根据配送站的设置的配送范围内
//2:如果配送员身上没有被单 根据配送站配置的配置



//if(strtolower(php_sapi_name()) != 'cli'){
  //  exit('only run cli');
//}
@ini_set("display_errors", "On");
@error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT ^ E_WARNING);;
@set_time_limit(0);
@ini_set('memory_limit','128M');
@ini_set('allow_url_fopen', 'On');
@date_default_timezone_set('Asia/Shanghai');
require(dirname(__DIR__).'/system/home/index.php');
$system = new Index('magic-shell');
$filter = array();
$filter[':SQL'] = "((`from`='waimai' AND ((`pei_type`=1 AND `order_status`=2) OR (`order_status`=0 AND `pei_type`=2))) OR (`from`='paotui' AND `order_status`=0))";
$filter['tmp_ltime'] = "<:".__TIME;
$filter[':OR'] = array(
    'pay_status'=>1,
    'online_pay'=>0
);
$filter['refund_status'] = '<=:0';
$filter['staff_id'] = 0;

$group_list = $group_ids = array();
if($items = K::M('order/order')->items($filter,array('order_id'=>'DESC'),1,50)){
    $shop_ids = $paotui_ids = array();
    foreach($items as $k=>$v){
        $group_list[$v['group_id']] = K::M('pei/group')->get_cache($v['group_id']);
        $group_ids[$v['group_id']] = $v['group_id'];
        if($v['from']=='waimai'){
            $shop_ids[$v['shop_id']] = $v['shop_id'];
        }else if(
            $v['from']=='paotui'){$paotui_ids[$v['order_id']] = $v['order_id'];
        }
    }

    $waimai_list = K::M('waimai/waimai')->items_by_ids($shop_ids);
    $paotui_list = K::M('paotui/order')->items_by_ids($paotui_ids);
    foreach($items as $ksk=>$vsv){
        if($vsv['from']=='waimai'){
            $items[$ksk]['o_addr'] =  $waimai_list[$vsv['shop_id']]['addr'];
        }else if($vsv['from']=='paotui'){
            $items[ $ksk]['o_addr'] =  $paotui_list[$vsv['order_id']]['o_addr']?$paotui_list[$vsv['order_id']]['o_addr']:"就近购买";
        }
        $items[$ksk]['o_lng'] = $vsv['o_lng']? $vsv['o_lng']:$vsv['lng'];
        $items[$ksk]['o_lat'] = $vsv['o_lat']? $vsv['o_lat']:$vsv['lat'];

    }
    foreach($group_list as $kk=>$vv){
        if(!$vv['autopei_config']['open_autopai']){
            unset($group_ids[$vv['group_id']]);
        }
    }
    $filter_work_on_staff = array();
    $filter_work_on_staff['status'] = 1 ;
    $filter_work_on_staff['from'] = 'paotui';
    $filter_work_on_staff['group_id'] = $group_ids;
    $filter_work_on_staff['closed'] = 0;
    $filter_work_on_staff['audit'] = 1;
    $filter_work_on_staff[':SQL'] = "( `lng` != 0 && `lat` != 0 )";
    $filter_work_on_staff['lastlogin'] = '>:0';

    if($staff = K::M('staff/staff')->items($filter_work_on_staff,array('staff_id'=>"DESC"),1,500)){
        $staff_ids = array_keys($staff);
        $paidan_staff = array();
        $filter_ing_order = array();
        $filter_ing_order['from'] = array('waimai','paotui');
        $filter_ing_order['staff_id'] = $staff_ids;
        $filter_ing_order[':SQL'] = "((`from`='waimai' AND (`pei_type`=1 AND `order_status` IN (2,3))) OR (`from`='paotui' AND `order_status` IN (1,3)))";
        $ing_order = K::M('order/order')->items($filter_ing_order,array('order_id'=>"DESC"),1,5000);
        foreach($staff as $staff_k=>$staff_v){
            $staff[$staff_k]['ing_order'] = 0;
            $staff[$staff_k]['last_pai'] = 0;
            $paidan_staff[$staff_k]['pai_order'] = 0;


        }
        foreach($ing_order as $ing_order_k=>$ing_order_v){
            $staff[$ing_order_v['staff_id']]['ing_order']++;
        }
    }
    foreach($items as $kk=>$vv){
        if($group_list[$vv['group_id']]['autopei_config']['open_autopai']!=1){
            continue;
        }
        foreach($staff as $stf_k=>$stf_v){
            $staff[$stf_k]['juli'] =K::M('helper/round')->juli($vv['o_lng'],$vv['o_lat'],$stf_v['lng'],$stf_v['lat']);
        }
        uasort($staff, 'price_order');
        if($vv['o_lng']==$vv['lng']&&$vv['o_lat']==$vv['lat']){
            foreach($staff as $kkkk=>$vvvv){
                if($vvvv['group_id']!=$vv['group_id']){
                    continue;
                }else if($group_list[$vv['group_id']]['autopei_config']['orders']<$vvvv['ing_order']){
                    continue;
                }else if($group_list[$vv['group_id']]['autopei_config']['pei_distance']<$vvvv['juli']){
                    continue;
                }else if($paidan_staff[$vvvv['staff_id']]['pai_order']>=$group_list[$vv['group_id']]['autopei_config']['limit_orders']){//最多指派10单
                    continue;
                }else{
                    if($vvvv['ing_order']==0){
                        if($group_list[$vv['group_id']]['autopei_config']['jiedan']==0){
                            $last_time = get_lasttime($group_list[$vv['group_id']]);
                            K::M('order/order')->update($vv['order_id'],array('tmp_staff_id'=>$vvvv['staff_id'],'tmp_ltime'=>$last_time));
                            $order_log = array();
                            $order_log['order_id'] = $vv['order_id'];
                            $order_log['status'] = 0;
                            $order_log['from'] = 'admin';
                            $order_log['log'] = '指派订单ID('.$vv['order_id'].')给骑手'.$vvvv['name'].'('.$vvvv['staff_id'].')成功';
                            K::M('order/log')->create($order_log);
                            $text = '取货地址:'.$vv['o_addr'];
                            K::M('staff/staff')->send($vvvv['staff_id'],'系统派单',$text,array('type'=>'paiOrder','order_id'=>$vv['order_id']));
                            $tongji_data = array(
                                'pai'=>1,
                                'accept'=>0,
                                'refuse'=>0,
                                'staff_id'=>$vvvv['staff_id'],
                            );
                            $paidan_log = array(
                                'order_id'=>$vv['order_id'],
                                'intro'=>$text
                            );
                            K::M('staff/paidanlog')->create($paidan_log);
                            K::M('staff/paidan')->create($tongji_data);
                            $staff[$vvvv['staff_id']]['last_pai']++;
                            $paidan_staff[$vvvv['staff_id']]['pai_order']++;
                            $vv['staff_id'] = $vvvv['staff_id'];
                            $ing_order[$vv['order_id']] = $vv;
                            break;
                        }else if($group_list[$vv['group_id']]['autopei_config']['jiedan']==1){
                            if($vv['from']=='waimai'){
                                $status = 2;
                            }else if($vv['from']=='paotui'){
                                $status = 1;
                            }else{
                                $status = 2;
                            }
                            K::M('order/order')->update($vv['order_id'],array('staff_id'=>$vvvv['staff_id'],'jd_time'=>__TIME,'order_status'=>$status));
                            $text = '取货地址:'.$vv['o_addr'];
                            K::M('staff/staff')->send($vvvv['staff_id'],'系统派单',$text,array('type'=>'newOrder','order_id'=>$vv['order_id']));
                            $tongji_data = array(
                                'pai'=>1,
                                'accept'=>1,
                                'refuse'=>0,
                                'staff_id'=>$vvvv['staff_id'],
                            );
                            $paidan_log = array(
                                'order_id'=>$vv['order_id'],
                                'intro'=>$text
                            );
                            $order_log = array();
                            $order_log['order_id'] = $vv['order_id'];
                            $order_log['status'] = 0;
                            $order_log['from'] = 'admin';
                            $order_log['log'] = '指派订单ID('.$vv['order_id'].')给骑手'.$vvvv['name'].'('.$vvvv['staff_id'].')成功';
                            K::M('order/log')->create($order_log);
                            K::M('staff/paidanlog')->create($paidan_log);
                            K::M('staff/paidan')->create($tongji_data);
                            $staff[$vvvv['staff_id']]['last_pai']++;
                            $paidan_staff[$vvvv['staff_id']]['pai_order']++;
                            K::M('order/time')->update($vv['order_id'],array('staff_jiedan_time'=>time()));
                            if($other_order = K::M('other/order')->find(array('p_order_id'=>$vv['order_id']))){
                                K::M('order/order')->update($other_order['order_id'],array('staff_id'=>$vvvv['staff_id']));
                            }
                            $vv['staff_id'] = $vvvv['staff_id'];
                            $ing_order[$vv['order_id']] = $vv;
                            break;
                        }
                    }else{
                        if($group_list[$vv['group_id']]['autopei_config']['pei_distance']>0){
                            if(in_or_not_circular($vvvv['lng'],$vvvv['lat'],$ing_order,$group_list[$vv['group_id']]['autopei_config']['pei_distance'],$vvvv['staff_id'])){
                                if($group_list[$vv['group_id']]['autopei_config']['jiedan']==0){
                                    $last_time = get_lasttime($group_list[$vv['group_id']])+time();
                                    K::M('order/order')->update($vv['order_id'],array('tmp_staff_id'=>$vvvv['staff_id'],'tmp_ltime'=>$last_time));
                                    $order_log = array();
                                    $order_log['order_id'] = $vv['order_id'];
                                    $order_log['status'] = 0;
                                    $order_log['from'] = 'admin';
                                    $order_log['log'] = '指派订单ID('.$vv['order_id'].')给骑手'.$vvvv['name'].'('.$vvvv['staff_id'].')成功';
                                    K::M('order/log')->create($order_log);

                                    $text = '取货地址:'.$vv['o_addr'];
                                    K::M('staff/staff')->send($vvvv['staff_id'],'系统派单',$text,array('type'=>'paiOrder','order_id'=>$vv['order_id']));
                                    $tongji_data = array(
                                        'pai'=>1,
                                        'accept'=>0,
                                        'refuse'=>0,
                                        'staff_id'=>$vvvv['staff_id'],
                                    );
                                    $paidan_log = array(
                                        'order_id'=>$vv['order_id'],
                                        'intro'=>$text
                                    );
                                    K::M('staff/paidanlog')->create($paidan_log);
                                    K::M('staff/paidan')->create($tongji_data);
                                    $staff[$vvvv['staff_id']]['last_pai']++;
                                    $paidan_staff[$vvvv['staff_id']]['pai_order']++;
                                    $vv['staff_id'] = $vvvv['staff_id'];
                                    $ing_order[$vv['order_id']] = $vv;
                                    break;
                                }else if($group_list[$vv['group_id']]['autopei_config']['jiedan']==1){
                                    if($vv['from']=='waimai'){
                                        $status = 2;
                                    }else if($vv['from']=='paotui'){
                                        $status = 1;
                                    }else{
                                        $status = 2;
                                    }
                                    K::M('order/order')->update($vv['order_id'],array('staff_id'=>$vvvv['staff_id'],'jd_time'=>time(),'order_status'=>$status));
                                    $text = '取货地址:'.$vv['o_addr'];
                                    K::M('staff/staff')->send($vvvv['staff_id'],'系统派单',$text,array('type'=>'newOrder','order_id'=>$vv['order_id']));
                                    $order_log = array();
                                    $order_log['order_id'] = $vv['order_id'];
                                    $order_log['status'] = 0;
                                    $order_log['from'] = 'admin';
                                    $order_log['log'] = '指派订单ID('.$vv['order_id'].')给骑手'.$vvvv['name'].'('.$vvvv['staff_id'].')成功';
                                    K::M('order/log')->create($order_log);
                                    $tongji_data = array(
                                        'pai'=>1,
                                        'accept'=>1,
                                        'refuse'=>0,
                                        'staff_id'=>$vvvv['staff_id'],
                                    );
                                    $paidan_log = array(
                                        'order_id'=>$vv['order_id'],
                                        'intro'=>$text
                                    );
                                    K::M('staff/paidanlog')->create($paidan_log);
                                    K::M('staff/paidan')->create($tongji_data);
                                    $staff[$vvvv['staff_id']]['last_pai']++;
                                    $paidan_staff[$vvvv['staff_id']]['pai_order']++;
                                    K::M('order/time')->update($vv['order_id'],array('staff_jiedan_time'=>time()));
                                    if($other_order = K::M('other/order')->find(array('p_order_id'=>$vv['order_id']))){
                                        K::M('order/order')->update($other_order['order_id'],array('staff_id'=>$vvvv['staff_id']));
                                    }
                                    $vv['staff_id'] = $vvvv['staff_id'];
                                    $ing_order[$vv['order_id']] = $vv;
                                    break;
                                }

                            }
                        }
                    }
                }
            }
        }else{
            foreach($staff as $kkkk=>$vvvv){
                if($vvvv['group_id']!=$vv['group_id']){
                    continue;
                }else if($group_list[$vv['group_id']]['autopei_config']['orders']<$vvvv['ing_order']){
                    continue;
                }else if($group_list[$vv['group_id']]['autopei_config']['pei_distance']<$vvvv['juli']){
                    continue;
                }else if( $paidan_staff[$vvvv['staff_id']]['pai_order']>=$group_list[$vv['group_id']]['autopei_config']['limit_orders']){//最多指派10单
                    continue;
                }else{

                    if($vvvv['ing_order']==0){
                            if($group_list[$vv['group_id']]['autopei_config']['jiedan']==0){
                                $last_time = get_lasttime($group_list[$vv['group_id']]);
                                K::M('order/order')->update($vv['order_id'],array('tmp_staff_id'=>$vvvv['staff_id'],'tmp_ltime'=>$last_time+time()));
                                $order_log = array();
                                $order_log['order_id'] = $vv['order_id'];
                                $order_log['status'] = 0;
                                $order_log['from'] = 'admin';
                                $order_log['log'] = '指派订单ID('.$vv['order_id'].')给骑手'.$vvvv['name'].'('.$vvvv['staff_id'].')成功';
                                K::M('order/log')->create($order_log);

                                $text = '取货地址:'.$vv['o_addr'];
                                K::M('staff/staff')->send($vvvv['staff_id'],'系统派单',$text,array('type'=>'paiOrder','order_id'=>$vv['order_id']));
                                $tongji_data = array(
                                    'pai'=>1,
                                    'accept'=>0,
                                    'refuse'=>0,
                                    'staff_id'=>$vvvv['staff_id'],
                                );
                                $paidan_log = array(
                                    'order_id'=>$vv['order_id'],
                                    'intro'=>$text
                                );
                                K::M('staff/paidanlog')->create($paidan_log);
                                K::M('staff/paidan')->create($tongji_data);
                                $staff[$vvvv['staff_id']]['last_pai']++;
                                $paidan_staff[$vvvv['staff_id']]['pai_order']++;
                                $vv['staff_id'] = $vvvv['staff_id'];
                                $ing_order[$vv['order_id']] = $vv;
                                break;
                            }else if($group_list[$vv['group_id']]['autopei_config']['jiedan']==1){
                                if($vv['from']=='waimai'){
                                    $status = 2;
                                }else if($vv['from']=='paotui'){
                                    $status = 1;
                                }else{
                                    $status = 2;
                                }
                                K::M('order/order')->update($vv['order_id'],array('staff_id'=>$vvvv['staff_id'],'jd_time'=>time(),'order_status'=>$status));
                                $text = '取货地址:'.$vv['o_addr'];
                                K::M('staff/staff')->send($vvvv['staff_id'],'系统派单',$text,array('type'=>'newOrder','order_id'=>$vv['order_id']));
                                $order_log = array();
                                $order_log['order_id'] = $vv['order_id'];
                                $order_log['status'] = 0;
                                $order_log['from'] = 'admin';
                                $order_log['log'] = '指派订单ID('.$vv['order_id'].')给骑手'.$vvvv['name'].'('.$vvvv['staff_id'].')成功';
                                K::M('order/log')->create($order_log);
                                $tongji_data = array(
                                    'pai'=>1,
                                    'accept'=>1,
                                    'refuse'=>0,
                                    'staff_id'=>$vvvv['staff_id'],
                                );
                                $paidan_log = array(
                                    'order_id'=>$vv['order_id'],
                                    'intro'=>$text
                                );
                                K::M('staff/paidanlog')->create($paidan_log);
                                K::M('staff/paidan')->create($tongji_data);
                                $staff[$vvvv['staff_id']]['last_pai']++;
                                $paidan_staff[$vvvv['staff_id']]['pai_order']++;
                                K::M('order/time')->update($vv['order_id'],array('staff_jiedan_time'=>time()));
                                if($other_order = K::M('other/order')->find(array('p_order_id'=>$vv['order_id']))){
                                    K::M('order/order')->update($other_order['order_id'],array('staff_id'=>$vvvv['staff_id']));
                                }
                                $vv['staff_id'] = $vvvv['staff_id'];
                                $ing_order[$vv['order_id']] = $vv;
                                break;
                            }
                        }else{
                           //
                        if($group_list[$vv['group_id']]['autopei_config']['pei_distance']>0){
                            if(in_or_not_circular($vvvv['lng'],$vvvv['lat'],$ing_order,$group_list[$vv['group_id']]['autopei_config']['pei_distance'],$vvvv['staff_id'])){
                                if($group_list[$vv['group_id']]['autopei_config']['jiedan']==0){
                                    $last_time = get_lasttime($group_list[$vv['group_id']]);
                                    K::M('order/order')->update($vv['order_id'],array('tmp_staff_id'=>$vvvv['staff_id'],'tmp_ltime'=>$last_time+time()));
                                    $order_log = array();
                                    $order_log['order_id'] = $vv['order_id'];
                                    $order_log['status'] = 0;
                                    $order_log['from'] = 'admin';
                                    $order_log['log'] = '指派订单ID('.$vv['order_id'].')给骑手'.$vvvv['name'].'('.$vvvv['staff_id'].')成功';
                                    K::M('order/log')->create($order_log);

                                    $text = '取货地址:'.$vv['o_addr'];
                                    K::M('staff/staff')->send($vvvv['staff_id'],'系统派单',$text,array('type'=>'paiOrder','order_id'=>$vv['order_id']));
                                    $tongji_data = array(
                                        'pai'=>1,
                                        'accept'=>0,
                                        'refuse'=>0,
                                        'staff_id'=>$vvvv['staff_id'],
                                    );
                                    $paidan_log = array(
                                        'order_id'=>$vv['order_id'],
                                        'intro'=>$text
                                    );
                                    K::M('staff/paidanlog')->create($paidan_log);
                                    K::M('staff/paidan')->create($tongji_data);
                                    $staff[$vvvv['staff_id']]['last_pai']++;
                                    $paidan_staff[$vvvv['staff_id']]['pai_order']++;
                                    $vv['staff_id'] = $vvvv['staff_id'];
                                    $ing_order[$vv['order_id']] = $vv;
                                    break;
                                }else if($group_list[$vv['group_id']]['autopei_config']['jiedan']==1){
                                    if($vv['from']=='waimai'){
                                        $status = 2;
                                    }else if($vv['from']=='paotui'){
                                        $status = 1;
                                    }else{
                                        $status = 2;
                                    }
                                    K::M('order/order')->update($vv['order_id'],array('staff_id'=>$vvvv['staff_id'],'jd_time'=>time(),'order_status'=>$status));
                                    $text = '取货地址:'.$vv['o_addr'];
                                    K::M('staff/staff')->send($vvvv['staff_id'],'系统派单',$text,array('type'=>'newOrder','order_id'=>$vv['order_id']));
                                    $order_log = array();
                                    $order_log['order_id'] = $vv['order_id'];
                                    $order_log['status'] = 0;
                                    $order_log['from'] = 'admin';
                                    $order_log['log'] = '指派订单ID('.$vv['order_id'].')给骑手'.$vvvv['name'].'('.$vvvv['staff_id'].')成功';
                                    K::M('order/log')->create($order_log);
                                    $tongji_data = array(
                                        'pai'=>1,
                                        'accept'=>1,
                                        'refuse'=>0,
                                        'staff_id'=>$vvvv['staff_id'],
                                    );
                                    $paidan_log = array(
                                        'order_id'=>$vv['order_id'],
                                        'intro'=>$text
                                    );
                                    K::M('staff/paidanlog')->create($paidan_log);
                                    K::M('staff/paidan')->create($tongji_data);
                                    $staff[$vvvv['staff_id']]['last_pai']++;
                                    $paidan_staff[$vvvv['staff_id']]['pai_order']++;
                                    K::M('order/time')->update($vv['order_id'],array('staff_jiedan_time'=>time()));
                                    if($other_order = K::M('other/order')->find(array('p_order_id'=>$vv['order_id']))){
                                        K::M('order/order')->update($other_order['order_id'],array('staff_id'=>$vvvv['staff_id']));
                                    }
                                    $vv['staff_id'] = $vvvv['staff_id'];
                                    $ing_order[$vv['order_id']] = $vv;
                                    break;
                                }

                            }
                        }


                      }

                   }


                }


            }

    }


}

 function price_order($a, $b)
{
    if($a['last_pai'] == $b['last_pai']){
        if($a['ing_order']==$b['ing_order']){
            if ($a['juli'] == $b['juli']) {
                return 0;
            }else{
                return ($a['juli'] < $b['juli']) ? -1 : 1;
            }
        }else{
            return ($a['ing_order'] < $b['ing_order']) ? -1 : 1;
        }
    }else{
        return ($a['last_pai'] > $b['last_pai']) ? -1 : 1;
    }
}

function  in_or_not_circular($lng,$lat,$ing_order,$round,$staff_id){

    $in = 0;
    foreach($ing_order as $k=>$v){
        if($v['staff_id']==$staff_id){
            if($v['o_lng']==$v['lng']&&$v['o_lat']==$v['lat']){
                //只需判断是否在圆形当中
                $juli = K::M('helper/round')->juli($lng,$lat,$v['lng'],$v['lat']);
                if($juli<$round){
                    $in++;
                }
            }else {
                if($round==0){
                    continue;
                }else{

                    $area = return_in_or_area($v['lng'],$v['lat'],$v['o_lng'],$v['o_lat'],$round);
                    if(K::M('helper/round')->in_or_out_polygon($area,$lat,$lng)){
                        $in++;
                    }
                }
            }
        }
    }
    return $in;
}

 function get_lasttime($group){
    $overtime = $group['overtime']?$group['overtime']:5;
    return $overtime*60;
}

function return_in_or_area($lng,$lat,$lng1,$lat1,$round){
    $kilo_meter = sprintf("%1\$.6f",$round/1000);
    $location_a = K::M('helper/round')->returnSquarePoint($lng,$lat,$kilo_meter);
    $location_b =K::M('helper/round')->returnSquarePoint($lng1,$lat1,$kilo_meter);
    $area = array();
    if($lng>$lng1&&$lat>$lat1){
        $area = array(
            array('lng'=>$location_b['left-top']['lng'],'lat'=>$location_b['left-top']['lat']),
            array('lng'=>$location_a['left-top']['lng'],'lat'=>$location_a['left-top']['lat']),
            array('lng'=>$location_a['right-top']['lng'],'lat'=>$location_a['right-top']['lat']),
            array('lng'=>$location_a['right-bottom']['lng'],'lat'=>$location_a['right-bottom']['lat']),
            array('lng'=>$location_b['right-bottom']['lng'],'lat'=>$location_b['right-bottom']['lat']),
            array('lng'=>$location_b['left-bottom']['lng'],'lat'=>$location_b['left-bottom']['lat']),

        );

    }else if($lng>$lng1&&$lat<$lat1){
        $area = array(
            array('lng'=>$location_b['left-top']['lng'],'lat'=>$location_b['left-top']['lat']),
            array('lng'=>$location_b['right-top']['lng'],'lat'=>$location_b['right-top']['lat']),
            array('lng'=>$location_a['right-top']['lng'],'lat'=>$location_a['right-top']['lat']),
            array('lng'=>$location_a['right-bottom']['lng'],'lat'=>$location_a['right-bottom']['lat']),
            array('lng'=>$location_a['left-bottom']['lng'],'lat'=>$location_a['left-bottom']['lat']),
            array('lng'=>$location_b['left-bottom']['lng'],'lat'=>$location_b['left-bottom']['lat']),
        );
    }else if($lng<$lng1&&$lat>$lat1){
        $area = array(
            array('lng'=>$location_a['left-top']['lng'],'lat'=>$location_a['left-top']['lat']),
            array('lng'=>$location_a['right-top']['lng'],'lat'=>$location_a['right-top']['lat']),
            array('lng'=>$location_b['right-top']['lng'],'lat'=>$location_b['right-top']['lat']),
            array('lng'=>$location_b['right-bottom']['lng'],'lat'=>$location_b['right-bottom']['lat']),
            array('lng'=>$location_b['left-bottom']['lng'],'lat'=>$location_b['left-bottom']['lat']),
            array('lng'=>$location_a['left-bottom']['lng'],'lat'=>$location_a['left-bottom']['lat']),
        );
    }else if($lng<$lng1&&$lat<$lat1){
        $area = array(
            array('lng'=>$location_a['left-top']['lng'],'lat'=>$location_a['left-top']['lat']),
            array('lng'=>$location_b['left-top']['lng'],'lat'=>$location_b['left-top']['lat']),
            array('lng'=>$location_b['right-top']['lng'],'lat'=>$location_b['right-top']['lat']),
            array('lng'=>$location_b['right-bottom']['lng'],'lat'=>$location_b['right-bottom']['lat']),
            array('lng'=>$location_a['right-bottom']['lng'],'lat'=>$location_a['right-bottom']['lat']),
            array('lng'=>$location_a['left-bottom']['lng'],'lat'=>$location_a['left-bottom']['lat']),
        );
    }else if($lng==$lng1&&$lat<$lat1){
        $area = array(
            array('lng'=>$location_b['left-top']['lng'],'lat'=>$location_b['left-top']['lat']),
            array('lng'=>$location_b['right-top']['lng'],'lat'=>$location_b['right-top']['lat']),
            array('lng'=>$location_a['right-bottom']['lng'],'lat'=>$location_a['right-bottom']['lat']),
            array('lng'=>$location_a['left-bottom']['lng'],'lat'=>$location_a['left-bottom']['lat']),
        );
    }else if($lng==$lng1&&$lat>$lat1){
        $area = array(
            array('lng'=>$location_a['left-top']['lng'],'lat'=>$location_a['left-top']['lat']),
            array('lng'=>$location_a['right-top']['lng'],'lat'=>$location_a['right-top']['lat']),
            array('lng'=>$location_b['right-bottom']['lng'],'lat'=>$location_b['right-bottom']['lat']),
            array('lng'=>$location_b['left-bottom']['lng'],'lat'=>$location_b['left-bottom']['lat']),
        );
    }else if($lat==$lat1&&$lng>$lng1){
        $area = array(
            array('lng'=>$location_b['left-top']['lng'],'lat'=>$location_b['left-top']['lat']),
            array('lng'=>$location_a['right-top']['lng'],'lat'=>$location_a['right-top']['lat']),
            array('lng'=>$location_a['right-bottom']['lng'],'lat'=>$location_a['right-bottom']['lat']),
            array('lng'=>$location_b['left-bottom']['lng'],'lat'=>$location_b['left-bottom']['lat']),
        );
    }else if($lat==$lat1&&$lng<$lng1){
        $area = array(
            array('lng'=>$location_a['left-top']['lng'],'lat'=>$location_a['left-top']['lat']),
            array('lng'=>$location_b['right-top']['lng'],'lat'=>$location_b['right-top']['lat']),
            array('lng'=>$location_b['right-bottom']['lng'],'lat'=>$location_b['right-bottom']['lat']),
            array('lng'=>$location_a['left-bottom']['lng'],'lat'=>$location_a['left-bottom']['lat']),
        );
    }

    return $area;
}










