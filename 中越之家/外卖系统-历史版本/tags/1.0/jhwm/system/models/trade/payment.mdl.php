<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: payment.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Trade_Payment extends Model
{


    public function order($code, $order, $from=false, &$msg='')
    {
        if(!$oPayApi = $this->loadPayment($code)){
            return false;
        }else if(!$log = $this->get_payment_log($code, $order, $level, $from)){
            return false;
        }
        $from = $from ? strtoupper($from) : $form;
        $params = $this->get_payment_params($log, $order);
        if($from == 'APP'){
            return $oPayApi->build_app($params);
        }else if($from == 'JSAPI'){
            $params['wx_openid'] = $order['wx_openid'];
            return $oPayApi->JsApiPay($params, $msg);           
        }elseif($from == 'WXAPP'){
            $params['wx_openid'] = $order['wx_openid'];
            return $oPayApi->WxAppPay($params, $msg);
        }else if($from == 'QRCODE'){
            return $oPayApi->qrcode($params);
        }else if($from == 'CODEPAY'){ //扫码支付
            $params['auth_code'] = $order['auth_code'];
            return $oPayApi->codepay($params);
        }else if($from){
            return $oPayApi->build_form($params);
        }else{
            return $oPayApi->build_url($params);
        }
    }
    
    public function money($uid, $code, $amount, $from=false)
    {
        if(!$uid = (int)$uid){
            return false;
        }else if(!$member = K::M('member/member')->detail($uid)){
            return false;
        }else if(!$oPayApi = $this->loadPayment($code)){
            return false;
        }
        $from = $from ? strtoupper($from) : $form;
        if('wxpay' == $code){
            //NATIVE,APP,WXAPP,JSAPI,QRCODE,H5
            if(empty($from)){
                $trade_type = 'NATIVE';
            }else{
                $trade_type = $from;
            }
        }elseif('alipay' == $code){
            //APP,QRCODE,WAP,H5
            if(empty($from)){
                $trade_type = 'WAP';
            }else{
                $trade_type = $from;
            }
        }
        $log = array('uid'=>$uid,'amount'=>$amount,'payment'=>$code,'from'=>'money', 'trade_type'=>$trade_type);
        if(!$log_id = K::M('payment/log')->create($log, true)){
            return false;
        }
        $log = K::M('payment/log')->detail($log_id);
        $site = K::$system->config->get('site');
        $params = array();
        $params['title'] = $site['title'].'-充值余额';
        $params['body'] = '会员:'.$member['nickname'].'('.$uid.')';
        $params['amount'] = $amount;
        $params['trade_no'] = $log['trade_no'];
        if($from == 'APP'){
            return $oPayApi->build_app($params);
        }else if($from == 'JSAPI'){
            $params['wx_openid'] = WX_OPENID;
            return $oPayApi->JsApiPay($params, $msg);    
        }elseif($from == 'WXAPP'){
            $params['wx_openid'] = WX_OPENID;
            return $oPayApi->WxAppPay($params, $msg);
        }else if($from == 'QRCODE'){
            return $oPayApi->qrcodepay($params);
        }else if($from){
            return $oPayApi->build_form($params);
        }else{
            return $oPayApi->build_url($params);
        }
    }

    public function deliver($shop_id, $code, $amount, $from=false)
    {
        if(!$uid = (int)$shop_id){
            return false;
        }else if(!$member = K::M('waimai/waimai')->detail($uid)){
            return false;
        }else if(!$oPayApi = $this->loadPayment($code)){
            return false;
        }
        $from = $from ? strtoupper($from) : $form;
        if('wxpay' == $code){
            //NATIVE,APP,WXAPP,JSAPI,QRCODE,H5
            if(empty($from)){
                $trade_type = 'NATIVE';
            }else{
                $trade_type = $from;
            }
        }elseif('alipay' == $code){
            //APP,QRCODE,WAP,H5
            if(empty($from)){
                $trade_type = 'WAP';
            }else{
                $trade_type = $from;
            }
        }
        $log = array('shop_id'=>$uid,'amount'=>$amount,'payment'=>$code,'from'=>'deliver', 'trade_type'=>$trade_type);
        if(!$log_id = K::M('payment/log')->create($log, true)){
            return false;
        }
        $log = K::M('payment/log')->detail($log_id);
        $site = K::$system->config->get('site');
        $params = array();
        $params['title'] = $site['title'].'-充值配送费';
        $params['body'] = '商户:'.$member['title'].'('.$uid.')';
        $params['amount'] = $amount;
        $params['trade_no'] = $log['trade_no'];
        if($from == 'APP'){
            return $oPayApi->build_app($params);
        }else if($from == 'JSAPI'){
            $params['wx_openid'] = WX_OPENID;
            return $oPayApi->JsApiPay($params, $msg);
        }elseif($from == 'WXAPP'){
            $params['wx_openid'] = WX_OPENID;
            return $oPayApi->WxAppPay($params, $msg);
        }else if($from == 'QRCODE'){
            return $oPayApi->qrcodepay($params);
        }else if($from){
            return $oPayApi->build_form($params);
        }else{
            return $oPayApi->build_url($params);
        }
    }
   
    public function orders($code, $order_ids, $from=false, &$msg='')
    {
        if(!$oPayApi = $this->loadPayment($code)){
            return false;
        }else if(!$log = $this->get_payment_logs($code, $order_ids, $level, $from)){
            return false;
        }
        $from = $from ? strtoupper($from) : $from;
        $order_id_array = explode(",", $order_ids);
        $order = K::M('order/order')->detail($order_id_array[0]);
        $params = $this->get_payment_params2($log, $order_ids);
        if($from == 'APP'){
            return $oPayApi->build_app($params);
        }else if($from == 'JSAPI'){
            $params['wx_openid'] = $order['wx_openid'];
            return $oPayApi->JsApiPay($params, $msg);
        }else if($from == 'QRCODE'){
            return $oPayApi->qrcode($params);
        }else if($from == 'CODEPAY'){ //扫码支付
            $params['auth_code'] = $order['auth_code'];
            return $oPayApi->codepay($params);
        }else if($from){
            return $oPayApi->build_form($params);
        }else{
            return $oPayApi->build_url($params);
        }
    }

    public function codepay($code, $auth_code, $order, &$msg='')
    {
        if(!$oPayApi = $this->loadPayment($code)){
            return false;
        }else if(empty($auth_code)){
            return false;
        }else if(!$log = $this->get_payment_log($code, $order, $level, 'CODEPAY')){
            return false;
        }
        $params = $this->get_payment_params($log, $order);
        $params['auth_code'] = $auth_code;
        if($trade  =$oPayApi->codepay($params, $msg)){
            $trade['order_id'] = $order['order_id'];
            K::M('payment/log')->set_payed($log['trade_no'], $trade['pay_trade_no']);
        }
        return $trade;
    }
    
    
    public function codepays($code, $auth_code, $order_ids, &$msg='')
    {
        if(!$oPayApi = $this->loadPayment($code)){
            return false;
        }else if(empty($auth_code)){
            return false;
        }else if(!$log = $this->get_payment_logs($code, $order_ids, $level, 'CODEPAY')){
            return false;
        }
        $order_id_array = explode(",", $order_ids);
        $order = K::M('order/order')->detail($order_id_array[0]);
        $params = $this->get_payment_params2($log, $order_ids);
        $params['auth_code'] = $auth_code;
        if($trade  =$oPayApi->codepay($params, $msg)){
            $trade['order_ids'] = $order_ids;
            K::M('payment/log')->set_payed($log['trade_no'], $trade['pay_trade_no']);
        }
        return $trade;
    }
    

    public function qrcodepay($code, $order)
    {
        if(!$oPayApi = $this->loadPayment($code)){
            return false;
        }else if(!$log = $this->get_payment_log($code, $order, $level, 'QRCODE')){
            return false;
        }
        $params = $this->get_payment_params($log, $order);
        return $trade  = $oPayApi->qrcodepay($params);
    }
    
    public function qrcodepays($code, $order_ids)
    {
        if(!$oPayApi = $this->loadPayment($code)){
            return false;
        }else if(!$log = $this->get_payment_logs($code, $order_ids, $level, 'QRCODE')){
            return false;
        }
        $order_id_array = explode(",", $order_ids);
        $order = K::M('order/order')->detail($order_id_array[0]);
        $params = $this->get_payment_params2($log, $order_ids);
        return $trade  =$oPayApi->qrcodepay($params);
    }
    

    public function payed_order($log, $trade)
    {
        if($log['order_id']){
            $order_ids = array($log['order_id']);
        }elseif($log['order_ids']){
            $order_ids = explode(',', $log['order_ids']);
        }else{
            //支付流水日志不包括订单号时退款
           $trade['refund_amount'] = $trade['amount'];
            $trade['refund_reason'] = sprintf('交易(%s)关联未订单，退回支付金额', $trade['trade_no']);
            if(!$refund_trade = $this->refund_by_trade($trade, $msg)){
                K::M('system/logs')->log('payment.refund.error.'.date('Ymd'), array($trade, $order, $msg));
            }            
            return false;
        }
        foreach($order_ids as $order_id){
            if($order = K::M('order/order')->detail($order_id)){
                //订单取消退回支付, 合并支付只要有一个订单不通过全部退款，由用户再次发起支付完成
                if($order['order_status'] < 0){
                    $trade['refund_amount'] = $trade['amount'];
                    $trade['refund_reason'] = sprintf('交易(%s)关联的订单(%s)已取消，退回支付金额', $trade['trade_no'], $order_id);
                    if(!$refund_trade = $this->refund_by_trade($trade, $msg)){
                        K::M('system/logs')->log('payment.refund.error.'.date('Ymd'), array($trade, $order, $msg));
                    }
                    return false;
                }elseif($order['payed'] && $order['trade_no'] && $order['trade_no']!=$trade['trade_no']){
                    $trade['refund_amount'] = $trade['amount'];
                    $trade['refund_reason'] = sprintf('交易(%s)关联的订单(%s)由流水(%s)支付过了', $trade['trade_no'], $order_id, $order['trade_no']);
                    if(!$refund_trade = $this->refund_by_trade($trade, $msg)){
                        K::M('system/logs')->log('payment.refund.error.'.date('Ymd'), array($trade, $msg));
                    }
                    return false;
                }
            }
        }
        if($log['order_id']||$log['order_ids']){
            if(K::M('order/order')->set_payed($log, $trade)){
                return true;
            }
        }
        return false;
    }
    
    public function payed_money($log, $trade)
    {
        $cfg = K::$system->config->get('moneypack');
        foreach($cfg as $k=>$v){
            if($v['money'] == $log['amount']){
                $intro = "在线充值￥{$log['amount']},送￥{$v['give']}红包";
                $hongbao = $v['hongbao'];
            }
        }
        if(K::M('member/money')->update($log['uid'], $log['amount'], $intro)){
            foreach($hongbao as $k=>$v){
               $data = array('title'=>'充值送红包',
                   'min_amount'=>$v['order_amount'],
                   'amount'=>$v['coupon_amount'],
                   'uid'=>$log['uid'],
                   'type'=>1,
                   'ltime'=>(__TIME+$v['day']*86400),
                   'dateline'=>__TIME,
                   'clientip'=>__IP,
                   'num'=>1,
                   'from'=>$v['type'],
                   'limit_stime'=>$v['stime'],
                   'limit_ltime'=>$v['ltime']
               );
               K::M('hongbao/hongbao')->create($data);
            }
            return true;
        }else{
            return false;
        }
    }

    public function payed_deliver($log, $trade)
    {
        if(K::M('waimai/waimai')->update_money($log['shop_id'],$log['amount'],"充值配送费:￥".$log['amount'])){
            return true;
        }else{
            return false;
        }
    }

    public function query($trade_no, &$trade_status='WAITAPY')
    {
        //WAITPAY,SUCCESS,FAIL,CLOSED
        $trade_status = 'WAITAPY';
        if(!$log = K::M('payment/log')->log_by_no($trade_no)){
            $trade_status = 'FAIL';
        }elseif($log['payed']){
            $trade_status = 'SUCCESS';
        }elseif(!$oPayApi = $this->loadPayment($log['payment'])){
            $trade_status = 'FAIL';
        }elseif($trade = $oPayApi->query(array('trade_no'=>$trade_no, 'trade_type'=>$log['trade_type']), $trade_status)){
            if(K::M('payment/log')->set_payed($trade['trade_no'], $trade['pay_trade_no'])){
                if($log['from'] == 'money'){
                    K::M('trade/payment')->payed_money($log, $trade);
                }else if($log['from']=='deliver'){
                    K::M('trade/payment')->payed_deliver($log, $trade);
                }else{
                    K::M('trade/payment')->payed_order($log, $trade);
                }
            }
            return $trade;
        }
        return false;
    }

    public function refund($code, $order, &$msg='SUCCESS')
    {
         if(!is_numeric($order['refund_amount']) || ($refund_amount = $order['refund_amount'])<=0){
            //退款金额必须大于0
            return false;
        }elseif(!$oPayApi = $this->loadPayment($code)){
            return false;
        }elseif(!$pay_log = K::M('payment/log')->log_by_no($order['trade_no'])){
            //没有支付流水的不能原路退回
            return false;
        }elseif($pay_log['payment'] != $code){
            //与支付通道不同返回
            return false;
        }elseif(!$pay_log['payed'] || ($pay_log['amount']<$refund_amount)){
            //必须为支付的交易并且退款金额不能大于之前支付过的金额
            return false;
        }
        $log_data = array(
                'uid'           => $order['uid'], 
                'order_id'      => $order['order_id'], 
                'payment'       => $code, 
                'amount'        => $refund_amount,
                'from'          => 'order',
                'type'          => 'refund',
                'trade_type'    => $pay_log['trade_type']
            ); 
        //插入订单记录表
        if(!$log_id = K::M('payment/log')->create($log_data)){
            return false;
        }
        $refund_log = K::M('payment/log')->detail($log_id);
        $params = array('refund_amount'=>$refund_amount, 'refund_reason'=>$order['refund_reason'], 'order_id'=>$order['order_id']);
        $params['trade_no'] = $order['trade_no'];
        $params['total_amount'] = $order['amount'];
        $params['refund_no'] = $refund_log['trade_no'];
        $params['trade_type'] = $pay_log['trade_type'];
        if($trade = $oPayApi->refund($params, $msg)){
            if(!K::M('payment/log')->set_payed($refund_log['trade_no'], $trade['pay_trade_no'])){
                return false;
            }
        }
        return $trade;
    }

    //根据trade退款，支付订单通知时订单取消，或已经由其它支付方式支付了定订单
    public function refund_by_trade($trade, &$msg='SUCCESS')
    {
        $refund_amount = (float)$trade['amount'];
        $refund_reason = $trade['refund_reason'] ? $trade['refund_reason'] : '流水('.$trade['trade_no'].')退款';
        if($refund_amount <= 0){
            return false;
        }elseif(!$oPayApi = $this->loadPayment($trade['code'])){
            return false;
        }
        $log_data = array(
                'from'          => 'order',
                'type'          => 'refund',
                'payment'       => $trade['code'], 
                'amount'        => $refund_amount,
                'trade_type'    => $trade['trade_type']
            );
        if($pay_log = K::M('payment/log')->log_by_no($trade['trade_no'])){
            $log_data['uid'] = $pay_log['uid'];
            $log_data['order_id'] = $pay_log['order_id'];
        }
        //插入订单记录表
        if(!$log_id = K::M('payment/log')->create($log_data)){
            return false;
        }
        $refund_log = K::M('payment/log')->detail($log_id);
        $params = array(
                'refund_amount'     => $refund_amount, 
                'total_amount'      => $refund_amount,
                'refund_reason'     => $refund_reason, 
                'order_id'          => $refund_log['order_id'],
                'trade_no'          => $trade['trade_no'],
                'trade_type'        => $trade['trade_type'],
                'refund_no'         => $refund_log['trade_no']
            );
        if($refund_trade = $oPayApi->refund($params, $msg)){
            $extra = 'refund_no:'.$trade['trade_no'].', refund_reason:'.$refund_reason;
            if(!K::M('payment/log')->set_payed($refund_log['trade_no'], $refund_trade['pay_trade_no'], $extra)){
                return false;
            }
        }
        return $refund_trade;
    }

    //取消发起的支付
    public function cancel($trade_no, &$msg='SUCCESS')
    {
        if(!$log = K::M('payment/log')->log_by_no($trade_no)){
            $msg = '交易不存在';
            return false;
        }elseif($log['payed']){
            $msg = '订单已经付款成功';
            return false;
        }elseif($log['trade_status']<0){
            $msg = '交易已经取消';
        }elseif(!$obj = $this->loadPayment($log['payment'])){
            $msg = "您选择的支付接口不存在";
        }else{
            $obj->cancel(array('trade_no'=>$trade_no),$msg);
        }
    }
    
    public function loadPayment($code)
    {
        static $_PayApiObj = array();
        if(!is_object($_PayApiObj[$code])){
            $file = __CFG::DIR."plugins/payments/{$code}/{$code}.php";
            if(!file_exists($file)){
                $this->msgbox->add('您选择的支付接口不存在', 311);
                return false;
            }else if(!$payment = K::M('payment/payment')->payment($code)){
                $this->msgbox->add('您选择的支付接口不存在', 312);
                return false;
            }else if(empty($payment['status'])){
                $this->msgbox->add('您选择的支付接口不可用', 313);
                return false;
            }
            include($file);
            $clsName = "Payment_".ucfirst($code);
            $config = $payment['config'];
            $site = K::$system->config->get('site');
            $auto_matic = K::$system->config->get('automatic');
            $config['return_url'] = K::M('helper/link')->mklink('trade/payment:return', array($code), null, 'www');
            $config['notify_url'] = K::M('helper/link')->mklink('trade/payment:notify', array($code), null, 'www');
            $config['app_return_url'] = K::M('helper/link')->mklink('trade/payment:return', array($code, 'app'), null, 'www');
            $config['app_notify_url'] = K::M('helper/link')->mklink('trade/payment:notify', array($code, 'app'), null, 'www');
            $config['wxapp_return_url'] = K::M('helper/link')->mklink('trade/payment:return', array($code, 'wxapp'), null, 'www');
            $config['wxapp_notify_url'] = K::M('helper/link')->mklink('trade/payment:notify', array($code, 'wxapp'), null, 'www');
            $config['pwxapp_return_url'] = K::M('helper/link')->mklink('trade/payment:return', array($code, 'pwxapp'), null, 'www');
            $config['pwxapp_notify_url'] = K::M('helper/link')->mklink('trade/payment:notify', array($code, 'pwxapp'), null, 'www');
            $config['show_url'] = $site['siteurl'];
            $config['unpay_cancel_time'] = $auto_matic['unpay_cancel_time']?$auto_matic['unpay_cancel_time']:5;
            $_PayApiObj[$code] = new $clsName($config);
        }
        return $_PayApiObj[$code];
    }

    protected function get_payment_log($code, $order, &$level=0, $from=null)
    {
        $from = $from ? strtoupper($from) : $form;
        if('wxpay' == $code){
            //NATIVE,APP,WXAPP,JSAPI,QRCODE,H5
            if(empty($from)){
                $trade_type = 'NATIVE';
            }else{
                $trade_type = $from;
            }
        }elseif('alipay' == $code){
            //APP,QRCODE,WAP,H5
            if(empty($from)){
                $trade_type = 'WAP';
            }else{
                $trade_type = $from;
            }
        }
        $amount = $order['amount']+$order['change_price'];
        if(!$log = K::M('payment/log')->log_by_order_id($order['order_id'], $level)){
            $log = array('uid'=>$order['uid'],'shop_id'=>$order['shop_id'], 'order_id'=>$order['order_id'], 'pay_level'=>$level, 'payment'=>$code, 'trade_type'=>$trade_type, 'amount'=>$amount);
            //插入订单记录表
            $log['from'] = 'order';
            if(!$log_id = K::M('payment/log')->create($log)){
                return false;
            }
            $log = K::M('payment/log')->detail($log_id);            
        }else if($log['payed']){
            K::$system->msgbox->add('该订单已经支付成功', 211);
            return false;
        }
        $a = array();
        if($log['amount'] != ($order['amount']+$order['change_price'])){
            $log['amount'] = $a['amount'] = $amount;
        }
        if($log['from'] != 'order'){
            $a['from'] = 'order';
        }
        if($log['payment'] != $code){
            $a['payment'] = $code;
        }
        if($trade_type && $log['trade_type'] != $trade_type){
            $a['trade_type'] = $trade_type;
        }
        if($a){
            $log = array_merge($log, $a);
            K::M('payment/log')->update($log['log_id'], $a,  true);
        }
        return $log;
    }

    //edit by shzhrui 2017-06-15 22:20
    protected function get_payment_logs($code, $order_ids, &$level=0, $from=null)
    {
        $from = $from ? strtoupper($from) : $form;
        if('wxpay' == $code){
            //NATIVE,APP,WXAPP,JSAPI,QRCODE,H5
            if(empty($from)){
                $trade_type = 'NATIVE';
            }else{
                $trade_type = $from;
            }
        }elseif('alipay' == $code){
            //APP,QRCODE,WAP,H5
            if(empty($from)){
                $trade_type = 'WAP';
            }else{
                $trade_type = $from;
            }
        }        
        if(is_array($order_ids)){
            asort($order_ids); //从小到大排序
            $order_ids = implode(',', $order_ids);
        }
        if($order_list = K::M('order/order')->items_by_ids($order_ids)){
            $amount = 0;
            $order = array();
            foreach($order_list as $k=>$v){
                if(empty($order)){
                    $order = $v;
                }elseif($order['uid'] != $v['uid']){
                    $this->msgbox->add('只有同一用户订单才能合并付款', 451);
                    return false;
                }
                $amount += ($v['amount'] + $v['change_price']);
            }
        }

        if(!$log = K::M('payment/log')->log_by_order_ids($order_ids, $level)){
            $log = array('uid'=>$order['uid'],'order_ids'=>$order_ids, 'pay_level'=>$level, 'payment'=>$code, 'trade_type'=>$trade_type, 'amount'=>$amount);
            //插入订单记录表
            $log['from'] = 'order';
            if(!$log_id = K::M('payment/log')->create($log)){
                return false;
            }
            $log = K::M('payment/log')->detail($log_id);            
        }else if($log['payed']){
            K::$system->msgbox->add('该订单已经支付成功', 211);
            return false;
        }
        $a = array();
        if($log['amount'] != $amount){
            $log['amount'] = $a['amount'] = $amount;
        }
        if($log['payment'] != $code){
            $a['payment'] = $code;
        }
        if($trade_type && $log['trade_type'] != $trade_type){
            $a['trade_type'] = $trade_type;
        }
        if($a){
            $log = array_merge($log, $a);
            K::M('payment/log')->update($log['log_id'], $a,  true);
        }
        return $log;
    }
    
    
    protected function get_payment_params($log, $order)
    {
        $params = array();
        $params['order_id'] = $order['order_id'];
        $params['trade_no'] = $log['trade_no'];
        if($order['title']){
            $params['title'] = $order['title'];
            $params['body'] = $order['body'] ? $order['body'] : $order['title'].'('.$params['trade_no'].')';  
        }else if($order['shop_id'] && $shop = K::M('shop/shop')->detail($order['shop_id'])){
            $params['title'] = $shop['title'].'订单';
            $params['body'] = $shop['title'].'订单('.$params['trade_no'].')';
        }else{
            $site = K::$system->config->get('site');
            $params['title'] = $site['title'].'订单';
            $params['body'] = $site['title'].'订单('.$params['trade_no'].')';  
        }
        $params['amount'] = $log['amount'];
        $params['code'] = $log['payment'];
        // $params['payee'] = $log['payee'];
        // $params = array_merge($params, (array)$log['payee_info']);
        return $params;
    }
       
    protected function get_payment_params2($log, $order_ids)
    {
        $params = array();
        $params['order_ids'] = $log['order_ids'];
        $params['trade_no'] = $log['trade_no'];
        $site = K::$system->config->get('site');
        $params['title'] = $site['title'].'订单';
        $params['body'] = $site['title'].'订单('.$params['trade_no'].')';  
        $params['amount'] = $log['amount'];
        return $params;
    }

    public function peicard($uid, $code, $card, $from=false, &$msg='')
    {
        if(!$uid = (int)$uid){
            return false;
        }else if(!$member = K::M('member/member')->detail($uid)){
            return false;
        }else if(!$oPayApi = $this->loadPayment($code)){
            return false;
        }
        $from = $from ? strtoupper($from) : $form;
        if('wxpay' == $code){
            if(empty($from)){
                $trade_type = 'NATIVE';
            }else{
                $trade_type = $from;
            }
        }elseif('alipay' == $code){
            if(empty($from)){
                $trade_type = 'WAP';
            }else{
                $trade_type = $from;
            }
        }
        $log = array('uid'=>$uid,'amount'=>$card['amount'],'payment'=>$code,'from'=>'peicard', 'trade_type'=>$trade_type, 'card_id'=>$card['card_id']);
        if(!$log_id = K::M('payment/log')->create($log, true)){
            return false;
        }
        $log = K::M('payment/log')->detail($log_id);
        $site = K::$system->config->get('site');
        $params = array();
        $params['title'] = $site['title'].'-购买配送会员卡';
        $params['body'] = '会员:'.$member['nickname'].'('.$uid.')';
        $params['amount'] = $card['amount'];
        $params['trade_no'] = $log['trade_no'];
        if($from == 'APP'){
            return $oPayApi->build_app($params);
        }else if($from == 'JSAPI'){
            $params['wx_openid'] = WX_OPENID;
            return $oPayApi->JsApiPay($params, $msg);    
        }elseif($from == 'WXAPP'){
            $params['wx_openid'] = WX_OPENID;
            return $oPayApi->WxAppPay($params, $msg);
        }else if($from == 'QRCODE'){
            return $oPayApi->qrcodepay($params);
        }else if($from){
            return $oPayApi->build_form($params);
        }else{
            return $oPayApi->build_url($params);
        }
    }

    public function payed_peicard($log, $trade)
    {
        if(!$card = K::M('peicard/card')->detail($log['card_id'])){
            return false;
        }else{
            $data = array(
                'card_id'=>$card['card_id'],
                'uid'=>$log['uid'],
                'title'=>$card['title'],
                'ltime'=>(__TIME+$card['days']*86400),
                'limits'=>$card['limits'],
                'reduce'=>$card['reduce'],
                'dateline'=>__TIME
                );
            if(K::M('peicard/member')->create($data)){
                $tongji = array(
                    'amount'=>$card['amount'],
                    'from'=>'peicard',
                    'uid'=>$log['uid'],
                    'year'=>date('Y', __TIME),
                    'mouth'=>date('Ym', __TIME),
                    'day'=>date('Ymd', __TIME),
                    'hour'=>date('H', __TIME),
                    'dateline'=>__TIME
                    );
                K::M('site/tongji')->create($tongji);
                return true;
            }else{
                return false;
            }
        }
    }

}
