<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15
 * Time: 13:57
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Config extends Model {

    //获取配送员 每单的配送费
    public function get_peiamount($order,$staff_id,$staff_amount){
       $staff = K::M('staff/staff')->detail($staff_id);
       //未绑定骑手等级的  直接返回配送费
       if(!$staff||$staff['level_id']==0){
           return $staff_amount;
       }
       $level = K::M('staff/level')->detail($staff['level_id']);
       //骑手等级被删除的 直接返回配送费
       if(!$level){
           return $staff_amount;
       }
        if ($level['stime']&&$level['ltime']) {
            $now_time = __TIME;
            $start_time = strtotime(date('Y-m-d ',$now_time).$level['stime']);
            $end_time = strtotime(date('Y-m-d ',$now_time).$level['ltime']);

            if(stristr($level['ltime'],'次日')){
                    $ltime = str_replace('次日', '', $level['ltime']);
                    $end_time = strtotime(date('Y-m-d ',$now_time).$ltime) + 86400;
                    $v['ltime_time'] = $end_time - strtotime(date('Y-m-d ',$now_time)) + 86400;
                    $start_time1 = strtotime(date('Y-m-d ',$now_time).'00:00');
                    $end_time1 = strtotime(date('Y-m-d ',$now_time).$ltime);
                    $start_time2 = $start_time;
                    $end_time2 = strtotime(date('Y-m-d ',$now_time).'23:59');
                    if(($now_time >= $start_time1 && $now_time <= $end_time1) || ($now_time >= $start_time2 && $now_time <= $end_time2)){
                        if($level['config_waimai_time']){
                            $level['config_waimai'] = $level['config_waimai_time'];
                        }
                        if($level['config_paotui_time']){
                            $level['config_paotui'] = $level['config_paotui_time'];
                        }
                    }
            }else{
                    if ($start_time && $end_time) {
                        if($now_time >= $start_time && $now_time <= $end_time){
                            if($level['config_waimai_time']){
                                $level['config_waimai'] = $level['config_waimai_time'];
                            }
                            if($level['config_paotui_time']){
                                $level['config_paotui'] = $level['config_paotui_time'];
                            }

                        }
                    }
            }

        }

       if($order['from']=='waimai'){
           //外卖单  读取外卖配置
         if($level['config_waimai']['type']==1){
             //配置为每单固定金额的  直接返回金额
             return $level['config_waimai']['fixed'];
         }else if($level['config_waimai']['type']==2){
             //配置为按照比例获取
             return  $fee = number_format(($staff_amount * $level['config_waimai']['bl'])/100, 2, '.', '');
         }else if($level['config_waimai']['type']==3){
             //配置为按照距离收费是
             if($order['lng']&&$order['lat']&&$order['o_lng']&&$order['o_lat']){
                 if (!$juli = K::M('magic/baidu')->juli($order['lng'], $order['lat'], $order['o_lng'], $order['o_lat'])) {
                     $juli = K::M('helper/round')->juli($order['lng'], $order['lat'], $order['o_lng'], $order['o_lat']);
                 }
             }else{
                 $juli = 0;
             }
             $juli_format = ceil($juli / 1000);
             if($juli_format>$level['config_waimai']['step']){
                 $diff_juli = $juli-($level['config_waimai']['step']*1000);
                 $distance_money = ceil($diff_juli/1000)* $level['config_waimai']['amplitude'];
                 if(($distance_money+$level['config_waimai']['base'])>$level['config_waimai']['max']){
                     return $level['config_waimai']['max'];
                 }else{
                     return number_format(($distance_money+$level['config_waimai']['base']),2,'.','');
                 }
             }else{
                 return $level['config_waimai']['base']>$level['config_waimai']['max']?$level['config_waimai']['max']: $level['config_waimai']['base'];

             }
         }else{
             //todo 后续处理新的规则 这边先返回配送费
             return $staff_amount;

         }

       }else if($order['from']=='paotui'){
           //跑腿订单的处理
           if($level['config_paotui']['type']==1){
               //配置为每单固定金额的  直接返回金额
               return $level['config_paotui']['fixed'];


           }else if($level['config_paotui']['type']==2){
               //配置为按照比例获取
               return  $fee = number_format(($staff_amount * $level['config_paotui']['bl'])/100, 2, '.', '');
           }else if($level['config_paotui']['type']==3){
               //配置为按照距离收费是
               if($order['lng']&&$order['lat']&&$order['o_lng']&&$order['o_lat']){
                   if (!$juli = K::M('magic/baidu')->juli($order['lng'], $order['lat'], $order['o_lng'], $order['o_lat'])) {
                       $juli = K::M('helper/round')->juli($order['lng'], $order['lat'], $order['o_lng'], $order['o_lat']);
                   }
               }else{
                   $juli = 0;
               }
               $juli_format = ceil($juli / 1000);
               if($juli_format>$level['config_paotui']['step']){
                   $diff_juli = $juli-($level['config_paotui']['step']*1000);
                   $distance_money = ceil($diff_juli/1000)* $level['config_paotui']['amplitude'];
                   if(($distance_money+$level['config_paotui']['base'])>$level['config_paotui']['max']){
                       return $level['config_paotui']['max'];
                   }else{
                       return number_format(($distance_money+$level['config_paotui']['base']),2,'.','');
                   }
               }else{
                   return $level['config_paotui']['base']>$level['config_paotui']['max']?$level['config_paotui']['max']: $level['config_paotui']['base'];
               }
           }else{
               //todo 后续处理新的规则 这边先返回配送费
               return $staff_amount;

           }

       }else{
           //todo 后期新增其他类型订单
           return  0;
       }


    }

}