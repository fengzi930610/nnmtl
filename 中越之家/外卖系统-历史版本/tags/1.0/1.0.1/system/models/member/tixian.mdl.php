<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/22
 * Time: 15:26
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Member_Tixian extends Mdl_Table {
    protected $_table = 'member_tixian';
    protected $_pk    = 'tixian_id';
    protected $_cols  = 'tixian_id,uid,money,intro,status,dateline';

    public function tixian($uid,$data){
        $data['dateline'] = __TIME;
        if(K::M('member/member')->update_money($uid,-$data['money'],"用户申请余额提现￥:".$data['money'])){
            if($this->create($data)){
                return true;
            }else{
                K::M('member/member')->update_money($uid,$data['money'],"提现失败退回余额￥".$data['money']);
                return false;
            }
        }else{
            return false;
        }
    }

    public function agree($tixian_id){
        if(!$tixian_id){
            return false;
        }else if(!$tixian = $this->detail($tixian_id)){
            return false;
        }else if($tixian['status']!=0){
            return false;
        }else {
            if($this->update($tixian_id,array('status'=>1))){
                $log_data = array();
                $log_data['uid'] = $tixian['uid'];
                $log_data['title'] = '平台同意提现';
                $log_data['content'] = "平台同意您的￥(".$tixian['money'].")提现申请,请注意查收金额";
                $log_data['type'] = 2;
                $log_data['is_read'] = 0;
                $log_data['order_id'] = 0;
                $log_data['can_id'] = 0;
                K::M('member/message')->create($log_data);
                return true;
            }else{
              return false;
            }
        }
    }

    public function unagree($tixian_id,$reason = ""){
        if(!$tixian_id){
            return false;
        }else if(!$reason){
           return false;
        }else if(!$tixian = $this->detail($tixian_id)){
            return false;
        }else {
            if($this->update($tixian_id,array('status'=>2))){
                K::M('member/member')->update_money($tixian['uid'],$tixian['money'],"平台拒绝退款申请，退回余额￥：".$tixian['money'].'。理由：'.$reason);
                return true;
            }else{
                return false;
            };
        }

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