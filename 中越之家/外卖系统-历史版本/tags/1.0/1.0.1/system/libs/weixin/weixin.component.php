<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: wechat.class.php 9343 2015-03-24 07:07:00Z youyi $
 */

class Weixin_Component
{   
 
    protected $component_appid = null;
    protected $component_appsecret = null;
    protected $component_token = null;
    protected $component_asekey = null;

    public function __construct($appid, $appsecret, $token=null, $asekey=null)
    {
        
        $this->component_appid = $appid;
        $this->component_appsecret = $appsecret;
        $this->component_token = $token;
        $this->component_asekey = $asekey;
    }

    public function component_access_token($ticket)
    {
        if(!$token = K::M('cache/cache')->get('wxopen.api.component_access_token')){
            if(empty($ticket)){
                if($a = K::M('cache/cache')->get('wxopen.api.ticket')){
                    $ticket = $a['ComponentVerifyTicket'];
                }
            }
            $api = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
            $params = array();
            $params['component_appid'] = $this->component_appid;
            $params['component_appsecret'] = $this->component_appsecret;
            $params['component_verify_ticket'] = $ticket;
            $json = json_encode($params);    
            if(!$ret = self::post($api, $json)){//expires_in
                return false;
            }           
            $data = json_decode($ret, true);
            if(!$token = $data['component_access_token']){
                return false;
            }
            $expires_in = $data['expires_in'] ? $data['expires_in'] : 7200;
            K::M('cache/cache')->set('wxopen.api.component_access_token', $token, $expires_in);
        }
        return $token;
    }


/*https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=xxx
POST数据示例:
{
"component_appid":"appid_value" 
}
请求参数说明
参数  说明
component_appid 第三方平台方appid
返回结果示例
{
"pre_auth_code":"Cx_Dk6qiBE0Dmx4EmlT3oRfArPvwSQ-oa3NL_fwHM7VI08r52wazoZX2Rhpz1dEw",
"expires_in":600
}*/

    public function pre_auth_code($access_token)
    {
        $api = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$access_token;
        $params = array();
        $params['component_appid'] = $this->component_appid;
        $json = json_encode($params);
        if($ret = self::post($api, $json)){
            return json_decode($ret, true);
        }
        return false;        
    }


    
    public function component_login_page($redirect_uri)
    {
        if($component_access_token = $this->component_access_token()){
            //var_dump($component_access_token);echo __FILE__."__________".__LINE__;exit;
            $pre_auth_code_ret = $this->pre_auth_code($component_access_token);
            if($pre_auth_code = $pre_auth_code_ret['pre_auth_code']){
                $redirect_uri = urlencode($redirect_uri);
                $url = "https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid={$this->component_appid}&pre_auth_code={$pre_auth_code}&redirect_uri={$redirect_uri}";
                return $url;   
            }            
        }//var_dump($component_access_token);echo __FILE__."__________".__LINE__;exit;
        return false;
    }

    /**
     * @method 公众号授权后根据返回的authorization_code取公众号信息
     * @param {string} authorization_code
     * @return {array} 返回公众号信息
     */
    public function authorizer_info_by_code($code)
    {
        if(!$component_access_token = $this->component_access_token()){
            return false;
        }
        $api = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$component_access_token;
        $params = array('component_appid'=>$this->component_appid, 'authorization_code'=>$code);
        $json = json_encode($params);
        if($ret = self::post($api, $json)){
            return json_decode($ret, true);
        }
        return false;
    }

    /**
     * @method 根据公众号appid取公众号信息
     * @param {string} 公众号ID
     * @return {array} 返回公众号信息
     */
    public function authorizer_info($authorizer_appid)
    {
        if(!$component_access_token = $this->component_access_token()){
            return false;
        }
        $api = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token='.$component_access_token;
        $params = array('component_appid'=>$this->component_appid, 'authorizer_appid'=>$authorizer_appid);
        $json = json_encode($params);        
        if($ret = self::post($api, $json)){
            return json_decode($ret, true);
        }
        return false;
    }

    /**
     * @method 根据公众号appid取公众号信息
     * @param {string} 公众号ID
     * @param {string} 公众号ID
     * @return {array} 刷新后的token
     */
    public function refresh_authorizer_token($authorizer_appid, $refresh_token)
    {
        if(!$component_access_token = $this->component_access_token()){
            return false;
        }
        $api = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token='.$component_access_token;
        $params = array('component_appid'=>$this->component_appid, 'authorizer_appid'=>$authorizer_appid, 'authorizer_refresh_token'=>$refresh_token);
        $json = json_encode($params);
        if($ret = self::post($api, $json)){
            return json_decode($ret, true);
        }
        return false;
    }


    /*
    location_report(地理位置上报选项) 0:无上报,1:进入会话时上报,2:每5s上报
    voice_recognize（语音识别开关选项）0:关闭语音识别,1:开启语音识别
    customer_service（多客服开关选项）0:关闭多客服,1:开启多客服
    */
    
    public function set_authorizer_option($authorizer_appid, $key, $val)
    {
        if(!$component_access_token = $this->component_access_token()){
            return false;
        }
        $api = 'https://api.weixin.qq.com/cgi-bin/component/ api_set_authorizer_option?component_access_token='.$component_access_token;
        $params = array('component_appid'=>$this->component_appid, 'authorizer_appid'=>$authorizer_appid, 'option_name'=>$key, 'option_value'=>$val);
        $json = json_encode($params);
        if($ret = self::post($api, $json)){
            return json_decode($ret, true);
        }
        return false;
    }

    /**
     * @method post
     * @static
     * @param  {string}        $url URL to post data to
     * @param  {string|array}  $data Data to be post
     * @return {string|boolen} Response string or false for failure.
     */
    private static function post($url, $data) {
        if (!function_exists('curl_init')) {
            return '';
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        # curl_setopt( $ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($ch);
        if (!$data) {
            error_log(curl_error($ch));
        }
        curl_close($ch);
        return $data;
    }
}
