<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: index.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Index extends Ctl
{
    
    public function index()
    {
        $top_menu = $this->admin->tree();
        if(!defined('__DEV_MODEL')){
            unset($top_menu[601]['menu'][7]);
        }
        if(!defined('HAVE_DITUI') || !HAVE_DITUI){
            unset($top_menu[1962]['menu'][2423]);
        }
        if(!defined('HAVE_PAOTUI') || !HAVE_PAOTUI){
           unset($top_menu[1962]['menu'][2502]);
        }
        if(!defined('HAVE_QIANG') || !HAVE_QIANG){
            unset($top_menu[5]['menu'][2687]);
        }
        if(!defined('HAVE_JIFEN') || !HAVE_JIFEN){
             unset($top_menu[5]['menu'][2490]);
        }
        $this->pagedata['top_menu'] = $top_menu;
        $menu_tree = $this->admin->tree();
        if(!$mid = intval($mid)){
            $tree = array_shift($menu_tree);
        }else{
            $tree = $menu_tree[$mid];
        }
        if(!defined('__DEV_MODEL')){
            unset($tree['menu'][7]);
        }

        if(!defined('HAVE_DITUI') || !HAVE_DITUI){
            unset($tree['menu'][2423]);
        }
        if(!defined('HAVE_PAOTUI') || !HAVE_PAOTUI){
            unset($tree['menu'][2502]);
        }
        if(!defined('HAVE_QIANG') || !HAVE_QIANG){
            unset($tree['menu'][2687]);
        }
        if(!defined('HAVE_JIFEN') || !HAVE_JIFEN){
            unset($tree['menu'][2490]);
        }

        $this->pagedata['menu_tree'] = $tree['menu'];

        $this->tmpl = 'admin:page/index.html';
        $this->output();
    }
    /*public function welcome()
    {
        $sysinfo = array(
            'version' => JH_VERSION . ' RELEASE '. JH_RELEASE .' [<a href="http://www.ijh.cc/" class="blue" target="_blank">查看最新版本</a>]',
            'server_domain' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            'server_os' => PHP_OS,
            'web_server' => $_SERVER["SERVER_SOFTWARE"],
            'php_version' => PHP_VERSION,
            'mysql_version' => mysql_get_server_info(),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_execution_time' => ini_get('max_execution_time') . '秒',
            'memory_limit' => ini_get('memory_limit'),
            'safe_mode' => (boolean) ini_get('safe_mode') ?  'YES' : 'NO',
            'zlib' => function_exists('gzclose') ?  'YES' : 'NO',
            'curl' => function_exists("curl_getinfo") ? 'YES' : 'NO',
            'timezone' => function_exists("date_default_timezone_get") ? date_default_timezone_get() : 'NO'
        );
        if(function_exists('gd_info')){
            $gd_info = @gd_info();
            $sysinfo['gd_version'] = $gd_info["GD Version"];
        }else{
            $sysinfo['gd_version'] = '<span class="red">NO</span>';
        }
        $this->pagedata['sysinfo'] = $sysinfo;
        $sdaytime = $this->system->sdaytime;
        $this->tmpl = 'admin:page/welcome.html';
    }*/
    public function login()
    {
		$access = $this->system->config->get('access');
        if($_POST['admin_name']){
            if(!$name = $this->GP('admin_name')){
                $this->msgbox->add('登录名不能为空',401);
            }else if(!$passwd = $this->GP('admin_pwd')){
                $this->msgbox->add('登录密码不能为空',402);
            }else{
                $verifycode_success = true;
                $access = $this->system->config->get('access');
                if($access['verifycode']['admin']){
                    if(!$code = $this->GP('verify_code')){
                         $verifycode_success = false;
                        $this->msgbox->add('验证码不能为空',403);
                    }else if(!K::M('magic/verify')->check($code)){
                        $verifycode_success = false;
                        $this->msgbox->add('验证码不正确',403);
                    }
                    if(!$verify_code = $this->GP('verify_code')){
                        $verifycode_success = false;
                        $this->msgbox->add('验证码不正确', 212);
                    }else if(!K::M('magic/verify')->check($verify_code)){
                        $verifycode_success = false;
                        $this->msgbox->add('验证码不正确', 212);
                    }
                }
                if($verifycode_success){
                    if($this->system->admin->login($name,$passwd)){
                        header("Location:?index.html");
                        exit();
                    }
                }
            }
            $this->msgbox->show("?index-login.html");
        }else{
            $this->pagedata['admin'] = $access['verifycode']['admin'];
            $this->tmpl = 'admin:page/login.html';
            $this->output();
        }
    }
    public function loginout()
    {
        $this->admin->loginout();
        $this->msgbox->add("帐户已经安全退出!!");
    }
    public function modifypasswd()
    {
        if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 211);
            }else if(empty($data['oldpasswd'])){
                $this->msgbox->add('旧密码不能为空', 212);
            }else if($data['newpasswd'] != $data['confirmpasswd']){
                $this->msgbox->add('两次密码不相同', 213);
            }else if($this->admin->modifypasswd($data['oldpasswd'], $data['newpasswd'])){
                $this->msgbox->add('修改密码成功,需重新登录');
            }
        }else{
            $this->tmpl = 'admin:admin/admin/modifypasswd.html';
        }
    }
    public function verify()
    {
        K::M('magic/verify')->output();
    }
    public function page($page)
    {
        $uri = $this->request['uri'];
        if(preg_match('/page-(\w+).html/i', $uri, $match)){
            $page = $match[1];
            $this->tmpl = "admin:page/{$page}.html";
        }
    }
    public function top()
    {
        $this->pagedata['top_menu'] = $this->admin->tree();
        $this->tmpl = 'admin:context/top.html'; 
    }
    public function ijh()
    {
        $cfg = $this->system->config->get('site_config');
        header("Content-type: image/png");
        echo base64_decode(K::M('secure/crypt')->hexstr($cfg['hash']));
        exit();
    }
    public function context($mid=null)
    {
        $menu_tree = $this->admin->tree();
        if(!$mid = intval($mid)){
            $tree = array_shift($menu_tree);
        }else{
            $tree = $menu_tree[$mid];
        }
        if(!defined('__DEV_MODEL')){
            unset($tree['menu'][7]);
        }

        if(!defined('HAVE_DITUI') || !HAVE_DITUI){
            unset($tree['menu'][2423]);
        }
        if(!defined('HAVE_QIANG') || !HAVE_QIANG){
            unset($tree['menu'][2687]);
        }
        if(!defined('HAVE_JIFEN') || !HAVE_JIFEN){
            unset($tree['menu'][2490]);
        }

        $this->pagedata['menu_tree'] = $tree['menu'];
        $this->tmpl = 'admin:context/menu.html';
    }

    public function welcome()
    {
        $day = date('Ymd');
        $yday = date('Ymd',strtotime('-1 day'));
        $str_yday = strtotime($yday);
        $yyday = date('Ymd',strtotime('-2 day'));
        //今天开始时间
        $today_time = strtotime(date('Y-m-d')).'~'.( strtotime(date('Y-m-d'))+86399);
        //昨天时间
        $yestaday_time = strtotime(date('Ymd',strtotime('-1 day'))).'~'.(strtotime(date('Y-m-d'))-1);
        //前天时间
        $before_day = strtotime(date('Ymd',strtotime('-2 day'))).'~'.(strtotime(date('Ymd',(strtotime('-1 day'))))-1);
        //今天统计数据
        $t_data = K::M('site/tongji')->sum_by_filter(array('dateline'=>$today_time));
        //昨日统计数据
        $y_data =K::M('site/tongji')->sum_by_filter(array('dateline'=>$yestaday_time));
       //前日同居数据
        $l_data  = K::M('site/tongji')->sum_by_filter(array('dateline'=>$before_day));
        //营业额
        $amount_t  = $t_data['amount'];
        $amount_y  = $y_data['amount'];
        $amount_l  = $l_data['amount'];
        $amount_bl =  K::M('helper/format')->get_bl($amount_y,$amount_l);
        //订单量
        $order_t  = $t_data['orders'];
        $order_y  = $y_data['orders'];
        $order_l  = $l_data['orders'];
        $order_bl =  K::M('helper/format')->get_bl($order_y,$order_l);
        //配送费
        $pei_t = $t_data['pei_amount'];
        $pei_y = $y_data['pei_amount'];
        $pei_l = $l_data['pei_amount'];
        $pei_bl = K::M('helper/format')->get_bl($pei_y,$pei_l);
        //客单价
        $avg_t = $amount_t>0?sprintf('%.2d',($amount_t/$order_t)):0;
        $avg_y = $amount_y>0?sprintf('%.2d',($amount_y/$order_y)):0;
        $avg_l = $amount_l>0?sprintf('%.2d',($amount_l/$order_l)):0;
        $avg_bl = K::M('helper/format')->get_bl($avg_y,$avg_l);
        //盈利
        $yinli_t = $t_data['yinli'];
        $yinli_y = $y_data['yinli'];
        $yinli_l = $l_data['yinli'];
        $yinli_bl = K::M('helper/format')->get_bl($yinli_y,$yinli_l);
        //新客
        $member_t = K::M('member/member')->count(array('dateline'=>$today_time));
        $member_y = K::M('member/member')->count(array('dateline'=>$yestaday_time));
        $member_l = K::M('member/member')->count(array('dateline'=>$before_day));
        $member_bl  = K::M('helper/format')->get_bl($member_y,$member_t);
        $data = array();
        $data['t'] = array(
            'amount'=>$amount_t,
            'orders'=>$order_t,
            'pei'=>$pei_t,
            'avg'=>$avg_t,
            'yinli'=>$yinli_t,
            'member'=>$member_t,
        );
        $data['y'] = array(
            'amount'=>$amount_y,
            'orders'=>$order_y,
            'pei'=>$pei_y,
            'avg'=>$avg_y,
            'yinli'=>$yinli_y,
            'member'=>$member_y,
        );
        $data['l'] = array(
            'amount'=>$amount_l,
            'orders'=>$order_l,
            'pei'=>$pei_l,
            'avg'=>$avg_l,
            'yinli'=>$yinli_l,
            'member'=>$member_l,
        );
        $data['bl'] = array(
            'amount'=>$amount_bl,
            'orders'=>$order_bl,
            'pei'=>$pei_bl,
            'avg'=>$avg_bl,
            'yinli'=>$yinli_bl,
            'member'=>$member_bl,
        );
        $this->pagedata['data'] = $data;

        //可提现余额
        $ditui_momey = K::M('ditui/ditui')->sum(array('closed'=>0),'money');
        $member_money = K::M('member/member')->sum(array('closed'=>0),'money');
        $staff_money = K::M('staff/staff')->sum(array('closed'=>0),'money');
        $shop_money = K::M('shop/shop')->sum(array('closed'=>0),'money');

        //已经体现
        //商户已提现
        $shop_tixian = K::M('shop/tixian')->sum(array('status'=>4),'money');
        $staff_tixian = K::M('staff/tixian')->sum(array('status'=>4),'money');
        $ditui_tixian = K::M('ditui/tixian')->sum(array('status'=>3),'money');

        //未入账
        $bill_shop = K::M('waimai/bills')->sum(array('status'=>0),'amount');
        $bill_staff = K::M('staff/bills')->sum(array('status'=>0),'amount');

        $this->pagedata['money'] = array(
            'ditui_money'=>$ditui_momey,
            'member_money'=>$member_money,
            'staff_money'=>$staff_money,
            'shop_money'=>$shop_money,
            'shop_tixian'=>$shop_tixian,
            'staff_tixian'=>$staff_tixian,
            'ditui_tixian'=>$ditui_tixian,
            'bills_shop'=>$bill_shop,
            'bills_staff'=>$bill_staff,
            'sum_money'=>$ditui_momey+$staff_money+$shop_money,
            'sum_bills'=>$bill_shop+$bill_staff,
            'sum_tixian'=>$shop_tixian+$staff_tixian+$ditui_tixian,
            'all_money'=>$ditui_momey+$member_money+$staff_money+$shop_money+$bill_shop+$bill_staff,
            'can_tixian'=>$ditui_momey+$staff_money+$shop_money+$member_money,
        );

        $time = strtotime(date('Y-m-d'))+86399;
        $pei_filter = array(
            'from'=>'waimai',
            'order_status'=>2,
            'pei_type'=>array(0,1),
            ':OR'=>array('pay_status'=>1, 'online_pay'=>0),// 已付款 || 货到付款
            ':SQL'=>"refund_status != '1' AND (pei_time = '0' OR (pei_time < ".$time." AND pei_time>".strtotime(date('Y-m-d'))."))",
            );
        $pei_orders = K::M('order/order')->count($pei_filter);
        $rights_filter = array('from'=>'waimai', 'refund_status'=>'<>:0');
        $rights_orders = K::M('order/order')->count($rights_filter);  //维权订单数
        $w_verify_filter = array('closed'=>0,'verify_name'=>array(0,2),'last_time'=>">:0");
        $shop_verifys = K::M('waimai/waimai')->count($w_verify_filter);  //商家入驻待审核
        $shop_tixians = K::M('shop/tixian')->count(array('status'=>0));   //商家提现待审
        $shop_transfers = K::M('shop/tixian')->count(array('status'=>1));  //商家提现待转账
        $hd_verify_filter = array('closed'=>0,'audit'=>0,'ltime'=>'>=:'.__TIME);
        $first_verifys = K::M('waimai/huodongfirst')->count($hd_verify_filter);
        $mj_verifys = K::M('waimai/huodongmj')->count($hd_verify_filter);
        $mf_verifys = K::M('waimai/huodongmf')->count($hd_verify_filter);
        $coupon_verifys = K::M('waimai/huodongcoupon')->count($hd_verify_filter);
        $huodong_verifys = $first_verifys+$mj_verifys+$mf_verifys+$coupon_verifys;    //待审活动数
        $s_verify_filter = array('closed'=>0,'audit'=>array(0,2),'from'=>'paotui');
        $staff_verifys = K::M('staff/staff')->count($s_verify_filter);      //待审骑手数
        $staff_tixians = K::M('staff/tixian')->count(array('status'=>0));   //骑手提现待审
        $staff_transfers = K::M('staff/tixian')->count(array('status'=>1)); //骑手提现待转
        $d_verify_filter = array('closed'=>0, 'audit'=>0);
        $ditui_verifys = K::M('ditui/ditui')->count($d_verify_filter);      //地推待审
        $ditui_tixians = K::M('ditui/tixian')->count(array('status'=>0));   //地推提现待审
        $ditui_transfers = K::M('ditui/tixian')->count(array('status'=>1)); //地推提现待转
        $this->pagedata['mouth'] = date('Y-m');
        $waitdo = array(
            array('label'=>'待配送订单（外卖）','count'=>$pei_orders,'link'=>$this->mklink('waimai/order-index',array(1,2))),
            array('label'=>'维权订单','count'=>$rights_orders,'link'=>$this->mklink('waimai/order-rights',null)),
            array('label'=>'商家入驻待审','count'=>$shop_verifys,'link'=>$this->mklink('waimai/apply-index',null)),
            array('label'=>'活动待审','count'=>$huodong_verifys,'link'=>$this->mklink('waimai/huodong-index',null)),
            array('label'=>'商家提现待审','count'=>$shop_tixians,'link'=>$this->mklink('finance/account-index',array(1,2))),
            array('label'=>'商家提现待转','count'=>$shop_transfers,'link'=>$this->mklink('finance/account-wait',null)),
            array('label'=>'骑手待审','count'=>$staff_verifys,'link'=>$this->mklink('group/staff-weiaudit',null)),
            array('label'=>'骑手提现待审','count'=>$staff_tixians,'link'=>$this->mklink('group/tixian-index',array(1,0))),
            array('label'=>'骑手提现待转','count'=>$staff_transfers,'link'=>$this->mklink('group/tixian-index',array(1,1))),
            //array('label'=>'地推待审','count'=>$ditui_verifys,'link'=>$this->mklink('ditui/ditui-audit',null)),
            //array('label'=>'地推提现待审','count'=>$ditui_tixians,'link'=>$this->mklink('ditui/tixian-index',array(1,2))),
            //array('label'=>'地推提现待转','count'=>$ditui_transfers,'link'=>$this->mklink('ditui/tixian-waititems',null)),
            );
        if(defined('HAVE_DITUI') && HAVE_DITUI){
            $waitdo[] = array('label'=>'地推待审','count'=>$ditui_verifys,'link'=>$this->mklink('ditui/ditui-audit',null));
            $waitdo[] = array('label'=>'地推提现待审','count'=>$ditui_tixians,'link'=>$this->mklink('ditui/tixian-index',array(1,2)));
            $waitdo[] = array('label'=>'地推提现待转','count'=>$ditui_transfers,'link'=>$this->mklink('ditui/tixian-waititems',null));
        }

        $this->pagedata['waitdo'] = $waitdo;
        $this->tmpl = 'admin:page/welcome.html';
    }
}