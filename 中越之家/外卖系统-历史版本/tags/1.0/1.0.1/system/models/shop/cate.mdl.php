<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Shop_Cate extends Mdl_Table
{   
  
    protected $_table = 'shop_cate';
    protected $_pk = 'cate_id';
    protected $_cols = 'cate_id,parent_id,title,icon,photo,orderby,dateline';
    protected $_pre_cache_key = 'shop-cate-list';
    protected $_orderby = array('parent_id'=>'ASC', 'orderby'=>'ASC');
    public function options()
    {
        $options = array();
        if($items = $this->fetch_all()){
            foreach($items as $k=>$v){
                $options[$k] = $v['title'];
            }
        }
        return $options;
    }
    public function children_ids($cate_id, $glue=',')
    {
        if(!$cate_id = (int)$cate_id){
            return false;
        }
        $cate_ids = array($cate_id=>$cate_id);
        if($items = $this->fetch_all()){
            foreach((array)$items as $k=>$v){
                if(in_array($v['parent_id'], $cate_ids)){
                    $cate_ids[$v['cate_id']] = $v['cate_id'];
                }
            }
        }
        return implode($glue, $cate_ids);
    }
    public function update($pk, $data, $checked=false)
    {
        $this->_checkpk();
        if(!$checked && !($data = $this->_check($data,  $pk))){
            return false;
        }
        if($ret = $this->db->update($this->_table, $data, $this->field($this->_pk, $pk))){
            $this->flush();
        }
        return $ret;
    }
    public function tree()
    {
        $tree = $tree_re_format= array();
        if($items = $this->fetch_all()){
            foreach($items as $k=>$v){
                if(!$v['parent_id']){
                    $v['childrens'] = array();
                    $v['children'] = $v['childrens'];
                    $v['all'] = array($k=>$v['cate_id']); //一级分类和旗下二级分类的集合
                    $tree[$k] = $v;
                }                
            }
            foreach($items as $k=>$v){
                if($tree[$v['parent_id']]){
                    $tree[$v['parent_id']]['childrens'][$k] = $v;
                    $tree[$v['parent_id']]['all'][$k] = $v['cate_id'];
                }
            }
        }
        
        foreach ($tree as $items){
            $items['count'] = count($items['all'])-1;
            $tree_re_format[] = $items;
           
            
        }
        return $tree_re_format;        
    }

    public function getChildren($cate_id ,$ty= true) {
        $local = array();
        //暂时 只支持 2级分类
        if($ty) $local[] = $cate_id;
        if($res = $this->items(array('parent_id'=>$cate_id))){
            foreach($res as $k=>$v){
                $local[] = $v['cate_id'];
                if($r = $this->items(array('parent_id'=>$v['cate_id']))){
                    foreach($r as $k1=>$v1){
                        $local[] = $v1['cate_id'];
                    }
                }
            }
        }
        return $local;
    }
    
    
    

    

}