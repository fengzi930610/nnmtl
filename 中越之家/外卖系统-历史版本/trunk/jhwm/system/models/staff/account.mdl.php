<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Account extends Mdl_Table
{   
	protected $_table = 'staff_account';
    protected $_pk = 'account_id';
    protected $_cols = 'account_id,staff_id,type,title,name,account,is_default,dateline';
    //账户列表
    public function bank_items()
    {
    	$items = array(
            array('title_value'=>'huishang', 'title'=>'徽商银行'),
            array('title_value'=>'nongye', 'title'=>'农业银行'),
            array('title_value'=>'jianshe', 'title'=>'建设银行'),
            array('title_value'=>'jiaotong', 'title'=>'交通银行'),
            array('title_value'=>'gongshang', 'title'=>'工商银行'),
            array('title_value'=>'zhaoshang', 'title'=>'招商银行'),
            array('title_value'=>'pufa', 'title'=>'浦发银行'),
            array('title_value'=>'jiujiang', 'title'=>'九江银行'),
            array('title_value'=>'xingye', 'title'=>'兴业银行'),
    	);
    	return $items;
    }
    //设置账户
    public function account($data, $staff_id)
    {
		if($row = $this->find(array('staff_id'=>$staff_id))){
			return $this->update($row['account_id'], $data);
		}else{
			$data['staff_id'] = $staff_id;
			return $this->create($data);
		}
//        $sql = "DELETE FROM ".$this->table($this->_table)." WHERE `staff_id`={$staff_id}";
//        $this->db->Execute($sql);
//        // $data =  array_map(function($n){return is_string($n) ? "'{$n}'" : $n;}, $data);
//        // $data = implode(",", $data);
//        $sql  = "INSERT INTO ".$this->table($this->_table)." VALUES($data)";
//        return $this->db->Execute($sql);
    }
    public function detail_by_staff_id($staff_id)
    {
        if(!$staff_id = (int)$staff_id){
            return false;
        }
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE `staff_id`={$staff_id}";
        if($row  = $this->db->GetRow($sql)){
            $row = $this->_format_row($row);
        }
        return $row;
    }
    //获取账户
    public function getone($staff_id)
    {
        return $this->detail_by_staff_id($staff_id);
    }
}