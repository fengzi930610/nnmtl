<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Comment extends Mdl_Table
{   
  
    protected $_table = 'waimai_comment';
    protected $_pk = 'comment_id';
    protected $_cols = 'comment_id,shop_id,uid,order_id,score,score_peisong,score_avg,content,pei_time,have_photo,reply,reply_ip,reply_time,closed,clientip,dateline,extend,is_anonymous';

    public function timestr($minute)
    {
        $str = '';
        if($minute <= 60){
            $str = '准时送达';
        }else if($minute >= 180){
            $str = '3小时以上';
        }else{
            if($minute%60 == 0){
                $str = intval($minute/60).'小时';
            }else{
                $str = intval($minute/60).'小时'.($minute%60).'分钟';
            }
            
        }
        return $str;
    }
    /**
     * 总评论数
     * @param $shop_id
     */
    public function comments($shop_id)
    {
         $sql = "SELECT SUM(`score`) FROM ".$this->table($this->_table)." WHERE `shop_id`={$shop_id}";
        return $this->db->GetOne($sql);
    }
    
    public function update_score($shop_id)
    {
        if(!$shop_id = (int)$shop_id){
            return false;
        }
        $sql = "SELECT SUM(`score`) total_score, COUNT(1), comment_count FROM ".$this->table($this->_table)." WHERE `shop_id`='{$shop_id}'";
        if($row = $this->GetRow($sql)){
            K::M('waimai/waimai')->update($shop_id, array('score'=>$row['total_score'], 'comments'=>$row['comment_count']));
        }
    }

    public function _format_row($row)
    {   
        $row['product_list'] = $row['extend'] ? unserialize($row['extend']) : array();// 产品详情
        $row['order_intro'] = $row['product_list'] ? $row['product_list']['intro'] : "";// 订单备注
        if ($row['product_list']) {
            unset($row['product_list']['intro']);
            unset($row['extend']);
        }
        return $row;
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