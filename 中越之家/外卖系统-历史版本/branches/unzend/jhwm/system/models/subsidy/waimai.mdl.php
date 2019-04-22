<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/29
 * Time: 18:08
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Subsidy_Waimai extends Mdl_Table {

    protected $_table = 'subsidy_waimai';
    protected $_pk = 'subsidy_id';
    protected $_cols = 'subsidy_id,order_id,shop_id,platform_first,platform_mj,platform_hongbao,shop_first,shop_mj,year,mouth,day,hour,group_id,city_id,dateline,bl,shop_coupon,shop_discount,shop_huangou,platform_peicard,uid';

    public function items_join_by_type($filter,$page=1,$limit=50,$order_by,$type='shop_id',&$count){
        $where = $this->where($filter);
        $order_by  = $this->order($order_by);
        $limit = $this->limit($page, $limit);
        $count_sql = "SELECT count(DISTINCT {$type},day) as count FROM ".$this->table($this->_table)." WHERE {$where}";
        $count_res = $this->db->Execute($count_sql);
        $count_res1 = $count_res->fetch();
        $count = $count_res1['count'];
        $sql = "SELECT SUM(platform_first+platform_mj+platform_hongbao+shop_first+shop_mj+shop_coupon+shop_discount+shop_huangou+platform_peicard) as amount,count(1) as count ,SUM(platform_first) as platform_first,SUM(platform_mj) as platform_mj ,SUM(platform_hongbao) as platform_hongbao,SUM(shop_first) as shop_first,SUM(shop_mj) as shop_mj,SUM(shop_coupon) as shop_coupon,SUM(shop_discount) as shop_discount,`{$type}`,`day`,SUM(shop_huangou) as shop_huangou,SUM(platform_peicard) as platform_peicard  FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `{$type}`,`day` $order_by $limit ";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[] = $row;
            }
        }
        return $items;
    }

    public function sum_and_count($filter){
        $where = $this->where($filter);

        $sql = "SELECT SUM(platform_first+platform_mj+platform_hongbao+shop_first+shop_mj+shop_coupon+shop_discount+shop_huangou+platform_peicard) as amount,count(1) as count ,SUM(platform_first) as platform_first,SUM(platform_mj) as platform_mj ,SUM(platform_hongbao) as platform_hongbao,SUM(shop_first) as shop_first,SUM(shop_mj) as shop_mj,SUM(shop_coupon) as shop_coupon,SUM(shop_discount) as shop_discount, SUM(shop_huangou) as shop_huangou, SUM(platform_peicard) as platform_peicard  FROM ".$this->table($this->_table)." WHERE {$where}";
        $count_res = $this->db->Execute($sql);
        $count_res_result = $count_res->fetch();
        return array(
            'sum'=>$count_res_result
        );
    }

    public function group_by_type($filter,$stime,$ltime,$step,$field){
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
        $sql = "SELECT SUM(platform_first+platform_mj+platform_hongbao+shop_first+shop_mj+shop_coupon+shop_discount+shop_huangou+platform_peicard) as amount,count(1) as count ,SUM(platform_first) as platform_first,SUM(platform_mj) as platform_mj ,SUM(platform_hongbao) as platform_hongbao,SUM(shop_first) as shop_first,SUM(shop_mj) as shop_mj,SUM(shop_coupon) as shop_coupon,SUM(shop_discount) as shop_discount,`{$group_by}`, SUM(shop_huangou) as shop_huangou, SUM(platform_peicard) as platform_peicard  FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `{$group_by}`  ";
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row[$group_by]] = $row;
            }
        }

        foreach ($data as $k=>$v){
            $sum_platform = $items[$k]['platform_first']+$items[$k]['platform_mj']+$items[$k]['platform_hongbao']+$items[$k]['platform_peicard'];
            $sum_shop = $items[$k]['shop_first']+$items[$k]['shop_mj']+$items[$k]['shop_coupon']+$items[$k]['shop_discount']+$items[$k]['shop_amount']+$items[$k]['shop_huangou'];
            
            $arr['x'][] = $v;
            $arr['amount'][] = $items[$k]['amount'] ? (float)$items[$k]['amount'] : 0;
            $arr['order'][] = $items[$k]['count'] ? (float)$items[$k]['count'] : 0;
            $arr['sum_platform'][] = $sum_platform ? (float)$sum_platform : 0;
            $arr['sum_shop'][] = $sum_shop ? (float)$sum_shop : 0;
            $arr['platform_first'][] = $items[$k]['platform_first']?(float)$items[$k]['platform_first']:0;
            $arr['platform_mj'][] = $items[$k]['platform_mj']?(float)$items[$k]['platform_mj']:0;
            $arr['platform_hongbao'][] = $items[$k]['platform_hongbao']?(float)$items[$k]['platform_hongbao']:0;
            $arr['shop_first'][] = $items[$k]['shop_first']?(float)$items[$k]['shop_first']:0;
            $arr['shop_mj'][] = $items[$k]['shop_mj']?(float)$items[$k]['shop_mj']:0;
            $arr['shop_coupon'][] = $items[$k]['shop_coupon']?(float)$items[$k]['shop_coupon']:0;
            $arr['shop_discount'][] = $items[$k]['shop_discount']?(float)$items[$k]['shop_discount']:0;
            $arr['shop_huangou'][] = $items[$k]['shop_huangou']?(float)$items[$k]['shop_huangou']:0;
            $arr['platform_peicard'][] = $items[$k]['platform_peicard']?(float)$items[$k]['platform_peicard']:0;
        }
        return $arr;
    }

}