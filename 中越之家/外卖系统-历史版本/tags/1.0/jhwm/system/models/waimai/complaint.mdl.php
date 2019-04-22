<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/26
 * Time: 16:06
 */
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Complaint extends Mdl_Table

{
    protected $_table = 'waimai_order_complaint';
    protected $_pk = 'complaint_id';
    protected $_cols = 'complaint_id,order_id,uid,shop_id,staff_id,title,content,clientip,reply,reply_time,dateline,have_photo';

    public function assessment_group_by_staff_order($filter){
        $where = $this->where($filter,'o.');
        $sql = "SELECT COUNT(1) as orders ,o.staff_id FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('order')." w ON o.order_id = w.order_id WHERE {$where} GROUP BY  `staff_id` ";

        $items  = array();
        if($res = $this->db->Execute($sql)){
            while($row = $res->fetch()){
                $items[$row['staff_id']] = $row;
            }
            return $items;
        }
    }

    public function group_sum_by_shop_id($filter = array())
    {
        $where = $this->where($filter);
        $items = $arr = array();
        $sql = "SELECT count(1) as orders,`shop_id` FROM " . $this->table($this->_table) . " WHERE {$where} GROUP BY shop_id ";
        if ($res = $this->db->Execute($sql)) {
            while ($row = $res->fetch()) {
                $items[$row['shop_id']] = $row;
            }
        }
        return $items;
    }


    public function group_by_type_group($filter,$stime,$ltime,$step){
        $where = $this->where($filter,'o.');
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
                $group_by = "days";
                break;
            case "h":
                $group_by = "hours";
                break;
            default:
                $group_by = "days";
                break;
        }
        $sql = "SELECT COUNT(1) as orders,FROM_UNIXTIME(w.dateline,'%Y%m%d') as days,FROM_UNIXTIME(w.dateline,'%H') as hours FROM ".$this->table($this->_table)." o  LEFT JOIN ".$this->table("order")." w ON o.order_id=w.order_id WHERE {$where} GROUP BY {$group_by}";
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[(int)$row[$group_by]] = $row;
            }
        }
        foreach ($data as $k=>$v){
            $arr['x'][] = $v;
            $arr['order'][] = $items[$k]['orders']? (float)$items[$k]['orders']:0;

        }
        return $arr;
    }

    public function items_join_staff($filter, $order_by=array(), $page=1, $limit = 50, &$count = 0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('staff')." w ON o.staff_id = w.staff_id WHERE {$where}";
        if($res_count = $this->db->Execute($count_sql)){
            if($count_row = $res_count->fetch()){
                $count = $count_row['count'];
            }
            $sql =  "SELECT  o.*, w.`mobile` as 'staff_mobile', w.`name` as 'staff_name'  FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('staff')." w ON o.staff_id = w.staff_id WHERE {$where} {$order_by} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[$row[$this->_pk]] = $this->_format_row($row);
                }
            }
        }
        return $items;
    }

    public function items_join_member_shop($filter,$order_by = array(),$page=1,$limit = 50,&$count = 0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('member')." w ON o.uid = w.uid LEFT JOIN ".$this->table('shop')." ext ON o.shop_id=ext.shop_id WHERE {$where}";
        if($count = $this->db->GetOne($count_sql)){
            $sql =  "SELECT  o.*, w.`mobile` as 'member_mobile', w.`nickname` as 'member_nickname', ext.`title` as 'shop_title', ext.`mobile` as 'shop_mobile' FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('member')." w ON o.uid = w.uid LEFT JOIN ".$this->table('shop')." ext ON o.shop_id=ext.shop_id WHERE {$where} {$order_by} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[$row[$this->_pk]] = $this->_format_row($row);
                }
            }
        }
        return $items;
    }

}