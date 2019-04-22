<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27
 * Time: 15:55
 */
if(!defined('__CORE_DIR')){

    exit("Access Denied");

}
class Mdl_Cash_Bills extends Mdl_Table {

    protected $_table = 'cash_bills';
    protected $_pk = 'bills_id';
    protected $_cols = 'bills_id,bills_sn,staff_id,status,amount,dateline,fee,pei_amount';

    public function create($data, $checked = false)
    {
        $data['bills_sn'] = date("Ymd");
        $data['dateline'] = __TIME;
        $format_sql = $this->_insert_sql($data);
        $sql = 'INSERT INTO '.$this->table($this->_table).$format_sql.' ON DUPLICATE KEY UPDATE '.'amount=amount+'.$data['amount'].',fee=fee+'.$data['fee'].',pei_amount=pei_amount+'.$data['pei_amount'];
        return  $this->db->Execute($sql);
    }

    protected function _insert_sql($data)

   {
    ksort($data);
    return "(`".implode("`,`",array_keys($data))."`) VALUES('".implode("','",$data)."')";
   }
    public function create_bills($order){
        $insert_data = array();
        $insert_data['staff_id'] = $order['staff_id'];
        $insert_data['status'] = 0;
        $insert_data['amount'] = $order['shop_amount'];
        $insert_data['fee'] = $order['fee'];
        $insert_data['pei_amount'] = $order['pei_amount'];
        if($this->create($insert_data)){
            $filter = array();
            $filter['staff_id'] = $insert_data['staff_id'];
            $filter['bills_sn'] = date('Ymd');
            $detail = $this->find($filter);
            $log_data = array();
            $log_data['bills_id'] = $detail['bills_id'];
            $log_data['bills_sn'] = date('Ymd');
            $log_data['staff_id'] = $order['staff_id'];
            $log_data['order_id']=$order['order_id'];
            $log_data['amount'] = $order['shop_amount'];
            $log_data['fee'] = $order['fee'];
            $log_data['dateline'] = __TIME;
            $log_data['pei_amount'] = $order['pei_amount'];
            return K::M('cash/billslog')->create($log_data);
        }else{
            return false;
        }
    }


    public function items_by_day($filter,$page=1,$limit=50,&$count=0){
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $orderby = $this->order(array('bills_id'=>"DESC"));

        $sql = "SELECT sum(amount) as sum_amount,sum(fee) as sum_fee,sum(pei_amount) as sum_pei_amount,bills_sn  FROM ".$this->table($this->_table)." WHERE".$where.'  GROUP BY bills_sn ORDER BY  bills_sn DESC'.$limit;

        $count_SQL =  "SELECT count(1) as count FROM ".$this->table($this->_table)." WHERE  ".$where.' GROUP BY bills_sn  ';

        $count_object  =$this->db->Execute($count_SQL);

        if($count_arr = $count_object->fetch()){

           $count = $count_arr['count'];

            $return_arr = array();

            if($res = $this->db->Execute($sql)){

                while($row = $res->fetch()){

                    $return_arr[] = $row;
                }
            }

            return $return_arr;
        }
        return array();




    }






}