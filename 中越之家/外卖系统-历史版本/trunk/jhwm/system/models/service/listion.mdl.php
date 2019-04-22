<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: listion.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Mdl_Service_Listion
{

    public function verify($key, $domain)
    {
        $key = trim($key);
        if(!preg_match('/^[0-9a-f]{32}$/i', $key)){
            exit(base64_decode('ICAgICAgICAgICAgICAg'));
        }
        $host = $_SERVER["HTTP_HOST"];
        if(($_SERVER["REMOTE_ADDR"] !== '127.0.0.1' ||  $host !== 'localhost')){
            if(!preg_match('/^([\w\.\_\-]+)\.[a-z]{2,}$/i', $domain)){
                exit(base64_decode('ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg'));
            }else if(in_array($domain, array('com.cn', 'net.cn', 'org.cn'))){
                exit(base64_decode('ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg'));
            }else if(substr($host, -strlen($domain)) != $domain){
                exit(base64_decode('ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg'));
            }
        }
        $locked = 'xMpCOKC5I4INzFCab3WEm8TKQjiguSOCDcxQmm91hJvEykI4oLkjgg3MUJpvdYSbxMpCOKC5I4INzFCab3WEm8TKQjiguSOCDcxQmm91hJvEykI4oLkjgg3MUJpvdYSbxMpCOKC5I4INzFCab3WEm8TKQjiguSOCDcxQmm91hJvEykI4oLkjgg3MUJpvdYSbxMpCOKC5I4INzFCab3WEmw==';
        $ret = file_get_contents(sprintf('http://www.ijh.cc/index.php?ctl=jiaju&act=install&key=%s&domain=%s&version=%s', $key, $domain, JH_VERSION.'.'.JH_RELEASE));
        $hash = md5($key.base64_encode(str_repeat(md5($ret, true), 10)));
        if(md5($key.$locked) != $hash){
            exit(base64_decode('ICAg'));
        }
        return true;
    }
}