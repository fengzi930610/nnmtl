<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/4/2
 * Time: 17:32
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Meituan_Meituan extends Model {

    private $app_id;
    private $app_signkey;
    private $request_url = "https://open-erp.meituan.com";
    private $app_url = "http://api.open.cater.meituan.com";
    protected $values = array();


    public function __construct()
    {

       $this->app_id = MEITUAN_APP_ID;
       $this->app_signkey = MWITUAN_SIGN_KEY;

    }


    public function get_auth_url($shop)
    {
        $url = $this->request_url. "/storemap?";
        $data = array(
            'charset'=>'UTF-8',
            'developerId'=>$this->app_id,
            'businessId'=>2,
            'ePoiId'=>$shop['shop_id'],
            'ePoiName'=>$shop['title'],
            'timestamp'=>$this->getMillisecond(),

        );
        $sign = $this->generate_signature($data);
        $data['sign'] = $sign;
        //$data['ePoiName'] = urlencode($data['ePoiName']);
        $query_string = $this->ToUrlParams($data);
        $url = $url.$query_string;
        return $url;

    }


    private function generate_signature($params)
    {

        ksort($params);
        $string = $this->ToSignParams($params);
        $string = $this->app_signkey.$string;
        $string = sha1($string);
        $result = strtolower($string);
        return $result;
    }



    public function ToUrlParams($data)
    {
        $buff = "";
        foreach ($data as $k => $v)
        {
            if($v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    public function ToSignParams($params)
    {
        $buff = "";
        foreach ($params as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k.$v;
            }
        }
        return $buff;
    }

    private static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return $t2.ceil( ($t1 * 1000) );
    }


    public function get_user($access_token,$shop_id)
    {

        $url  = $this->app_url."/waimai/poi/queryPoiInfo";
        $data = array();
        $data['appAuthToken'] = $access_token;
        $data['charset'] ="UTF-8";
        $data['timestamp'] = $this->getMillisecond();
        $data['version'] = 1;
        $data['ePoiIds'] = $shop_id;
        $data['sign'] = $this->generate_signature($data);
        return K::M('net/http')->get($url,$data);

    }


    private  function postCurl($data, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;

    }



    public function confirm_order_lite($order_id,$access_token )
    {
        $url  = $this->app_url."/waimai/order/confirm";
        $data = array();
        $data['appAuthToken'] = $access_token;
        $data['charset'] ="UTF-8";
        $data['timestamp'] = $this->getMillisecond();
        $data['version'] = 1;
        $data['orderId'] = $order_id;
        $data['sign'] = $this->generate_signature($data);
        $res =  K::M('net/http')->post($url,$data);
        if($result = json_decode($res,true)){
            if($result['data']=='ok'){
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

    public function cancel_order_lite($order_id,$access_token )
    {
        $url  = $this->app_url."/waimai/order/cancel";
        $data['appAuthToken'] = $access_token;
        $data['charset'] ="UTF-8";
        $data['timestamp'] =  $this->getMillisecond();
        $data['orderId'] = $order_id;
        $data['version'] = 1;
        $data['reasonCode'] = 2010;
        $data['reason'] = urlencode("地址无法配送");
        $data['sign'] = $this->generate_signature($data);
        $res = K::M('net/http')->post($url,$data);
        if($result = json_decode($res,true)){
            if($result['data']=='ok'){
                 return true;
            }else{
                return false;
            }
        }
        return false;


    }



    public function return_unbind_url($access_token){
       $url ="https://open-erp.meituan.com/releasebinding?signKey=".$this->app_signkey."&businessId=2&appAuthToken=".$access_token;
       return  $url;
    }



    public function agree_refund_lite($order_id,$access_token){
        $url  = $this->app_url."/waimai/order/agreeRefund";
        $data['appAuthToken'] = $access_token;
        $data['charset'] ="UTF-8";
        $data['timestamp'] =  $this->getMillisecond();
        $data['orderId'] = $order_id;
        $data['version'] = 1;
        $data['reason'] = "同意退款";
        $data['reason'] = urlencode($data['reason']);
        $data['sign'] = $this->generate_signature($data);
        $res = K::M('net/http')->post($url,$data);
            if($result = json_decode($res,true)){
                if($result['data']=='ok'){
                    return true;
                }else{
                    return false;
                }
            }
        return false;

    }


    public function disagree_refund_lite($order_id,$access_token){
        $url  = $this->app_url."/waimai/order/rejectRefund";
        $data['appAuthToken'] = $access_token;
        $data['charset'] ="UTF-8";
        $data['timestamp'] =  $this->getMillisecond();
        $data['orderId'] = $order_id;
        $data['version'] = 1;
        $data['reason'] = "拒绝退款";
        $data['reason'] = urlencode($data['reason']);
        $data['sign'] = $this->generate_signature($data);
        if($res = K::M('net/http')->post($url,$data)){
            if($result = json_decode($res,true)){
                if($result['data']=='ok'){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return false;

    }


    public function received_order($order_id,$access_token){
        $url  = $this->app_url."/waimai/order/delivered";
        $data['appAuthToken'] = $access_token;
        $data['charset'] ="UTF-8";
        $data['timestamp'] =  $this->getMillisecond();
        $data['orderId'] = $order_id;
        $data['version'] = 1;
        $data['sign'] = $this->generate_signature($data);
        if($res = K::M('net/http')->post($url,$data)){
            if($result = json_decode($res,true)){
                if($result['data']=='ok'){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return false;

    }























}