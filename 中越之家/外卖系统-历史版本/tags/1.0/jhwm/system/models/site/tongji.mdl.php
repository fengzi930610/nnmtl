<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 13:37
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Site_Tongji extends Mdl_Table {

    protected $_table = 'site_tongji';
    protected $_pk = 'tongji_id';
    protected $_cols = 'tongji_id,amount,order_id,from,shop_amount,staff_amount,shop_id,uid,staff_id,pei_amount,shop_fee,staff_fee,platform_first,platform_mj,platform_hongbao,platform_staff,shop_first,shop_mj,shop_coupon,shop_discount,year,mouth,day,hour,city_id,dateline,site_fee,shop_huangou,platform_peicard';

    public function sum_amount_by_filter($filter){

        $where = $this->where($filter);
        $sql = "SELECT SUM(shop_fee+staff_fee-platform_first-platform_mj-platform_hongbao-platform_peicard) as amount  FROM ".$this->table($this->_table)." WHERE {$where}";
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $amount = $row['amount'];
            }
        }
        return $amount?$amount:0;
    }

    public function sum_fee_by_filter($filter){

        $where = $this->where($filter);
        $sql = "SELECT SUM(platform_first+platform_mj+platform_hongbao+platform_staff+platform_peicard) as fee  FROM ".$this->table($this->_table)." WHERE {$where}";
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $amount = $row['fee'];
            }
        }
        return $amount?$amount:0;
    }

    public function group_by_type($filter,$stime,$ltime,$step){
        $where = $this->where($filter);
        $data = K::M('helper/date')->get_arr_by_type($stime,$ltime,$step);
        $arr = $items = array();
        switch ($step){
            /*   case "y":
                   $group_by = "year";
                   break;
               case "m":
                   $group_by = "mouth";
                   break;*/
            case "d":
                $group_by = "day";
                break;
            case "h":
                $group_by = "hour";
                break;
            default:
                $group_by = "day";
                break;
        }
        $sql = "SELECT SUM(shop_fee+staff_fee-platform_first-platform_mj-platform_hongbao-platform_peicard) as amount,`{$group_by}`  FROM ".$this->table($this->_table)." WHERE {$where} GROUP by `{$group_by}` ";
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row[$group_by]] = $row;
            }
        }
        foreach ($data as $k=>$v){
            $arr['x'][] = $v;
            $arr['amount'][] = $items[$k]['amount']?(float)$items[$k]['amount']:0;
        }
        return $arr;
    }


    //根据时间获取
    public function get_data_by_type($filter,$stime,$ltime,$step){
        $where = $this->where($filter);
        $data = K::M('helper/date')->get_arr_by_type($stime,$ltime,$step);
        $arr = $items = array();
        switch ($step){
            /*   case "y":
                   $group_by = "year";
                   break;
               case "m":
                   $group_by = "mouth";
                   break;*/
            case "d":
                $group_by = "day";
                break;
            case "h":
                $group_by = "hour";
                break;
            default:
                $group_by = "day";
                break;
        }

        $sql = "SELECT SUM(amount) as amount, count(1) as orders, SUM(shop_amount) as shop_amount, SUM(staff_amount) as staff_amount,SUM(pei_amount) as pei_amount,SUM(shop_fee+staff_fee-platform_first-platform_mj-platform_hongbao-platform_peicard) as yinli,SUM(site_fee) as site_fee,SUM(platform_first) as platform_first ,SUM(platform_mj) as platform_mj,SUM(platform_hongbao) as platform_hongbao,SUM(platform_staff) as  platform_staff, SUM(platform_peicard) as platform_peicard ,`{$group_by}` FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `{$group_by}`";
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row[$group_by]] = $row;
            }
        }

        foreach ($data as $k=>$v){
            $arr['x'][] = $v;
            $arr['amount'][] = $items[$k]['amount']?(float)$items[$k]['amount']:0;
            $arr['orders'][] = $items[$k]['orders']?(float)$items[$k]['orders']:0;
            $arr['shop_amount'][] = $items[$k]['shop_amount']?(float)$items[$k]['shop_amount']:0;
            $arr['staff_amount'][] = $items[$k]['staff_amount']?(float)$items[$k]['staff_amount']:0;
            $arr['pei_amount'][] = $items[$k]['pei_amount']?(float)$items[$k]['pei_amount']:0;
            $arr['yinli'][] = $items[$k]['yinli']?(float)$items[$k]['yinli']:0;
            $arr['site_fee'][] = $items[$k]['site_fee']?(float)$items[$k]['site_fee']:0;
            $arr['platform_first'][] = $items[$k]['platform_first']?(float)$items[$k]['platform_first']:0;
            $arr['platform_mj'][] = $items[$k]['platform_mj']?(float)$items[$k]['platform_mj']:0;
            $arr['platform_hongbao'][] = $items[$k]['platform_hongbao']?(float)$items[$k]['platform_hongbao']:0;
            $arr['platform_staff'][] = $items[$k]['platform_staff']?(float)$items[$k]['platform_staff']:0;
            $arr['platform_peicard'][] = $items[$k]['platform_peicard']?(float)$items[$k]['platform_peicard']:0;
        }
        return $arr;
    }

    public function sum_by_filter($filter){
        $where = $this->where($filter);
        $sql =  "SELECT SUM(amount) as amount, count(1) as orders, SUM(shop_amount) as shop_amount, SUM(staff_amount) as staff_amount,SUM(pei_amount) as pei_amount,SUM(shop_fee+staff_fee-platform_first-platform_mj-platform_hongbao-platform_peicard) as yinli,SUM(site_fee) as site_fee,SUM(platform_first) as platform_first ,SUM(platform_mj) as platform_mj,SUM(platform_hongbao) as platform_hongbao,SUM(platform_staff) as  platform_staff  ,SUM(platform_peicard) as  platform_peicard FROM ".$this->table($this->_table)." WHERE {$where} ";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $row['amount'] =   $row['amount']?$row['amount']:0;
                $row['orders'] =   $row['orders']?$row['orders']:0;
                $row['shop_amount'] =   $row['shop_amount']?$row['shop_amount']:0;
                $row['staff_amount'] =   $row['staff_amount']?$row['staff_amount']:0;
                $row['pei_amount'] =   $row['pei_amount']?$row['pei_amount']:0;
                $row['yinli'] =   $row['yinli']?$row['yinli']:0;
                $row['site_fee'] =   $row['site_fee']?$row['site_fee']:0;
                $row['platform_first'] =   $row['platform_first']?$row['platform_first']:0;
                $row['platform_mj'] =   $row['platform_mj']?$row['platform_mj']:0;
                $row['platform_hongbao'] =   $row['platform_hongbao']?$row['platform_hongbao']:0;
                $row['platform_staff'] =   $row['platform_staff']?$row['platform_staff']:0;
                $row['platform_peicard'] =   $row['platform_peicard']?$row['platform_peicard']:0;
                $items = $row;
            }
        }
        return $items;
    }

    //获取统计数据
    public function get_three_day_data(){
        //今天时间范围
        $t_dateline = array(
            'stime'=>strtotime(date('Y-m-d')),
            'ltime'=>(strtotime(date('Y-m-d'))+86399),
            'k'=>'t'
        );

        $l_dateline = array(
            'stime'=> strtotime(date('Y-m-d')."-1 day"),
            'ltime'=>(strtotime(date('Y-m-d'))-1),
            'k'=>'l'
        );

        $w_dateline = array(
            'stime'=>strtotime(date('Y-m-d')."-1 week"),
            'ltime'=>(strtotime(date('Y-m-d')."-1 week")+86399),
            'k'=>'w'
        );

        $arr = array($t_dateline,$l_dateline,$w_dateline);

        $data = array();
        foreach ($arr as $k=>$v){
            /*外卖数据---------------------------------------------------------------------------------------*/
            $tmp_data = $this->sum_by_filter(array('dateline'=>$v['stime'].'~'.$v['ltime'],'from'=>'waimai'));
            //交易额
            $data[$v['k']]['waimai']['amount'] = $tmp_data['amount'];
            //配送费
            $data[$v['k']]['waimai']['pei_amount'] = $tmp_data['pei_amount'];
            //商家应得
            $data[$v['k']]['waimai']['shop_amount'] = $tmp_data['shop_amount'];
            //平台应得
            $data[$v['k']]['waimai']['yinli'] = $tmp_data['yinli'];
            //平台补贴金额
            $data[$v['k']]['waimai']['platform_butie'] = $tmp_data['platform_first']+$tmp_data['platform_mj']+$tmp_data['platform_hongbao']+$tmp_data['platform_staff']+$tmp_data['platform_peicard'];
            //客单价
            $data[$v['k']]['waimai']['avg'] = $tmp_data['orders']>0? sprintf("%.2d",($tmp_data['amount']/$tmp_data['orders'])):0;
            //订单数
            $data[$v['k']]['waimai']['a_orders'] = K::M('order/order')->count(array('dateline'=>$v['stime'].'~'.$v['ltime'],'from'=>'waimai'));
            //有效订单量
            $data[$v['k']]['waimai']['orders'] = $tmp_data['orders'];
            //异常订单
            $data[$v['k']]['waimai']['y_orders'] = K::M('order/order')->count(array('dateline'=>$v['stime'].'~'.$v['ltime'],'from'=>'waimai','refund_status'=>">:0"));
            //新客数量
            $data[$v['k']]['waimai']['member'] =  $data[$v['k']]['paotui']['member'] = K::M('member/member')->count(array('dateline'=>$v['stime'].'~'.$v['ltime']));

            /*跑腿数据---------------------------------------------------------------------------------------*/
            $paotui_tmp = $this->sum_by_filter(array('dateline'=>$v['stime'].'~'.$v['ltime'],'from'=>'paotui'));
            //交易额
            $data[$v['k']]['paotui']['amount'] = $paotui_tmp['amount'];
            //配送费
            $data[$v['k']]['paotui']['pei_amount'] = $paotui_tmp['pei_amount'];
            //平台应得
            $data[$v['k']]['paotui']['yinli'] = $paotui_tmp['yinli'];
            //平台补贴金额
            $data[$v['k']]['paotui']['platform_butie'] = $paotui_tmp['platform_first']+$paotui_tmp['platform_mj']+$paotui_tmp['platform_hongbao']+$paotui_tmp['platform_staff'];
            //客单价
            $data[$v['k']]['paotui']['avg'] = $paotui_tmp['orders']>0? sprintf("%.2d",($paotui_tmp['amount']/$paotui_tmp['orders'])):0;
            //订单数
            $data[$v['k']]['paotui']['a_orders'] = K::M('order/order')->count(array('dateline'=>$v['stime'].'~'.$v['ltime'],'from'=>'paotui'));
            //有效订单量
            $data[$v['k']]['paotui']['orders'] = $paotui_tmp['orders'];

            /*抢购数据---------------------------------------------------------------------------------------*/
            $qiang_tmp = $this->sum_by_filter(array('dateline'=>$v['stime'].'~'.$v['ltime'],'from'=>'qiang'));
            //交易额
            $data[$v['k']]['qiang']['amount'] = $qiang_tmp['amount'];
            //配送费
            $data[$v['k']]['qiang']['pei_amount'] = $qiang_tmp['pei_amount'];
            //平台应得
            $data[$v['k']]['qiang']['yinli'] = $qiang_tmp['yinli'];
            //平台补贴金额
            $data[$v['k']]['qiang']['platform_butie'] = $qiang_tmp['platform_first']+$qiang_tmp['platform_mj']+$qiang_tmp['platform_hongbao']+$qiang_tmp['platform_staff'];
            //客单价
            $data[$v['k']]['qiang']['avg'] = $qiang_tmp['orders']>0? sprintf("%.2d",($qiang_tmp['amount']/$qiang_tmp['orders'])):0;
            //订单数
            $data[$v['k']]['qiang']['a_orders'] = K::M('order/order')->count(array('dateline'=>$v['stime'].'~'.$v['ltime'],'from'=>'qiang'));
            //有效订单量
            $data[$v['k']]['qiang']['orders'] = $qiang_tmp['orders'];

            /*配送会员卡数据---------------------------------------------------------------------------------------*/
            $peicard_tmp = $this->sum_by_filter(array('dateline'=>$v['stime'].'~'.$v['ltime'],'from'=>'peicard'));
            //交易额
            $data[$v['k']]['peicard']['amount'] = $peicard_tmp['amount'];
            $data[$v['k']]['peicard']['orders'] = $peicard_tmp['orders'];
        }

        /*外卖---------------------------------*/
        $waimai_t = array(
            'amount','pei_amount','shop_amount',
            'yinli','platform_butie','avg',
            'a_orders','orders','y_orders',
            'member'
        );
        foreach ($waimai_t as $v){
            $data['t']['waimai'][$v.'_l'] = K::M('helper/format')->get_bl( $data['t']['waimai'][$v],$data['l']['waimai'][$v]);
            $data['t']['waimai'][$v.'_w'] = K::M('helper/format')->get_bl( $data['t']['waimai'][$v],$data['w']['waimai'][$v]);
        }

        /*跑腿---------------------------------*/
        $paotui_t = array(
            'amount','pei_amount',
            'yinli','platform_butie','avg',
            'a_orders','orders',
            'member'
        );
        foreach ($paotui_t as $v){
            $data['t']['paotui'][$v.'_l'] = K::M('helper/format')->get_bl( $data['t']['paotui'][$v],$data['l']['paotui'][$v]);
            $data['t']['paotui'][$v.'_w'] = K::M('helper/format')->get_bl( $data['t']['paotui'][$v],$data['w']['paotui'][$v]);
        }

        /*抢购---------------------------------*/
        $qiang_t = array(
            'amount','pei_amount',
            'yinli','platform_butie','avg',
            'a_orders','orders',
            'member'
        );
        foreach ($qiang_t as $v){
            $data['t']['qiang'][$v.'_l'] = K::M('helper/format')->get_bl( $data['t']['qiang'][$v],$data['l']['qiang'][$v]);
            $data['t']['qiang'][$v.'_w'] = K::M('helper/format')->get_bl( $data['t']['qiang'][$v],$data['w']['qiang'][$v]);
        }

        /*配送会员卡---------------------------------*/
        $peicard_t = array(
            'amount','orders'
        );
        foreach ($qiang_t as $v){
            $data['t']['peicard'][$v.'_l'] = K::M('helper/format')->get_bl($data['t']['peicard'][$v],$data['l']['peicard'][$v]);
            $data['t']['peicard'][$v.'_w'] = K::M('helper/format')->get_bl($data['t']['peicard'][$v],$data['w']['peicard'][$v]);
        }

        return $data['t'];
    }
}