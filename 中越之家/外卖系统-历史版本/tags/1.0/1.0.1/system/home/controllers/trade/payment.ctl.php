<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
class Ctl_Trade_Payment extends Ctl
{

    public function __construct(&$system)
    {
        parent::__construct($system);
        $uri = $this->request['uri'];
        if(preg_match('/(return|notify)-(\w+)(-(wxapp|app|pwxapp))?/i', $uri, $match)){
            $system->request['act'] = $match[1] . '_verify';
            $system->request['args'] = array($match[2], $match[4]);
        }
    }

    public function return_verify($code, $from = null)
    {
        if($from && strtolower($from) == 'app' && !defined('IN_APP')){
            define('IN_APP', 'api');
        }
        if($from && strtolower($from) == 'wxapp'){
            if(!defined('IN_WXAPP')){
                define('IN_WXAPP', 'WXAPP');
            }
        }
        if($from && strtolower($from) == 'pwxapp'){
            if(!defined('IN_WXAPP')){
                define('IN_WXAPP', 'WXAPP');
            }
            if(!defined('CLIENT_OS')){
                define('CLIENT_OS', 'PWXAPP');
            }
        }
        if($obj = K::M('trade/payment')->loadPayment($code)){
            if($trade = $obj->return_verify()){
                if(!$log = K::M('payment/log')->log_by_no($trade['trade_no'])){
                    $this->msgbox->add('支付的订单不存在', 211);
                }elseif($log['payed'] && $log['payment'] != $trade['code']){
                    //流水号已经支付并且与改通知不符的交易刚退回支付金额
                    $trade['refund_reason'] = sprintf('流水(%s)已由(%s)支付', $trade['trade_no'], $log['payment']);
                    if(!$refund_trade = K::M('trade/payment')->refund_by_trade($trade, $msg)){
                        K::M('system/logs')->log('payment.refund.error.'.date('Ymd'), array($trade, $msg));
                    }
                    $this->msgbox->add('该交易已经支付过了', 213);
                }elseif(K::M('payment/log')->set_payed($trade['trade_no'], $trade['pay_trade_no'])){
                    if($log['from'] == 'money'){
                        K::M('trade/payment')->payed_money($log, $trade);
                    }else if($log['from']=='deliver'){
                        K::M('trade/payment')->payed_deliver($log, $trade);
                    }else if($log['from']=='peicard'){
                        K::M('trade/payment')->payed_peicard($log, $trade);
                    }else{
                        K::M('trade/payment')->payed_order($log, $trade);
                    }
                }else{
                    $this->msgbox->add('该订单已经支付过了', 213);
                }
            }else{
                $this->msgbox->add('支付验证签名失败', 215);
            }
            if(!$rebackurl = $this->GP('rebackurl')){
                $rebackurl = $this->cookie->get('pay_rebackurl');
            }
            if(empty($rebackurl)){
                $rebackurl = K::M('helper/link')->mklink('trade/payment:success', array($order_id), array(), 'www');
            }
            if(defined('IN_WEIXIN') && $code == 'alipay'){
                $this->msgbox->set_js('window.top.location.href="' . $rebackurl . '";');
            }else{
                header("Location:{$rebackurl}");
                exit;
            }
        }
    }

    public function notify_verify($code, $from = null)
    {
        if($from && strtolower($from) == 'app' && !defined('IN_APP')){
            define('IN_APP', 'api');
        }
        if($from && strtolower($from) == 'wxapp'){
            if(!defined('IN_WXAPP')){
                define('IN_WXAPP', 'WXAPP');
            }
        }
        if($from && strtolower($from) == 'pwxapp'){
            if(!defined('IN_WXAPP')){
                define('IN_WXAPP', 'WXAPP');
            }
            if(!defined('CLIENT_OS')){
                define('CLIENT_OS', 'PWXAPP');
            }
        }
        $success = false;
        if($obj = K::M('trade/payment')->loadPayment($code)){
            if($trade = $obj->notify_verify()){
                if(!$log = K::M('payment/log')->log_by_no($trade['trade_no'])){
                    //未查寻到本地交易号，发起退款
                    $trade['refund_amount'] = $trade['amount'];
                    $trade['refund_reason'] = sprintf('(%s)交易流水(%s)不存在', $log['payment'], $trade['trade_no']);
                    if(!$refund_trade = K::M('trade/payment')->refund_by_trade($trade, $msg)){
                        K::M('system/logs')->log('payment.refund.error.'.date('Ymd'), array($trade, $msg));
                        $success = false;
                    }else{
                        $success = true;
                    }
                }elseif($log['payed'] && ($log['payment'] != $trade['code'])){
                    //流水号已经支付并且与改通知不符的交易刚退回支付金额
                    $trade['refund_amount'] = $trade['amount'];
                    $trade['refund_reason'] = sprintf('流水(%s)已由(%s)支付', $trade['trade_no'], $log['payment']);
                    if(!$refund_trade = K::M('trade/payment')->refund_by_trade($trade, $msg)){
                        K::M('system/logs')->log('payment.refund.error.'.date('Ymd'), array($trade, $msg));
                        $success = false;
                    }else{
                        $success = true;
                    }
                }elseif(K::M('payment/log')->set_payed($trade['trade_no'], $trade['pay_trade_no'])){
                    if($log['from'] == 'money'){
                        K::M('trade/payment')->payed_money($log, $trade);
                    }else if($log['from']=='deliver'){
                        K::M('trade/payment')->payed_deliver($log, $trade);
                    }else if($log['from']=='peicard'){
                        K::M('trade/payment')->payed_peicard($log, $trade);
                    }else{
                        K::M('trade/payment')->payed_order($log, $trade);
                    }
                    $success = true;
                }
            }
            $obj->notify_success($success);
        }
    }

