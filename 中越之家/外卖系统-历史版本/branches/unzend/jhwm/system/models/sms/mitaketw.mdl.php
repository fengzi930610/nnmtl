<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: 56dxw.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */

//http://smexpress.mitake.com.tw:9600/SmSendGet.asp?username=Test001&password=TestPwd&dstaddr=09001234567&DestName=王先生&dlvtime=20060811093000&vldtime=20060812093000&smbody=%C2%B2%B0T%B4%FA%B8%D5&response=http://192.168.1.200/smreply.asp

Import::I('sms');
class Mdl_Sms_Mitaketw implements Sms_Interface
{   
    protected $gateway = 'http://smexpress.mitake.com.tw:9600/SmSendGet.asp?username={uname}&password={passwd}&dstaddr={mobile}&smbody={content}&encoding=UTF8';
    protected $_cfg = array();

    public $lastmsg = '';
    public $lastcode = 1;

    public function __construct($system)
    {
        $this->_cfg = $system->config->get('sms');
        $this->gateway = str_replace("{uname}", $this->_cfg['uname'], $this->gateway);
        $this->gateway = str_replace("{passwd}", $this->_cfg['passwd'], $this->gateway);
    }
    

    public function send($mobile, $content)
    {
        $content = mb_convert_encoding($content, 'BIG5', 'UTF-8');
        $url = $this->gateway;
        $url = str_replace("{mobile}", $mobile, $url);
        $url = str_replace("{content}", $content, $url);
        $http = K::M('net/http');
        if($return_str = $http->http($url, array(), 'GET')){
            $return_str = mb_convert_encoding($return_str, 'UTF-8', 'BIG5');
            if(!preg_match('/statuscode=(\d+)/', $return_str, $m)){
                return false;
            }
            $status_code = $m[1];
            if($status_code == '0' || $status_code == '1'){
                return true;
            }else{
                switch($status_code){
/*
0   預約傳送中
1   已送達業者
2   已送達業者
3   已送達業者
4   已送達手機
5   內容有錯誤
6   門號有錯誤
7   簡訊已停用
8   逾時無送達
9   預約已取消
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