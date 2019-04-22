<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Verify extends Mdl_Table
{   
  
    protected $_table = 'waimai_verify';
    protected $_pk = 'shop_id';
    protected $_cols = 'shop_id,id_name,id_number,id_photo,shop_photo,verify_dianzhu,yz_number,yz_photo,verify_yyzz,cy_number,cy_photo,verify_cy,refuse,verify,verify_time,updatetime,company_name,yz_photo_s,id_photo_s,env_photo,id_photo_sf,id_photo_f,yz_addr,cy_addr,yz_time,cy_time,yz_name,cy_name';
    public function create($data, $checked=false)
    {
        
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        return $this->db->insert($this->_table, $data, false);
    }
   

}