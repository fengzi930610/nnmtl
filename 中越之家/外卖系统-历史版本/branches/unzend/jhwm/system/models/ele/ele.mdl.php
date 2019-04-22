<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/3/22
 * Time: 14:04
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Ele_Ele extends Model {



    private $app_key;
    private $app_secret;
    private $sandbox;
    private $request_url;
    private $log;
    private $default_request_url = "https://open-api.shop.ele.me";
    private $default_sandbox_request_url = "https://open-api-sandbox.shop.ele.me";
    private $token_url;//获取token的url
    private $authorize_url;//获取授权的地址
    private $api_request_url;//api请求地址
    private $access_token;



    //初始化饿了模型
    public function __construct()
    {

        if (ELE_SANDBOX == false) {
            $this->request_url = $this->default_request_url;
        } elseif (ELE_SANDBOX == true) {
            $this->request_url = $this->default_sandbox_request_url;
        } else {
            throw new InvalidArgumentException("the type of sandbox should be a boolean");
        }

        if (ELE_APP_KEY == null || ELE_APP_KEY == "") {
            throw new InvalidArgumentException("app_key is required");
        }

        if (ELE_APP_SECRET == null || ELE_APP_SECRET == "") {
            throw new InvalidArgumentException("app_secret is required");
        }
        $this->token_url = $this->request_url."/token";
        $this->authorize_url =  $this->request_url."/authorize";
        $this->app_key = ELE_APP_KEY;
        $this->app_secret = ELE_APP_SECRET;
        $this->sandbox = ELE_SANDBOX;
        $this->api_request_url =  $this->request_url. "/api/v1";
    }


    public function get_auth_url()
    {
        $url = $this->request_url. "/authorize";
        $response_type = "code";
        $client_id = $this->app_key;
        $callback = CALL_BACK_URL;
        return $url . "?response_type=" . $response_type . "&client_id=" . $client_id . "&state=" . $this->create_uuid() . "&redirect_uri=" . $callback . "&scope=all" ;
    }


   public function create_uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }




    private function get_headers()
    {
        return array(
            "Authorization: Basic " . base64_encode(urlencode($this->app_key) . ":" . urlencode($this->app_secret)),
            "Content-Type: application/x-www-form-urlencoded; charset=utf-8",
            "Accept-Encoding: gzip");
    }



    private function request($body)
    {
        if ($this->log != null) {
            $this->log->info("request data: " . json_encode($body));
        }

        $ch = curl_init($this->token_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->get_headers());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "eleme-openapi-php-sdk");
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        $request_response = curl_exec($ch);
        if (curl_errno($ch)) {
            if ($this->log != null) {
                $this->log->error("error: " . curl_error($ch));
            }
            throw new Exception(curl_error($ch));
        }
        $response = json_decode($request_response);
        if (is_null($response)) {
            throw new Exception("illegal response :" . $request_response);
        }
        if (isset($response->error)) {
            throw new _Exception(json_encode($response));
        }

        return $response;
    }



    public function get_token_by_code($code)
    {
        $body = array(
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => CALL_BACK_URL,
            "client_id" => $this->app_key
        );
        $response = $this->request($body);
        return $response;
    }


    public function get_token_by_refresh_token($refresh_token)
    {
        $body = array(
            "grant_type" => "refresh_token",
            "refresh_token" => $refresh_token,
            "scope" => ELE_SCOPE
        );
        $response = $this->request($body);
        return $response;
    }



    public function get_user($access_token)
    {
        $this->access_token = $access_token;
        $response =  $this->call("eleme.user.getUser", array());
        return $response->authorizedShops;
    }


    public function call($action, $parameters)
    {
        $protocol = array(
            "nop" => '1.0.0',
            "token" => $this->access_token,
            'secret'=>$this->app_secret,
            "metas" => array(
                "app_key" => $this->app_key,
                "timestamp" => time(),
            ),
            "params" => $parameters,
            "action" => $action,
            "id" => $this->create_uuid(),

        );

        $protocol['signature'] = $this->generate_signature($protocol);


        if (count($parameters) == 0) {
            $protocol["params"] = (object)array();
        }

        $result = $this->post($this->api_request_url, $protocol);
        $response = json_decode($result, false);

        if (is_null($response)) {
            throw new Exception("invalid response.");
        }
        if (isset($response->error)) {
            switch ($response->error->code) {
                case "SERVER_ERROR":
                    throw new Exception($response->error->message);
                case "ILLEGAL_REQUEST":
                    throw new Exception($response->error->message);
                case "UNAUTHORIZED":
                    throw new Exception($response->error->message);
                case "ACCESS_DENIED":
                    throw new Exception($response->error->message);
                case "METHOD_NOT_ALLOWED":
                    throw new Exception($response->error->message);
                case "PERMISSION_DENIED":
                    throw new Exception($response->error->message);
                case "EXCEED_LIMIT":
                    throw new Exception($response->error->message);
                case "INVALID_SIGNATURE":
                    throw new Exception($response->error->message);
                case "INVALID_TIMESTAMP":
                    throw new Exception($response->error->message);
                case "VALIDATION_FAILED":
                    throw new Exception($response->error->message);
                default:
                    throw new Exception($response->error->message);
            }
        }

        return $response->result;
    }


    private function generate_signature($protocol)
    {
        $merged = array_merge($protocol['metas'], $protocol['params']);
        ksort($merged);
        $string = "";
        foreach ($merged as $key => $value) {
            if($key=="app_key"||$key=="orderId"||$key=='type'||$key=='remark'||$key=='reason'){
                $string .= $key . "=" . '"'.$value.'"';
            }else{
                $string .= $key . "=" . $value;
            }

        }
        $splice = $protocol['action'] . $this->access_token . $string . $this->app_secret;
        $encode = mb_detect_encoding($splice, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
        if ($encode != null) {
            $splice = mb_convert_encoding($splice, 'UTF-8', $encode);
        }

        return strtoupper(md5($splice));
    }



    private function post($url, $data)
    {
        $log = $this->log;
        if ($log != null) {
            $log->info("request data: " . json_encode($data));
        }


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json; charset=utf-8", "Accept-Encoding: gzip"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_USERAGENT, "eleme-openapi-php-sdk");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            if ($log != null) {
                $log->error("error: " . curl_error($ch));
            }
            throw new Exception(curl_error($ch));
        }

        if ($log != null) {
            $log->info("response: " . $response);
        }

        curl_close($ch);
        return $response;
    }


    public function confirm_order_lite($order_id,$access_token )
    {
        $this->access_token = $access_token;
        try {
            $this->call("eleme.order.confirmOrderLite", array("orderId" => $order_id));
            return true;
        }catch (Exception $e) {
            return false;
        }

    }


    public function cancel_order_lite($order_id,$access_token )
    {
        $this->access_token = $access_token;
        try {
            $this->call("eleme.order.cancelOrderLite", array("orderId" => $order_id,"type"=>'deliveryFault',"remark"=>'暂时无法配送'));
            return true;
        }catch (Exception $e) {
            return false;
        }

    }



    public function agree_refund_lite($order_id,$access_token){
        $this->access_token = $access_token;
        try {
            $this->call("eleme.order.agreeRefundLite", array("orderId" => $order_id));
            return true;
        }catch (Exception $e) {
            return false;
        }

    }


    public function disagree_refund_lite($order_id,$access_token,$reason){
        $this->access_token = $access_token;
        try {
            $this->call("eleme.order.disagreeRefundLite", array("orderId" => $order_id,'reason'=>$reason));
            return true;
        }catch (Exception $e) {
            K::M('system/logs')->log('aaa',$e->getMessage());
            return false;
        }
    }




    public function received_order($order_id,$access_token){
        $this->access_token = $access_token;
        try {
            $this->call("eleme.order.receivedOrderLite", array("orderId" => $order_id));
            return true;
        }catch (Exception $e) {
            return false;
        }
    }






















}