<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Qiang_Bills extends Mdl_Table
{

    protected $_table = 'qiang_bills';
    protected $_pk = 'bills_id';
    protected $_cols = 'bills_id,bills_sn,shop_id,status,user_amount,amount,fee,freight,dateline';

    public function create($data)
    {
        $data['dateline'] = __TIME;
        $data['bills_sn'] = date('Ymd');
        $format_sql = $this->_insert_sql($data);
        $sql = 'INSERT INTO '.$this->table($this->_table).$format_sql.' ON DUPLICATE KEY UPDATE '.'amount=amount+'.$data['amount'].','.'fee=fee+'.$data['fee'].',freight=freight+'.$data['freight'].',user_amount=user_amount+'.$data['user_amount'];
        return  $this->db->Execute($sql);
    }
    protected function _insert_sql($data)
    {
        ksort($data);
        return "(`".implode("`,`",array_keys($data))."`) VALUES('".implode("','",$data)."')";
    }

    public function get_bills_amount($month)
    {
        $month_time = strtotime($month)."~".(strtotime($month."+1 month")-1);
        $data = array();
        $data['fee'] = K::M('qiang/bills')->sum(array('dateline'=>$month_time),'fee');
        $data['roof_amount'] = 0;
        $data['roof'] = $data['fee'] - $data['roof_amount'];
        $last_month_time = strtotime(date("Y-m-01",strtotime($month))."-1 month")."~".(strtotime($month)-1);
        $data['last_fee'] = K::M('qiang/bills')->sum(array('dateline'=>$last_month_time),'fee');
        $data['last_roof_amount'] = 0;
        $data['last_roof'] = $data['last_fee'] - $data['last_roof_amount'];
        $data['amount'] = K::M('qiang/bills')->sum(array('dateline'=>$month_time),'amount');
        $data['shop_amount'] = 0;
        $data['shop'] =  $data['amount']-$data['shop_amount'];
        $data['last_amount'] = K::M('qiang/bills')->sum(array('dateline'=>$last_month_time),'amount');
        $data['last_shop'] =   $data['last_amount']-$data['shop_amount'];
        return $data;
    }

    public function group_by_type($filter,$stime,$ltime,$order_by = array('dateline'=>"DESC"))
    {
        $where = $this->where($filter);
        $order_by = $this->order($order_by);
        $data = K::M('helper/date')->get_arr_by_type($stime,$ltime,'d');
        $arr = $items = array();
        $sql = "SELECT  SUM(fee) as fee,SUM(amount) as amount,`bills_sn` FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `bills_sn`  {$order_by}";
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['bills_sn']] = $row;
            }
        }
        $arr['x'] = range(1,31);
        foreach ($data as $k=>$v){
            $arr['fee'][] = $items[$k]['fee']?(float)$items[$k]['fee']:0;
            $arr['amount'][] = $items[$k]['amount']?(float)$items[$k]['amount']:0;
        }
        return $arr;
    }


}
