<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: logs.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Member_Log extends Mdl_Table
{   
  
    protected $_table = 'member_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,uid,type,number,intro,admin,day,clientip,dateline,balance';
    protected $_orderby = array('log_id'=>'DESC');
    
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        $data['clientip'] = $data['clientip'] ? $data['clientip'] : __IP;
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __CFG::TIME;
        $data['day'] = date('Ymd', $data['dateline']);
        return $this->db->insert($this->_table, $data, true);
    }
    public function log($uid, $type='money', $num=0, $intro='',$balance = 0)
    {
        $a = array();
        if(!$uid = (int)$uid){
            return false;
        }else if($type == 'money'){
            $num = floatval($num);
        }else if($type == 'jifen'){
            $num = intval($num);
        }else if($type == 'coin'){
            $num = intval($num);
        }else{
            return false;
        }
        $a = array('uid'=>$uid, 'type'=>$type,'number'=>$num, 'intro'=>$intro,'balance'=>$balance);
        if(defined('IN_ADMIN')){
            $admin = K::$system->admin->admin;
            $a['admin'] = "{$admin['admin_id']}:{$admin['admin_name']}";
        }
        $a['clientip'] = __IP;
        $a['dateline'] = __CFG::TIME;
        return $this->db->insert($this->_table, $a, true);
    }
    public function update($pk, $data, $checked=false)
    {
        $this->_checkpk();
        if(!$checked && !$data = $this->_check_schema($data,  $pk)){
            return false;
        }
        return $this->db->update($this->_table, $data, $this->field($this->_pk, $pk));
    }

    public function items_join_member($filter, $order_by=array(), $page=1, $limit = 50, &$count = 0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('member')." w ON o.uid = w.uid WHERE {$where}";
        if($res_count = $this->db->Execute($count_sql)){
            if($count_row = $res_count->fetch()){
                $count = $count_row['count'];
            }
            $sql =  "SELECT  o.*, w.`mobile` as 'mobile', w.`nickname` as 'nickname'  FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('member')." w ON o.uid = w.uid WHERE {$where} {$order_by} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[$row[$this->_pk]] = $this->_format_row($row);
                }
            }
        }
        return $items;
    }    
}