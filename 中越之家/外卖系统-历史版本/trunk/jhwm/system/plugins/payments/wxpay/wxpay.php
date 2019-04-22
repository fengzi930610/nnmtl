<?php
require_once __CFG::DIR . "plugins/payments/wxpay/lib/WxPay.Config.php";
require_once __CFG::DIR . "plugins/payments/wxpay/lib/WxPay.Api.php";
require_once __CFG::DIR . "plugins/payments/wxpay/lib/WxPay.Data.php";
require_once __CFG::DIR . "plugins/payments/wxpay/lib/WxPay.Notify.php";
require_once __CFG::DIR."plugins/payments/wxpay/WxPay.JsApiPay.php";

class Payment_Wxpay extends WxPayNotify {

    public function __construct($cfg)
    {
        $this->config = $cfg;
        $unpay_cancel_time = max((int)$cfg['unpay_cancel_time'], 5);
        $this->unpay_cancel_time = date("YmdHis", (__TIME + $unpay_cancel_time*60));
        $this->_parameter = array();
        $this->_parameter['APPID'] = $cfg['appid'];
        $this->_parameter['APPSECRET'] = $cfg['appsecret'];
        $this->_parameter['MCHID'] = $cfg['mch_id'];
        $this->_parameter['KEY'] = $cfg['key'];
        if(defined('IN_WXAPP') && IN_WXAPP){
            $this->setConfig('WXAPP');
        }elseif(defined('IN_APP') && IN_APP){
            $this->setConfig('APP');
        }else{
            $this->setConfig('NATIVE');
        }
        //测试支付期间,始终不用app配置        
        require_once __CFG::DIR . "plugins/payments/wxpay/WxPay.NativePay.php";
    }

    protected function setConfig($type='NATIVE')
    {

        // curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        // curl_setopt($ch,CURLOPT_SSLCERT, WxPayConfig::$SSLCERT_PATH);
        // curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        // curl_setopt($ch,CURLOPT_SSLKEY, WxPayConfig::$SSLKEY_PATH);

        if(strtoupper($type) == 'APP'){
            WxPayConfig::$APPID = $this->config['app_appid'];
            WxPayConfig::$MCHID = $this->config['app_mch_id'];
            WxPayConfig::$KEY = $this->config['app_key'];
            WxPayConfig::$APPSECRET = $this->config['app_appsecret'];
            WxPayConfig::$SSLCERT_PATH = $this->config['app_cert_pem'];
            WxPayConfig::$SSLKEY_PATH = $this->config['app_key_pem'];
            $this->config['return_url'] = $this->config['app_return_url'];
            $this->config['notify_url'] = $this->config['app_notify_url'];
        }elseif(strtoupper($type) == 'WXAPP'){
            if(CLIENT_OS == 'PWXAPP'){
                WxPayConfig::$APPID = $this->config['wxapp_appid_paotui'];
                WxPayConfig::$MCHID = $this->config['wxapp_mch_id_paotui'];
                WxPayConfig::$KEY = $this->config['wxapp_key_paotui'];
                WxPayConfig::$APPSECRET = $this->config['wxapp_appsecret_paotui'];
                WxPayConfig::$SSLCERT_PATH = $this->config['wxapp_cert_pem_paotui'];
                WxPayConfig::$SSLKEY_PATH = $this->config['wxapp_key_pem_paotui'];
                $this->config['return_url'] = $this->config['pwxapp_return_url'];
                $this->config['notify_url'] = $this->config['pwxapp_notify_url'];
            }else{
                WxPayConfig::$APPID = $this->config['wxapp_appid'];
                WxPayConfig::$MCHID = $this->config['wxapp_mch_id'];
                WxPayConfig::$KEY = $this->config['wxapp_key'];
                WxPayConfig::$APPSECRET = $this->config['wxapp_appsecret'];
                WxPayConfig::$SSLCERT_PATH = $this->config['wxapp_cert_pem'];
                WxPayConfig::$SSLKEY_PATH = $this->config['wxapp_key_pem'];
                $this->config['return_url'] = $this->config['wxapp_return_url'];
                $this->config['notify_url'] = $this->config['wxapp_notify_url'];               
            }
        }else{
            WxPayConfig::$APPID = $this->config['appid'];
            WxPayConfig::$MCHID = $this->config['mch_id'];
            WxPayConfig::$KEY = $this->config['key'];
            WxPayConfig::$APPSECRET = $this->config['appsecret'];
            WxPayConfig::$SSLCERT_PATH = $this->config['mp_cert_pem'];
            WxPayConfig::$SSLKEY_PATH = $this->config['mp_key_pem'];
            $this->config['return_url'] = $this->config['return_url'];
            $this->config['notify_url'] = $this->config['notify_url'];
        }       
    }