    public function orders($order_ids){
        $orders_str = $order_ids;
        $order_ids = explode("_", $order_ids);
        $amount = 0;
        foreach($order_ids as $order_id){
            if(!$order_id = (int)$order_id){
                $this->error(404);
            }else if(!$order = K::M('order/order')->detail($order_id)){
                $this->msgbox->add('提交订单有不存在或已经删除', 211)->response();
            }else if($order['order_status'] < 0){
                $this->msgbox->add('提交订单有已取消,不可支付', 214)->response();
            }else if($order['pay_status'] == 1){
                $this->msgbox->add('提交该订单有已经支付', 216)->response();
            }else if($order['amount'] <= 0){
                $this->msgbox->add('提交订单有不需要支付', 217)->response();
            }else if($order['order_status'] == 8){
                $this->msgbox->add('提交订单有已经完成,无需支付', 218)->response();
            }else if(empty($order['online_pay'])){
                $this->msgbox->add('提交订单有无需在线支付', 219)->response();
            }else{
                $amount += ($order['amount']+$order['change_price']);
            }
        }
        if(!$rebackurl = $this->GP('rebackurl')){
            $rebackurl = K::M('helper/link')->mklink('trade/payment:success', array($order_id), array(), 'www');
        }
        $this->pagedata['orders_str'] = $orders_str;
        $this->pagedata['rebackurl'] = $rebackurl;
        $this->pagedata['order_ids'] = $order_ids;
        $this->pagedata['amount'] = $amount;
        $this->tmpl = 'trade/payment/pay2.html';
    }

    
    public function pays($code, $orders_str)
    {
        $this->cookie->delete('pay_rebackurl');
        if(!$rebackurl = $this->GP('rebackurl')){
            $rebackurl = K::M('helper/link')->mklink('trade/payment:success', array(), array(), 'www');
        }
        $this->cookie->set('pay_rebackurl', $rebackurl);
        $orders_str2 = str_replace("_",",",$orders_str);
        $order_ids = explode("_", $orders_str);
        $order = K::M('order/order')->detail($order_ids[0]);
        foreach($order_ids as $order_id){
            
            if(!$order_id = (int)$order_id){
                $this->msgbox->add('订单不存在', 210)->response();
            }else if(!$order = K::M('order/order')->detail($order_id)){
                $this->msgbox->add('订单不存在或已经删除', 211)->response();
            }else if($order['order_status'] < 0){
                $this->msgbox->add('订单已取消不可支付', 214)->response();
            }else if($order['pay_status'] == 1){
                $this->msgbox->add('该订单已经支付', 216)->response();
            }else if($order['amount'] <= 0){
                $this->msgbox->add('该订单不需要支付', 217)->response();
            }else if($order['order_status'] == 8){
                $this->msgbox->add('订单已经完成无需支付', 218)->response();
            }else if(empty($order['online_pay'])){
                $this->msgbox->add('订单无需在线支付', 219)->response();
            }
        }
        if(defined('IN_WEIXIN') && in_array($code, array('wxpay', 'alipay'))){
            if($code == 'wxpay'){
                $order['wx_openid'] = $this->get_wx_openid();
                if(!$trade = K::M('trade/payment')->orders('wxpay', $orders_str2, 'JSAPI')){
                    $this->msgbox->add('创建支付请求失败', 223);
                }else{
                    $this->pagedata['rebackurl'] = $rebackurl;
                    $this->pagedata['jsApiParameters'] = $trade['jsApiParameters'];
                    $trade['order_ids'] = $order_ids;
                    $this->pagedata['trade'] = $trade;
                    $this->tmpl = 'trade/payment/wxjspay.html';
                }
            }elseif($code == 'alipay'){
                if(!$trade = K::M('trade/payment')->qrcodepays('alipay', $orders_str2)){
                    $this->msgbox->add('创建支付请求失败', 223);
                }else{
                    $this->pagedata['rebackurl'] = $rebackurl;
                    $trade['order_ids'] = $order_ids;
                    $this->pagedata['trade'] = $trade;
                    $this->tmpl = 'trade/payment/aliqrcode.html';
                }
            }
        }else if(!defined('IN_WEIXIN') && ($code == 'wxpay')){
            if(!$trade = K::M('trade/payment')->qrcodepays('wxpay', $orders_str2)){
                $this->msgbox->add('创建支付请求失败', 223);
            }else{
                $this->pagedata['rebackurl'] = $rebackurl;
                $trade['order_ids'] = $order_ids;
                $this->pagedata['trade'] = $trade;
                $this->tmpl = 'trade/payment/wxqrcode.html';
            }
        }else if($trade = K::M('trade/payment')->orders($code, $orders_str2)){
            if($code == 'money'){
                if(!$log = K::M('payment/log')->log_by_no($trade['trade_no'])){
                    $this->msgbox->add('获取支付流水失败', 223);
                }else if($log['payed']){
                    $this->msgbox->add('单号已经支付过了', 224);
                }else if(!$member = K::M('member/member')->detail($log['uid'])){
                    $this->msgbox->add('用户不存在', 221);
                }else if(($amount = (float)$trade['amount']) <=0 ){
                    $this->msgbox->add('支付金额不合法', 222);
                }else if($amount != $log['amount']){
                    $this->msgbox->add('支付金额不合法', 222);
                }else if($member['money'] < $trade['amount']){
                    $this->msgbox->add('账户余额不足！', 223)->response();
                }else if(K::M('member/member')->update_money($this->uid, -$amount, '支付订单(ID:' . $orders_str2 . ')')){
                    if(K::M('payment/log')->set_payed($trade['trade_no'])){
                        if($res = K::M('trade/payment')->payed_order($log, $trade)){
                            if($order['wx_openid']){
                                $wx_config = $this->system->config->get('wx_config');
                                $config = $this->system->config->get('site');
                                $a = array('title' => '恭喜您！订单支付成功！！', 'items' => array('OrderSn' => $order_ids, 'OrderStatus' => '订单支付成功'), 'remark' => '您的订单于 ' . date('Y-m-d H:i:s', __TIME) . ' 支付成功');
                                K::M('weixin/wechat')->admin_wechat_client()->sendTempMsg($order['wx_openid'], $wx_config['order_id'], $rebackurl, $a);
                            }
                        }
                    }
                }
                if($rebackurl = $this->GP('rebackurl')){
                    header("Location:{$rebackurl}");
                }else{
                    $this->redirect($trade['trade_no']);
                }
            }else{
                $url = is_array($trade) ? $trade['payurl'] : $trade;
                if(!defined('IN_WEIXIN') && ($code == 'wxpay')){
                    $qrurl = $this->mklink('trade/payment:wxqrcode', array(), array('codeurl' => $url, 'amount' => $amount, 'order_id' => $orders_str));
                    header("Location:{$qrurl}");
                }else{
                    header("Location:{$url}");
                }
                exit;
            }
        }else{
            $this->msgbox->add('请求支付失败', 231);
        }
        $this->msgbox->set_data('forward', $rebackurl);
    }


