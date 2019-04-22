<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/2
 * Time: 10:17
 */
if(!defined('__CORE_DIR')){

    exit("Access Denied");

}

//print_r(__CFG::DIR);exit;

Import::L('alivoice/sdk/Config.php');
Import::L('alivoice/Dyvmsapi/Request/V20170525/SingleCallByTtsRequest.php');
Import::L('alivoice/Dyvmsapi/Request/V20170525/SingleCallByTtsRequest.php');

class Mdl_Waimai_Alicall extends Model {

    function singleCallByVoice($mobile) {

        //此处需要替换成自己的AK信息
        if( !__CFG::ALIVOICE_ID||!__CFG::ALIVOICE_Secret||!__CFG::ALIVOICE_MOBILE||!$mobile){
            return false;
        }
        $accessKeyId = __CFG::ALIVOICE_ID;
        $accessKeySecret = __CFG::ALIVOICE_Secret;
        //流量API产品名
        $product = "Dyvmsapi";
        //流量API产品域名
        $domain = "dyvmsapi.aliyuncs.com";
        //暂时不支持多Region
        $region = "cn-hangzhou";

        //初始化访问的acsCleint
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient= new DefaultAcsClient($profile);

        $request = new Dyvmsapi\Request\V20170525\SingleCallByVoiceRequest();
        //必填-被叫显号
        $request->setCalledShowNumber(__CFG::ALIVOICE_MOBILE);
        //必填-被叫号码
        $request->setCalledNumber($mobile);
        //必填-语音文件Code
        $request->setVoiceCode("0290a8d3-4f6d-4bdc-850a-5e7180ab7e7c.wav");
        //选填-外呼流水号
       //
        // $request->setOutId("1234");

        //发起访问请求
        return  $acsResponse = $acsClient->getAcsResponse($request);



    }

    function singleCallByTts($shop) {

        if( !__CFG::ALIVOICE_ID||!__CFG::ALIVOICE_Secret||!__CFG::ALIVOICE_MOBILE||!$shop){
            return false;
        }
        $accessKeyId = __CFG::ALIVOICE_ID;
        $accessKeySecret = __CFG::ALIVOICE_Secret;
        $product = "Dyvmsapi";
        $domain = "dyvmsapi.aliyuncs.com";
        $region = "cn-hangzhou";

        //初始化访问的acsCleint
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient= new DefaultAcsClient($profile);

        $request = new Dyvmsapi\Request\V20170525\SingleCallByTtsRequest();
        //必填-被叫显号
        $request->setCalledShowNumber(__CFG::ALIVOICE_MOBILE);
        //必填-被叫号码
        $request->setCalledNumber($shop['phone']);
        //必填-Tts模板Code
        $request->setTtsCode("TTS_82805004");
        //选填-Tts模板中的变量替换JSON,假如Tts模板中存在变量，则此处必填
       // $contact = $shop['contact'];
       // $request->setTtsParam("{\"name\":\"$contact\"}");
        //选填-外呼流水号
        //$request->setOutId("1234");

        //发起访问请求
        return $acsResponse = $acsClient->getAcsResponse($request);


    }



}