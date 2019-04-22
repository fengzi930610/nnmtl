<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Ditui_Log extends Mdl_Table
{   
  
    protected $_table = 'ditui_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,ditui_id,uid,money,intro,admin,clientip,dateline,type';
    public function create($data, $checked=false)
    {
        $data['clientip'] = $data['clientip'] ? $data['clientip'] : __IP;
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __CFG::TIME;
        return $this->db->insert($this->_table, $data, true);
    }

    public function total_revenue($ditui_id,$days=1)
    {
        if(!$ditui_id = (int)$ditui_id){
            return false;
        }

        $where = array();
        
        $where['ditui_id'] = $ditui_id;
        $where['type'] = 'invite';

        if($days = (int)$days){
            $stime = strtotime(date("Y-m-d"))-86400*($days-1);
            $ltime = strtotime(date("Y-m-d"))+86400;
            $where['dateline'] = $stime.'~'.$ltime;
        }               

        return $this->sum($where,'money');
    }

    public function log($ditui_id,$uid,$money,$intro,$type='invite')
    {
        if(!$ditui_id = (int)$ditui_id){
            return false;
        }else if(!$money = (float)$money){
            return false;
        }else if(empty($intro)){
            return false;
        }else if(!in_array($type, array('invite','tixian'))){
            return false;
        }else{
            $data = array();
            $data['ditui_id'] = $ditui_id;
            $data['money'] = $money;
            $data['uid'] = $uid;
            $data['intro'] = $intro;
            $data['type'] = $type;
            return $this->create($data);
        }
    }

    public function items_join_ditui($filter, $order_by=array(), $page=1, $limit = 50, &$count = 0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('ditui')." w ON o.ditui_id = w.ditui_id WHERE {$where}";
        if($res_count = $this->db->Execute($count_sql)){
            if($count_row = $res_count->fetch()){
                $count = $count_row['count'];
            }
            $sql =  "SELECT  o.*, w.`mobile` as 'mobile', w.`name` as 'name'  FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('ditui')." w ON o.ditui_id = w.ditui_id WHERE {$where} {$order_by} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[$row[$this->_pk]] = $this->_format_row($row);
                }
            }
        }
        return $items;
    } 
}