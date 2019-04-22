<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Youhui extends Mdl_Table
{
    protected $_table = 'waimai_youhui';
    protected $_pk = 'youhui_id';
    protected $_cols = 'youhui_id,shop_id,order_amount,youhui_amount,use_count,orderby,dateline';
    public function create($data)
    {
	    $data['use_count'] = 0;
	    $data['dateline'] = $data['dateline'] ? $data['dateline'] :  __CFG::TIME;
        if ($youhui_id = $this->db->insert($this->_table, $data, true)) {
            return $youhui_id;
        }
    }

    public function delete($youhui_id)
    {
        $detail = K::M('waimai/youhui')->detail($youhui_id);
        $shop_id = $detail['shop_id'];
        $filter = array('shop_id' => $shop_id);

        $sql = "delete from ".$this->table($this->_table)." where youhui_id = ". $youhui_id;
        $is_delete = $this->db->execute($sql);

        $youhui = array();
        if($arr_youhui = K::M('waimai/youhui')->items($filter)){
            foreach ($arr_youhui as $key => $value) {
                $youhui[] = (int)$value['order_amount'].":".$value['youhui_amount'];
            }
        }
        if($youhui){
            $youhui = implode(',', $youhui);
        }
        else{
            $youhui = '';
        }
        K::M('waimai/waimai')->update($shop_id, array('youhui'=>$youhui));

        return $is_delete;
    }

    public function order_youhui($shop_id, $amount)
    {
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE `shop_id`={$shop_id} AND `order_amount`<={$amount} ORDER BY order_amount DESC LIMIT 0,1";
        $row = $this->db->GetRow($sql);
        return empty($row) ? false : $row;
    }
    
    public function update_youhui($shop_id, $order_youhui=array())
    {
        if(!$shop_id = (int)$shop_id){
            return false;
        }
        $this->db->Execute("DELETE FROM ".$this->table($this->_table)." WHERE shop_id=".$shop_id);
        $sql = $youhui = array();
        foreach((array)$order_youhui as $k=>$v){
            $k = (float)$k;
            $v = (float)$v;
            if($k && $v){
                $sql[] = "('{$shop_id}', '{$k}', '{$v}')";
                $youhui[] ="{$k}:{$v}";
            }
        }
        if($sql){
            $sql = "INSERT INTO ".$this->table($this->_table)."(`shop_id`,`order_amount`,`youhui_amount`) VALUES".implode(',', $sql);
            $this->db->Execute($sql);            
        }
        K::M('waimai/waimai')->update($shop_id, array('youhui'=>implode(',', $youhui)));
        return true;
    }
    
}
