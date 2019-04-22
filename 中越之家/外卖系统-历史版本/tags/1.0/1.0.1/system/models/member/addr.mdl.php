<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Member_Addr extends Mdl_Table
{
    protected $_table = 'member_addr';
    protected $_pk = 'addr_id';
    protected $_cols = 'addr_id,uid,contact,mobile,addr,house,is_default,dateline,type,lat,lng';
    public function set_default($uid, $addr_id)
    {
        if(!($uid = (int)$uid) || !($addr_id = (int)$addr_id)){
            return false;
        }
        $this->db->update($this->_table, array('is_default'=>0), "uid={$uid}");
        $this->db->update($this->_table, array('is_default'=>1), "uid={$uid} AND addr_id={$addr_id}");
        return true;
    }
    public function check_contact($contact)
    {
        $length = strlen($contact);
        if($length>2 && $length<16) {
            return $contact;
        }else {
            return false;
        }
    }
    public function check_mobile($mobile)
    {
        return K::M('verify/check')->vietnamMobile($mobile);
    }

    protected function _format_row($row)
    {
        if(isset($row['lat'])){
            $row['lat'] = bcdiv($row['lat'], 1000000, 6);
        } 
        if(isset($row['lng'])){
            $row['lng'] = bcdiv($row['lng'], 1000000, 6);
        }
        if(!in_array($row['type'], array(1,2,3,4))){
            $row['type'] = 0;
        }
        return $row;
    }
    protected function _check($data, $addr_id=null)
    {
        if(isset($data['lat'])){
            $data['lat'] = round(bcmul($data['lat'], 1000000));
        } 
        if(isset($data['lng'])){
            $data['lng'] = round(bcmul($data['lng'], 1000000));
        }
        return parent::_check($data, $addr_id);
    }
    
    public function where($filter=null, $pre='', $ANDOR='AND')
    {
        
        if(is_array($filter)){
            if(isset($filter['lat'])){
                $lat = explode('~',$filter['lat']);
                if(count($lat)>1){  //判断查询的时候是否是范围查询
                    $filter['lat'] = $lat[0].'~'.$lat[1];
                }else{
                   $filter['lat'] = bcdiv((int)$filter['lat'], 1000000,6); 
                }   
            }
            if(isset($filter['lng'])){
                $lng = explode('~',$filter['lng']);
                if(count($lng)>1){ //判断查询的时候是否是范围查询
                    $lat = explode($filter['lng']);
                    $filter['lng'] = $lng[0].'~'.$lng[1];
                }else{
                   $filter['lng'] = bcdiv((int)$filter['lng'], 1000000,6); 
                }
            } 
        }
        return parent::where($filter, $pre, $ANDOR);
    }

}
