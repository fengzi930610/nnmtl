<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: config.php 9343 2015-03-24 07:07:00Z youyi $
 */
return array(
	'code'=>'alipay',
	'name'=>'支付宝',
	'content'=>'支付宝网站(www.alipay.com) 是国内先进的网上支付平台。',
	'website'   => 'http://www.alipay.com',
	'version'   => '1.0',
	'currency'  => '人民币',
	'config'    => array(
        // 'alipay_service'  => array(
        //     'text'      => '接口类型',
        //     'desc'  => '1.5%费率用户请选“担保交易接口”',
        //     'type'      => 'select',
        //     'items'     => array(
        //         'trade_create_by_buyer'   => '标准双接口',
        //         'create_partner_trade_by_buyer'   => '担保交易接口',
        //         'create_direct_pay_by_user'   => '即时到帐交易接口',
        //     ),
        // ),
        'refund_type'  => array(
            'text'      => '退款方式',
            'desc'  => '取消订单时是否原路退回资金',
            'type'      => 'radio',
            'items'     => array(
                '0'   => '退到余额',
                '1'   => '原路退回'
            ),
        ),
        'alipay_rsa_type'   => array(
            'text'  => '签名类型',
            'desc'  =>'签名类型,目前支持RSA、RSA2两种类型',
            'type'  => 'radio',
            'items'     => array(
                'RSA'   => 'RSA',
                'RSA2'   => 'RSA2',
            ),
        ),

        'alipay_account'   => array(
            'text'  => '支付宝账户',
            'desc'  => '您必须拥有支付宝账户，能才通过支付宝收款，<a href="https://memberprod.alipay.com/account/reg/index.htm" style="color:red; font-weight:bold;" target="_blank">点此注册</a>我还未签约支付宝，<a href="https://b.alipay.com/order/serviceIndex.htm" style="color:red; font-weight:bold;" target="_blank">点此获取PID、Key</a>',
            'type'  => 'text',
        ),
        'alipay_partner'   => array(
            'text'  => '合作者身份(PID)',
       		'desc'	=>'<a href="https://b.alipay.com/order/serviceIndex.htm" id="pidKey" style="color:red; font-weight:bold;" target="_blank">我已签约支付宝，获取PID、Key</a>',
            'type'  => 'text',
        ),        
        'alipay_key' => array(
            'text'  => '安全校验码(Key)',
            'desc'  => '',
            'type'  => 'password',
        ),
        'alipay_rsa_private'   => array(
            'text'  => '合作者RSA私钥',
            'desc'  =>'合作者RSA私钥',
            'type'  => 'text',
        ),
        'alipay_rsa_public'   => array(
            'text'  => '支付宝RSA公钥',
            'desc'  =>'支付宝RSA公钥',
            'type'  => 'text',
        ),
        'alipay_appid' => array(
            'text'  => '开放平台(APPID)',
            'desc'  => '',
            'type'  => 'text',
        ),
        'open_rsa_private'   => array(
            'text'  => '开放平台合作者RSA私钥',
            'desc'  =>'开放平台合作者RSA私钥',
            'type'  => 'text',
        ),
        'open_rsa_public'   => array(
            'text'  => '开放平台支付宝RSA公钥',
            'desc'  =>'开放平台支付宝RSA公钥',
            'type'  => 'text',
        )
    ),
);