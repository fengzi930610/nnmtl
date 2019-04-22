<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: sniffer.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Mdl_Net_Sniffer 
{
    public function check_robot($useragent = '')
    {
        static $kw_spiders = array('bot', 'crawl', 'spider' ,'slurp', 'sohu-search', 'lycos', 'robozilla');
        static $kw_browsers = array('msie', 'netscape', 'opera', 'konqueror', 'mozilla');
        $useragent = strtolower(empty($useragent) ? $_SERVER['HTTP_USER_AGENT'] : $useragent);
        if(strpos($useragent, 'http://') === false && K::M('content/string')->strpos($useragent, $kw_browsers)) return false;
        return K::M('content/string')->strpos($useragent, $kw_spiders, true);
    }
    public function check_mobile($useragent=null)
    {
        $useragent = $useragent ? $useragent : strtolower($_SERVER['HTTP_USER_AGENT']);
        static $touchbrowser_list =array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
                    'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',
                    'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
                    'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
                    'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
                    'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
                    'benq', 'haier', '^lct', '320x320', '240x320', '176x220');
        static $mobilebrowser_list =array('windows phone');
        static $wmlbrowser_list = array('cect', 'compal', 'ctl', 'lg', 'nec', 'tcl', 'alcatel', 'ericsson', 'bird', 'daxian', 'dbtel', 'eastcom',
                'pantech', 'dopod', 'philips', 'haier', 'konka', 'kejian', 'lenovo', 'benq', 'mot', 'soutec', 'nokia', 'sagem', 'sgh',
                'sed', 'capitel', 'panasonic', 'sonyericsson', 'sharp', 'amoi', 'panda', 'zte');
        $pad_list = array('pad', 'gt-p1000');        
        if(K::M('content/string')->strpos($useragent, $pad_list)) {
            return false;
        }else if(($v = K::M('content/string')->strpos($useragent, $mobilebrowser_list, true))){
            return $v;
        }else if(($v = K::M('content/string')->strpos($useragent, $touchbrowser_list, true))){
            return $v;
        }else if(($v = K::M('content/string')->strpos($useragent, $wmlbrowser_list))) {
            return $v;
        }
        //$brower = array('mozilla', 'chrome', 'safari', 'opera', 'm3gate', 'winwap', 'openwave', 'myop');
        //if(K::M('content/string')->strpos($useragent, $brower)) return false;
        return false;
    }
    function robot_from($referer='')
    {
        $referer = empty($referer) ? $_SERVER['HTTP_REFERER'] : $referer;
        if(strstr($referer, 'baidu.com')){ //百度
            preg_match("/baidu.+wo?r?d=([^\&]*)/is", $referer, $match);
            $q = urldecode($match[1]);
            $from = 'baidu';
        }else if(strstr($referer, 'google.com') or strstr($referer, 'google.cn')){ //谷歌
            preg_match("/google.+q=([^\&]*)/is", $referer, $match);
            $q = urldecode($match[1]);
            $from = 'google';
        }else if(strstr($referer, 'so.com')){ //360搜索
            preg_match("/so.+q=([^\&]*)/is", $referer, $match);
            $q = urldecode($match[1]);
            $from = '360';  
        }else if(strstr($referer, 'sogou.com')){ //搜狗
            preg_match("/sogou.com.+query=([^\&]*)/is", $referer, $match);
            $q = urldecode($tmp[1]);
            $from = 'sogou';    
        }else if(strstr($referer, 'soso.com')){ //搜搜
            preg_match("/soso.com.+w=([^\&]*)/is", $referer, $match);
            $q = urldecode($match[1]);
            $from = 'soso';
        }else if(strstr($referer, 'bing.com')){ //bing
            preg_match("/bing.com.+q=([^\&]*)/is", $referer, $match);
            $q = urldecode($match[1]);
            $from = 'bing';
        }else {
            return false;
        }
        return array('q'=>$q,'from'=>$from);
    }       
}