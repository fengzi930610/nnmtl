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
class Ctl_Tongji_Export extends Ctl {

    public function yy_tongji()
    {
        if($time = $this->GP('time')){
            if($time==1){
                //昨日
                $step_first = strtotime(date('Y-m-d')."-1 day");
                $step_last = (strtotime(date('Y-m-d'))-1);
                $time_label = '(昨日)';
            }else if($time==2){
                //今天
                $step_first = strtotime(date('Y-m-d'));
                $step_last = (strtotime(date('Y-m-d'))+86399);
                $time_label = '(今日)';
            }else if($time==3){
                //本周
                $w_dateline = K::M('helper/date')->get_date_filter('l');
                $arr = explode("~",$w_dateline['t']);
                $step_first = $arr[0];
                $step_last = $arr[1];
                $time_label = '(本周)';
            }else{
                //指定月份
                $step_first = strtotime(date('Y-m-01',strtotime($time)));  //获取本月第一天时间戳
                $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01',$step_first)." +1 month")-1)))+86399;
                $time_label = "({$time})";
            }
            $filter = array('dateline'=>$step_first.'~'.$step_last);
            $data = array();
            $data['外卖'] = K::M('site/tongji')->sum_by_filter(array('dateline'=>$step_first.'~'.$step_last,'from'=>'waimai'));
            $data['跑腿'] = K::M('site/tongji')->sum_by_filter(array('dateline'=>$step_first.'~'.$step_last,'from'=>'paotui'));
            $data['抢购'] = K::M('site/tongji')->sum_by_filter(array('dateline'=>$step_first.'~'.$step_last,'from'=>'qiang'));
            $data['合计'] = K::M('site/tongji')->sum_by_filter(array('dateline'=>$step_first.'~'.$step_last));

            $file_name = "营业统计".$time_label;
            $key_arr = array(
                "平台", "订单数(单)", "营业额(￥)", "商家收入(￥)", "骑手收入(￥)", "平台抽佣(￥)", "红包补贴(￥)", "首单补贴(￥)", "满减补贴(￥)", "骑手补贴(￥)", "配送会员卡补贴(￥)", "平台盈利(￥)"
            );
            $row = array();
            foreach ($data as $k=>$v){
                $row[] = array(
                    $k,
                    $v['orders'],
                    $v['amount'],
                    $v['shop_amount'],
                    $v['staff_amount'],
                    $v['site_fee'],
                    $v['platform_hongbao'],
                    $v['platform_first'],
                    $v['platform_mj'],
                    $v['platform_staff'],
                    $v['platform_peicard'],
                    $v['yinli'],                    
                );
            }
            K::M('dataio/xls')->export($key_arr, $row, $file_name);
        }
    }


    public function subsidy($from='staff')
    {
        switch ($from) {
            case 'staff':
                $this->subsidy_staff();
                break;
            case 'member':
                $this->subsidy_member();
                break;
            case 'waimai':
                $this->subsidy_waimai();
                break;
            default:
                $this->subsidy_staff();
                break;
        }
    }

