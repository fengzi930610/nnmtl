<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/22
 * Time: 10:24
 */
/*if(strtolower(php_sapi_name()) != 'cli'){
    exit('only run cli');
}*/
@ini_set("display_errors", "On");
@error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT ^ E_WARNING);;
@set_time_limit(0);
@ini_set('memory_limit','128M');
@ini_set('allow_url_fopen', 'On');
@date_default_timezone_set('Asia/Shanghai');
require(dirname(__DIR__).'/system/home/index.php');
$system = new Index('magic-shell');

$whcfg = K::M('system/config')->get('waimaihuodongconfig');
if($whcfg['autopei']){
    $autopei_time = $whcfg['autopei_time'] ? $whcfg['autopei_time'] : 30;
    $filter = $success = $fail = array();
    $filter['order_status'] = 1;
    $filter[':OR'] = array('pay_status'=>1,'online_pay'=>0,);// 已付款 || 货到付款
    $filter['return_status'] = '<>:1';
    //$filter['pei_time'] = '>:0';
    //$filter['pei_time'] = '<=:'.(__TIME+$autopei_time*60);
    $filter[':SQL'] = "`pei_time`>0 AND `pei_time`<=".(__TIME+$autopei_time*60);
    $filter['closed'] = 0;
    $filter['from'] = 'waimai';
    $page = 1;
    while($items = K::M('order/order')->items($filter,array(),$page,50,$count)){
        foreach ($items as $k=>$v){
            if( K::M('waimai/order')->autopei($v['order_id'])){
                $success[$v['shop_id']][] = array(
                    'order_id'=>$v['order_id']
                );
            }else{
                $fail[$v['shop_id']][] = array(
                    'order_id'=>$v['order_id'],
                );
            }
        }
        $page++;
    }
    K::M('system/logs')->log('autopei_'.date('Ymd'), array('success'=>$success,'error'=>$fail));
}








