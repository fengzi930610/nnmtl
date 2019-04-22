<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/31
 * Time: 17:59
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Config extends Model {

    //修改为根据
    public function getfright($group_id = 0){

        if($freight = K::M('pei/group')->get_cache($group_id)){
            if($freight['badweather']['config']&&$freight['badweather']['is_used']==1){
                return $freight['badweather']['config'];
            }
            if($freight['timeconfig']['time']['stime']&&$freight['timeconfig']['time']['ltime']&&$freight['timeconfig']['is_used']==1&&$freight['timeconfig']['config']){
                $stime = strtotime($freight['timeconfig']['time']['stime']);
                $ltime = strtotime($freight['timeconfig']['time']['ltime']);
                $todaystime = strtotime(date('Y-m-d'));
                $todayltime =  $todaystime+86399;
                if($stime>$ltime){
                    if(($stime<__TIME&&$todayltime>__TIME)||($todaystime<__TIME&&$ltime>__TIME)){
                        return$freight['timeconfig']['config'];
                    }else{
                        return $freight['baseconfig'];
                    }
                }else if($stime<$ltime){
                    if($stime<__TIME&&$ltime>__TIME){
                        return$freight['timeconfig']['config'];
                    }else{
                        return $freight['baseconfig'];
                    }
                }else{
                    return $freight['baseconfig'];
                }
            }else{
                return $freight['baseconfig'];
            }
        }else{
            return array();
        }
    }

    public function return_web_config(){
        $freight = K::M('system/config')->get('fright');
        $freighttime = K::M('system/config')->get('frighttime');
        if($freighttime['stime']&&$freighttime['ltime']&&$freighttime['config']&&$freighttime['used']){
            $stime = strtotime($freighttime['stime']);
            $ltime = strtotime($freighttime['ltime']);
            if($stime>$ltime){
                $todaystime = strtotime(date('Y-m-d'));
                $todayltime =  $todaystime+86399;
                if(($stime<__TIME&&$todayltime>__TIME)||($todaystime<__TIME&&$ltime>__TIME)){
                    return $freighttime['config'];
                }else{
                    return $freight;
                }
            }else if($stime<$ltime){
                if($stime<__TIME&&$ltime>__TIME){
                    return $freighttime['config'];
                }else{
                    return $freight;
                }
            }else{
                return $freight;
            }
        }else{
            return $freight;
        }
    }

    public function gethuodongconfig($key){
        if(!$waimai_config = K::M('system/config')->get('waimaihuodongconfig')){
            return false;
        }else if($key){
            return $waimai_config[$key];
        }else{
            $tmp_config = array();
            $tmp_config['hongbao'] = $waimai_config['hongbao'];
            $tmp_config['first'] = $waimai_config['first'];
            $tmp_config['manjian'] = $waimai_config['manjian'];
            $tmp_config['youhui'] = $waimai_config['youhui'];
            $tmp_config['manfan'] = $waimai_config['manfan'];
            $tmp_config['first_share'] = $waimai_config['first_share'];
            return $tmp_config;
        }
    }

    public function gettimeoutconfig(){
        if(!$waimai_config = K::M('system/config')->get('waimaihuodongconfig')){
            return 30;
        }else{
            return $waimai_config['timeout'];
        }
    }

    public function getsiteopen(){
        if(!$waimai_config = K::M('system/config')->get('waimaihuodongconfig')){
            return false;
        }else{
            return $waimai_config['opendaofu'];
        }
    }

    public function getordervoice(){
        if(!$waimai_config = K::M('system/config')->get('waimaihuodongconfig')){
           return array(
               'neworder'=>0,
               'cuiorder'=>0,
               'tuiorder'=>0
           );
        }else{
            return array(
                'neworder'=>$waimai_config['neworder'],
                'cuiorder'=>$waimai_config['cuiorder'],
                'tuiorder'=>$waimai_config['tuiorder']
            );
        }
    }

    //获取外卖订单流程走向 外卖3.7
    public function getPokemanGo(){
        if(!$waimai_config = K::M('system/config')->get('waimaihuodongconfig')){
            return false;
        }else{
            return $waimai_config['go']==1?true:false;
        }

    }

    //获取是否显示
    public function get_hot_show(){
        if($show_config = K::M('system/config')->get('waimaihuodongconfig')){
         //   if($show_config)
           if($show_config['show']==1){
               return false;
           }else{
               return true;
           }
        }else{
            return true;
        }
    }

    //返回参数 是否允许用户
    public function allow_user_tixian(){
        if($show_config = K::M('system/config')->get('waimaihuodongconfig')){
            if($show_config['user_tixian']==1){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function auto_bills(){
        if($auto_bills = K::M('system/config')->get('waimaihuodongconfig')){
            if($auto_bills['autobills']==1){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function auto_bills_staff(){
        if($auto_bills = K::M('system/config')->get('waimaihuodongconfig')){
            if($auto_bills['autobills_staff']==1){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
