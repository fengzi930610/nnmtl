<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

require_once __CFG::DIR."plugins/payments/wxpay/WxPay.JsApiPay.php";
require_once __CFG::DIR."plugins/payments/wxpay/lib/WxPay.Api.php";
require_once __CFG::DIR."plugins/payments/wxpay/lib/WxPay.Config.php";

class Weixin_Jsapi
{

	public function __construct($cfg)
    {
		$this->config = $cfg;
		$this->_parameter = array();
        $this->_parameter['APPID'] = $cfg['appid'];
		$this->_parameter['APPSECRET'] = $cfg['appsecret'];
        $this->_parameter['MCHID'] = $cfg['mch_id'];
        $this->_parameter['KEY'] = $cfg['key'];
        WxPayConfig::$APPID = $this->config['appid'];
        WxPayConfig::$MCHID = $this->config['mch_id'];
        WxPayConfig::$KEY = $this->config['key'];
        WxPayConfig::$APPSECRET = $this->config['appsecret'];
	}

	public function build_url($input,$flag=false)
	{
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
		$order = WxPayApi::unifiedOrder($inputObj);

		//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
		//$this->printf_info($input);
		$jsApiParameters = $tools->GetJsApiParameters($order);
                if($flag){
                    return $jsApiParameters;
                }else{
                    //获取共享收货地址js函数参数
                    $editAddress = $tools->GetEditAddressParameters();
                    $smarty = K::M('system/frontend');
                    $smarty->assign('jsApiParameters',$jsApiParameters);
                    $smarty->assign('editAddress',$editAddress);
                    $smarty->assign('input',$input);
                    $smarty->display(__CFG::DIR."plugins/payments/wxpay/tpl/index.html");
                    exit;
                }
	}

	public function printf_info($data)
	{
		$arr = array('trade_no'=>'订单号','contact'=>'联系人','mobile'=>'电话','addr'=>'地址');
		foreach($arr as $key=>$value){
			echo "<font color='#00ff55;'>".$value."</font> : ".$data[$key]." <br/>";
		}
	}

}
