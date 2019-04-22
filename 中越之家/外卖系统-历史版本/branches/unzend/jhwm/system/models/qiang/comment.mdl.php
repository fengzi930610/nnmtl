<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Qiang_Comment extends Mdl_Table
{   
  
    protected $_table = 'qiang_comment';
    protected $_pk = 'comment_id';
    protected $_cols = 'comment_id,qiang_id,shop_id,uid,order_id,score,content,have_photo,reply,reply_ip,reply_time,closed,clientip,dateline';

    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __TIME;
        $data['clientip'] = $data['clientip'] ? $data['clientip'] : __IP;
        $data['closed'] = $data['closed'] ? $data['closed'] : 0;
        if($comment_id = $this->db->insert($this->_table, $data, true)){
            $this->flush();
        }        
        return $comment_id;        
    }

    public function items_join_member($filter,$order_by = array(),$page=1,$limit = 50,&$count = 0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('member')." w ON o.uid = w.uid WHERE {$where}";
        if($res_count = $this->db->Execute($count_sql)){
            if($count_row = $res_count->fetch()){
                $count = $count_row['count'];
            } 
            $sql =  "SELECT  o.*, w.`mobile` as 'member_mobile', w.`nickname` as 'member_nickname' FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('member')." w ON o.uid = w.uid WHERE {$where} {$order_by} {$limit}";
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
        if($res_count = $this->db->Execute($count_sql)){
            if($count_row = $res_count->fetch()){
                $count = $count_row['count'];
            } 
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