<?php
require_once __CORE_DIR.'libs/yilianyun/YLYConfigClient.php';
require_once __CORE_DIR.'libs/yilianyun/YLYSignAndUuidClient.php';
require_once __CORE_DIR.'libs/yilianyun/YLYHttpClient.php';

/*Import::L('yilianyuan/YLYConfigClient.php');
Import::L('yilianyuan/YLYSignAndUuidClient.php');
Import::L('yilianyuan/YLYHttpClient.php');*/
class YLYOpenApiClient {

    /**
     * 自有应用模式下的授权终端,扫码绑定请把msign参数改成qr_key
     * @param $machineCode
     * @param $msign or $qrKey
     * @param $accessToken
     * @param $timesTamp
     * @return mixed
     */
    public static function addPrint($machineCode, $msign, $accessToken, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printer/addprinter';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'msign' => $msign,
            'access_token' => $accessToken,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }

    /**
     * 打印接口
     * @param $machineCode
     * @param $accessToken
     * @param $content
     * @param $originId
     * @param $timesTamp
     * @return mixed
     */
    public static function printIndex($machineCode, $accessToken, $content, $originId, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/print/index';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'content' => $content,
            'origin_id' => $originId,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }

    /**
     * 删除终端授权
     * @param $machineCode
     * @param $accessToken
     * @param $timesTamp
     * @return mixed
     */
    public static function deletePrint($machineCode, $accessToken, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printer/deleteprinter';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }

    /**
     * 设置应用菜单接口
     * @param $machineCode
     * @param $accessToken
     * @param $content
     * @param $timesTamp
     * @return mixed
     */
    public static function printMenu($machineCode, $accessToken, $content, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printmenu/addprintmenu';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'content' => $content,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }

    /**
     * 关机重启接口
     * @param $machineCode
     * @param $accessToken
     * @param $responseType
     * @param $timesTamp
     * @return mixed
     */
    public static function shutdownRestart($machineCode, $accessToken, $responseType, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printer/shutdownrestart';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'response_type' => $responseType,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }

    /**
     * 声音调节接口
     * @param $machineCode
     * @param $accessToken
     * @param $responseType
     * @param $voice
     * @param $timesTamp
     * @return mixed
     */
    public static function setSound($machineCode, $accessToken, $responseType, $voice, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printer/setsound';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'response_type' => $responseType,
            'voice' => $voice,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }

    /**
     * 获取机型打印宽度接口
     * @param $machineCode
     * @param $accessToken
     * @param $timesTamp
     * @return mixed
     */
    public static function printInfo($machineCode, $accessToken, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printer/printinfo';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }


    /**
     *获取机型软硬件版本接口
     * @param $machineCode
     * @param $accessToken
     * @param $timesTamp
     * @return mixed
     */
    public static function getVersion($machineCode, $accessToken, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printer/getversion';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }

    /**
     * 取消所有未打印接口
     * @param $machineCode
     * @param $accessToken
     * @param $timesTamp
     * @return mixed
     */
    public static function cancelAll($machineCode, $accessToken, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printer/cancelall';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }

    /**
     * 取消单条未打印订单接口
     * @param $machineCode
     * @param $accessToken
     * @param $orderId
     * @param $timesTamp
     * @return mixed
     */
    public static function cancelOne($machineCode, $accessToken, $orderId, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printer/cancelone';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'order_id' => $orderId,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }

    /**
     * 设置logo接口
     * @param $machineCode
     * @param $accessToken
     * @param $imgUrl
     * @param $timesTamp
     * @return mixed
     */
    public static function setIcon($machineCode, $accessToken, $imgUrl, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printer/seticon';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'img_url' => $imgUrl,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }

    /**
     * 取消logo接口
     * @param $machineCode
     * @param $accessToken
     * @param $timesTamp
     * @return mixed
     */
    public static function deleteIcon($machineCode, $accessToken, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printer/deleteicon';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }

    /**
     * 按键打印接口
     * @param $machineCode
     * @param $accessToken
     * @param $responseType
     * @param $timesTamp
     * @return mixed
     */
    public static function btnPrint($machineCode, $accessToken, $responseType, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printer/btnprint';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'response_type' => $responseType,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }

    /**
     * 接单拒单接口
     * @param $machineCode
     * @param $accessToken
     * @param $responseType
     * @param $timesTamp
     * @return mixed
     */
    public static function getOrder($machineCode, $accessToken, $responseType, $timesTamp)
    {
        $requestUrl = YLYConfigClient::$YLYRequestUrl.'/printer/getorder';
        $requestAll = array(
            'client_id' => YLYConfigClient::$YLYClientId,
            'sign' => YLYSignAndUuidClient::GetSign($timesTamp),
            'id' => YLYSignAndUuidClient::Uuid4(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'response_type' => $responseType,
            'timestamp' => $timesTamp,
        );
        $requestInfo = http_build_query($requestAll);
        return YLYHttpClient::push($requestInfo,$requestUrl);
    }


}

