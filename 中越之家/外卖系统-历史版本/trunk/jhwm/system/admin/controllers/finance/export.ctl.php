<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/25
 * Time: 15:25
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
//导出


class Ctl_Finance_Export extends Ctl {

    public function waimai(){
        if($data = $this->checksubmit('data')){
            $filter = array();
            if($data['shop_id']){
                $filter['shop_id'] = $data['shop_id'];
            }
            if($data['status']==1){
                //已入账
                $filter['status'] = 1;
            }else if($data['status']==2){
                //未入账
                $filter['status'] = 0;
            }
            if($data['dateline'][0]&&$data['dateline'][1]){
               $a = strtotime($data['dateline'][0]);
               $b = strtotime($data['dateline'][1])+86399;
               $filter['dateline'] = $a.'~'.$b;
            }else if(!$data['dateline'][0]&&$data['dateline'][1]){
                $b = strtotime($data['dateline'][1])+86399;
                $filter['dateline'] = "<:".$b;
            }else if($data['dateline'][0]&&!$data['dateline'][1]){
                $a = strtotime($data['dateline'][0]);
                $filter['dateline'] = ">:".$a;
            }
            if($items = K::M('waimai/bills')->items($filter,array('bills_id'=>"DESC"),1,3000,$count)){
                if($count>3000){
                    $this->msgbox->add("需要导出的对账单超过3000条，请修改搜索条件",202)->response();
                }else{
                    $shop_ids = array();
                    foreach ($items as $k=>$v){
                        $shop_ids[$v['shop_id']] = $v['shop_id'];
                    }
                    $waimai_shop_list = K::M('waimai/waimai')->items_by_ids($shop_ids);
                    $file_name = "商家对账";
                    $key_arr = array(
                       "账单日期","	账单商家","入账状态","平台应得","用户支付","平台补贴活动款","商家应得","配送费","平台配送费","配送附加费"
                    );
                    $row = array();
                    foreach ($items as $k=>$v){
                        $row[] = array(
                            $v['bills_sn'],
                            $waimai_shop_list[$v['shop_id']]['title'],
                            $status = $v['status']==1?"已入账":"未入账",
                            $v['fee']+$v['sys_freight'] + $v['freight_addone'],
                            $v['user_amount'],
                            $v['roof_amount'],
                            $v['amount'],
                            $v['freight'],
                            $v['sys_freight'],
                            $v['freight_addone']
                        );
                    }
                    K::M('dataio/xls')->export($key_arr, $row, $file_name);
                }
            }else{
                $this->msgbox->add('未找到需要导出的对账单',201);
            }

        }else{
            $this->tmpl = "admin:finance/export/waimai.html";
        }
    }


    public function staff(){
        if($data = $this->checksubmit('data')){
          //  Array ( [staff_id] => 1 [status] => -1 [dateline] => Array ( [0] => 2017-10-04 [1] => 2017-11-03 ) )
           $filter = array();
            if($data['staff_id']){
                $filter['staff_id'] = $data['staff_id'];
            }
            if($data['status']==1){
                //已入账
                $filter['status'] = 1;
            }else if($data['status']==2){
                //未入账
                $filter['status'] = 0;
            }
            if($data['dateline'][0]&&$data['dateline'][1]){
                $a = strtotime($data['dateline'][0]);
                $b = strtotime($data['dateline'][1])+86399;
                $filter['dateline'] = $a.'~'.$b;
            }else if(!$data['dateline'][0]&&$data['dateline'][1]){
                $b = strtotime($data['dateline'][1])+86399;
                $filter['dateline'] = "<:".$b;
            }else if($data['dateline'][0]&&!$data['dateline'][1]){
                $a = strtotime($data['dateline'][0]);
                $filter['dateline'] = ">:".$a;
            }
            if($items = K::M('staff/bills')->items($filter,array('bills_id'=>"DESC"),1,3000,$count)){
                if($count>3000){
                    $this->msgbox->add('需要导出的对账单大于3000条，请修改筛选条件',202);
                }else{
                    $staff_ids = array();
                    foreach ($items as $k=>$v){
                        $staff_ids[$v['staff_id']] = $v['staff_id'];
                    }
                    $staff_list = K::M('staff/staff')->items_by_ids($staff_ids);
                    $file_name = "配送员对账";
                    $key_arr = array(
                        "账单日期","	账单配送员","入账状态","配送费","平台抽成","配送员应得"
                    );
                    $row = array();
                    foreach ($items as $k1=>$v1){
                        $row[] = array(
                            $v1['bills_sn'],
                            $staff_list[$v1['staff_id']]['name'],
                            $status = $v1['status']==1?"已入账":"未入账",
                            $v1['freight_amount'],
                            $v1['freight_amount']-$v1['amount'],
                            $v1['amount'],
                        );

                    }
                    K::M('dataio/xls')->export($key_arr, $row, $file_name);

                }

            }else{
                $this->msgbox->add('未找到需要导出的对账单',201);
            }


        }else{
            $this->tmpl = "admin:finance/export/staff.html";
        }

    }

    public function jifen(){

    }

    public function chongzhi(){

    }


    public function cash(){

        if($SO=$this->checksubmit('data')){
            $filter = array();
            if($SO['stime']&&$SO['ltime']){
                $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }
            if(!$SO['stime']&&$SO['ltime']){
                $filter['dateline'] = "<:".(strtotime($SO['ltime'])+86399);
            }
            if($SO['stime']&&!$SO['ltime']){
                $filter['dateline'] = ">:".strtotime($SO['stime']);
            }
            if($SO['staff_id']){
                $filter['staff_id'] = $SO['staff_id'];
            }
            if($items = K::M('cash/bills')->items($filter,array('bills_id'=>"DESC"),1,5000,$count)){
                if(!$count){
                    $this->msgbox->add('未找到需要导出的对账单',201);
                }else if($count>5000){
                    $this->msgbox->add('需要导出的对账单超过5000条，请修改筛选条件',202);
                }else{
                    $staff_ids = array();
                    foreach ($items as $k=>$v){
                        $staff_ids[$v['staff_id']] = $v['staff_id'];
                    }
                    $staff_list = K::M('staff/staff')->items_by_ids($staff_ids);
                    foreach ($items as $k1=>$v1){
                        $items[$k1]['staff'] = $staff_list[$v1['staff_id']];
                        $items[$k1]['str'] = $v1['status']==1?"已上缴":"未上缴";
                    }

                    $file_name = "配送员代收款对账";
                    $key_arr = array(
                        "账单日期","	代收配送员","平台应得","用户支付(需要上缴)","配送费（元）",'上缴状态'
                    );
                    $row = array();
                    foreach ($items as $k2=>$v2){
                        $row[] = array(
                            $v2['bills_sn'],
                            $v2['staff']['name'].'('.$v2['staff']['mobile'].')',
                            $v2['fee'],
                            $v2['amount'],
                            $v2['pei_amount'],
                            $v2['str']
                        );
                    }

                    K::M('dataio/xls')->export($key_arr, $row, $file_name);
                }
            }
        }else{
            $this->tmpl = "admin:finance/export/cash.html";

        }
    }

}