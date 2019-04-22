<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_ProductCate extends Mdl_Table
{
    protected $_table = 'waimai_product_cate';
    protected $_pk = 'cate_id';
    protected $_cols = 'cate_id,parent_id,shop_id,title,icon,orderby,type,spec,dateline,settime,show_type';
    protected $_pre_cache_key = 'waimai_product-cate-list';
    protected $_orderby = array('parent_id'=>'ASC', 'orderby'=>'ASC');
    public function create($data, $checked=false)
    {
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __TIME;
        if($cate_id = $this->db->insert($this->_table, $data, true)){
            $this->flush();
        }        
        return $cate_id;        
    }
    public function options($shop_id)
    {
        $options = array();
        if($shop_id = (int)$shop_id){
            if($items = $this->items(array('shop_id'=>$shop_id))){
                foreach($items as $k=>$v){
                    $options[$k] = $v['title'];
                }
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
        if($items = $this->items(array('parent_id'=>$cate_id))){
            foreach((array)$items as $k=>$v){
                if(in_array($v['parent_id'], $cate_ids)){
                    $cate_ids[$v['cate_id']] = $v['cate_id'];
                }
            }
        }
        return implode($glue, $cate_ids);
    }
    public function tree($shop_id)
    {
        $tree = array();
        if($items = $this->items(array('shop_id'=>$shop_id,'closed'=>0), null, 1, 500)){
            foreach($items as $k=>$v){
                if(!$v['parent_id']){
                    $v['children'] = $v['childrens'] = $v['child_ids'] = array();
                    $v['child_ids'][$k] = $v['cate_id'];
                    //$v['children'] = $v['childrens'];
                    $tree[$k] = $v;
                }
            }
            foreach($items as $k=>$v){
                if($tree[$v['parent_id']]){
                    $tree[$v['parent_id']]['childrens'][$k] = $v;
                    $tree[$v['parent_id']]['child_ids'][$k] = $v['cate_id'];//2017/03/29yu
                }
            }
        }
        return $tree;
    }

    public function lists($shop_id){
        $cates = array();
        if($shop_id = (int)$shop_id){
            if($items = $this->items(array('shop_id'=>$shop_id), array('parent_id'=>'ASC', 'orderby'=>'ASC'), 1, 500)){
                foreach($items as $k=>$v){
                    if($v['parent_id'] == 0){
                        $cates[$v['cate_id']]['title'] = $v['title'];
                        $cates[$v['cate_id']]['cate_id'] = $v['cate_id'];
                        $cates[$v['cate_id']]['ids'][] = $v['cate_id'];
                        $cates[$v['cate_id']]['hidden'] = $v['hidden'];
                        foreach($items as $k1=>$v1){
                            if($v1['parent_id'] == $v['cate_id']){
                                $cates[$v['cate_id']]['ids'][] = $v1['cate_id'];
                            }
                        }
                    }
                }
            }
        }
        return array_values($cates);
    }    
    
    public function deletes($cate_id)
    {
        if($cate_id = (int)$cate_id){
            if(!$detail = $this->detail($cate_id)){
                return 5;
            }else if($c1 = $this->count(array('parent_id'=>$cate_id))){
                return 2;
            }else if($c2 = K::M('waimai/product')->count(array('cate_id'=>$cate_id,'closed'=>0))){
                return 3;
            }else{
                if($this->delete($cate_id)){
                    return 1;
                }
            }
        }else{
            return 4;
        }
    }
    
    
    /*public function delete($id)
    {
        $filter = array('parent_id' => $id);
        $detail = $this->find($filter);
        if($detail)
        {
            $this->msgbox->add('含子分类, 请先删子分类! ', 211);
            return false;
        }
        parent::delete($id);
        return true;
    }*/
    
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


    public function _format_row($row){
        //新增分类按时间隐藏
        $row['settime'] = $row['settime']?unserialize($row['settime']):array();
        $row['hidden'] = 0;

        /*$stime = strtotime($row['settime']['stime']);
        $ltime = strtotime($row['settime']['ltime']);
        $today_stime = strtotime(date('Y-m-d'));
        $today_ltime  = $today_stime+86399;

        if($stime<$ltime){
            if($stime<__TIME&&$ltime>__TIME){
                $row['hidden'] = 0;
            }else{
                $row['hidden']= 1;
            }
        }
        if($stime>=$ltime){
            $row['settime']['ltime'] = '次日 '.$row['settime']['ltime'];
            $row['settime']['ltime_time'] = $row['settime']['ltime_time'] + 86400;
            if(($stime<__TIME&&$today_ltime>__TIME)||($today_stime<__TIME&&$ltime>__TIME)){
                $row['hidden'] = 0;
            }else{
                $row['hidden']= 1;
            }
        }*/

        switch ($row['show_type']) {   //v3.6分类的显示设置 -1不展示, 0全天展示 1自定义时间
            case '-1':
                $row['hidden'] = 1;
                break;
            case '0':
                $row['hidden'] = 0;
                break;
            case '1':
                if($row['settime']){
                    $stime = strtotime($row['settime']['stime']);
                    $ltime = strtotime($row['settime']['ltime']);
                    $today_stime = strtotime(date('Y-m-d'));
                    $today_ltime  = $today_stime+86399;

                    $row['settime']['stime_time'] = $stime - $today_stime;
                    $row['settime']['ltime_time'] = $ltime - $today_stime;
                    if(stristr($row['settime']['ltime'],'次日')){
                        $ltime = strtotime(str_replace('次日', '', $row['settime']['ltime']));
                        $row['settime']['ltime_time'] = $ltime - $today_stime + 86400;

                        $start_time1 = $today_stime;
                        $end_time1 = $ltime;
                        $start_time2 = $stime;
                        $end_time2 = $today_ltime;
                        if((__TIME >= $start_time1 && __TIME <= $end_time1) || (__TIME >= $start_time2 && __TIME <= $end_time2)){
                            $row['hidden'] = 0;
                        }else{
                            $row['hidden']= 1;
                        }
                    }else{
                        if($stime < __TIME && $ltime > __TIME){
                            $row['hidden'] = 0;
                        }else{
                            $row['hidden']= 1;
                        }
                    }
                }
                break;
            default:
                # code...
                break;
        }

        return $row;
    }
    
}
