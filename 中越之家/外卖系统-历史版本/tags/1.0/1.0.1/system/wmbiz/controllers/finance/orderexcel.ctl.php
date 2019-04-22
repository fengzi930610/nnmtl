<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/17
 * Time: 15:28
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Finance_Orderexcel extends Ctl{

    public function output(){
       $key_arr = array(
           '编号','入账状态','商家应得','用户支付','平台手续费','活动款（平台补贴）','配送费','时间'
       );
       $file_name = '订单对账';
       $status = $this->GP('status');
       $stime  = $this->GP('stime');
       $ltime  = $this->GP('ltime');
       $filter = array();
        $filter['shop_id'] = $this->shop_id;
       if($status==2){
           $filter['status'] = 0;
       }else if($status==3){
           $filter['status'] = 1;
       }
       if($stime){
           $filter['dateline'] = '>:'.strtotime($stime);
       }
       if($ltime){
           $filter['dateline'] = '<:'.(strtotime($stime)+86399);
       }
       if($stime&&$ltime){
           $filter['dateline'] = strtotime($stime).'~'.(strtotime($stime)+86399);
        }
        if(!$items=K::M('waimai/billslog')->items($filter,array('log_id'=>'desc'),1,9999,$count)){
           $this->msgbox->add('没有找到数据',250)->response();
           $this->msgbox->set_data('forward',$this->mklink('finance/balance/index'));
        }
        $row = array();
        foreach ($items as $k=>$v){
            if($v['status']==0){
                $str = '未入账';
            }else{
                $str = '已入账';
            }
            $data = array(
                $v['log_id'],$str,$v['amount'],$v['amount']+$v['fee']-$v['roof_amount'],$v['fee'],$v['roof_amount'],$v['freight'],date('Y-m-d H:i',$v['dateline'])
            );
            $row[]=$data;
        }
        K::M('dataio/xls')->export($key_arr,$row,$file_name);
    }

    public function qiang_output()
    {
       $key_arr = array(
           '编号','入账状态','商家应得','用户支付','平台手续费','运费','时间'
       );
       $file_name = '订单对账';
       $status = $this->GP('status');
       $stime  = $this->GP('stime');
       $ltime  = $this->GP('ltime');
       $filter = array();
        $filter['shop_id'] = $this->shop_id;
       if($status==2){
           $filter['status'] = 0;
       }else if($status==3){
           $filter['status'] = 1;
       }
       if($stime){
           $filter['dateline'] = '>:'.strtotime($stime);
       }
       if($ltime){
           $filter['dateline'] = '<:'.(strtotime($stime)+86399);
       }
       if($stime&&$ltime){
           $filter['dateline'] = strtotime($stime).'~'.(strtotime($stime)+86399);
        }
        if(!$items=K::M('qiang/billslog')->items($filter,array('log_id'=>'desc'),1,9999,$count)){
           $this->msgbox->add('没有找到数据',250)->response();
           $this->msgbox->set_data('forward',$this->mklink('finance/qiang_balance/index'));
        }
        $row = array();
        foreach ($items as $k=>$v){
            if($v['status']==0){
                $str = '未入账';
            }else{
                $str = '已入账';
            }
            $data = array(
                $v['log_id'],$str,$v['amount'],$v['amount']+$v['fee'],$v['fee'],$v['freight'],date('Y-m-d H:i',$v['dateline'])
            );
            $row[]=$data;
        }
        K::M('dataio/xls')->export($key_arr,$row,$file_name);
    }
    

}