    public function order($order_id)
    {
        if(!$order_id = (int)$order_id){
            $this->error(404);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已经删除', 211);
        }else if($order['order_status'] < 0){
            $this->msgbox->add('订单已取消不可支付', 214);
        }else if($order['pay_status'] == 1){
            $this->msgbox->add('该订单已经支付', 216);
        }else if($order['amount'] <= 0){
            $this->msgbox->add('该订单不需要支付', 217);
        }else if($order['order_status'] == 8){
            $this->msgbox->add('订单已经完成无需支付', 218);
        }else if(empty($order['online_pay'])){
            $this->msgbox->add('订单无需在线支付', 219);
        }else{
            if(!$rebackurl = $this->GP('rebackurl')){
                $rebackurl = K::M('helper/link')->mklink('trade/payment:success', array($order_id), array(), 'www');
            }

            if($order['card_amount']){
                $order['amount'] = $order['amount'] + $order['card_amount'];//购买配送会员卡的金额
            }

            $this->pagedata['rebackurl'] = $rebackurl;
            $this->pagedata['order'] = $order;
           
            if($this->request['IN_APP_CLIENT']){
                $this->tmpl = 'trade/payment/apppay.html';
            }else{
                if($this->uid){
                    if($this->MEMBER['money'] >= ($order['amount'] + $order['change_price'])){
                        $use_money_last_amount = 0;
                    }else{
                        $use_money_last_amount = $order['amount'] + $order['change_price'] - $this->MEMBER['money'];
                    }
                    $this->pagedata['use_money_last_amount'] = $use_money_last_amount;
                }
                if(K::M('payment/payment')->payment('cash')){
                    $have_cash = 1;
                }else{
                    $have_cash = 0;
                }
                $wxpay_mweb = false;
                if($wxpay_config = K::M('payment/payment')->payment('wxpay')){
                    $wxpay_mweb = (bool)$wxpay_config['config']['wxpay_mweb'];
                }
                $this->pagedata['wxpay_mweb'] = $wxpay_mweb;
                $this->pagedata['have_cash'] = $have_cash;
                $this->tmpl = 'trade/payment/pay.html';
            }            
        }
    }

