<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2015-07-22 01:25:14Z wanglei $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Cates extends Ctl
{
    
    public function index($cate_id)
    {   
        $cfg = $this->system->config->get('hotwaimai');
        $cfg = str_replace('，', ',', $cfg['hotwaimai']);
        $this->pagedata['hotwaimai'] = explode(',', $cfg);
        $pcates = K::M('waimai/cate')->items(array('parent_id'=>0),array('cate_id'=>'asc'));
        $pcates = array_values($pcates);
        if($from = (int)$this->GP('from')){
            $this->pagedata['from'] = $from;
        }
        $filter = array();
        if($cate_id = (int)$cate_id){
            $filter['parent_id'] = $cate_id;
        }else{
            $filter['parent_id'] = $pcates[0]['cate_id'];
        }
        $scates =  K::M('waimai/cate')->items($filter,array('cate_id'=>'asc'));
        $cates = K::M('waimai/cate')->fetch_all();
        foreach($scates as $k=>$v){
            foreach($cates as $k1=>$v1){
                if($v['cate_id'] == $v1['parent_id']){
                    $scates[$k]['children'][] = $v1;
                }
            }
        }
        //print_r($scates);die;
        $this->pagedata['pcates'] = $pcates;
        $this->pagedata['scates'] = $scates;
        $this->pagedata['cate_id'] = $cate_id;
        $this->tmpl = 'cates/index.html';
    }
    
  
}