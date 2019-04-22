<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Shop_Print extends Mdl_Table
{
	protected $_table = 'shop_print';
    protected $_pk = 'plat_id';
    protected $_cols = 'shop_id,plat_id,title,partner,apikey,machine_code,mkey,num,status,dateline,online';
    protected $_orderby = array('plat_id'=>'DESC');
    public function create($data)
    {
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __TIME;
        if($id = $this->db->insert($this->_table, $data, true)){
            return $id;
        } 
    }
    public function set_status($shop_id, $plat_id, $status)
    {
        if(!($shop_id = (int)$shop_id) || !($plat_id = (int)$plat_id)){
            return false;
        }

        if(!$status) {
            $this->db->update($this->_table, array('status'=>0), "shop_id={$shop_id} AND plat_id={$plat_id}");
        }else {
            $this->db->update($this->_table, array('status'=>1), "shop_id={$shop_id} AND plat_id={$plat_id}");
        }
        
        return true;
    }

    public function set_print_status($shop_id,$status){
       return  $this->db->update($this->_table, array('status'=>$status), "shop_id={$shop_id} ");
    }

    public function set_print_online_by_code($machine_code,$online){
        return  $this->db->update($this->_table, array('online'=>$online), "machine_code={$machine_code} ");
    }

    public function items_join_shop($filter, $order_by=array(), $page=1,$limit=50, &$count=0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('waimai')." w ON o.shop_id = w.shop_id WHERE {$where}";
        if($res_count = $this->db->Execute($count_sql)){
            if($count_row = $res_count->fetch()){
                $count = $count_row['count'];
            }
            $sql =  "SELECT  o.*, w.`title` as 'waimai_title'  FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('waimai')." w ON o.shop_id = w.shop_id WHERE {$where} {$order_by} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[$row[$this->_pk]] = $this->_format_row($row);
                }
            }
        }
        return $items;
    }
}