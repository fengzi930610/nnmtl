<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8
 * Time: 10:02
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Forcedlog extends Mdl_Table {

    protected $_table = 'staff_forcedlog';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,order_id,staff_id,group_id,distance,lng,lat,o_lng,o_lat,dateline';

    protected function _format_row($row){
        if($row['lat']){
            $row['lat'] = bcdiv($row['lat'], 1000000,6);
        }
        if($row['lng']){
            $row['lng'] = bcdiv($row['lng'], 1000000,6);
        }
        if($row['o_lat']){
            $row['o_lat'] = bcdiv($row['o_lat'], 1000000,6);
        }
        if($row['o_lng']){
            $row['o_lng'] = bcdiv($row['o_lng'], 1000000,6);
        }
        return $row;
    }

    protected function _check($data, $order_id=null)
    {
        if(isset($data['lat'])){
            $data['lat'] = round(bcmul($data['lat'], 1000000));
        }
        if(isset($data['lng'])){
            $data['lng'] = round(bcmul($data['lng'], 1000000));
        }
        if(isset($data['o_lat'])){
            $data['o_lat'] = round(bcmul($data['o_lat'], 1000000));
        }
        if(isset($data['o_lng'])){
            $data['o_lng'] = round(bcmul($data['o_lng'], 1000000));
        }
        return parent::_check($data, $order_id);
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
}











