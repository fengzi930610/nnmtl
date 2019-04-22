<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Verify extends Mdl_Table
{   
  
    protected $_table = 'staff_verify';
    protected $_pk = 'staff_id';
    protected $_cols = 'staff_id,id_name,id_number,id_photo,verify,verify_time,refuse,updatetime,dateline';

    public function create($data, $checked=false)
    {
        $data['updatetime'] = $data['updatetime'] ? $data['updatetime'] : __TIME;
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __TIME;
        return $this->db->insert($this->_table, $data, true);
    }
    public function verify($staff_id,$data)
    {
        if(!$row = $this->detail($staff_id)){
            return false;
        }else if($row['verify'] == 1){
            $this->msgbox->add('已经实名认证', 451);
            return false;
        }else if($this->update($staff_id, array('verify'=>0, 'verify_time'=>__TIME, 'updatetime'=>__TIME, 'refuse'=>'','id_name'=>$data['id_name'],'id_number'=>$data['id_number'],'id_photo'=>$data['id_photo']), true)){
            K::M('staff/staff')->update($staff_id, array('verify_name'=>0, 'name'=>$row['id_name']), true);
            return true;
        }
        return false;
    }
    public function refuse($staff_id, $refuse='')
    {
        if(!$row = $this->detail($staff_id)){
            return false;
        }
        return $this->update($staff_id, array('verify'=>2, 'refuse'=>$refuse), true);
    }

    public function items_join_staff($filter, $order_by=array(), $page=1,$limit=50, &$count=0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('staff')." w ON o.staff_id = w.staff_id WHERE {$where}";
        if($res_count = $this->db->Execute($count_sql)){
            if($count_row = $res_count->fetch()){
                $count = $count_row['count'];
            }
            $sql =  "SELECT  o.*, w.`name` as 'name', w.`from` as 'from', w.`mobile` as 'mobile'  FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('staff')." w ON o.staff_id = w.staff_id WHERE {$where} {$order_by} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[$row[$this->_pk]] = $this->_format_row($row);
                }
            }
        }
        return $items;
    }
}