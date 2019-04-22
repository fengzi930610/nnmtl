<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Ditui_Tixian extends Mdl_Table
{   
  
    protected $_table = 'ditui_tixian';
    protected $_pk = 'tixian_id';
    protected $_cols = 'tixian_id,ditui_id,money,intro,account_info,status,reason,updatetime,clientip,dateline,end_money';

    public function tixian($ditui_id,$money)
    {

        if(!$ditui_id == (int)$ditui_id){
            return false;
        }else if(!$ditui = K::M('ditui/ditui')->detail($ditui_id)){
            return false;
        }else if(!$money = (float)$money){
            $this->msgbox->add('提现金额不正确！',411);
        }else if($money > $ditui['money']){
            $this->msgbox->add('提现金额不正确！',412);
        }else{
            $tx_data = array();
            $tx_data['ditui_id'] = $ditui_id;
            $tx_data['money'] = $money;
            $tx_data['intro'] = substr($ditui['mobile'],0,3).'****'.substr($ditui['mobile'],-4).'('.$ditui_id.')'.'提现'.$money.'元';
            $tx_data['account_info'] = serialize(array('account_type'=>$ditui['account_type'],'account_name'=>$ditui['account_name'],'account_number'=>$ditui['account_number']));
            $tx_data['status'] = 0;
            if(K::M('ditui/ditui')->update_money($ditui_id,-$money)){
                if($tx_id = $this->create($tx_data)){
                    $account_info = $ditui['account_type'].'('.$ditui['account_name'].','.substr_replace($ditui['account_number'],'**** **** ****',0,strlen($account_number)-4).')';
                    K::M('ditui/log')->log($ditui_id,0,-$money,sprintf(L('账户资金提现:%s'), $account_info),'tixian');
                    return $tx_id;
                }else{
                    K::M('ditui/ditui')->update_money($this->uid,$money,false);
                    return false;
                }
            }else{
                return false;
            }              
        }
    }

    public function format_data($row)
    {        
        if($row['account_info']['account_number']){
            $account_number = $row['account_info']['account_number'];
            $row['account_info']['account_number'] = substr_replace($account_number,'**** **** ****',0,strlen($account_number)-4);
        }        
        return $row;
    }

    protected function _format_row($row)
    {
        $row['account_info'] = $row['account_info']?unserialize($row['account_info']):array();
        return $row;
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