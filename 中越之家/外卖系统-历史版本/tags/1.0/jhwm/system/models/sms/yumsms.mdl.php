<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: 56dxw.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */

Import::I('sms');
class Mdl_Sms_Yumsms implements Sms_Interface
{   
    protected $gateway = 'http://http.yunsms.cn/tx/?uid=140243&pwd={passwd}&mobile={mobile}&content={content}&encode=utf8';
    protected $_cfg = array();

    public $lastmsg = '';
    public $lastcode = 1;

    public function __construct($system)
    {
        $this->_cfg = $system->config->get('sms');
    }
    
    public function send($mobile, $content)
    {

        $url = $this->gateway;
        $url = str_replace("{mobile}", $mobile, $url);
        $url = str_replace("{content}", $content, $url);
         $http = K::M('net/http');
        if($ret = $http->http($url, array(), 'GET')){
            if((int)$ret == 100){
                return true;
            }else{
                switch($ret){
                   case 101:$error=L('验证失败');break;
                   case 102:$error=L('短信不足');break;
                   case 103:$error=L('操作失败');break;
                   case 104:$error=L('非法字符');break;
                   case 105:$error=L('内容过多');break;
                   case 106:$error=L('号码过多');break;
                   case 107:$error=L('频率过快');break;
                   case 108:$error=L('号码内容空');break;
                   case 109:$error=L('账号冻结');break;
                   case 110:$error=L('禁止频繁单条发送');break;
                   case 111:$error=L('系统暂定发送');break;
                   case 112:$error=L('有错误号码');break;
                   case 113:$error=L('定时时间不对');break;
                   case 114:$error=L('账号被锁，10分钟后登录');break;
                   case 115:$error=L('连接失败');break;
                   case 116:$error=L('绑定IP不正确');break;
                   case 120:$error=L('系统升级');break;
                   default:$error=L('未知的错误');
                }
                $this->lastcode = $ret;
                $this->lastmsg = $error;
            }
        }else{
            $this->lastmsg = L('未知的错误');
        }
        return false;
    }

    public function query()
    {
        return 0;
    }
}