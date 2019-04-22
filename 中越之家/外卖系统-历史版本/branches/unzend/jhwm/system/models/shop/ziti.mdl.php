<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Shop_Ziti extends Mdl_Table
{   
  
    protected $_table = 'shop_ziti';
    protected $_pk = 'addr_id';
    protected $_cols = 'addr_id,shop_id,title,region,province_id,city_id,area_id,address_detail,phone,fuwu_stime,fuwu_ltime,photo1,photo2,photo3,photo4,description,is_store,orderby,dateline,closed,data_city_id';

    public function create($data)
    {
    	if(!$data = $this->_check($data)){
			return false;
		}
		$data['dateline'] = __CFG::TIME;
		return $this->db->insert($this->_table, $data, true);
    }

    protected function _format_row($row)
    {
        if($row['region']) {
            $region = explode(',', $row['region']);
            $row['province_name'] = $region[0];
            $row['city_name'] = $region[1];
            $row['area_name'] = $region[2];
        }
        return $row;
    }
}