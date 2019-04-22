<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: item.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Adv_Item extends Mdl_Table
{   
  
    protected $_table = 'adv_item';
    protected $_pk = 'item_id';
    protected $_cols = 'item_id,adv_id,city_id,title,link,thumb,script,clicks,stime,ltime,desc,target,orderby,audit,closed,dateline,wxapp_link';
    protected $_orderby = array('orderby'=>'ASC', 'item_id'=>'DESC');
    protected $_pre_cache_key = 'adv-item-list';
    
    public function create($data)
    {
        if(!$data = $this->_check($data)){
            return false;
        }
        $data['dateline'] = __CFG::TIME;
        if($item_id = $this->db->insert($this->_table, $data, true)){
            $this->flush();
        }
        return $item_id;
    }
    public function update($item_id, $data, $checked=false)
    {
        if(!$item_id = intval($item_id)){
            return false;
        }else if(!$checked && !($data = $this->_check($data,  $item_id))){
            return false;
        }
        if($ret = $this->db->update($this->_table, $data, $this->field($this->_pk, $item_id), true)){
            $this->flush();
        }
        return $ret;
    }
    public function items_by_adv($adv_id)
    {
        $items = array();
        $cache_key = $this->_pre_cache_key.'-adv-'.(int)$adv_id;
        if(!$adv_id = intval($adv_id)){
            return false;
        }else if(!$items = K::M('cache/cache')->get($cache_key)){
            $items = $this->items(array('adv_id'=>$adv_id, 'closed'=>0), $this->_orderby, 1, 500);
            K::M('cache/cache')->set($cache_key, $items);
        }

        return $items;
    }
    public function item($item_id)
    {
        if(!$item_id = intval($item_id)){
            return false;
        }else if($items = $this->fetch_all()){
            return $items[$item_id];
        }
        return false;
    }
    protected function _format_row($row)
    {
        $site = K::$system->_CFG['site'];
        if($city = K::M('data/city')->city($row['city_id'])){
            $row['city_name'] = $city['city_name'];
        }
        $row['stime_format'] = empty($row['stime']) ? 0 : date('Y-m-d', $row['stime']);
        $row['ltime_format'] = empty($row['ltime']) ? 0 : date('Y-m-d', $row['ltime']);
        $link = trim($row['link']);
        if(empty($link)){
            $row['clickurl'] = '###';
            $row['target'] = '';
        }else if(substr($link, 0, 1) == '#'){
            $row['clickurl'] = $link;
            $row['target'] = '';
        }else if($site['rewrite']){
            $row['clickurl'] = $site['siteurl'].'/market-adclick-'.$row['item_id'].'.html';
        }else{
            $row['clickurl'] = $site['siteurl'].'/index.php?market-adclick-'.$row['item_id'].'.html';
        }
        return $row;
    }
    protected function _check($data, $item_id=null)
    {
        if(!$item_id || isset($data['title'])){
            if(empty($data['title'])){
                $this->msgbox->add('广告标题不能为空', 451);
                return false;
            }
            $data['title'] = K::M('content/html')->encode($data['title']);
        }
        if(!$item_id || isset($data['adv_id'])){
            if(!$data['adv_id'] = intval($data['adv_id'])){
                $this->msgbox->add(' 未指定要保存到的广告位', 452);
                return false;
            }
        }
        // if(!$item_id || isset($data['city_id'])){
        //     if(empty($data['city_id'])){
        //         $this->msgbox->add('广告未关联城市', 451);
        //         return false;
        //     }
        // }        
        if(isset($data['stime'])){
            $data['stime'] = empty($data['stime']) ? 0 : strtotime($data['stime']);
        }
        if(isset($data['ltime'])){
            $data['ltime'] = empty($data['ltime']) ? 0 : strtotime($data['ltime']);
        }
        if(isset($data['audit'])){
            $data['audit'] = $data['audit'] ? 1 : 0;
        }
        return parent::_check($data);       
    }
}