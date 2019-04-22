<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: 56dxw.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */

Import::I('sms');
class Mdl_Sms_Bulksms implements Sms_Interface
{   
    //protected $gateway = '/submission/send_sms/2/2.0?username={uname}&password={passwd}&message={content}&msisdn={mobile}&dca=16bit';
    protected $gateway = 'https://bulksms.vsms.net/eapi/submission/send_sms/2/2.0';
    //https://bulksms.vsms.net/eapi/submission/send_sms/2/2.0?username=yishun1006&password=info123456&message=%E4%B8%AD%E6%96%87&msisdn=44123123456,44123123457
    protected $_cfg = array();

    public $lastmsg = '';
    public $lastcode = 1;

    public function __construct($system)
    {
        $this->_cfg = $system->config->get('sms');
        $this->gateway = str_replace("{uname}", 'yishun1006', $this->gateway);
        $this->gateway = str_replace("{passwd}", 'info123456', $this->gateway);
    }
    

    public function send($mobile, $content)
    {
        $url = $this->gateway;
        if(!$mobile || !$contet){
            return false;
        }
        $data = array(
            'username' => 'yishun1006',
            'password' => 'info123456',
            'message'  => mb_convert_encoding($content, "UTF-16", "UTF-8"),
            'msisdn'   => $mobile,
            'dca'      => '16bit'
        );
        //$content = bin2hex(mb_convert_encoding($content, "UTF-16", "UTF-8"));
        //$url = str_replace("{mobile}", $mobile, $url);
        //$url = str_replace("{content}", $content, $url);
         $http = K::M('net/http');
        if($return_str = $http->http($url, $data, 'POST')){
            $ret = explode( '|', $return_str );
            $status_code = $ret[0];
            if($status_code == '0' || $status_code == '1'){
                return true;
            }else{
                switch($status_code){
/*                  22  Internal fatal error
                    23  Authentication failure
                    24  Data validation failed
                    25  You do not have sufficient credits
                    26  Upstream credits not available
                    27  You have exceeded your daily quota
                    28  Upstream quota exceeded
                    40  Temporarily unavailable
                    201 Maximum batch size exceeded
*/
                   default:$error=L('发送短信失败');
                }
                $this->lastcode = $ret;
                $this->lastmsg = $error;
            }
        }else{
            $this->lastmsg = L('未知的错误');
        }
        return false;
    }
}