    public function money($code = null, $amount = null)
    {
        
        /*if($rebackurl = $this->GP('rebackurl')){
            $this->cookie->delete('pay_rebackurl');
            $this->cookie->set('pay_rebackurl', $rebackurl);
            //$rebackurl = K::M('helper/link')->mklink('trade/payment:success', array(), array(), 'www');
        }*/

        $rebackurl = $this->getrebackurl();
        if($rebackurl){
            $this->cookie->delete('pay_rebackurl');
            $this->cookie->set('pay_rebackurl', $rebackurl);
        }
        
        $code = $this->GP('code');
        $amount = $this->GP('amount');
        if(!$code){
            $this->msgbox->add('没有选择支付方式', 212);
        }else if(empty($amount)){
            $this->msgbox->add('付款金额不合法', 211);
        }else if($this->check_login()){
            if($code == 'wxpay'){
                $wxpay_mweb = false;
                if($wxpay_config = K::M('payment/payment')->payment('wxpay')){
                    $wxpay_mweb = (bool)$wxpay_config['config']['wxpay_mweb'];
                }
            }
            if(defined('IN_WEIXIN')){
                if($code == 'wxpay'){
                    $wx_openid = $this->check_wxopenid();
                    if(!$trade = K::M('trade/payment')->money($this->uid,'wxpay',$amount,'JSAPI')){
                        $this->msgbox->add('创建支付请求失败', 223);
                    }else{
                        $this->pagedata['jsApiParameters'] = $trade['jsApiParameters'];
                        $this->pagedata['trade'] = $trade;
                        $this->tmpl = 'trade/payment/wxjspay.html';
                    }
                }elseif($code == 'alipay'){
                    if(!$trade = K::M('trade/payment')->money($this->uid,'alipay',$amount,'qrcode')){
                        $this->msgbox->add('创建支付请求失败', 223);
                    }else{
                        $this->pagedata['trade'] = $trade;
                        $this->tmpl = 'trade/payment/aliqrcode.html';
                    }
                }
            }else if(!defined('IN_WEIXIN') && ($code == 'wxpay' && !$wxpay_mweb)){
                if(!$trade = K::M('trade/payment')->money($this->uid,'wxpay',$amount, 'qrcode')){
                    $this->msgbox->add('创建支付请求失败', 223);
                }else{
                    $this->pagedata['trade'] = $trade;
                    $this->tmpl = 'trade/payment/wxqrcode.html';
                }
            }elseif($trade = K::M('trade/payment')->money($this->uid, $code, $amount)){
                if(is_array($trade)){
                    if(isset($trade['payurl'])){
                        $url = $trade['payurl'];
                    }elseif(isset($trade['qrcode'])){
                        $qrurl = $this->mklink('trade/payment:wxqrcode', array(), array('codeurl' => $trade['qrcode'], 'amount' => $order['amount'], 'order_id' => $order['order_id']));
                        header("Location:{$qrurl}");
                        exit();
                    }else{
                        $this->msgbox->add('请求支付失败', 231);
                    }
                }else{
                    $url = $trade;
                }
                header("Location:{$url}");
            }else{
                $this->msgbox->add('请求支付失败', 231);
            }
        }
    }

