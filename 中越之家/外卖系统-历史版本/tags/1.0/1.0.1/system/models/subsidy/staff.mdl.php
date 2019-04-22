<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 15:39
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Subsidy_Staff extends Mdl_Table {
    protected $_table = 'subsidy_staff';
    protected $_pk = 'subsidy_id';
    protected $_cols = 'subsidy_id,order_id,staff_id,pei_amount,staff_amount,diff_amount,from,bl,year,mouth,day,hour,dateline,group_id,city_id';


    public function items_join_by_staff_id($filter,$page=1,$limit=50,$order_by,&$count){
        $where = $this->where($filter);
        $order_by  = $this->order($order_by);
        $limit = $this->limit($page, $limit);
        $count_sql = "SELECT count(DISTINCT staff_id,day) as count FROM ".$this->table($this->_table)." WHERE {$where}";
        $count_res = $this->db->Execute($count_sql);
        $count_res1 = $count_res->fetch();
        $count = $count_res1['count'];
        $sql = "SELECT SUM(diff_amount) as amount,count(1) as count ,`staff_id`,`day`,'subsidy_id'  FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `staff_id`,`day` $order_by $limit ";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[] = $row;
            }
        }
        return $items;

    }

    public function group_by_data($filter,$stime,$ltime,$step){
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
        $sql ="SELECT sum(diff_amount) as amount,count(order_id) as count,{$group_by} FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY {$group_by}";
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row[$group_by]] = $row;
            }
        }
        foreach ($data as $k=>$v){
            $arr['x'][] = $v;
            $arr['amount'][] = $items[$k]['amount']? (float)$items[$k]['amount']:0;
            $arr['order'][] = $items[$k]['count']? (int)$items[$k]['count']:0;
        }

        return $arr;



    }

}