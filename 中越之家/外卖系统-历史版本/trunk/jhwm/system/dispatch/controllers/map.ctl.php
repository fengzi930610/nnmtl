<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/3/3
 * Time: 10:47
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Map extends Ctl {
    // //protected $all_order_filter = "order_id,day_num,addr,shop_addr";
    // public function loadorder(){
    //     $filter = array();
    //     $filter['group_id'] = $this->group_id;
    //     $filter[':SQL'] = "((`from`='waimai' AND ((`pei_type`=1 AND `order_status`=2) OR (`order_status`=0 AND `pei_type`=2))) OR (`from`='paotui' AND `order_status`=0))";
    //     $filter['tmp_ltime'] = "<:".__TIME;
    //     $filter[':OR'] = array(
    //         'pay_status'=>1,
    //         'online_pay'=>0
    //     );
    //     $filter['refund_status'] = '<=:0';
    //     $filter['staff_id'] = 0;    // 0等待接单
    //     $shop_ids = array();
    //     $orderby = array('dateline' => 'ASC');
    //     if($title = $this->GP('title')){
    //        $waimai_count = K::M('waimai/waimai')->count(array('group_id'=>$this->group_id));
    //        $filter_shop = array();
    //        $filter_shop['group_id'] = $this->group_id;
    //        $filter_shop[':OR'] = array(
    //            'title'=>"LIKE:%".$title."%",
    //            'phone'=>"LIKE:%".$title."%"
    //        );
    //        $waimai_list = K::M('waimai/waimai')->items($filter_shop,array('shop_id'=>"DESC"),1,$waimai_count);
    //        foreach ($waimai_list as $k1=>$v1){
    //            $shop_ids[$v1['shop_id']] = $v1['shop_id'];
    //        }
    //        if($shop_ids){
    //            $filter['shop_id'] = $shop_ids;
    //        }
    //     }

    //     if($items = K::M('order/order')->items($filter,$orderby,1,500,$count)){
    //         foreach ($items as $k=>$v){
    //             if($v['pei_time']>0){
    //                 $items[$k]['day_num'] = $v['day_num'].'[预]';
    //             }else if($v['pei_time']==0){
    //                 $items[$k]['day_num'] = $v['day_num'];
    //             }
    //             $items[$k]['show_time'] = date('m-d H:i:s',$v['dateline']);
    //             if($v['from']=='waimai'){
    //                 $items[$k]['expect_label'] =$v['expect_time']? date('m-d H:i',$v['expect_time']):"尽快送达";
    //                 $waimai_ids[$v['shop_id']] = $v['shop_id'];
    //             }else if($v['from']=='paotui'){
    //                 $items[$k]['expect_label']  = "尽快送达";
    //                 $items[$k]['o_lng'] = $v['o_lng']? $v['o_lng']:$v['lng'];
    //                 $items[$k]['o_lat'] = $v['o_lat']? $v['o_lat']:$v['lat'];
    //                 $paotui_ids[] = $v['order_id'];
    //             }
    //             if($v['from']=='waimai'){
    //               if($v['expect_time']>0&&$v['expect_time']<__TIME){
    //                   $items[$k]['time_label'] = "已超时:".$this->formatTime(__TIME-$v['expect_time']);
    //                   $items[$k]['label_time'] = "预计:".date('m-d H:i',$v['expect_time']).'送达';
    //               }else if($v['expect_time']>0&&$v['expect_time']>=__TIME){
    //                   $items[$k]['time_label'] = "距超时:".$this->formatTime($v['expect_time']-__TIME);
    //                   $items[$k]['label_time'] = "预计:".date('m-d H:i',$v['expect_time']).'送达';
    //               }else{
    //                   $items[$k]['label_time'] = "";
    //                   $items[$k]['time_label'] = "----";
    //               }
    //             }else{
    //                 $items[$k]['label_time'] = "";
    //                 $items[$k]['time_label'] = "----";
    //             }
    //         }
    //         $waimai_list = K::M('waimai/waimai')->items_by_ids($waimai_ids);
    //         foreach($items as $kk=>$vv){
    //             $items[$kk]['shop_addr'] = $waimai_list[$vv['shop_id']]['addr'] ? $waimai_list[$vv['shop_id']]['addr'] : '';
    //             $items[$kk]['shop_title'] = $waimai_list[$vv['shop_id']]['title'] ? $waimai_list[$vv['shop_id']]['title'] : '';
    //         }
    //         $paotui_order_list = K::M('paotui/order')->items_by_ids($paotui_ids);
    //         foreach($items as $kkk=>$vvv){
    //             foreach($paotui_order_list as $key=>$val){
    //                 if($vvv['order_id']==$val['order_id']){
    //                     $items[$kkk]['shop_addr'] = $val['o_addr']?$val['o_addr']:"就近购买";
    //                 }
    //             }
    //         }
    //     }
    //     //K::M('system/logs')->log('log',$this->system->db->SQLLOG());
    //     if($title&&!$shop_ids){
    //         $this->msgbox->set_data('data',array());
    //     }else{
    //         $this->msgbox->set_data('data',array_values($items));
    //     }
    //     $this->msgbox->json();
    // }

    // public function formatTime($strtime)
    // {
    //     $label = '';
    //     $minutes = intval($strtime/60);
    //     if($strtime<60){
    //         $label = $strtime.'秒';
    //     }else if($minutes < 60){
    //         $label = $minutes.'分钟';
    //     }else if($minutes < 60*24){
    //         $h = intval($minutes/60);
    //         $m = $minutes%60;
    //         $label = $h.'小时'.$m.'分钟';
    //     }else{
    //         $d = intval($minutes/(60*24));
    //         $h = intval(intval(($minutes%(60*24)))/60);
    //         $m = intval(intval(($minutes%(60*24)))%60);
    //         $label = $d.'天'.$h.'小时';
    //     }
    //     return $label;
    // }

    // public function loadstaff(){
    //     $filter = array();
    //     $filter['status'] = 1 ;
    //     $filter['from'] = 'paotui';
    //     $filter['group_id'] = $this->group_id;
    //     $filter['closed'] = 0;
    //     $filter['audit'] = 1;
    //     $filter['lastlogin'] = '>:0';
    //     $staff_ids = array();
    //     $limit_order = -1;
    //     if($so = $this->GP('SO')){
    //         if($so['title']){
    //             $filter[':OR'] = array(
    //                 'name'=>"LIKE:%".$so['title'].'%',
    //                 'mobile'=>"LIKE:%".$so['title'].'%'
    //             );
    //         }
    //         $limit_order = $so['limit_order'];
    //     }
    //     $level_ids = array();
    //     $staff_count = K::M('staff/staff')->count(array('group_id'=>$this->group_id));
    //     if($staff = K::M('staff/staff')->items($filter,null,1,$staff_count,$count)){
    //         foreach ($staff as $k=> $v){
    //             $location = K::M('helper/date')->bd_decrypt($v['lng'],$v['lat']);
    //             $staff[$k]['lng'] = $location['gg_lon'];
    //             $staff[$k]['lat'] = $location['gg_lat'];
    //             $staff_ids[] = $v['staff_id'];
    //             $level_ids[$v['level_id']] = $v['level_id'];
    //         }
    //         $level_list = K::M('staff/level')->items_by_ids($level_ids);
    //         //待取： 待送达：
    //         //  -1:配送员弃单   0：未处理，1：已接单（跑腿订单已接单）  3：配送开始（跑腿服务中），  4：配送完成（跑腿服务完成），8：订单完成
    //         $filter_no = $filter_yes = array();
    //         $filter_no['from']= array('waimai','paotui');
    //         $filter_no['staff_id'] = $filter_yes['staff_id'] = $staff_ids;
    //         //$filter_no['order_status'] = array(1,2);
    //         $filter_no[':SQL'] = "((`from`='waimai' AND `order_status` in (2)) OR (`from`='paotui' AND `order_status` in (1)))";
    //         $filter_yes['order_status'] = 3;
    //         $filter_yes['from'] = array('waimai','paotui');

    //         //配送员代取
    //         $order_no = K::M('order/order')->items_group_by_staff($filter_no);
    //         //待送达：
    //         $order_yes =  K::M('order/order')->items_group_by_staff($filter_yes);
    //         foreach ($staff as $k=>$v){
    //             $staff[$k]['level_name'] = $level_list[$v['level_id']]['title']?$level_list[$v['level_id']]['title']:"--";
    //             if($order_yes[$v['staff_id']]){
    //                 $staff[$k]['ds_order'] = $order_yes[$v['staff_id']]['orders'];
    //             }else{
    //                 $staff[$k]['ds_order'] = 0;
    //             }
    //             if($order_no[$v['staff_id']]){
    //                 $staff[$k]['dq_order'] = $order_no[$v['staff_id']]['orders'];
    //             }else{
    //                 $staff[$k]['dq_order'] = 0;
    //             }

    //             if($limit_order==0){
    //                 if(($staff[$k]['dq_order']+$staff[$k]['ds_order'])>0){
    //                     unset($staff[$k]);
    //                 }
    //             }else if($limit_order==1){
    //                 if(($staff[$k]['dq_order']+$staff[$k]['ds_order'])>=2){
    //                     unset($staff[$k]);
    //                 }
    //             }else if($limit_order==3){
    //                 if(($staff[$k]['dq_order']+$staff[$k]['ds_order'])>=4){
    //                     unset($staff[$k]);
    //                 }
    //             }else if($limit_order==5){
    //                 if(($staff[$k]['dq_order']+$staff[$k]['ds_order'])>=6){
    //                     unset($staff[$k]);
    //                 }
    //             }else if($limit_order==7){
    //                 if(($staff[$k]['dq_order']+$staff[$k]['ds_order'])>=8){
    //                     unset($staff[$k]);
    //                 }
    //             }
    //         }
    //     }else{
    //         $staff = array();
    //     }
    //     $this->msgbox->set_data('data',array_values($staff));
    //     $this->msgbox->json();
    // }


    // public function point_order(){
    //     if($data = $this->checksubmit('data')){
    //        if(!$staff_id = $data['staff_id']){
    //            $this->msgbox->add('未指定配送员',202);
    //        }else if(!$staff = K::M('staff/staff')->detail($staff_id)){
    //            $this->msgbox->add('指定的配送员不存在',203);
    //        }else if($staff['status']==0){
    //            $this->msgbox->add('该配送员已休息，不可指定',204);
    //        }else if($staff['group_id']!=$this->group_id){
    //            $this->msgbox->add("指定的配送员不属于您的配送站",205);
    //        }else if(!$order_ids = $data['order_ids']){
    //            $this->msgbox->add('未指定指派的订单',206);
    //        }else if(!$order_list = K::M('order/order')->items_by_ids($order_ids)){
    //            $this->msgbox->add('指派的订单不存在',207);
    //        }else{
    //            $last_time = $this->get_lasttime();
    //            foreach ($order_list as $k=>$v){
    //                if($v['online_pay']==1&&$v['pay_status']==0){
    //                    $this->msgbox->add('有订单未支付',208)->response();
    //                }else if($v['tmp_ltime']>__TIME){
    //                    $this->msgbox->add('有订单指派未过期',209)->response();
    //                }
    //                if($v['from']=='waimai'){
    //                    $waimai = K::M('waimai/waimai')->detail($v['shop_id']);
    //                    if($data['check']==1){
    //                        $true_staff_id =  $staff_id;
    //                        $jd_time = __TIME;

    //                    }else{
    //                        $true_staff_id = 0;
    //                        $jd_time = 0;
    //                    }
    //                    $order_status = 2;
    //                }else{
    //                    $paotui_order = K::M('paotui/order')->detail($v['order_id']);
    //                    if($data['check']==1){
    //                        $true_staff_id  = $staff_id;
    //                        $order_status = 1;
    //                        $jd_time = __TIME;
    //                    }else{
    //                        $true_staff_id = 0;
    //                        $order_status = 0;
    //                        $jd_time = 0;
    //                    }
    //                }
    //                if(K::M('order/order')->update($v['order_id'],array('tmp_staff_id'=>$staff_id,'tmp_ltime'=>__TIME+$last_time,'staff_id'=>$true_staff_id,'order_status'=>$order_status,'jd_time'=>$jd_time))){
    //                    if($data['check']==1){
    //                        K::M('order/time')->update($v['order_id'],array('staff_jiedan_time'=>__TIME));
    //                    }
    //                    $order_log = array();
    //                    $order_log['order_id'] = $v['order_id'];
    //                    $order_log['status'] = 0;
    //                    $order_log['from'] = 'admin';
    //                    $order_log['log'] = '指派订单ID('.$v['order_id'].')给骑手ID'.$staff['name'].'('.$staff_id.')成功';
    //                    $liuyan =trim($data['message']);
    //                    $intro = '';
    //                    if($v['from']=='waimai'){
    //                        if($liuyan){
    //                            $text = '取货地址:'.$waimai['addr'].'备注:('.$liuyan.')';
    //                            $intro = $liuyan;
    //                        }else{
    //                            $text = '取货地址:'.$waimai['addr'];
    //                        }
    //                    }else{
    //                        $addr = $paotui_order['o_addr']?$paotui_order['o_addr']:"就近购买";
    //                        if($liuyan){
    //                            $text = '取货地址:'.$addr.'备注:('.$liuyan.')';
    //                            $intro = $liuyan;
    //                        }else{
    //                            $text = '取货地址:'.$addr;
    //                        }
    //                    }
    //                    K::M('order/log')->create($order_log);
    //                    if($data['check']==1){
    //                        if($other_order = K::M('other/order')->find(array('p_order_id'=>$v['order_id']))){
    //                            K::M('order/order')->update($other_order['order_id'],array('staff_id'=>$staff_id));
    //                            K::M('order/time')->update($other_order['order_id'],array('staff_jiedan_time'=>__TIME));
    //                            $order_log['order_id'] = $other_order['order_id'];
    //                            K::M('order/log')->create($order_log);
    //                        }
    //                        K::M('staff/staff')->send($staff_id,'系统派单',$text,array('type'=>'newOrder','order_id'=>$v['order_id']));
    //                    }else{
    //                        K::M('staff/staff')->send($staff_id,'系统派单',$text,array('type'=>'paiOrder','order_id'=>$v['order_id']));
    //                    }
    //                    if($data['check']==1){
    //                        $accept = 1;
    //                    }else{
    //                        $accept = 0;
    //                    }
    //                    $tongji_data = array(
    //                        'pai'=>1,
    //                        'accept'=>$accept,
    //                        'refuse'=>0,
    //                        'staff_id'=>$staff_id,
    //                    );
    //                    $paidan_log = array(
    //                        'order_id'=>$v['order_id'],
    //                        'intro'=>$intro
    //                    );

    //                    K::M('staff/paidanlog')->create($paidan_log);
    //                    K::M('staff/paidan')->create($tongji_data);

    //                }else{
    //                    $this->msgbox->add('指派失败',210)->response();
    //                }
    //            }
    //            $this->msgbox->add('指派成功');
    //        }

    //     }else{
    //         $this->msgbox->add('非法数据请求',201);
    //     }
    // }

    // public function get_lasttime(){
    //     $overtime = $this->group['overtime']?$this->group['overtime']:5;
    //     return $overtime*60;
    // }

    // public function get_staff_order($staff_id){
    //     if(!$staff_id){
    //         $this->msgbox->add('配送员不存在',201);
    //     }else if(!$staff = K::M('staff/staff')->detail($staff_id)){
    //         $this->msgbox->add('配送员不存在',205);
    //     }else if($staff['group_id']!=$this->group['group_id']){
    //         $this->msgbox->add('不可查看其他配送站订单',202);
    //     }else {
    //         $filter = array();
    //         $filter['staff_id'] = $staff_id;
    //         //$filter['order_status'] = array(2,3);
    //         $filter[':SQL'] = "((`from`='waimai' AND `order_status` in (2,3)) OR (`from`='paotui' AND `order_status` in (1,3)))";
    //         if($items = K::M('order/order')->items($filter,array('order_id'=>'DESC'),1,999,$count)){
    //            foreach ($items as $k=>$v){
    //                $items[$k]['o_lng'] = $v['o_lng']?$v['o_lng']:$v['lng'];
    //                $items[$k]['o_lat'] = $v['o_lat']?$v['o_lat']:$v['lat'];
    //            }
    //         }
    //         $this->msgbox->add('success');
    //         $this->msgbox->set_data('data',array_values($items));
    //         $this->msgbox->json();
    //     }
    // }
}