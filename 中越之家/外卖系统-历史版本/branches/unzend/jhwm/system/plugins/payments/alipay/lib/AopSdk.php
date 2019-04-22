<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: alipay.php 5379 2014-05-30 10:17:21Z youyi $
 */

define('__ALIPAY_LIB', dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once __ALIPAY_LIB.'aop/AopClient.php';
require_once __ALIPAY_LIB.'aop/AlipayMobilePublicMultiMediaClient.php';
require_once __ALIPAY_LIB.'aop/AlipayMobilePublicMultiMediaExecute.php';
require_once __ALIPAY_LIB.'aop/AopEncrypt.php';
require_once __ALIPAY_LIB.'aop/EncryptParseItem.php';
require_once __ALIPAY_LIB.'aop/EncryptResponseData.php';
require_once __ALIPAY_LIB.'aop/SignData.php';

require_once __ALIPAY_LIB.'f2fpay/service/AlipayTradeService.php';
require_once __ALIPAY_LIB.'f2fpay/model/result/AlipayF2FPayResult.php';
require_once __ALIPAY_LIB.'f2fpay/model/result/AlipayF2FQueryResult.php';
require_once __ALIPAY_LIB.'f2fpay/model/result/AlipayF2FRefundResult.php';
require_once __ALIPAY_LIB.'f2fpay/model/result/AlipayF2FPrecreateResult.php';
require_once __ALIPAY_LIB.'f2fpay/model/builder/AlipayTradeQueryContentBuilder.php';
require_once __ALIPAY_LIB.'f2fpay/model/builder/AlipayTradeCancelContentBuilder.php';
require_once __ALIPAY_LIB.'f2fpay/model/builder/AlipayTradePayContentBuilder.php';
require_once __ALIPAY_LIB.'f2fpay/model/builder/AlipayTradePrecreateContentBuilder.php';
require_once __ALIPAY_LIB.'f2fpay/model/builder/AlipayTradeRefundContentBuilder.php';
require_once __ALIPAY_LIB.'f2fpay/model/builder/AlipayTradeWapPayContentBuilder.php';

require_once __ALIPAY_LIB.'f2fpay/model/builder/ContentBuilder.php';
require_once __ALIPAY_LIB.'f2fpay/model/builder/ExtendParams.php';
require_once __ALIPAY_LIB.'f2fpay/model/builder/GoodsDetail.php';
require_once __ALIPAY_LIB.'f2fpay/model/builder/RoyaltyDetailInfo.php';

require_once __ALIPAY_LIB.'aop/request/AlipayTradeCancelRequest.php';
require_once __ALIPAY_LIB.'aop/request/AlipayTradeCloseRequest.php';
require_once __ALIPAY_LIB.'aop/request/AlipayTradeCreateRequest.php';
require_once __ALIPAY_LIB.'aop/request/AlipayTradeFastpayRefundQueryRequest.php';
require_once __ALIPAY_LIB.'aop/request/AlipayTradePayRequest.php';
require_once __ALIPAY_LIB.'aop/request/AlipayTradePrecreateRequest.php';
require_once __ALIPAY_LIB.'aop/request/AlipayTradeQueryRequest.php';
require_once __ALIPAY_LIB.'aop/request/AlipayTradeRefundRequest.php';
require_once __ALIPAY_LIB.'aop/request/AlipayTradeVendorpayDevicedataUploadRequest.php';
require_once __ALIPAY_LIB.'aop/request/AlipayTradeWapPayRequest.php';
require_once __ALIPAY_LIB.'aop/request/AlipayFundTransToaccountTransferRequest.php';