    public function deliver($shop_id= null)
    {
        if($rebackurl = $this->GP('rebackurl')){
            $this->cookie->delete('pay_rebackurl');
            $this->cookie->set('pay_rebackurl', $rebackurl);
            $this->pagedata['rebackurl'] = $rebackurl;
            //$rebackurl = K::M('helper/link')->mklink('trade/payment:success', array(), array(), 'www');
        }
        $code = $this->GP('code');
        $amount = $this->GP('amount');
        if(!$code){
            $this->msgbox->add('没有选择支付方式', 212);
        }else if(empty($amount)){
            $this->msgbox->add('付款金额不合法', 211);
        }else if($shop_id){
            $this->pagedata['payd_type'] = 'deliver';
            if($code == 'wxpay'){
                $wxpay_mweb = false;
                if($wxpay_config = K::M('payment/payment')->payment('wxpay')){
                    $wxpay_mweb = (bool)$wxpay_config['config']['wxpay_mweb'];
                }
            }
            if(($code == 'wxpay')){
                if(!$trade = K::M('trade/payment')->deliver($shop_id,'wxpay',$amount, 'qrcode')){
                    $this->msgbox->add('创建支付请求失败', 223);
                }else{
                    $this->pagedata['trade'] = $trade;
                    $this->tmpl = 'trade/payment/wxqrcode.html';
                }
            }elseif($code == 'alipay'){
                if(!$trade = K::M('trade/payment')->deliver($shop_id,'alipay',$amount,'qrcode')){
                    $this->msgbox->add('创建支付请求失败', 223);
                }else{
                    $this->pagedata['trade'] = $trade;
                    $this->tmpl = 'trade/payment/aliqrcode.html';
                }
            }
           /* elseif($trade = K::M('trade/payment')->deliver($shop_id, $code, $amount)){
                if(is_array($trade)){
                    if(isset($trade['payurl'])){
                        $url = $trade['payurl'];
                    }elseif(isset($trade['qrcode'])){
                        $qrurl = $this->mklink('trade/payment:wxqrcode', array(), array('codeurl' => $trade['qrcode'], 'amount' => $order['amount'], 'order_id' => $order['order_id']));
                        header("Location:{$qrurl}");
                        exit();
                    }else{
                        $this->msgbox->add('请求支付失败', 231);
                    }
                }else{
                    $url = $trade;
                }
                header("Location:{$url}");*/
            else{
                $this->msgbox->add('请求支付失败', 231);
            }
        }
    }
    
