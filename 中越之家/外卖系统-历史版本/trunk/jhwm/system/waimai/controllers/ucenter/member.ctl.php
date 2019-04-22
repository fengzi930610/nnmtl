<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/24
 * Time: 9:56
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Ucenter_Member extends Ctl_Ucenter {
    //用户中心
    public function index2()
    {
        $this->check_login();
        $filter = array();
        $filter['uid'] = $this->uid;
        $filter['order_status'] = 8;
        $filter['pay_status'] = 1;
        $filter['comment_status'] = 0;
        if($nums = K::M('order/order')->count($filter)) {
            $this->pagedata['comments'] = $nums;
        }
        $wait_payment_count = $wait_ticket_count = $wait_comment_count = $new_msg_count = $hongbao_count = 0;
        //待付款
        $wait_payment_count = K::M('order/order')->count(array('uid'=>$this->uid, 'from'=>"<>:'maidan'", 'pay_status'=>0, 'order_status'=>0, 'online_pay'=>1, 'closed'=>0));
        //待使用

        //待评价
        $map = array('uid'=>$this->uid, 'comment_status'=>0, 'order_status'=>'8', 'closed'=>0);
        $map['from'] = '<>:weidian';
        $wait_comment_count = K::M('order/order')->count($map);
        $pager = array('wait_payment_count'=>$wait_payment_count, 'wait_ticket_count'=>$wait_ticket_count, 'wait_comment_count'=>$wait_comment_count);
        //未使用红包数
        $pager['hongbao_count'] = K::M('hongbao/hongbao')->count(array('uid'=>$this->uid, 'order_id'=>0,'ltime'=>'>:'.__TIME));
        //新消息数
        $pager['new_msg_count'] = K::M('member/message')->count(array('uid'=>$this->uid, 'is_read'=>0));
        $this->pagedata['pager'] = $pager;

        $this->tmpl = 'ucenter/temp.html';
        //$this->tmpl = 'ucenter/index.html';
    }

    public function index()
    {        
        $this->check_login();        
        $pager['hongbao_count'] = K::M('hongbao/hongbao')->count(array('uid'=>$this->uid, 'order_id'=>0,'ltime'=>'>:'.__TIME));//未使用红包数        
        $pager['new_msg_count'] = K::M('member/message')->count(array('uid'=>$this->uid, 'is_read'=>0));//新消息数
        $pager['coupon_count'] = K::M('waimai/coupon')->count(array('uid'=>$this->uid, 'use_time'=>0, 'order_id'=>0, 'stime'=>'<:'.__TIME, 'ltime'=>'>:'.__TIME));

        $attachCfg = K::M('system/config')->get('attach');
        $site = $this->system->config->get("site");
        $site['attachurl'] = $attachCfg['attachurl'];
        $customModule = array();
        $attachurl = trim($site['siteurl'], '/').'/attachs/';
        $customModule[] = array('title'=>'收货地址', 'link'=>$this->mklink('ucenter/addr/index', null, null, 'waimai'), 'photo'=>$attachurl.'default/icon/ucenter/nav_01@2x.png');
        $customModule[] = array('title'=>'我的收藏', 'link'=>$this->mklink('ucenter/collect/index', null, null, 'waimai'), 'photo'=>$attachurl.'default/icon/ucenter/nav_02@2x.png');
        $customModule[] = array('title'=>'邀请好友', 'link'=>$this->mklink('ucenter/share/index', null, null, 'waimai'), 'photo'=>$attachurl.'default/icon/ucenter/nav_03@2x.png');

        if(defined('HAVE_JIFEN') && HAVE_JIFEN){
            $customModule[] = array('title'=>'积分商城', 'link'=>$this->mklink('index', null, null, 'jifen'), 'photo'=>$attachurl.'default/icon/ucenter/nav_04@2x.png');
        }
        if(defined('HAVE_PAOTUI') && HAVE_PAOTUI){
            $customModule[] = array('title'=>'跑腿', 'link'=>$this->mklink('index', null, null, 'paotui'), 'photo'=>$attachurl.'default/icon/ucenter/nav_05@2x.png');
        }
        if(defined('HAVE_QIANG') && HAVE_QIANG){
            $customModule[] = array('title'=>'抢购', 'link'=>$this->mklink('index', null, null, 'qiang'), 'photo'=>$attachurl.'default/icon/ucenter/nav_06@2x.png');
        }        
        // $customModule[] = array('title'=>'配送会员卡', 'link'=>$this->mklink('ucenter/peicard/mycard', null, null, 'waimai'), 'photo'=>$attachurl.'default/icon/ucenter/nav_07@2x.png');
        $customModule[] = array('title'=>'我的消息', 'link'=>$this->mklink('ucenter/msg/index', null, null, 'waimai'), 'photo'=>$attachurl.'default/icon/ucenter/nav_08@2x.png');
        // $customModule[] = array('title'=>'商家入驻', 'link'=>$this->mklink('signup/shop', null, null, 'waimai'), 'photo'=>$attachurl.'default/icon/ucenter/nav_09@2x.png');
        // $customModule[] = array('title'=>'骑手入驻', 'link'=>$this->mklink('signup/staff', null, null, 'waimai'), 'photo'=>$attachurl.'default/icon/ucenter/nav_10@2x.png');
        $customModule[] = array('title'=>'联系客服', 'link'=>'tel:'.$site['phone'], 'photo'=>$attachurl.'default/icon/ucenter/nav_12@2x.png', 'phone'=>$site['phone']);
        // $customModule[] = array('title'=>'关于我们', 'link'=>$this->mklink('about/about', array(1), null, 'www'), 'photo'=>$attachurl.'default/icon/ucenter/nav_13@2x.png');

        //echo '<pre>';print_r($customModule);die;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['customModule'] = $customModule;

        $this->tmpl = 'ucenter/temp.html';
    }
    
}