<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Chart extends Ctl
{
    public function index()
    {
    	$filter = $items = $amount_data = $jiesuan_data = array();
        // $filter = array('shop_id' => $this->shop_id);
        $ym = date('Ym', __TIME);
        // $filter['bills_sn'] = "{$ym}01~{$ym}31";
        $day_count = date('t', __TIME);
        for($i=1; $i<=$day_count; $i++){
        	$day = sprintf("{$ym}%02d", $i);
        	$amount_data[$day] = $jiesuan_data[$day] = 0;
        }
        if($bills = K::M('site/tongji')->items(array('day'=>"{$ym}01~{$ym}31", 'shop_id'=>$this->shop_id))){
            foreach ($bills as $k => $v) {
                $amount_data[$v['day']] += (float)($v['shop_amount']+$v['site_fee']);
                $jiesuan_data[$v['day']] += (float)($v['shop_amount']);
            }
        }

        /*if($items  = K::M('waimai/bills')->items($filter, array('bills_id'=>'ASC'))){
        	foreach($items as $v){
        		$amount_data[$v['bills_sn']] = (float)($v['amount']+$v['fee']);
        		$jiesuan_data[$v['bills_sn']] = (float)($v['amount']);
        	}
        }*/
        $this->pagedata['amount_data'] = json_encode(array_values($amount_data));
        $this->pagedata['jiesuan_data'] = json_encode(array_values($jiesuan_data));
        $this->tmpl = 'chart/index.html';
    }
}