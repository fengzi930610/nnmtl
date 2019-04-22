<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/21
 * Time: 9:23
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Index extends Ctl
{

    protected function juli_order($a, $b)
    {
        if ($a['juli'] == $b['juli']) {
            return 0;
        }
        return ($a['juli'] < $b['juli']) ? -1 : 1;
    }

    //外卖首页
    public function index2()
    {
        //K::M('order/order')->set_order_day_num(6365,177);

        /*if(!defined('IS_MOBILE') && !$this->cookie->get('is_wm_web')){
            $this->cookie->set('is_wm_web', 1);
            header("Location:".$this->mklink('web/index', null, null, 'waimai'));
            exit;
        }*/
        // //外卖首页轮播图
        // $banner_adv = array();
        // if($dv =K::M('adv/adv')->adv_by_name('V3外卖首页轮播')){
        //     $banner_adv = K::M('adv/item')->items_by_adv($adv['adv_id']);
        // }
        //外卖分类广告       
        $cate_adv = array();
        if($adv = K::M('adv/adv')->adv_by_name('v3外卖首页分类')){
            $cate_adv = K::M('adv/item')->items_by_adv($adv['adv_id']);
        }
        $this->pagedata['cate_adv'] = $cate_adv;
        //推荐广告位
        $tuijian_adv = array();
        if($adv = K::M('adv/adv')->adv_by_name('V3外卖首页推荐')){
            $tuijian_adv = K::M('adv/item')->items_by_adv($adv['adv_id']);
        }        
        $this->pagedata['tuijian_adv'] = array_values($tuijian_adv);
        // $cfg = $this->system->config->get('hotwaimai');
        // $cfg = str_replace('，', ',', $cfg['hotwaimai']);
        // $cfg =explode(',', $cfg);
        // $this->pagedata['hotwaimai'] = $cfg[0];
        $this->tmpl='index.html';
    }


    public function test(){

        error_reporting(E_ALL);
        ini_set("display_errors","On");

        //测试部分退款
        if(false)
        {
            $id = 6;
            var_dump(K::M('order/order')->refund_ex($id,1.12,"测试模型接口"));
            var_dump(K::M('order/order')->get_real_refund_amount($id));
        }
        
        //更新没有unionid的用户的unionid(只有关注的用户才可更新成功)
        if(false)
        {
            echo "update user unionid...<br>";
            $users = K::M('member/member')->items([],NULL,1,99999999);
            if($users)
            {
                foreach($users as &$user)
                {
                    $openid = trim($user['wx_openid']);
                    if(trim($user['wx_unionid']) === "" && $openid !== "")
                    {
                        $wxclient = K::M("weixin/wechat")->admin_wechat_client();
                        if($wxclient)
                        {
                            echo "<br>---update {$openid} ... ";
                            $userInfo = $wxclient->getUserInfoById($openid);
                            if($userInfo && isset($userInfo['unionid']) && trim($userInfo['unionid']) !== "")
                            {
                                K::M('member/member')->update($user['uid'],['wx_unionid'=>trim($userInfo['unionid'])],true);
                                echo " >>> {$userInfo['unionid']} ... done";
                            }
                            else
                            {
                                echo "response error or no unionid data!";
                                echo "<pre>";
                                var_dump($userInfo);
                                echo "</pre>";
                                echo "<br>-----------------</br>";
                            }
                            echo "<br>";
                        }
                        else
                            echo "Error: init wxsdk failed<br>";
                        
                        unset($wxclient);
                    }
                    unset($user);
                }
            }
            unset($users,$user);
        }
        
        //批量更新虚拟商家的配送费计算模式为-1（即“外卖（系统配置）”）
        if(false)
        {
            echo "update shop freight calculate type...<br>";
            $shops = K::M('waimai/waimai')->items([],NULL,1,99999999);
            if($shops)
            {
                foreach($shops as &$shop)
                {
                    if(K::M('waimai/waimai')->is_custom_mgr_shop($shop['shop_id']) && (int)$shop['freight_calc_type'] !== -1)
                    {
                        echo "<br>---update {$shop['shop_id']} {$shop['title']}<br>";
                        K::M('waimai/waimai')->update($shop['shop_id'],['freight_calc_type'=>'-1'],true);
                    }
                    unset($shop);
                }
            }
            unset($shops,$shop);
        }
        
        exit;
        // $this->tmpl = 'test.html';
    }

    public function index()
    {
        $theme = K::M('adv/themes')->getTheme();        
        $this->pagedata['config'] = $theme['config'] ? $theme['config'] : array();
        //$this->pagedata['cates'] = K::M('waimai/cate')->tree();//隐藏附近商家，故不再读取分类
        $this->pagedata['show_huodong'] = K::M('adv/themes')->show_huodong();
        //$this->pagedata['app_download'] = $this->system->config->get('app_download');

        //2019-02-25 添加 输出所有城市数据
        $citys = K::M('data/city')->items([],null,1,99999999);
        $this->pagedata['citys'] = $citys;

        $this->tmpl='index.html';
    }

    public function appdown()
    {
        //$this->pagedata['app_download'] = $this->system->config->get('app_download');
        $this->tmpl = "appdown.html";
    }

    public function getOrders($params)
    {
        $filter = array('order_status'=>'>=:0', 'from'=>'waimai', 'dateline'=>(__TIME-3600).'~'.__TIME);
        $filter[':SQL'] = " ((`online_pay`=0 AND `pay_status`=0) OR (`online_pay`=1 AND `pay_status`=1))";
        $orderby = array('dateline'=>'desc');
        $page = 1;
        $limit = 10;
        if($items = K::M('order/order')->items($filter, $orderby, $page, $limit, $count)){
            $shop_ids = $uids = array();
            foreach ($items as $k => $v) {
                if($v['shop_id']){
                    $shop_ids[$v['shop_id']] = $v['shop_id'];
                }
                if($v['uid']){
                    $uids[$v['uid']] = $v['uid'];
                }
                $v['shop'] = array();
                $v['member'] = array();
                $items[$k] = $v;
            }
            $members = K::M('member/member')->items_by_ids($uids);
            $shops = K::M('waimai/waimai')->items_by_ids($shop_ids);
            
            foreach ($items as $k => $v) {
                if($shop = $shops[$v['shop_id']]){
                    $shop['logo'] = K::M('magic/upload')->geturl($shop['logo']);
                    $v['shop'] = $this->filter_fields('shop_id,title,logo', $shop);
                }
                if($member = $members[$v['uid']]){
                    $v['member'] = $this->filter_fields('uid,nickname', $member);
                }
                $v['timer'] = K::M('helper/format')->format_Time((__TIME-$v['dateline']+6));
                $items[$k] = $this->filter_fields('order_id,uid,shop_id,shop,member,dateline,timer', $v);
            }            
        }else{
            $items = array();
        }
        $this->msgbox->set_data('data', array('items'=>array_values($items)));
    }
}