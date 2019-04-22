<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_WeChat extends Ctl
{   

    public function code()
    {
        $appid = $this->GP('appid');
        $rebackurl = $this->getrebackurl();
        if($code = $this->GP('code')){
            if(strpos($rebackurl, '?')){
                $rebackurl = $rebackurl . '&code='.$code;
            }else{
                $rebackurl = $rebackurl . '?code='.$code;
            }
            header("Location:{$rebackurl}");
            exit;
        }else{
            $wx_config = K::M('system/config')->get('wechat');
            if($appid != $wx_config['appid']){
                exit('appid not found');
            }else{
                Import::L('weixin/wechat.class.php');
                if(!$client = new WechatClient($wx_config['appid'], $wx_config['appsecret'])){
                    exit('WechatClient error');
                }
                $state = md5($appid.uniqid());
                $url = 'http://waimai.jhcms.cn/wechat/code.html';
                $this->cookie->set('wx_code_rebackurl', $rebackurl);
                //$url .= '?wx_code_rebackurl='.urldecode($rebackurl);
                $authurl = $client->getOAuthConnectUri($url, $state, 'snsapi_userinfo');
                header('Location:'.$authurl);
                exit();
            }            
        }

    }

    protected function getrebackurl($rebackurl=null)
    {
        if(empty($rebackurl)){
            if(!$rebackurl = $this->GP('wx_code_rebackurl')){
                if(!$rebackurl = $this->GP('rebackurl')){
                    if(!$rebackurl = $this->cookie->get('wx_code_rebackurl')){
                        exit('rebackurl error');
                    }
                }
                
            }
        }
        return $rebackurl;
    }
}