<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: city.mdl.php 3159 2014-01-22 01:54:39Z youyi $
 */
class Mdl_Data_City extends Mdl_Table
{   
  
    protected $_table = 'data_city';
    protected $_pk = 'city_id';
    protected $_cols = 'city_id,city_name,city_name_vn,province_id,pinyin,theme_id,logo,phone,city_code,mobile,mail,kfqq,orderby,audit,dateline';
    protected $_orderby = array('province_id'=>'ASC', 'orderby'=>'ASC', 'city_id'=>'ASC');
    protected $_pre_cache_key = 'data-city-list';
    
    public function create($data)
    {
        if(!$data = $this->_check_schema($data)){
            return false;
        }
        $data['dateline'] = __CFG::TIME;
        if($city_id = $this->db->insert($this->_table, $data, true)){
            $this->flush();
        }
        return $city_id;
    }
    public function update($pk, $data, $checked=false)
    {
        $this->_checkpk();
        if(!$data = $this->_check_schema($data,  $pk)){
            return false;
        }
        unset($data['city_id'], $data['dateline']);
        if($this->db->update($this->_table, $data, $this->field($this->_pk, $pk))){
            $this->flush();
        }
        return true;
    }
    public function detail($city_id, $closed=false)
    {
        return $this->city($city_id);
    }
    public function fetch_all()
    {
        if(!$citys = $this->cache->get($this->_pre_cache_key)){
            $sql = "SELECT * FROM ".$this->table($this->_table)." ORDER BY orderby ASC";
            if($rs = $this->db->Execute($sql)){
                $site = K::$system->config->get('site');
                while($row = $rs->fetch()){
                    $row = $this->_format_row($row);
                    // if($site['city_domain']){
                    //     $row['domain'] = $row['pinyin'].'.'.$site['city_domain'];
                    //     $row['siteurl'] = 'http://'.$row['domain'];
                    // }
                    // if($province = K::M('data/province')->province($row['province_id'])){
                    //     $row['province_name'] = $province['province_name'];
                    // }
                    $citys[$row[$this->_pk]] = $row;
                }
            }
            $this->cache->set($this->_pre_cache_key, $citys, $this->_cache_ttl);
        }
        return $citys;
    }
    
    public function options($province_id=0)
    {
        $options = array();
        if($citys = $this->fetch_all()){
            foreach($citys as $k=>$v){
                if(!$province_id || $v['province_id'] == $province_id){
                    $options[$k] = $v['city_name'];
                }
            }
        }
        return $options;
    }
    public function options_by_province($province_id)
    {
        $options = array();
        if($province_id = (int)$province_id){
            if($citys = $this->fetch_all()){
                foreach($citys as $k=>$v){
                    if($v['province_id'] == $province_id){
                        $options[$k] = $v['city_name'];
                    }
                }
            }
        }
        return $options;        
    }
    public function city($city_id)
    {
        if(!$city_id = intval($city_id)){
            return false;
        }else if($items = $this->fetch_all()){
            return $items[$city_id];
        }
        return false;
    }
    
    public function items_by_province($province_id)
    {
        $city_list = array();
        if($province_id = (int)$province_id){
            if($citys = $this->fetch_all()){
                foreach($citys as $v){
                    if($province_id == $v['province_id']){
                        $city_list[$v['city_id']] = $v;
                    }
                }
            }
        }
        return $city_list;        
    }
        
    public function city_by_pinyin($py)
    {
        if($citys = $this->fetch_all()){
            foreach($citys as $v){
                if($py == $v['pinyin']){
                    return $v;
                }
            }
        }
        return false;
    }
    public function city_by_ip($ip)
    {
        if($loc = K::M('misc/location')->location($ip)){
            if($citys = $this->fetch_all()){
                foreach($citys as $city){
                    if($city['audit'] && strpos($loc, $city['city_name'])){
                        return $city;
                    }
                }
            }
        }
        return false;
    }
    public function city_by_code($code)
    {
        if($code && $citys = $this->fetch_all()){
            foreach($citys as $city){
                if($city['city_code'] == $code){
                    return $city;
                }
            }
        }
        return false;
    }
    protected function _format_row($row)
    {
        static $site = null;
        if($site === null){
            $site = K::$system->config->get('site');
        }
        if($site['city_domain']){
            $row['domain'] = $row['pinyin'].'.'.$site['city_domain'];
            $row['siteurl'] = 'http://'.$row['domain'];
        }
        if($province = K::M('data/province')->province($row['province_id'])){
            $row['province_name'] = $province['province_name'];
        }
        $row['py'] = strtoupper(substr($row['pinyin'], 0, 1));
        return $row;
    }
    protected function _check($data, $city_id=null)
    {
        if(isset($data['pinyin'])){
            if($city = $this->city_by_pinyin($data['pinyin'])){
                if(!$city_id || ($city['city_id'] != $city_id)){
                    $this->msgbox->add('城市拼音不能重复', 455);
                    return false;
                }
            }
        }
        return parent::_check($data, $city_id);
    }
}