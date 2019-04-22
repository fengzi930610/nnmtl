<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Shop_Comment extends Mdl_Table
{   
  
    protected $_table = 'shop_comment';
    protected $_pk = 'comment_id';
    protected $_cols = 'comment_id,shop_id,uid,order_id,score,score_fuwu,score_kouwei,content,pei_time,have_photo,reply,reply_ip,reply_time,closed,clientip,dateline';
    
    
    public function create($data)
    {
    	$data['dateline'] = $data['dateline'] ? $data['dateline']: __CFG::TIME;
    	$data['clientip'] = __IP;
    	if ($comment_id = $this->db->insert($this->_table, $data, true)) {
            return $comment_id;
        }
    }
    public function count_uid_score($filter, $page=1, $limit=1000)
    {
    	if($filter['shop_id'] = (int)$filter['shop_id']) {
    		$where = $this->where($filter);
    		$limit = $this->limit($page, $limit);
    	    $sql = "SELECT `uid`, COUNT(1) as counts,SUM(`score`) as scores FROM {$this->table($this->_table)} WHERE {$where} GROUP BY `uid` ORDER BY `uid` DESC $limit";
    	    if ($row = $this->db->GetRow($sql)) {
	            return $row;
	        }
    	}
    }
    public function items_by_order_ids($ids)
    {
        if(is_array($ids)){
            $ids = implode(',', $ids);
        }       
        if(!K::M('verify/check')->ids($ids)){
            return false;
        }
        $where = "order_id IN ($ids)";
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE $where $orderby";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['order_id']] = $this->_format_row($row);
            }
        }
        return $items;
    }
    
    public function detail_by_order_id($order_id)
    {
        if(!$order_id = (int)$order_id){
            return false;
        }
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE `order_id`={$order_id}";
        if($row = $this->db->GetRow($sql)){
            $row = $this->_format_row($row);
        }
        return $row;
    }
    
    public function peitime()
    {
        $peitime = array();
        for($i=10;$i<=180;$i=$i+10){
            $peitime[$i] = $i;
        }
        foreach($peitime as $k =>$v){
            if($v%60 == 0){
                $peitime[$k] = ($v/60).'小时';
            }else{
                $peitime[$k] = $v.'分钟';
            }
        }
        $peitime[181] = '3小时以上';
        return $peitime;
    }
   
    
}