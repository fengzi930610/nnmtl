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
$filter = array();
$filter['status'] = 0;
$filter['bills_sn']  = "<:".date('Ymd');
/*$page = 1;
$success_bills = $fail_bills = array();
if(K::M('waimai/config')->auto_bills()){
    while($items = K::M('waimai/bills')->items($filter,array(),$page,50,$count)){
        foreach ($items as $k=>$v){
            if( K::M('waimai/bills')->entry($v['bills_id'])){
                $success_bills[] = array(
                    'bills_id'=>$v['bills_id']
                );
            }else{
                $fail_bills[] = array(
                    'bills_id'=>$v['bills_id'],
                );
            }
        }
        $page++;
    }
    K::M('system/logs')->log('bills_entry_'.date('Ymd'), array('success'=>$success_bills,'error'=>$fail_bills));
}*/

$page_shop = $page_staff = 1;
$success_shop = $fail_shop = $success_staff = $fail_staff = array();
$config = K::M('system/config')->get('waimaihuodongconfig');
if($config['autobills'] == 1){
    while($items = K::M('waimai/bills')->items($filter,array(),$page,50,$count)){
        foreach ($items as $k=>$v){
            if( K::M('waimai/bills')->entry($v['bills_id'])){
                $success_shop[] = $v['bills_id'];
            }else{
                $fail_shop[] = $v['bills_id'];
            }
        }
        $page_shop++;
    }
    K::M('system/logs')->log('bills_shop_entry_'.date('Ymd'), array('success'=>$success_shop,'error'=>$fail_shop));
}

if($config['autobills_staff'] == 1){
    while($items = K::M('staff/bills')->items($filter,array(),$page,50,$count)){
        foreach ($items as $k=>$v){
            if( K::M('staff/bills')->entry($v['bills_id'])){
                $success_staff[] = $v['bills_id'];
            }else{
                $fail_staff[] = $v['bills_id'];
            }
        }
        $page_staff++;
    }
    K::M('system/logs')->log('bills_staff_entry_'.date('Ymd'), array('success'=>$success_staff,'error'=>$fail_staff));
}


