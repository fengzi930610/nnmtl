<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/4/2
 * Time: 15:41
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Oauth_Index extends Ctl {

    public function index(){
        $ele_access_token = "";
        $meituan_access_token = "";
        if($access_token = K::M('waimai/accesstoken')->get_access_token($this->shop_id)){
            $ele_access_token = $access_token['access_token'];
            $meituan_access_token = $access_token['meituan_token'];
        }
        $oauth_url_ele = K::M('ele/ele')->get_auth_url();
        $oauth_url_meituan = K::M('meituan/meituan')->get_auth_url($this->waimai_shop);

        $unbind_url_ele = '';
        $unbind_url_meituan = '';
        if($ele_access_token){
            $unbind_url_ele = 'https://melody.shop.ele.me';
        }
        if($meituan_access_token){
            $unbind_url_meituan = K::M('meituan/meituan')->return_unbind_url($meituan_access_token);
        }

        $data = array(
            'ele'=>$ele_access_token,
            'meituan'=>$meituan_access_token,
            'oauth_url_ele'=>$oauth_url_ele,
            'oauth_url_meituan'=>$oauth_url_meituan,
            'unbind_url_ele'=>$unbind_url_ele,
            'unbind_url_meituan'=>$unbind_url_meituan
        );

        $this->pagedata['data'] = $data;
        $this->tmpl = "oauth/index.html";
    }

}