    public function pay($code, $order_id, $is_use_money=0)
    {
        $this->cookie->delete('pay_rebackurl');
        if(!$rebackurl = $this->GP('rebackurl')){
            $rebackurl = K::M('helper/link')->mklink('trade/payment:success', array($order_id), array(), 'www');
        }
        $this->cookie->set('pay_rebackurl', $rebackurl);
        if(!$order_id = (int)$order_id){
            $this->error(404);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已经删除', 211);
        }else if($order['order_status'] < 0){
            $this->msgbox->add('订单已取消不可支付', 214);
        }else if($order['pay_status'] == 1){
            $this->msgbox->add('该订单已经支付', 216);
        }else if($order['amount'] <= 0){
            $this->msgbox->add('该订单不需要支付', 217);
        }else if($order['order_status'] == 8){
            $this->msgbox->add('订单已经完成无需支付', 218);
        }else if(empty($order['online_pay'])){
            $this->msgbox->add('订单无需在线支付', 219);
        }else{
            //余额抵扣部分
           /* if($this->uid && $this->uid == $order['uid'] && $is_use_money && $order['money'] == 0 &&$code=='cash'){
                $this->msgbox->add('现金支付不可使用余额抵扣',220)->response();
            }*/
            if($this->uid && $this->uid == $order['uid'] && $is_use_money && $order['money'] == 0){
                if($this->MEMBER['money'] >= ($order['amount']+$order['change_price']+$order['card_amount'])){
                    $code = 'money';
                }else{
                    $pay_money = $this->MEMBER['money'];
                    $pay_amount = $order['amount']+$order['change_price']-$pay_money;
                    if($a = K::M('member/member')->update_money($this->uid, -$pay_money, '支付订单(ID:'.$order_id.')')){
                        if(K::M('order/order')->update($order_id, array('money'=>$pay_money, 'amount'=>$pay_amount), true)){
                            $order['money'] = $pay_money;
                            $order['amount'] = $pay_amount;
                        }
                    }
                }
            }
            $order['amount'] = $order['amount'] + $order['card_amount']; //4.0会员卡金额
            
            if($code == 'wxpay'){
                $wxpay_mweb = false;
                if($wxpay_config = K::M('payment/payment')->payment('wxpay')){
                    $wxpay_mweb = $wxpay_config['config']['wxpay_mweb'];
                }
            }
            if(defined('IN_WEIXIN') && in_array($code, array('wxpay', 'alipay'))){
                if($code == 'wxpay'){
                    //$order['wx_openid'] = $this->get_wx_openid();
                    if(!$trade = K::M('trade/payment')->order('wxpay', $order, 'JSAPI')){
                        $this->msgbox->add('创建支付请求失败', 223);
                    }else{
                        $this->pagedata['rebackurl'] = $rebackurl;
                        $this->pagedata['jsApiParameters'] = $trade['jsApiParameters'];
                        $trade['order_id'] = $order_id;
                        $this->pagedata['trade'] = $trade;
                        $this->pagedata['order'] = $order;
                        $this->tmpl = 'trade/payment/wxjspay.html';
                    }
                }elseif($code == 'alipay'){
                    if(!$trade = K::M('trade/payment')->qrcodepay('alipay', $order)){
                        $this->msgbox->add('创建支付请求失败', 223);
                    }else{
                        $this->pagedata['rebackurl'] = $rebackurl;
                        $trade['order_id'] = $order_id;
                        $this->pagedata['trade'] = $trade;
                        $this->pagedata['order'] = $order;
                        $this->tmpl = 'trade/payment/aliqrcode.html';
                    }
                }
            }else if(!defined('IN_WEIXIN') && ($code == 'wxpay' && !$wxpay_mweb)){
                if(!$trade = K::M('trade/payment')->qrcodepay('wxpay', $order)){
                    $this->msgbox->add('创建支付请求失败', 223);
                }else{
                    $this->pagedata['rebackurl'] = $rebackurl;
                    $trade['order_id'] = $order_id;
                    $this->pagedata['trade'] = $trade;
                    $this->pagedata['order'] = $order;
                    $this->tmpl = 'trade/payment/wxqrcode.html';
                }
            }else if($trade = K::M('trade/payment')->order($code, $order)){
                if($code == 'money'){
                    if(!$log = K::M('payment/log')->log_by_no($trade['trade_no'])){
                        $this->msgbox->add('获取支付流水失败', 223);
                    }else if($log['payed']){
                        $this->msgbox->add('单号已经支付过了', 224);
                    }else if(!$member = K::M('member/member')->detail($order['uid'])){
                        $this->msgbox->add('该订单不支持余额支付', 221);
                    }else if(($amount = (float)$trade['amount']) <=0 ){
                        $this->msgbox->add('支付金额不合法', 222);
                    }else if($amount != $log['amount']){
                        $this->msgbox->add('支付金额不合法', 222);
                    }else if($member['money'] < $trade['amount']){
                        $this->msgbox->add('账户余额不足！', 223)->response();
                    }else if(K::M('member/member')->update_money($this->uid, -$amount, '支付订单(ID:' . $order_id . ')')){
                        if(K::M('payment/log')->set_payed($trade['trade_no'])){
                            if($res = K::M('trade/payment')->payed_order($log, $trade)){
                                if($order['wx_openid']){
                                    $wx_config = $this->system->config->get('wx_config');
                                    $config = $this->system->config->get('site');
                                    $a = array('title' => '恭喜您！订单支付成功！！', 'items' => array('OrderSn' => $order_id, 'OrderStatus' => '订单支付成功'), 'remark' => '您的订单于 ' . date('Y-m-d H:i:s', __TIME) . ' 支付成功');
                                    K::M('weixin/wechat')->admin_wechat_client()->sendTempMsg($order['wx_openid'], $wx_config['order_id'], $rebackurl, $a);
                                }
                            }
                        }
                    }
                    if($rebackurl = $this->GP('rebackurl')){
                        header("Location:{$rebackurl}");
                    }else{
                        $this->redirect($trade['trade_no']);
                    }
                    exit;
                } else{
                    if(is_array($trade)){
                        if(isset($trade['payurl'])){
                            $url = $trade['payurl'];
                        }elseif(isset($trade['qrcode'])){
                            $qrurl = $this->mklink('trade/payment:wxqrcode', array(), array('codeurl' => $trade['qrcode'], 'amount' => $order['amount'], 'order_id' => $order['order_id']));
                            header("Location:{$qrurl}");
                            exit();
                        }else{
                            $this->msgbox->add('请求支付失败', 231);
                        }
                    }else{
                        $url = $trade;
                    }
                    $url = is_array($trade) ? $trade['payurl'] : $trade;
                    if($code == 'wxpay' && $wxpay_mweb){
                        $redirect_url = $this->mklink('trade/payment:wxmweb', array($trade['trade_no']), array('rebackurl'=>$rebackurl), 'www');
                        $url .= '&redirect_url='.rawurlencode($redirect_url);
                    }
                    header("Location:{$url}");
                    exit();
                }
            }else{
                $this->msgbox->add('请求支付失败', 231);
            }
        } 
        $this->msgbox->set_data('forward', $rebackurl);
    }

