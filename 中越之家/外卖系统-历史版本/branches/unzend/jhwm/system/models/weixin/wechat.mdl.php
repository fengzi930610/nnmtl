<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Weixin_Wechat extends Model
{   

    protected $_wxtmpl = array(
            'wxmp' => array('order'=>'TM00017'),
            'wxapp' => array('order'=>'TM00017')
        );
  
    public function wechat_client($shop_id)
    {
        static $client = array();
        if(empty($client)){
            if(!$token = K::M('weixin/weixin')->get_access_token($shop_id)){
                return false;
            }else{
                $shop_weixin = K::M('weixin/weixin')->detail($shop_id);
                Import::L('weixin/wechat.class.php');
                $client = new WeChatClient($shop_weixin['wx_appid'], $shop_weixin['wx_appsecret']);   
                $client->setAccessToken(array('token'=>$token, 'expire'=>$shop_weixin['token_expire_in']));
            }
        }
        return $client;
    }

    public function admin_wechat_client()
    {
        static $client = array();
        if(!$client){
            if($config = K::M('system/config')->get('wechat')){
                Import::L('weixin/wechat.class.php');
                $client = new WechatClient($config['appid'], $config['appsecret']);
                $client->weixin_type = $config['type'];
            }
        }
        return $client;
    }
    
    public function app_client()
    {
        static $client = null;
        if ($client === null) {
            $wx_config = K::M('system/config')->get('wechat');
            if (!$wx_config['app_appid'] || !$wx_config['app_appsecret']) {
                return false;
            } else {
                Import::L('weixin/wechat.class.php');
                $client = new WeChatClient($wx_config['app_appid'], $wx_config['app_appsecret']);
                $client->weixin_type = $wx_config['type'];
            }
        }
        return $client;        
    }

    public function component_client()
    {
        static $client = null;
        if ($client === null) {
            $wx_config = K::M('system/config')->get('wechat');
            if (!$wx_config['open_mp_appid'] || !$wx_config['open_mp_appsecret']) {
                exit('开放平台公众号应用 appId, appSecret 未设置');
                return false;
            } else {
                Import::L('weixin/weixin.component.php');
                $client = new Weixin_Component($wx_config['open_mp_appid'], $wx_config['open_mp_appsecret'], $wx_config['open_mp_token'], $wx_config['open_mp_aeskey']);
            }
        }
        return $client;   
    }

    public function sendwxmp($openid, $title, $content, $url, $type='order', $extends=array())
    {
        if(!$TMID = $this->_wxtmpl['wxmp'][$type]){
            return false;
        }
        if(!$tmpl_id = K::M('cache/cache')->get('wxtmpl_wxmp_'.$TMID)){
            if(!$tmpl_id = $this->admin_wechat_client()->getTmplId($TMID)){
                return false;
            }
            K::M('cache/cache')->set('wxtmpl_wxmp_'.$TMID, $tmpl_id);
        }
        $a = array('title'=>$title, 'items' => $extends, 'remark' =>$content);
        $this->admin_wechat_client()->sendTempMsg($openid, $tmpl_id, $url, $a);
    }

    public function sendwxapp($openid, $title, $content, $url, $type='order', $extends=array())
    {
        static $wxtmpl = null;
        if($wxtmpl === null){
            $wxtmpl = K::M('system/config')->get('wxtmpl');
        }
        $a = array('title'=>$title, 'items' => $extends, 'remark' =>$content);
        $this->admin_wxapp_client()->sendTempMsg($openid, $wxtmpl['wxapp'][$type], $url, $a);
    }

}