    //付款码付款
    public function codepay($input, &$msg='SUCCESS')
    {
        $this->setConfig('NATIVE');
        require_once __CFG::DIR . "plugins/payments/wxpay/WxPay.MicroPay.php";
        if(empty($input['auth_code'])){
            return false;
        }
        try{
            $inputObj = new WxPayMicroPay();
            $inputObj->SetAuth_code($input['auth_code']);
            $inputObj->SetBody($input['title']);
            $inputObj->SetTotal_fee($input['amount'] * 100);
            $inputObj->SetOut_trade_no($input['trade_no']);
            if(!empty($input['device_info'])){
                $inputObj->SetDevice_info($input['device_info']);
            }

            $microPay = new MicroPay();
            if($result = $microPay->pay($inputObj)){
                $trade = array('code'=>'wxpay', 'trade_no'=>$input['trade_no'], 'amount'=>$input['amount']);
                $trade['pay_trade_no'] = $result['transaction_id'];
                $trade['trade_status'] = $result['result_code'];
                $trade['pay_info'] = $result;
                return $trade;
            }
            return false;
        }catch(Exception $e){
            $msg = $e->getMessage();
            $this->_errlogs(array('codepay', $msg, $input));
            return false;
        }
    }

    public function qrcodepay($input)
    {
        $this->setConfig('NATIVE');
        $inputObj = new WxPayUnifiedOrder();
        $inputObj->SetBody($input['title']);
        $inputObj->SetOut_trade_no($input['trade_no']);
        $inputObj->SetTotal_fee($input['amount'] * 100);
        $inputObj->SetNotify_url($this->config['notify_url']);
        $inputObj->SetTrade_type("NATIVE");
        $inputObj->SetProduct_id($input['trade_no']);
        $inputObj->SetTime_start(date("YmdHis"));
        $inputObj->SetTime_expire($this->unpay_cancel_time);
        if ($inputObj->GetTrade_type() == "NATIVE") {
            $result = WxPayApi::unifiedOrder($inputObj);
            if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
                return array('code'=>'wxpay', 'trade_no'=>$input['trade_no'], 'qrcode'=>$result["code_url"], 'amount'=>$input['amount'],'prepay_id'=>$result['prepay_id']);
            }
        }
        $this->_errlogs(array('qrcodepay', $input, $result));
        return false;
    }

    public function Queryorder($transaction_id=null, $trade_no=null)
    {
        $inputObj = new WxPayOrderQuery();
        if($transaction_id){
            $inputObj->SetTransaction_id($transaction_id);
        }else if($trade_no){
            $inputObj->SetOut_trade_no($trade_no);
        }else{
            return false;
        }        
        $result = WxPayApi::orderQuery($inputObj);
        $this->_logs('query:' . K::M('utility/json')->encode($result));
        if (array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
            $trade = array(
                'code'      => 'wxpay',
                'trade_no'  => $result['out_trade_no'],
                'amount'    => $result['total_fee'] / 100,
                'pay_trade_no' => $result['transaction_id'], 
                'trade_status' => $result['trade_state'], 
                'trade_state'  => $result['trade_state'], 
                'trade_type'   => $result['trade_type'],
                'appid'        => $result['appid'],
                'mch_id'       => $result['mch_id'],
                'openid'       => $result['openid'],
                'is_subscribe' => $result['is_subscribe']
            );
            return $trade;
        }
        return false;
    }

    public function build_app($input)
    {
        $this->SetConfig('APP');
        $inputObj = new WxPayUnifiedOrder();
        $inputObj->SetBody($input['title']);
        $inputObj->SetOut_trade_no($input['trade_no']);
        $inputObj->SetTotal_fee($input['amount'] * 100);
        $inputObj->SetNotify_url($this->config['notify_url']);
        $inputObj->SetTrade_type("APP");
        $inputObj->SetProduct_id($input['trade_no']);
        $inputObj->SetTime_start(date("YmdHis"));
        $inputObj->SetTime_expire($this->unpay_cancel_time);
        if ($inputObj->GetTrade_type() == "APP") {
            $ret = WxPayApi::unifiedOrder($inputObj);
            if($ret['return_code'] == 'SUCCESS' && $ret['result_code'] == 'SUCCESS'){
                $timestamp = __TIME;
                $data = array(
                    'appid'     => $ret['appid'],
                    'partnerid' => $ret['mch_id'],
                    'noncestr' => $ret['nonce_str'],
                    'prepayid' => $ret['prepay_id'],
                    'package' => 'Sign=WXPay',
                    'timestamp' => "$timestamp"
                );
                $data['sign'] = $this->create_sign($data);
                $data['wxpackage'] = $data['package'];
                $data['sign_string'] = $this->sign_string;
                return $data;
            }
        }
        $this->_errlogs(array('build_app', $input, $ret));
        return false;
    }

    public function build_url($input)
    {
        $this->setConfig('NATIVE');
        $inputObj = new WxPayUnifiedOrder();
        $inputObj->SetBody($input['title']);
        $inputObj->SetOut_trade_no($input['trade_no']);
        $inputObj->SetTotal_fee($input['amount'] * 100);
        $inputObj->SetNotify_url($this->config['notify_url']);
        $inputObj->SetTrade_type("MWEB");
        $inputObj->SetProduct_id($input['trade_no']);
        $inputObj->SetTime_start(date("YmdHis"));
        $inputObj->SetTime_expire($this->unpay_cancel_time);
        if ($inputObj->GetTrade_type() == "MWEB") {
            $result = WxPayApi::unifiedOrder($inputObj);
            if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
                return array('trade_no'=>$input['trade_no'], 'payurl'=>$result["mweb_url"], 'amount'=>$input['amount'],'prepay_id'=>$result['prepay_id']);
            }
            $this->_errlogs(array('build_url',$input, $result));
        }
        return false;
    }

    public function build_qrcode($input)
    {
        $this->setConfig('NATIVE');
        $inputObj = new WxPayUnifiedOrder();
        $inputObj->SetBody($input['title']);
        $inputObj->SetOut_trade_no($input['trade_no']);
        $inputObj->SetTotal_fee($input['amount'] * 100);
        $inputObj->SetNotify_url($this->config['notify_url']);
        $inputObj->SetTrade_type("NATIVE");
        $inputObj->SetProduct_id($input['trade_no']);
        $inputObj->SetTime_start(date("YmdHis"));
        $inputObj->SetTime_expire($this->unpay_cancel_time);
        if ($inputObj->GetTrade_type() == "NATIVE") {
            $result = WxPayApi::unifiedOrder($inputObj);
            if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
                return array('trade_no'=>$input['trade_no'], 'qrcode'=>$result["code_url"], 'amount'=>$input['amount'],'prepay_id'=>$result['prepay_id']);
            }
            $this->_errlogs(array('build_url',$input, $result));
        }
        return false;
    }

    public function WxAppPay($input, &$msg='SUCCESS')
    {
        $this->setConfig('WXAPP');
        $tools = new JsApiPay();
        if(!$openid = $input['wx_openid']){
            $openid = K::$system->cookie->get('wx_openid');
        }
        $inputObj = new WxPayUnifiedOrder();
        $inputObj->SetBody($input['title']);
        $inputObj->SetOut_trade_no($input['trade_no']);
        $inputObj->SetTotal_fee($input['amount']*100);
        $inputObj->SetNotify_url($this->config['notify_url']);
        $inputObj->SetTrade_type("JSAPI");
        //$inputObj->SetProduct_id($input['trade_no']);
        $inputObj->SetTime_start(date("YmdHis"));
        $inputObj->SetTime_expire(date("YmdHis", time() + 600));
        $inputObj->SetOpenid($openid);
        $inputObj->SetTime_start(date("YmdHis"));
        $inputObj->SetTime_expire($this->unpay_cancel_time);
        $order = WxPayApi::unifiedOrder($inputObj);
        if($order['return_code'] == 'SUCCESS' && $order['result_code'] == 'SUCCESS'){
            $jsApiParameters = $tools->GetJsApiParameters($order);
            return array('trade_no'=>$input['trade_no'], 'qrcode'=>$result["code_url"], 'amount'=>$input['amount'],'jsApiParameters'=>json_decode($jsApiParameters, true),'prepay_id'=>$order['prepay_id']); 
        }else{
            $msg = $order['return_msg'];
            return false;
        }
    }

    public function JsApiPay($input, &$msg='SUCCESS', $flag=false)
    {
        $this->setConfig('NATIVE');
        $tools = new JsApiPay();
        if(!$openid = $input['wx_openid']){
            $openid = K::$system->cookie->get('wx_openid');
        }
        $inputObj = new WxPayUnifiedOrder();
        $inputObj->SetBody($input['title']);
        $inputObj->SetOut_trade_no($input['trade_no']);
        $inputObj->SetTotal_fee($input['amount']*100);
        $inputObj->SetNotify_url($this->config['notify_url']);
        $inputObj->SetTrade_type("JSAPI");
        //$inputObj->SetProduct_id($input['trade_no']);
        $inputObj->SetTime_start(date("YmdHis"));
        $inputObj->SetTime_expire($this->unpay_cancel_time);
        $inputObj->SetOpenid($openid);
        $order = WxPayApi::unifiedOrder($inputObj);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        return array('jsApiParameters'=>$jsApiParameters, 'trade_no'=>$input['trade_no'], 'amount'=>$input['amount']);
    }

    //trade_status: WAITPAY,SUCCESS,FAIL,CLOSED
    public function query($params, &$trade_status='WAITPAY')
    {
        if($params['trade_type'] == 'APP'){
            $this->setConfig('APP');
        }elseif($params['trade_type'] == 'WXAPP'){
            $this->setConfig('WXAPP');
        }else{
            $this->setConfig('NATIVE');
        }
        if($trade = $this->Queryorder($params['pay_trade_no'], $params['trade_no'])){
            if($trade['trade_state'] == 'SUCCESS'){
                $trade_status = 'SUCCESS';
                return $trade;
            }elseif(in_array($trade['trade_state'], array('NOTPAY','USERPAYING'))){
                $trade_status = 'WAITPAY';
            }elseif($trade['trade_state'] == 'CLOSED'){
                $trade_status = 'CLOSED';
            }else{
                $trade_status = 'FAIL';
            }
        }else{
            $trade_status = 'FAIL';
        }
        return false;
    }

    public function cancel($params, &$msg='SUCCESS')
    {
        
    }

    public function refund($input, &$msg='SUCCESS')
    {
        if(!$this->config['refund_type']){
            $msg = '不支持原路退回';
            return false;
        }
        try{
            $trade_type = strtoupper($input['trade_type']);
            if($trade_type == 'APP'){ //如果是APP付款的刚从APP商户MCHID退款
                $this->setConfig('APP');
            }elseif($trade_type == 'WXAPP'){
                $this->setConfig('WXAPP');
            }else{
                $this->setConfig('NATIVE');
            }
            $inputObj = new WxPayRefund();
            if($trade_no = $input['trade_no']){
                $inputObj->SetOut_trade_no($trade_no);
            }else if($transaction_id = $input['pay_trade_no']){
                $inputObj->SetTransaction_id($transaction_id);
            }else{
                throw new WxPayException("退款申请接口中，out_trade_no、transaction_id至少填一个！");
            }
            if(!$out_refund_no = $input['refund_no']){
                $out_refund_no = WxPayConfig::$MCHID.date('YmdHis');
            }
            $inputObj->SetOut_refund_no($out_refund_no);
            $inputObj->SetTotal_fee(bcmul($input['total_amount'], 100, 0));
            $inputObj->SetRefund_fee(bcmul($input['refund_amount'], 100, 0));
            $inputObj->SetOp_user_id(WxPayConfig::$MCHID);
            $result = WxPayApi::refund($inputObj);
            $this->_logs('refund:'.K::M('utility/json')->encode($result));
            if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
                $trade = array(
                    'code'          => 'wxpay',
                    'trade_no'      => $result['out_trade_no'], 
                    'pay_trade_no'  => $result['transaction_id'],
                    'refund_amount' => $result['refund_fee'] /100,
                    'refund_no'     => $result['out_refund_no'],
                    'pay_refund_no' => $result['refund_id'],
                    'refund_log'    => '资金退回微信（'.$trade['pay_trade_no'].'）'
                );
                return $trade;
            }
        }catch(WxPayException $e){
            $msg = $e->getMessage();
        }catch(Execption $e){
            $msg = $e->getMessage();
        }
        $this->_errlogs(array($input, $result,  $this->config, WxPayConfig::$SSLCERT_PATH, WxPayConfig::$SSLKEY_PATH, $msg));
        return false;
    } 

    public function NotifyProcess($trade, &$msg)
    {

        $success = false;
        $this->_logs('notify:' . K::M('utility/json')->encode($trade));
        if(!array_key_exists("transaction_id", $trade)) {
            $msg = "输入参数不正确";
        }elseif($trade['return_code'] != 'SUCCESS' || $trade['result_code'] != 'SUCCESS'){
            $msg = '支付失败';
        }elseif(!$query_trade = $this->Queryorder($trade["transaction_id"])) {//查询订单，判断订单真实性
            $msg = "订单查询失败";
        }elseif($query_trade['trade_state'] == 'SUCCESS') {
            //增加通过查询接口结果判断，以免以后微信会给类似支付宝的未支付成功也通知状态
            $_notify_trade = array(
                'code'      => 'wxpay',
                'trade_no'  => $trade['out_trade_no'],
                'amount'    => $trade['total_fee'] / 100,
                'pay_trade_no' => $trade['transaction_id'], 
                'trade_status' => $query_trade['trade_state'], 
                'trade_type'   => $trade['trade_type'],
                'appid'        => $trade['appid'],
                'mch_id'       => $trade['mch_id'],
                'openid'       => $trade['openid'],
                'is_subscribe' => $trade['is_subscribe']
            );
            $success = true;
            $this->_notify_trade = $_notify_trade;
        }
        return $success;
    }

    public function notify_verify()
    {
        $this->_notify_trade = null;
        $handle = $this->Handle(true);
        return $this->_notify_trade;
    }

    public function query_verify($trade_no, $from='NATIVE', &$msg='success')
    {
        //NATIVE,APP,WXAPP,MICROPAY      
        $this->setConfig($from);
        try{
            $inputObj = new WxPayOrderQuery();
            $inputObj->SetOut_trade_no($trade_no);
            $result = WxPayApi::orderQuery($inputObj);
            if (array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS" && $result['trade_state'] == 'SUCCESS') {
                $trade = array(
                    'code'      => 'wxpay',
                    'trade_no'  => $trade_no,
                    'amount'    => $result['total_fee']/100,
                    'pay_trade_no' => $result['transaction_id'], 
                    'trade_status' => $result['trade_state'], 
                    'trade_type'   => $result['trade_type'],
                    'appid'        => $result['appid'],
                    'mch_id'       => $result['mch_id'],
                    'openid'       => $result['openid'],
                    'is_subscribe' => $result['is_subscribe']
                );
                return $trade;
            }
        }catch(Exception $e){
            $msg = $e->getMessage();
        }
        return false;
    }

    public function notify_success($success)
    {
        if ($success) {
            echo "success";
            exit;
        } else {
            echo "fail";
            exit;
        }
    }

    private function params_to_url($params)
    {
        $buff = "";
        foreach ($params as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    private function create_sign($params)
    {
        ksort($params);
        $sign_string = $this->params_to_url($params);
        $sign_string = $sign_string . "&key=".WxPayConfig::$KEY;
        $this->sign_string = $sign_string;
        return strtoupper(md5($sign_string));
    }


    protected function _errlogs($log)
    {
        $key = 'payment-wxpay-error-' . date('Ymd');
        K::M('system/logs')->log($key, $log);
    }

    protected function _logs($log)
    {
        $key = 'payment-wxpay-' . date('Ymd');
        K::M('system/logs')->log($key, $log);
    }

}
