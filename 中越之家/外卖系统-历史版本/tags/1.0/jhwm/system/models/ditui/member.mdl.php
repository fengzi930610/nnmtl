<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Ditui_Member extends Mdl_Table
{   
  
    protected $_table = 'ditui_member';
    protected $_pk = 'mid';
    protected $_cols = 'mid,ditui_id,uid,signup_amount,first_amount,first_order_id,first_order_amount,first_order_time,clientip,dateline,day';
    public function create($data, $checked=false)
    {
        $data['clientip'] = $data['clientip'] ? $data['clientip'] : __IP;
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __CFG::TIME;
        return $this->db->insert($this->_table, $data, true);
    }
    // 近一月推荐用户曲线
    public function ditui_member($filter=null, $page=1,$limit=31) 
    {
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT FROM_UNIXTIME(dateline,'%Y-%m-%d') as date,COUNT(uid) as uids FROM {$this->table($this->_table)} WHERE {$where} GROUP BY date ORDER BY date DESC $limit";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['date']] = $row;
            }
        }
        return $items;
    }
    // 近一月地推用户成功下单数
    public function first_amount($filter=null, $page=1,$limit=31) 
    {
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT FROM_UNIXTIME(dateline,'%Y-%m-%d') as date, COUNT(1) as first FROM {$this->table($this->_table)} WHERE {$where} AND first_amount>0 GROUP BY date ORDER BY date DESC $limit";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[] = $row;
            }
        }
        return $items;
    }
 
    // 近一月推用户收入曲线
    public function ditui_income($filter=null, $page=1,$limit=31)
    {
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT FROM_UNIXTIME(dateline,'%Y-%m-%d') as date,SUM(`signup_amount`+`first_amount`) as money FROM {$this->table($this->_table)} WHERE {$where} GROUP BY date ORDER BY date DESC $limit";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['date']] = $row;
            }
        }
        return $items;
    }
    public function count_income($filter)
    {
        $where = $this->where($filter);
        $sql = "SELECT SUM(`signup_amount`+`first_amount`) as money FROM {$this->table($this->_table)} WHERE {$where}";
        if($row = $this->db->GetRow($sql)) {
            return $row['money'];
        }
    }

    public function send_hongbao_by_cfg($uid,$cfg)
    {
        if (is_array($cfg) && !empty($cfg)) {
            foreach ($cfg as $k => $v) {
                $ltime = __TIME + $v['day'] * 86400 - 1;
                $hongbao = array(
                    'from'=>$v['type'],
                    'min_amount'=>$v['min_amount'],
                    'amount'=>$v['amount'],
                    'ltime'=>$ltime,
                    'title'=>L('推广员邀请新人红包'),
                    'type'=>7,
                    'limit_stime'=>$v['stime'],
                    'limit_ltime'=>$v['ltime'],
                    );
                K::M('hongbao/hongbao')->send($uid, $hongbao);
            }
            return true;
        }
        return false;
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