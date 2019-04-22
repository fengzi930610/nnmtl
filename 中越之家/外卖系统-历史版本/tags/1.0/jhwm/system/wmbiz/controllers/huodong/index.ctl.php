<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Huodong_Index extends Ctl
{

    public function index()
    {
        $manjian = K::M('waimai/huodongmj')->find(array('shop_id'=>$this->shop_id, 'audit'=>1, 'closed'=>0));
        if($manjian){
            $manjian['coupons'] = $manjian['config']; 
        }
        $this->pagedata['manjian'] = $manjian;
        $first = K::M('waimai/huodongfirst')->find(array('shop_id'=>$this->shop_id, 'audit'=>1, 'closed'=>0));
        $this->pagedata['first'] = $first;
        $this->tmpl = 'huodong/index.html';
    }

}
