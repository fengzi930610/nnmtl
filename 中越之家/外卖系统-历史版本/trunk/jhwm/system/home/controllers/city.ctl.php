<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2015-07-22 01:25:14Z wanglei $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_City extends Ctl
{
    
    public function index()
    {   
        $city = K::M('data/city')->items();
        $city_list = array();
        foreach($city as $k => $v){
            $city_list[$v['py']][] = $v;
        }
        ksort($city_list);
        $this->pagedata['city_list']= $city_list;
        $this->tmpl = 'city.html';
    }
    
  
}