    public function peicard($code=null, $card_id=null)
    {
        $code = $this->GP('code');
        $card_id = $this->GP('card_id');

        $this->cookie->delete('pay_rebackurl');
        if(!$rebackurl = $this->GP('rebackurl')){
            $rebackurl = K::M('helper/link')->mklink('trade/payment:success', array($order_id), array(), 'www');
        }
        $this->cookie->set('pay_rebackurl', $rebackurl);
        if(!$card_id = (int)$card_id){
            $this->error(404);
        }else if(!$card = K::M('peicard/card')->detail($card_id)){
            $this->msgbox->add('会员卡不存在或已经删除', 211);
        }else if(!($amount = $card['amount']) || ($amount <= 0)){
            $this->msgbox->add('会员卡金额有误', 217);
        }else if($this->check_login()){
            if($code == 'wxpay'){
                $wxpay_mweb = false;
                if($wxpay_config = K::M('payment/payment')->payment('wxpay')){
                    $wxpay_mweb = $wxpay_config['config']['wxpay_mweb'];
                }
            }

            $this->pagedata['payd_type'] = 'peicard';

            if(defined('IN_WEIXIN') && in_array($code, array('wxpay', 'alipay'))){
                if($code == 'wxpay'){
                    $wx_openid = $this->check_wxopenid();
                    if(!$trade = K::M('trade/payment')->peicard($this->uid, 'wxpay', $card, 'JSAPI')){
                        $this->msgbox->add('创建支付请求失败', 223);
                    }else{
                        $this->pagedata['rebackurl'] = $rebackurl;
                        $this->pagedata['jsApiParameters'] = $trade['jsApiParameters'];
                        $this->pagedata['trade'] = $trade;
                        $this->tmpl = 'trade/payment/wxjspay.html';
                    }
                }elseif($code == 'alipay'){
                    if(!$trade = K::M('trade/payment')->peicard($this->uid, 'alipay', $card, 'qrcode')){
                        $this->msgbox->add('创建支付请求失败', 223);
                    }else{
                        $this->pagedata['rebackurl'] = $rebackurl;
                        $this->pagedata['trade'] = $trade;
                        $this->tmpl = 'trade/payment/aliqrcode.html';
                    }
                }
            }else if(!defined('IN_WEIXIN') && ($code == 'wxpay' && !$wxpay_mweb)){
                if(!$trade = K::M('trade/payment')->peicard($this->uid, 'wxpay', $card, 'qrcode')){
                    $this->msgbox->add('创建支付请求失败', 223);
                }else{
                    $this->pagedata['rebackurl'] = $rebackurl;
                    $this->pagedata['trade'] = $trade;
                    $this->tmpl = 'trade/payment/wxqrcode.html';
                }
            }elseif($trade = K::M('trade/payment')->peicard($this->uid, $code, $card)){
                if($code == 'money'){
                    if(!$log = K::M('payment/log')->log_by_no($trade['trade_no'])){
                        $this->msgbox->add('获取支付流水失败', 223);
                    }else if($log['payed']){
                        $this->msgbox->add('单号已经支付过了', 224);
                    }else if(($amount = (float)$trade['amount']) <=0 ){
                        $this->msgbox->add('支付金额不合法', 222);
                    }else if($amount != $log['amount']){
                        $this->msgbox->add('支付金额不合法', 222);
                    }else if($this->MEMBER['money'] < $trade['amount']){
                        $this->msgbox->add('账户余额不足！', 223)->response();
                    }else if(K::M('member/member')->update_money($this->uid, -$amount, sprintf("购买配送会员卡(%s:￥%s)", $card['title'], $amount))){
                        if(K::M('payment/log')->set_payed($trade['trade_no'])){
                            if($res = K::M('trade/payment')->payed_peicard($log, $trade)){
                                
                            }
                        }
                    }
                    if($rebackurl = $this->GP('rebackurl')){
                        header("Location:{$rebackurl}");
                    }else{
                        $this->redirect($trade['trade_no']);
                    }
                    exit;
                }else{
                    if(is_array($trade)){
                        if(isset($trade['payurl'])){
                            $url = $trade['payurl'];
                        }elseif(isset($trade['qrcode'])){
                            $qrurl = $this->mklink('trade/payment:wxqrcode', array(), array('codeurl' => $trade['qrcode'], 'amount' => $order['amount'], 'order_id' => $order['order_id']));
                            header("Location:{$qrurl}");
                            exit();
                        }else{
                            $this->msgbox->add('请求支付失败', 231);
                        }
                    }else{
                        $url = $trade;
                    }
                    header("Location:{$url}");
                    exit;
                }                
            }else{
                $this->msgbox->add('请求支付失败', 231);
            }
        }
    }