    public function subsidy_staff(){
        $filter = array();
        $step_first = strtotime(date('Y-m-01'));  //获取本月第一天时间戳
        $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01')." +1 month")-1)))+86399;
        $filter['dateline'] = $step_first.'~'.$step_last;
        if($SO = $this->GP("SO")){
            if($SO['mouth']){
                $step_first = strtotime(date('Y-m-01',strtotime($SO['mouth'])));  //获取本月第一天时间戳
                $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01',$step_first)." +1 month")-1)))+86399;
                $filter['dateline'] = $step_first.'~'.$step_last;
                $time_label = "({$SO['mouth']})";
            }
            if($SO['staff_id']){
                $filter['staff_id'] = $SO['staff_id'];
            }
        }
        if($subsidy_list = K::M('subsidy/staff')->items_join_by_staff_id($filter, 1, 50, array('day'=>"DESC"), $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            $staff_ids = $staff_level_ids = array();
            foreach ($subsidy_list as $k=>$v){
                $staff_ids[$v['staff_id']] =$v['staff_id'];
            }
            $staff_list = K::M('staff/staff')->items_by_ids($staff_ids);
            foreach ($staff_list as $kk=>$vv){
                $staff_level_ids[$vv['level_id']] = $vv['level_id'];
            }
            $level_list = K::M('staff/level')->items_by_ids($staff_level_ids);
            foreach ($staff_list as $kkk=>$vvv){
                $staff_list[$kkk]['level'] = $level_list[$vvv['level_id']];
            }
            foreach ($subsidy_list as $k1=>$v1){
                $subsidy_list[$k1]['staff'] = $staff_list[$v1['staff_id']];
            }
        }

        $sum_diff_amount = K::M('subsidy/staff')->sum($filter,'diff_amount'); //补贴总金额
        $count_diff = K::M('subsidy/staff')->count($filter);  //补贴订单数

        $file_name = "骑手补贴统计".$time_label;
        $key_arr = array("日期", "骑手名", "骑手手机", "骑手等级", "补贴订单数", "补贴金额(￥)");
        $row = array(array('总计', '----', '----', '----', $count_diff, $sum_diff_amount));
        foreach ($subsidy_list as $k=>$v){
            $row[] = array(
                $v['day'],
                $v['staff']['name'],
                $v['staff']['mobile'],
                $v['staff']['level']['title'] ? $v['staff']['level']['title'] : '暂未绑定',
                $v['count'],
                $v['amount'],                 
            );
        }
        K::M('dataio/xls')->export($key_arr, $row, $file_name);

    }

    public function subsidy_member()
    {
        $filter = array();
        $step_first = strtotime(date('Y-m-01'));  //获取本月第一天时间戳
        $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01')." +1 month")-1)))+86399;
        $filter['dateline'] = $step_first.'~'.$step_last;
        $filter[':SQL']= ' `uid` > 0 ';
        if($SO = $this->GP("SO")){
            if($SO['uid']){
                $filter['uid'] = $SO['uid'];
            }
            if($SO['mouth']){
                $step_first = strtotime(date('Y-m-01',strtotime($SO['mouth'])));  //获取本月第一天时间戳
                $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01',$step_first)." +1 month")-1)))+86399;
                $filter['dateline'] = $step_first.'~'.$step_last;
                $time_label = "({$SO['mouth']})";
            }
        }

        if($items = K::M('subsidy/waimai')->items_join_by_type($filter, 1, 50, array('dateline'=>"DESC"), 'uid', $count)){
            $waimai_ids = array();
            foreach ($items as $k=>$v){
                $waimai_ids[$v['uid']] = $v['uid'];
            }
            $waimai_list  = K::M('member/member')->items_by_ids($waimai_ids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['member'] = $waimai_list[$vv['uid']];
            }
        }

        $data = K::M('subsidy/waimai')->sum_and_count($filter);

        $file_name = "用户补贴统计".$time_label;
        $key_arr = array("日期", "用户ID", "用户昵称", "补贴订单数", "补贴金额(￥)", "平台承担金额(合计)", "平台承担金额(首单)", "平台承担金额(满减)", "平台承担金额(红包)", "平台承担金额(配送会员卡)", "商户承担金额(合计)", "商户承担金额(首单)", "商户承担金额(满减)", "商户承担金额(优惠券)", "商户承担金额(折扣)", "商户承担金额(换购)");
        $data['sum']['platform'] = $data['sum']['platform_first'] + $data['sum']['platform_mj'] + $data['sum']['platform_hongbao'] + $data['sum']['platform_peicard'];
        $data['sum']['shop'] = $data['sum']['shop_first'] + $data['sum']['shop_mj'] + $data['sum']['shop_coupon'] + $data['sum']['shop_discount'] + $data['sum']['shop_huangou'];
        $row = array(array('总计', '----', '----', $data['sum']['count'], $data['sum']['amount'], $data['sum']['platform'], $data['sum']['platform_first'], $data['sum']['platform_mj'], $data['sum']['platform_hongbao'], $data['sum']['platform_peicard'], $data['sum']['shop'], $data['sum']['shop_first'], $data['sum']['shop_mj'], $data['sum']['shop_coupon'], $data['sum']['shop_discount'], $data['sum']['shop_huangou']));
        foreach ($items as $k=>$v){
            $row[] = array(
                $v['day'],
                $v['uid'],
                $v['member']['nickname'],
                $v['count'],
                $v['amount'],
                $v['platform_first']+$v['platform_mj']+$v['platform_hongbao']+$v['platform_peicard'],
                $v['platform_first'],
                $v['platform_mj'],
                $v['platform_hongbao'],
                $v['platform_peicard'],
                $v['shop_first']+$v['shop_mj']+$v['shop_coupon']+$v['shop_discount']+$v['shop_huangou'],
                $v['shop_first'],
                $v['shop_mj'],
                $v['shop_coupon'],
                $v['shop_discount'],
                $v['shop_huangou']               
            );
        }
        K::M('dataio/xls')->export($key_arr, $row, $file_name);
    }

    public function subsidy_waimai()
    {
        $filter = array();
        $filter[':SQL']= ' `shop_id` > 0 ';
        $step_first = strtotime(date('Y-m-01'));  //获取本月第一天时间戳
        $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01')." +1 month")-1)))+86399;
        $filter['dateline'] = $step_first.'~'.$step_last;
        if($SO = $this->GP("SO")){
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }
            if($SO['mouth']){
                $step_first = strtotime(date('Y-m-01',strtotime($SO['mouth'])));  //获取本月第一天时间戳
                $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01',$step_first)." +1 month")-1)))+86399;
                $filter['dateline'] = $step_first.'~'.$step_last;
                $time_label = "({$SO['mouth']})";
            }
        }

        if($items = K::M('subsidy/waimai')->items_join_by_type($filter,$page,$limit,array('dateline'=>"DESC"),'shop_id',$count)){
            $waimai_ids = array();
            foreach ($items as $k=>$v){
                $waimai_ids[$v['shop_id']] = $v['shop_id'];
            }
            $waimai_list  = K::M('waimai/waimai')->items_by_ids($waimai_ids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['shop'] = $waimai_list[$vv['shop_id']];
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }

        $data = K::M('subsidy/waimai')->sum_and_count($filter);

        $file_name = "商家补贴统计".$time_label;
        $key_arr = array("日期", "商家ID", "商家名称", "补贴订单数", "补贴金额(￥)", "平台承担金额(合计)", "平台承担金额(首单)", "平台承担金额(满减)", "平台承担金额(红包)", "平台承担金额(配送会员卡)", "商户承担金额(合计)", "商户承担金额(首单)", "商户承担金额(满减)", "商户承担金额(优惠券)", "商户承担金额(折扣)", "商户承担金额(换购)");
        $data['sum']['platform'] = $data['sum']['platform_first'] + $data['sum']['platform_mj'] + $data['sum']['platform_hongbao'] + $data['sum']['platform_peicard'];
        $data['sum']['shop'] = $data['sum']['shop_first'] + $data['sum']['shop_mj'] + $data['sum']['shop_coupon'] + $data['sum']['shop_discount'] + $data['sum']['shop_huangou'];
        $row = array(array('总计', '----', '----', $data['sum']['count'], $data['sum']['amount'], $data['sum']['platform'], $data['sum']['platform_first'], $data['sum']['platform_mj'], $data['sum']['platform_hongbao'], $data['sum']['platform_peicard'], $data['sum']['shop'], $data['sum']['shop_first'], $data['sum']['shop_mj'], $data['sum']['shop_coupon'], $data['sum']['shop_discount'], $data['sum']['shop_huangou']));
        foreach ($items as $k=>$v){
            $row[] = array(
                $v['day'],
                $v['shop_idid'],
                $v['shop']['title'],
                $v['count'],
                $v['amount'],
                $v['platform_first']+$v['platform_mj']+$v['platform_hongbao']+$v['platform_peicard'],
                $v['platform_first'],
                $v['platform_mj'],
                $v['platform_hongbao'],
                $v['platform_peicard'],
                $v['shop_first']+$v['shop_mj']+$v['shop_coupon']+$v['shop_discount']+$v['shop_huangou'],
                $v['shop_first'],
                $v['shop_mj'],
                $v['shop_coupon'],
                $v['shop_discount'],
                $v['shop_huangou']               
            );
        }
        K::M('dataio/xls')->export($key_arr, $row, $file_name);
    }

}