<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: wechat.class.php 9343 2015-03-24 07:07:00Z youyi $
 */


class WxAppClient
{

    protected $_appid = null;
    protected $_appsecret = null;
    
    public function __construct($appid, $appsecret)
    {

        $this->_appid = $appid;
        $this->_appsecret = $appsecret;

    }

    public function getAccessToken()
    {
        static $access_token = null;
        if(($access_token === null) || !($access_token = K::M('cache/cache')->get('wxapp_access_token'))){
            $url ='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->_appid.'&secret='.$this->_appsecret;
            if($ret = self::get($url)){
                $ret = json_decode($ret, true);
                if($ret['errcode']){
                    exit($ret['errmsg']);
                }
                $access_token = $ret['access_token'];
                K::M('cache/cache')->set('wxapp_access_token', $access_token, __TIME + $ret['expires_in'] - 300);
            }
        }
        return $access_token;
    }

    public function sendTempMsg($openid, $tmpl_id, $page, $form_id, $params)
    {
        if(!$access_token = $this->getAccessToken()){
            return false;
        }
        $api_url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
     
        $data = array(
            'touser'=>$openid, 
            'template_id'=>$tmpl_id, 
            'page'=>$page,
            'form_id'=>$form_id, 
            );
        foreach((array)$params as $k=>$v){
            if(is_array($v)){
                $data['data'][$k] = $v;
            }else{
                $data['data'][$k] = array('value'=>$v, 'color'=>'#080808');
            }
        }
        //$data['emphasis_keyword'] = "keyword1.DATA";        
        $json = json_encode($data);
        $json = urldecode($json);
        $res = self::post($api_url, $json);
        return json_decode($res,true);
    }

    /**
     * @method post
     * @static
     * @param  {string}        $url URL to post data to
     * @param  {string|array}  $data Data to be post
     * @return {string|boolen} Response string or false for failure.
     */
    protected static function post($url, $data=array())
    {
        if (!function_exists('curl_init')) {
            return false;
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

    /**
     * @method get
     * @static
     * @param  {string}
     * @return {string|boolen}
     */
    public static function get($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        # curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!curl_exec($ch)) {
            error_log(curl_error($ch));
            $data = '';
        } else {
            $data = curl_multi_getcontent($ch);
        }
        curl_close($ch);
        return $data;
    }

    public function getTmpls($offset=0, $count=20)
    {
        $token = $this->getAccessToken();
        $api_url = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token='.$token;
        $count = min((int)$count,20);
        $params = array('offset'=>$offset,'count'=>$count);
        $json = self::post($api_url,json_encode($params));
        $data = json_decode($json,true);
        return $data;
    }

    public function getTmplid($type)
    {
        if(!in_array($type,array('order','money'))){
            return false;
        }else if(!$res = self::getTmpls(0,20)){
            return false;
        }else if(isset($res['errcode']) && ( 0 !== (int) $res['errcode'])){
            return false;
        }else{
            $tmpl_id = '';
            switch ($type) {
                case 'order':
                    $title = '订单状态通知';
                    $id = 'AT0202';
                    $keyword_id_list = array(4, 5, 9);
                    break;
                case 'money':
                    $title = '账户资金变动提醒';
                    $id = 'AT0338';
                    $keyword_id_list = array(1, 2, 3, 4);
                    break;                
                default:
                    $title = '';
                    $id = '';
                    $keyword_id_list = array();
                    break;
            }
            foreach ($res['list'] as $k => $v) {
                if($v['title'] == $title){
                    $tmpl_id = $v['template_id'];
                    break;
                }
            }
            if(!$tmpl_id && $id && !empty($keyword_id_list)){
                if($add_res = self::addTmpl($id, $keyword_id_list)){
                    if($add_res['errcode'] == 0 && $add_res['template_id']){
                        $tmpl_id = $add_res['template_id'];
                    }
                }
            }

            return $tmpl_id;
        }
    }

    public function getLibraryTmpls($offset=0, $count=20)
    {
        $token = $this->getAccessToken();
        $api_url = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/library/list?access_token='.$token;
        $count = min((int)$count,20);
        $params = array('offset'=>$offset,'count'=>$count);
        $json = self::post($api_url,json_encode($params));
        $data = json_decode($json,true);
        return $data;
    }

    public function getTmplKeywords($id)
    {
        $token = $this->getAccessToken();
        $api_url = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/library/get?access_token='.$token;
        $params = array('id'=>$id);
        $json = self::post($api_url,json_encode($params));
        $data = json_decode($json,true);
        return $data;
    }

    public function addTmpl($id, $keyword_id_list)
    {
        $token = $this->getAccessToken();
        $api_url = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token='.$token;
        $params = array('id'=>$id,'keyword_id_list'=>$keyword_id_list);
        $json = self::post($api_url,json_encode($params));
        $data = json_decode($json,true);
        return $data;
    }

    public function delTmpl($template_id)
    {
        $token = $this->getAccessToken();
        $api_url = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/del?access_token='.$token;
        $params = array('template_id'=>$template_id);
        $json = self::post($api_url,json_encode($params));
        $data = json_decode($json,true);
        return $data;
    }
}
