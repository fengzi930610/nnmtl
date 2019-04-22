<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Tongji_Money extends Ctl
{
    
    public function index()
    {
        $config = $this->system->config->load(array('site'));
        $sdate = strtotime("2017-6-1");
        $this->pagedata['bills_month'] = K::M('helper/date')->get_date_list($sdate, __TIME);// 获取2个时间之间的月份数组（不论前后顺序）
        $this->pagedata['bills_date'] = K::M('helper/date')->get_day_list('2017-3');
        $this->tmpl = 'admin:tongji/money/items.html';
    }
    
    public function get_data(){
        $month = htmlspecialchars($this->GP('month'));
        $waimai_bills = K::M('waimai/bills')->get_bills_amount($month);
        $qiang_bills = K::M('qiang/bills')->get_bills_amount($month);

        $bills = array(
           'fee' => ($waimai_bills['roof'] + $qiang_bills['roof']),
           'last_fee' => ($waimai_bills['last_roof'] + $qiang_bills['last_roof']),
           'cha_money' => ($waimai_bills['roof'] + $qiang_bills['roof'] -$waimai_bills['last_roof'] - $qiang_bills['last_roof']),
           'cha' => abs($waimai_bills['roof'] + (-$waimai_bills['last_roof']) + $qiang_bills['roof'] + (-$qiang_bills['last_roof']) ),
            'month' => $month,
            'last_month'=> date("Y-m",strtotime(date("Y-m-01",strtotime($month))."-1 month")),
            'shop'=>($waimai_bills['shop'] + $qiang_bills['shop']),
            'last_shop'=>($waimai_bills['last_shop'] + $qiang_bills['last_shop']),
            'cha_shop'=>($waimai_bills['shop'] + $qiang_bills['shop'] - $waimai_bills['last_shop'] - $qiang_bills['last_shop'])
        );
        $data['bills'] = $bills;         //平台账单
        $t_mouth_start_time = strtotime($month);
        $t_mouth_end_time = (strtotime($month."+1 month")-1);
        $l_mouth_strat_time = strtotime(date("Y-m-01",strtotime($month))."-1 month");
        $l_mouth_end_time = (strtotime($month)-1);
        $site_amount = array();
        $site_amount['mouth_entry'] = K::M('site/tongji')->sum_amount_by_filter(array('dateline'=>$t_mouth_start_time.'~'.$t_mouth_end_time));
        $site_amount['last_entry'] = K::M('site/tongji')->sum_amount_by_filter(array('dateline'=>$l_mouth_strat_time.'~'.$l_mouth_end_time));
        $site_amount['cha_entry'] =  $site_amount['mouth_entry']-$site_amount['last_entry'];
        $data['entry'] = $site_amount;
        $t_mouth_data = K::M('waimai/bills')->group_by_type(array('dateline'=>$t_mouth_start_time.'~'.$t_mouth_end_time),$t_mouth_start_time,$t_mouth_end_time);
        $l_mouth_data = K::M('waimai/bills')->group_by_type(array('dateline'=>$l_mouth_strat_time.'~'.$l_mouth_end_time),$l_mouth_strat_time,$l_mouth_end_time);
        $t_q_mouth_data = K::M('qiang/bills')->group_by_type(array('dateline'=>$t_mouth_start_time.'~'.$t_mouth_end_time),$t_mouth_start_time,$t_mouth_end_time);
        $l_q_mouth_data = K::M('qiang/bills')->group_by_type(array('dateline'=>$l_mouth_strat_time.'~'.$l_mouth_end_time),$l_mouth_strat_time,$l_mouth_end_time);
        $new_t_mouth_data['x'] = $new_l_mouth_data['x'] = range(1,31);
        foreach ($new_t_mouth_data['x'] as $k => $v) {
            $new_t_mouth_data['fee'][$k] = $t_mouth_data['fee'][$k] + $t_q_mouth_data['fee'][$k];
            $new_t_mouth_data['amount'][$k] = $t_mouth_data['amount'][$k] + $t_q_mouth_data['amount'][$k];
            $new_l_mouth_data['fee'][$k] = $l_mouth_data['fee'][$k] + $l_q_mouth_data['fee'][$k];
            $new_l_mouth_data['amount'][$k] = $l_mouth_data['amount'][$k] + $l_q_mouth_data['amount'][$k];
        }
        
        $data['t_mouth_bills'] = $new_t_mouth_data;
        $data['l_mouth_bills'] = $new_l_mouth_data;
        $t_tongji = K::M('site/tongji')->group_by_type(array('dateline'=>$t_mouth_start_time.'~'.$t_mouth_end_time),$t_mouth_start_time,$t_mouth_end_time,'d');
        $l_tongji = K::M('site/tongji')->group_by_type(array('dateline'=>$l_mouth_strat_time.'~'.$l_mouth_end_time),$l_mouth_strat_time,$l_mouth_end_time,'d');
        $data['t_month_tongji'] = $t_tongji['amount'];
        $data['l_month_tongji'] = $l_tongji['amount'];
        $this->msgbox->set_data('data',$data);

    }


    
    
}