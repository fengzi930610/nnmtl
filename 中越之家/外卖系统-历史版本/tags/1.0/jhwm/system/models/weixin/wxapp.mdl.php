<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Weixin_WxApp extends Model
{   
  
    protected $wxapp_appid = '';
    protected $wxapp_asecret = '';

    public function __construct($system)
    {
        parent::__construct($system);
        $config = K::M('system/config')->get('wechat');

        if(defined('CLIENT_OS') && CLIENT_OS == 'PWXAPP'){
            $this->wxapp_appid = $config['wxapp_appid_paotui'];
            $this->wxapp_secret = $config['wxapp_secret_paotui'];
        }else{            
            $this->wxapp_appid = $config['wxapp_appid'];
            $this->wxapp_secret = $config['wxapp_secret'];
        }

        //临时修改


    }

    public function onlogin($code)
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
        $api = sprintf($url, $this->wxapp_appid, $this->wxapp_secret, $code);
        if($res = K::M('net/http')->get($api)){
            if($ret = json_decode($res, true)){
                if($ret['errcode']){
                    $this->msgbox->add('网络请求错误', 511);
                }else{
                    return $ret;
                }
            }
        }
        $this->msgbox->add('网络请求错误', 511);
        return false;
    }

    public function get_appid()
    {
        return $this->wxapp_appid;
    }

    public function admin_wxapp_client()
    {
        static $client = array();
        if(!$client){
            if($config = K::M('system/config')->get('wechat')){
                Import::L('weixin/wxapp.class.php');
                $client = new WxappClient($this->wxapp_appid, $this->wxapp_secret);
            }
        }
        return $client;
    }
}