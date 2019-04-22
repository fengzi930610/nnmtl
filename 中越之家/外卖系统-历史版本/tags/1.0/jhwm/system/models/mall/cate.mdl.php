<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Mall_Cate extends Mdl_Table
{   
  
    protected $_table = 'mall_cate';
    protected $_pk = 'cate_id';
    protected $_cols = 'cate_id,parent_id,title,icon,orderby';
    protected $_orderby = array('parent_id'=>'ASC', 'orderby'=>'ASC');
    protected $_pre_cache_key = 'mall-cate-list';

    public function tree()
    {
        $tree = array();
        if($items = $this->fetch_all()){
            foreach($items as $k=>$v){
                if(empty($v['parent_id'])){
                    $v['childrens'] = array();
                    $tree[$k] = $v;
                }                
            }
            foreach($items as $k=>$v){
                if($v['parent_id'] && $tree[$v['parent_id']]){
                    $tree[$v['parent_id']]['childrens'][$k] = $v;
                }
            }
        }        
        return $tree;        
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

    // 获取商品分类名称
    public function get_cate()
    {
        return $this->fetch_all();        
    }

    
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