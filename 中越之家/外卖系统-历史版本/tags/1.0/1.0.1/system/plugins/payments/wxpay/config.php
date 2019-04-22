<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: config.php 3053 2014-01-15 02:00:13Z youyi $
 */
$data = array(
    'code'=>'wxpay',
    'name'=>'微信',
    'content'=>'微信支付(pay.weixin.qq.com) 是国内先进的网上支付平台。<a href="http://pay.weixin.qq.com" target="_blank" style="color:red; font-weight:bold;">立即在线申请</a>（<a href="http://pay.weixin.qq.com" style="color:red; font-weight:bold;" target="_blank">如何启用微信收款</a>）',
    'website'   => 'https://pay.weixin.qq.com',
    'version'   => '1.0',
    'currency'  => '人民币',
    'config'    => array(
        'refund_type'  => array(
            'text'      => '退款方式',
            'desc'  => '取消订单时是否原路退回资金',
            'type'      => 'radio',
            'items'     => array(
                '0'   => '退到余额',
                '1'   => '原路退回'
            ),
        ),
        'wxpay_mweb'  => array(
            'text'      => '是否支持H5付款',
            'desc'  => '支持微信H5付款,需要在微信支付商户后申请',
            'type'      => 'radio',
            'items'     => array(
                '0'   => '否',
                '1'   => '是'
            ),
        ),
        'appid'   => array(
            'text'  => 'APPID',
            'desc'  =>'微信公众账号(服务号)APPID。<a href="http://mp.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        ),
        'appsecret' => array(
            'text'  => 'secret',
            'desc'  => '微信公众账号(服务号)AppSecret。<a href="http://mp.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        ),      
        'mch_id'   => array(
            'text'  => '公众号支付商户号',
            'desc'  =>'微信公众号(服务号)申请微信支付后，由微信支付分配的商户收款账号。',
            'type'  => 'text',
        ),
        'key' => array(
            'text'  => '公众号支付密钥',
            'desc'  => '微信公众号(服务号)申请微信支付后，在商户平台获取。<a href="http://pay.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'password',
        ),
        'mp_cert_pem' => array(
            'text'  => '证书pem格式',
            'desc'  => '公众号支付原路退款用，证书pem格式（apiclient_cert.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        ),
        'mp_key_pem' => array(
            'text'  => '证书密钥pem格式',
            'desc'  => '公众号支付原路退款用，证书密钥pem格式（apiclient_key.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        ),
        'app_appid'   => array(
            'text'  => '微信APP应用APPID',
            'desc'  =>'APP应用接入微信支付时需要到开发者平台申请移动应用的appid。<a href="http://open.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        ),
        'app_appsecret' => array(
            'text'  => '微信APP应用Appsecret',
            'desc'  => 'APP应用接入微信支付时需要到开发者平台申请移动应用的appsecret。<a href="http://open.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        ),
        'app_mch_id'   => array(
            'text'  => '微信APP支付商户号',
            'desc'  =>'APP应用接入微信支付时需要到开发者平台申请移动应用APP支付权限后获取.<a href="http://open.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        ),
        'app_key' => array(
            'text'  => '微信APP支付密钥',
            'desc'  => '微信开放平台应用申请微信支付后，在商户平台获取。<a href="http://pay.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'password',
        ),
        'app_cert_pem' => array(
            'text'  => '证书pem格式',
            'desc'  => 'APP支付原路退款用，证书pem格式（apiclient_cert.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        ),
        'app_key_pem' => array(
            'text'  => '证书密钥pem格式',
            'desc'  => 'APP支付原路退款用，证书密钥pem格式（apiclient_key.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        )        
    ),
);

if(defined('HAVE_WX_APP') && HAVE_WX_APP){
    $data['config']['wxapp_appid'] = array(
            'text'  => '微信小程序支付APPID',
            'desc'  =>'微信小程序支付APPID， <a href="http://mp.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        );
    $data['config']['wxapp_appsecret'] = array(
            'text'  => '微信小程序支付secret',
            'desc'  => '微信小程序支付secret, <a href="http://mp.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        );
    $data['config']['wxapp_mch_id'] = array(
            'text'  => '微信小程序支付商户号',
            'desc'  =>'微信小程序支付商户号',
            'type'  => 'text',
        );
    $data['config']['wxapp_key'] = array(
            'text'  => '微信小程序支付密钥',
            'desc'  => '微信小程序申请微信支付后，在商户平台获取。<a href="http://pay.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'password',
        );
    $data['config']['wxapp_cert_pem'] = array(
            'text'  => '证书pem格式',
            'desc'  => '小程序支付原路退款用，证书pem格式（apiclient_cert.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        );
    $data['config']['wxapp_key_pem'] = array(
            'text'  => '证书密钥pem格式',
            'desc'  => '小程序支付原路退款用，证书密钥pem格式（apiclient_key.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        );
}

if(defined('HAVE_PWXAPP') && HAVE_PWXAPP){
    $data['config']['wxapp_appid_paotui'] = array(
            'text'  => '微信小程序支付APPID(跑腿)',
            'desc'  =>'微信小程序支付APPID， <a href="http://mp.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        );
    $data['config']['wxapp_appsecret_paotui'] = array(
            'text'  => '微信小程序支付secret(跑腿)',
            'desc'  => '微信小程序支付secret, <a href="http://mp.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        );
    $data['config']['wxapp_mch_id_paotui'] = array(
            'text'  => '微信小程序支付商户号(跑腿)',
            'desc'  =>'微信小程序支付商户号',
            'type'  => 'text',
        );
    $data['config']['wxapp_key_paotui'] = array(
            'text'  => '微信小程序支付密钥(跑腿)',
            'desc'  => '微信小程序申请微信支付后，在商户平台获取。<a href="http://pay.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'password',
        );
    $data['config']['wxapp_cert_pem_paotui'] = array(
            'text'  => '证书pem格式(跑腿)',
            'desc'  => '小程序支付原路退款用，证书pem格式（apiclient_cert.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        );
    $data['config']['wxapp_key_pem_paotui'] = array(
            'text'  => '证书密钥pem格式(跑腿)',
            'desc'  => '小程序支付原路退款用，证书密钥pem格式（apiclient_key.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        );
}

return $data;


/*return array(
	'code'=>'wxpay',
	'name'=>'微信',
	'content'=>'微信支付(pay.weixin.qq.com) 是国内先进的网上支付平台。<a href="http://pay.weixin.qq.com" target="_blank" style="color:red; font-weight:bold;">立即在线申请</a>（<a href="http://pay.weixin.qq.com" style="color:red; font-weight:bold;" target="_blank">如何启用微信收款</a>）',
	'website'   => 'https://pay.weixin.qq.com',
	'version'   => '1.0',
	'currency'  => '人民币',
	'config'    => array(
        'refund_type'  => array(
            'text'      => '退款方式',
            'desc'  => '取消订单时是否原路退回资金',
            'type'      => 'radio',
            'items'     => array(
                '0'   => '退到余额',
                '1'   => '原路退回'
            ),
        ),
        'wxpay_mweb'  => array(
            'text'      => '是否支持H5付款',
            'desc'  => '支持微信H5付款,需要在微信支付商户后申请',
            'type'      => 'radio',
            'items'     => array(
                '0'   => '否',
                '1'   => '是'
            ),
        ),
        'appid'   => array(
            'text'  => 'APPID',
			'desc'	=>'微信公众账号(服务号)APPID。<a href="http://mp.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        ),
		'appsecret' => array(
			'text'  => 'secret',
			'desc'  => '微信公众账号(服务号)AppSecret。<a href="http://mp.weixin.qq.com" target="_blank">获取地址</a>',
			'type'  => 'text',
		),		
        'mch_id'   => array(
            'text'  => '公众号支付商户号',
       		'desc'	=>'微信公众号(服务号)申请微信支付后，由微信支付分配的商户收款账号。',
            'type'  => 'text',
        ),
        'key' => array(
            'text'  => '公众号支付密钥',
            'desc'  => '微信公众号(服务号)申请微信支付后，在商户平台获取。<a href="http://pay.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'password',
        ),
        'mp_cert_pem' => array(
            'text'  => '证书pem格式',
            'desc'  => '公众号支付原路退款用，证书pem格式（apiclient_cert.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        ),
        'mp_key_pem' => array(
            'text'  => '证书密钥pem格式',
            'desc'  => '公众号支付原路退款用，证书密钥pem格式（apiclient_key.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        ),
        'app_appid'   => array(
            'text'  => '微信APP应用APPID',
			'desc'	=>'APP应用接入微信支付时需要到开发者平台申请移动应用的appid。<a href="http://open.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        ),
		'app_appsecret' => array(
			'text'  => '微信APP应用Appsecret',
			'desc'  => 'APP应用接入微信支付时需要到开发者平台申请移动应用的appsecret。<a href="http://open.weixin.qq.com" target="_blank">获取地址</a>',
			'type'  => 'text',
		),
		'app_mch_id'   => array(
            'text'  => '微信APP支付商户号',
       		'desc'	=>'APP应用接入微信支付时需要到开发者平台申请移动应用APP支付权限后获取.<a href="http://open.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        ),
        'app_key' => array(
            'text'  => '微信APP支付密钥',
            'desc'  => '微信开放平台应用申请微信支付后，在商户平台获取。<a href="http://pay.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'password',
        ),
        'app_cert_pem' => array(
            'text'  => '证书pem格式',
            'desc'  => 'APP支付原路退款用，证书pem格式（apiclient_cert.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        ),
        'app_key_pem' => array(
            'text'  => '证书密钥pem格式',
            'desc'  => 'APP支付原路退款用，证书密钥pem格式（apiclient_key.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        ),
        'wxapp_appid'   => array(
            'text'  => '微信小程序支付APPID',
            'desc'  =>'微信小程序支付APPID， <a href="http://mp.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        ),
        'wxapp_appsecret' => array(
            'text'  => '微信小程序支付secret',
            'desc'  => '微信小程序支付secret, <a href="http://mp.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        ),
        'wxapp_mch_id'   => array(
            'text'  => '微信小程序支付商户号',
            'desc'  =>'微信小程序支付商户号',
            'type'  => 'text',
        ),
        'wxapp_key' => array(
            'text'  => '微信小程序支付密钥',
            'desc'  => '微信小程序申请微信支付后，在商户平台获取。<a href="http://pay.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'password',
        ),
        'wxapp_cert_pem' => array(
            'text'  => '证书pem格式',
            'desc'  => '小程序支付原路退款用，证书pem格式（apiclient_cert.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        ),
        'wxapp_key_pem' => array(
            'text'  => '证书密钥pem格式',
            'desc'  => '小程序支付原路退款用，证书密钥pem格式（apiclient_key.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        ),

        'wxapp_appid_paotui'   => array(
            'text'  => '微信小程序支付APPID(跑腿)',
            'desc'  =>'微信小程序支付APPID， <a href="http://mp.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        ),
        'wxapp_appsecret_paotui' => array(
            'text'  => '微信小程序支付secret(跑腿)',
            'desc'  => '微信小程序支付secret, <a href="http://mp.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'text',
        ),
        'wxapp_mch_id_paotui'   => array(
            'text'  => '微信小程序支付商户号(跑腿)',
            'desc'  =>'微信小程序支付商户号',
            'type'  => 'text',
        ),
        'wxapp_key_paotui' => array(
            'text'  => '微信小程序支付密钥(跑腿)',
            'desc'  => '微信小程序申请微信支付后，在商户平台获取。<a href="http://pay.weixin.qq.com" target="_blank">获取地址</a>',
            'type'  => 'password',
        ),
        'wxapp_cert_pem_paotui' => array(
            'text'  => '证书pem格式(跑腿)',
            'desc'  => '小程序支付原路退款用，证书pem格式（apiclient_cert.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        ),
        'wxapp_key_pem_paotui' => array(
            'text'  => '证书密钥pem格式(跑腿)',
            'desc'  => '小程序支付原路退款用，证书密钥pem格式（apiclient_key.pem）,请放在web不能访问到的目录，以免被恶意下载。',
            'type'  => 'text',
        )
    ),
);*/
