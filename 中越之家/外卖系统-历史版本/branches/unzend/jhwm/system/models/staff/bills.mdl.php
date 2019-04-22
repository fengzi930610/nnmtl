<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Staff_Bills extends Mdl_Table
{
    protected $_table = 'staff_bills';
    protected $_pk = 'bills_id';
    protected $_cols = 'bills_id,bills_sn,staff_id,status,freight_amount,amount,diff_amount,orders,dateline';

	public function create($data)
	{
		$data['dateline'] = __TIME;
		$data['bills_sn'] = date('Ymd');
		$format_sql = $this->_insert_sql($data);
		$sql = 'INSERT INTO '.$this->table($this->_table).$format_sql.' ON DUPLICATE KEY UPDATE '.'freight_amount=freight_amount+'.$data['freight_amount'].','.'amount=amount+'.$data['amount'].','.'orders=orders+'.$data['orders'];
		return  $this->db->Execute($sql);
	}

	protected function _insert_sql($data)
	{
		ksort($data);
		return "(`".implode("`,`",array_keys($data))."`) VALUES('".implode("','",$data)."')";
	}

	protected function _format_row($row)
    {
        $row['bills_sn_lable'] = date('Y-m-d', strtotime($row['bills_sn']));
        return $row;
    }

    public function items_join_by_staff_id($filter,$page=1,$limit=50){
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT SUM(amount) as amount,SUM(freight_amount-amount) as fee ,`staff_id`   FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `staff_id` $limit";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['staff_id']] = $row;
            }
        }
        return $items;
    }

    // 关联配送员主表并分组sum 求和 扩展GROUP字段
    public function sum_join_staff_group($filter, $orderby=null, $p=1, $l=50, &$count=0)
    {
        $where = '1';
        $ext_sql = $_field = "";
        $field = array('city_id'=>'s.`city_id`', 'bills_id'=>'ext.`bills_id`', 'bills_sn'=>'ext.`bills_sn`',
            'staff_id'=>'ext.`staff_id`', 'status'=>'ext.`status`', 'dateline'=>'ext.`dateline`');
        $group = $_group = "ext.`bills_sn`"; // 默认按天
        $key = "bills_sn";
        $sum = "SUM(ext.`freight_amount`) AS freight_amount, SUM(ext.`orders`) AS orders,";
        if(is_array($filter[':SUM']) && !empty($filter[':SUM'])){
            foreach ($filter[':SUM'] as $v) {
                if($this->field_exists("{$v}")){
                    if (!in_array($v, array('freight_amount', 'orders'))) {
                        $sum .= " SUM(ext.`{$v}`) AS {$v},";
                    }
                }
            }
        }else{ // 没有给默认全部  配送费、配送员应得、分站佣金fee（`freight_amount`-`amount`）
            $sum = " SUM(ext.`freight_amount`) AS freight_amount, SUM(ext.`amount`) AS amount, SUM(ext.`freight_amount`-ext.`amount`) AS fee, SUM(ext.`orders`) AS orders,";
        }
        if (isset($filter[':GROUP']) && !empty($filter[':GROUP'])) {
            if($this->field_exists($filter[':GROUP'])){
                $group = "ext.`".$filter[':GROUP']."`";
                $key = $filter[':GROUP'];
            }elseif (K::M('staff/staff')->field_exists($filter[':GROUP'])) {
                $group = "s.`".$filter[':GROUP']."`";
                $key = $filter[':GROUP'];
            }
        }
        foreach ($field as $k => $v) {
            if ($key != $k) {
                $_field .= ", {$v}";
            }
        }
        $_group = $group.$_field;
        if(is_array($filter)){
            $ext_sql = " JOIN ".$this->table('staff')." s ON s.`staff_id`=ext.`staff_id`";
        }
        $where .= " AND ". $this->where($filter, 'ext.');
        $orderby = $this->order($orderby);
        $limit = $this->limit($p, $l);
        $sql = "SELECT COUNT(1) FROM ".$this->table($this->_table)." ext $ext_sql WHERE $where GROUP BY $group";
        $items = array();
        if($count = (int) $this->db->GetOne($sql)){
            $count = $this->db->Execute($sql) ? $this->db->GetOne("SELECT FOUND_ROWS()") : 0; // 获取记录数，不能带limit参数，否则返回值为limit的值
            $sql = "SELECT *, ROUND(`freight_amount` / `orders`, 2) AS avg_amount FROM (SELECT $_group, {$sum} COUNT(1) as count FROM ".$this->table($this->_table)." ext $ext_sql WHERE $where GROUP BY $group) t $orderby $limit";
            if($rs = $this->db->query($sql)){
                while($row = $rs->fetch()){
                    $items[$row["{$key}"]] = $row;
                }
            }
        }
        return $items;
    }

    public function items_group_by_day($filter,$order_by = array(),$page,$limit,&$count){
	    $where  = $this->where($filter);
	    $order_by = $this->order($order_by);
	    $limit = $this->limit($page,$limit);
        $count_sql = "SELECT count(DISTINCT bills_sn) as count FROM ".$this->table($this->_table)." WHERE {$where}";
        $count_res = $this->db->Execute($count_sql);
        $count_res1 = $count_res->fetch();
        $count = $count_res1['count'];
        $sql = " SELECT SUM(freight_amount) as freight_amount, SUM(amount) as amount,bills_sn FROM ".$this->table($this->_table)." WHERE {$where} GROUP by bills_sn $order_by  $limit";
        $items = array();
        if($rs = $this->db->query($sql)){
            while($row = $rs->fetch()){
                $items[] = $row;
            }
        }
        return $items;
    }

    public function entry($bills_id){
        if(!$bills_id){
           return false;
        }else if(!$bills = $this->detail($bills_id)){
            return false;
        }else if($bills['status'] == 1){
            return false;
        }else  if($bills['bills_sn'] >= date('Ymd')){
            return false;
        }else{
            $this->db->begin();
            $this->update($bills_id,array('status'=>1));
            $intro = sprintf('您%s的对账单已入账：金额:%s', $bills['bills_sn'], $bills['amount']);

            if(($bills['amount'] > 0) && ($log_id = K::M('staff/staff')->update_money($bills['staff_id'], $bills['amount'], $intro, ''))){
                K::M('staff/log')->update($log_id, array('extend'=>serialize(array('type'=>1,'can_id'=>$bills['bills_id']))));
            }
            if($this->db->tranform_errno > 0){
                $this->db->rollback();
                return false;
            }else{
                $this->db->commit();
                return true;
            }
        }
    }
}