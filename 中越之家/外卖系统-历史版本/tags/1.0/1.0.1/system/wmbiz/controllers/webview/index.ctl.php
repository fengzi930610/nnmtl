<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/4
 * Time: 9:41
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Webview_Index extends Ctl
{
    
    public function index(){
       /* if(!$waimai = K::M('waimai/waimai')->detail($this->shop_id)){
            $waimai = array();
        };
        $this->pagedata['waimai'] = $waimai;
        if(!$verify = K::M('waimai/verify')->detail($this->shop_id)){
            $verify = array();
        }
        $this->pagedata['verify'] = $verify;
        if(!$account = K::M('shop/account')->detail($this->shop_id)){
            $account = array();
        }

        $this->pagedata['account'] = $account;*/
        $waimai = K::M('waimai/waimai')->detail($this->shop_id);
        $this->pagedata['waimai_detail'] = $waimai;
        $this->tmpl = 'webview/index/index.html';
    }
    
    
}