    public function wxmweb($trade_no)
    {
        if(!is_numeric($trade_no)){
            $this->msgbox->add('交易号不正确', 211);
        }elseif(!$log = K::M('payment/log')->log_by_no($trade_no)){
            $this->msgbox->add('交易不存在', 212);
        }
        if(!$rebackurl = $this->GP('rebackurl')){
            $rebackurl = $this->gorebackurl();
        }
        $this->pagedata['rebackurl'] = $rebackurl;
        $this->pagedata['log'] = $log;
        $this->tmpl = 'trade/payment/wxmweb.html';
    }

    public function aliqrcode()
    {
        if(!$codeurl = $this->GP('codeurl')){
            exit('params error');
        }
        if(!$amount = $this->GP('amount')){
            exit('params error');
        }
        if(!$order_id = $this->GP('order_id')){
            exit('params error');
        }
        $amount = sprintf("%.2f", $amount);
        $this->pagedata['codeurl'] = $codeurl;
        $this->pagedata['amount'] = $amount;
        $this->pagedata['order_id'] = $order_id;
        $this->tmpl = 'trade/payment/aliqrcode.html';
    }

    public function wxjspay($order_id)
    {
        if(!$order_id = (int)$order_id){
            $this->error(404);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已经删除', 211);
        }else if($order['order_status'] < 0){
            $this->msgbox->add('订单已取消不可支付', 214);
        }else if($order['pay_status'] == 1){
            $this->msgbox->add('该订单已经支付', 216);
        }else if($order['amount'] <= 0){
            $this->msgbox->add('该订单不需要支付', 217);
        }else if($order['order_status'] == 8){
            $this->msgbox->add('订单已经完成无需支付', 218);
        }else if(empty($order['online_pay'])){
            $this->msgbox->add('订单无需在线支付', 219);
        }else{
            $order['wx_openid'] = $this->get_wx_openid();
            if(!$trade = K::M('trade/payment')->order('wxpay', $order, 'JSAPI')){
                $this->msgbox->add('创建支付请求失败', 223);
            }else{
                if($rebackurl = $this->GP('rebackurl')){
                    $this->pagedata['rebackurl'] = $rebackurl;
                }
                $this->pagedata['jsApiParameters'] = $trade['jsApiParameters'];
                $this->pagedata['trade'] = $trade;
                $this->pagedata['order'] = $order;
                $this->tmpl = 'trade/payment/wxjspay.html';
            }
        }
    }

    public function queryorder($trade_no)
    {
        //$this->check_login();
        $trade_status = 'WAITPAY'; 
        if(!is_numeric($trade_no)){
            $trade_status = 'FAIL';
        }else{
            $trade = K::M('trade/payment')->query($trade_no, $trade_status);
        }
        $this->msgbox->set('trade_status', $trade_status);
    }

    protected function gorebackurl($status='SUCCESS', $rebackurl=null)
    {
        if(empty($rebackurl) && (!$rebackurl = $this->cookie->get('pay_rebackurl'))){
            $rebackurl = K::M('helper/link')->mklink('trade/payment:success', array($order_id), array(), 'www');
        }
        return $rebackurl;
    }

    public function success($order_id)
    {
        $this->tmpl = 'trade/payment/success.html';
    }

    public function redirect($trade_no)
    {
        $url = K::M('helper/link')->mklink('ucenter/money:index', null, null, 'base');
        if($log = K::M('payment/log')->log_by_no($trade_no)){
            if($log['from'] == 'order'){
                $url = K::M('helper/link')->mklink('ucenter/order:detail', array($log['order_id']), array(), 'base');
            }
        }
        header("Location:{$url}");
        exit;
    }

}
