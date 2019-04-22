<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/27
 * Time: 13:41
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Pei_Bind extends Mdl_Table {

    protected $_table = 'pei_bind';
    protected $_pk = 'bind_id';
    protected $_cols = 'bind_id,group_id,shop_id,addr,lng,lat,dateline,title,contact,mobile';
    protected $_orderby = array('bind_id'=>'DESC');

    protected function _check($data)
    {
        if(isset($data['lat'])){
            $data['lat'] = round(bcmul($data['lat'], 1000000));
        }
        if(isset($data['lng'])){
            $data['lng'] = round(bcmul($data['lng'], 1000000));
        }
       return $data;
    }

    protected function _format_row($row)
    {
        if($row['lat']){
            $row['lat'] = bcdiv($row['lat'], 1000000,6);
        }
        if($row['lng']){
            $row['lng'] = bcdiv($row['lng'], 1000000,6);
        }
        return $row;
    }

    public function items_join_group($filter, $order_by=array(), $page=1, $limit = 50, &$count = 0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('pei_group')." w ON o.group_id = w.group_id WHERE {$where}";
        if($res_count = $this->db->Execute($count_sql)){
            if($count_row = $res_count->fetch()){
                $count = $count_row['count'];
            }
            $sql =  "SELECT  o.*, w.`group_name` as 'group_name', w.`mobile` as 'group_mobile' FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('pei_group')." w ON o.group_id = w.group_id WHERE {$where} {$order_by} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[$row[$this->_pk]] = $this->_format_row($row);
                }
            }
        }
        return $items;
    }



}