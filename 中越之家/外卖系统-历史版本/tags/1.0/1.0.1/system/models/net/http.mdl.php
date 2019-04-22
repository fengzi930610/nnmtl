<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: http.mdl.php 14743 2015-07-30 14:25:08Z maoge $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Net_Http
{
    
    public $timeout = 15;
    public $connect_timeout = 15;
    public $useragent = 'KT-API Client V1.0';
    public $http_code = 0;
    public $http_info = array();
    public function http($url, $params=array(), $method='POST', $headers = array())
    {
        if(!function_exists('curl_init')){
            return $this->fsockopen($url, $params, $method);
        }
        $this->http_code = 0;
        $this->http_info = array();
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'get_header'));
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        $params = http_build_query($params);
        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($params)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                    $this->postdata = $params;
                }
                break;
            case 'PUT' : 
                 curl_setopt($ch, CURLOPT_PUT, true);
                if (!empty($params)) {
                    $url = strpos('?',$url)===false ? "{$url}?{$params}" : "{$url}&{$params}";
                }
                break;
            case 'DELETE':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($params)) {
                    $url = strpos('?',$url)===false ? "{$url}?{$params}" : "{$url}&{$params}";
                }
                break;
            case 'GET':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'GET');
                if (!empty($params)) {
                    $url = strpos('?',$url)===false ? "{$url}?{$params}" : "{$url}&{$params}";
                }
        }
        $headers[] = "API-ClientIP: " . $_SERVER['REMOTE_ADDR'];
        curl_setopt($ci, CURLOPT_URL, $url );
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
        $res = curl_exec($ci);
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
        $this->url = $url;
        curl_close($ci);
        return $res;        
    }
    public function fsockopen($url, $params=array(), $method='POST')
    {
        $return = '';
        $cookie = '';
        $bysocket = FALSE;
        $ip = '';
        $timeout = $this->timeout;
        $block = TRUE;
        $matches = parse_url($url);
        !isset($matches['host']) && $matches['host'] = '';
        !isset($matches['path']) && $matches['path'] = '';
        !isset($matches['query']) && $matches['query'] = '';
        !isset($matches['port']) && $matches['port'] = '';
        $host = $matches['host'];
        $path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
        $port = !empty($matches['port']) ? $matches['port'] : 80;
        $post = '';
        if($params){
            $post = http_build_query($params);
        }
        if(strtoupper($method) == 'POST') {
            $out = "POST $path HTTP/1.0\r\n";
            $out .= "Accept: */*\r\n";
            //$out .= "Referer: $boardurl\r\n";
            $out .= "Accept-Language: zh-cn\r\n";
            $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
            $out .= "Host: $host\r\n";
            $out .= 'Content-Length: '.strlen($post)."\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Cache-Control: no-cache\r\n";
            $out .= "Cookie: $cookie\r\n\r\n";
            $out .= $post;
        } else {
            $out = "GET $path HTTP/1.0\r\n";
            $out .= "Accept: */*\r\n";
            //$out .= "Referer: $boardurl\r\n";
            $out .= "Accept-Language: zh-cn\r\n";
            $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Cookie: $cookie\r\n\r\n";
        }
        if(function_exists('fsockopen')) {
            $fp = @fsockopen(($ip ? $ip : $host), $post, $errno, $errstr, $timeout);
        } elseif (function_exists('pfsockopen')) {
            $fp = @pfsockopen(($ip ? $ip : $host), $post, $errno, $errstr, $timeout);
        } else {
            $fp = false;
        }
        if(!$fp) {
            return '';
        } else {
            stream_set_blocking($fp, $block);
            stream_set_timeout($fp, $timeout);
            @fwrite($fp, $out);
            $status = stream_get_meta_data($fp);
            if(!$status['timed_out']) {
                while (!feof($fp)) {
                    if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                        break;
                    }
                }
                $stop = false;
                while(!feof($fp) && !$stop) {
                    $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                    $return .= $data;
                    if($limit) {
                        $limit -= strlen($data);
                        $stop = $limit <= 0;
                    }
                }
            }
            @fclose($fp);
            return $return;
        }        
    }
    public function get($url,$params=array())
    {
        return $this->http($url,$params,'GET');
    }
    public function post($url,$params=array())
    {
        return $this->http($url,$params,'POST');
    }
    public function code()
    {
        return $this->http_code;
    }
    public function info()
    {
        return $this->http_info;
    }
    public function ping($url)
    {
        $this->http($url);
        return $this->http_code;
    }
    protected function get_header($ci, $header)
    {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }
    
    private  $state_domain=array('al','dz','af','ar','ae','aw','om','az','eg','et','ie','ee','ad','ao','ai','ag','at','au','mo','bb','pg','bs','pk','py','ps','bh','pa','br','by','bm','bg','mp','bj','be','is','pr','ba','pl','bo','bz','bw','bt','bf','bi','bv','kp','gq','dk','de','tl','tp','tg','dm','do','ru','ec','er','fr','fo','pf','gf','tf','va','ph','fj','fi','cv','fk','gm','cg','cd','co','cr','gg','gd','gl','ge','cu','gp','gu','gy','kz','ht','kr','nl','an','hm','hn','ki','dj','kg','gn','gw','ca','gh','ga','kh','cz','zw','cm','qa','ky','km','ci','kw','cc','hr','ke','ck','lv','ls','la','lb','lt','lr','ly','li','re','lu','rw','ro','mg','im','mv','mt','mw','my','ml','mk','mh','mq','yt','mu','mr','us','um','as','vi','mn','ms','bd','pe','fm','mm','md','ma','mc','mz','mx','nr','np','ni','ne','ng','nu','no','nf','na','za','aq','gs','eu','pw','pn','pt','jp','se','ch','sv','ws','yu','sl','sn','cy','sc','sa','cx','st','sh','kn','lc','sm','pm','vc','lk','sk','si','sj','sz','sd','sr','sb','so','tj','tw','th','tz','to','tc','tt','tn','tv','tr','tm','tk','wf','vu','gt','ve','bn','ug','ua','uy','uz','es','eh','gr','hk','sg','nc','nz','hu','sy','jm','am','ac','ye','iq','ir','il','it','in','id','uk','vg','io','jo','vn','zm','je','td','gi','cl','cf','cn','yr','top'); 
    private $top_domain=array('com','arpa','edu','gov','int','mil','net','org','biz','info','pro','name','museum','coop','aero','xxx','idv','me','mobi','wang','asia','travel','jobs');
    public function rootdomain($domain=null)
    {
        $domain = $domain ? $domain : $_SERVER['HTTP_HOST'];
        if(!preg_match("/^[\w\-\.]+$/i", $domain)){
            return false;
        }
        $a = explode('.', $domain);
        $count = count($a);
        if($count <= 2){
            $rootdomain = $domain;
        }else{
            $last=array_pop($a); 
            $last_1=array_pop($a); 
            if(in_array($last, $this->top_domain)){ 
                $rootdomain=$last_1.'.'.$last; 
            }else if (in_array($last, $this->state_domain)){ 
                $last_2=array_pop($a); 
                if(in_array($last_1, $this->top_domain)){ 
                    $rootdomain=$last_2.'.'.$last_1.'.'.$last; 
                }else{ 
                    $rootdomain=$last_1.'.'.$last; 
                } 
            }
        }
        return  $rootdomain;
    }

    public function callapi($api, $data = array())
    {
        static $gateway = 'http://api.wxzhu.com/';
        $data = json_encode($data);
        $params = array('APPKEY'=>__CFG::Authorize, 'data'=>$data);
        if($ret = $this->http($gateway.$api, $params)){
            if($jsondata = json_decode($ret, true)){
                if(!$jsondata['error']){
                    return $jsondata['data'];
                }
            }
        }
        return false;
    }

    function https_request($url,$data,$type)
    {
        if ($type == 'json') {//json $_POST=json_decode(file_get_contents('php://input'), TRUE);
            $headers = array("Content-type: application/json;charset=UTF-8", "Accept: application/json", "Cache-Control: no-cache", "Pragma: no-cache");
            $data = json_encode($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


    function apirequest($api,$data = array()){
        $str = "";
        foreach ($data as $k=>$v){
            $str.= $k.'&'.$v;
        }
        $data['data'] = json_encode($data);
        $data['sign'] = md5($str.API_KEY);
        $data['API'] = $api;
        $data['CLIENT_API'] = "MALL";

      //  return $this->post(API_URL,$data);
        //
        return  json_decode($this->post(API_URL,$data),true);

    }


     public function download ($filename, $showname='',$content='',$expire=180) {
        if(is_file($filename)) {
            $length = filesize($filename);
        }elseif(is_file($filename)) {
            $length = filesize($filename);
        }elseif($content != '') {
            $length = strlen($content);
        }else {
            throw_exception($filename.L('下载文件不存在！'));
        }
        if(empty($showname)) {
            $showname = $filename;
        }
        $showname = basename($showname);
        if(!empty($filename)) {
            $type = mime_content_type($filename);
        }else{
            $type	 =	 "application/octet-stream";
        }
        //发送Http Header信息 开始下载
        header("Pragma: public");
        header("Cache-control: max-age=".$expire);
        //header('Cache-Control: no-store, no-cache, must-revalidate');
        header("Expires: " . gmdate("D, d M Y H:i:s",time()+$expire) . "GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s",time()) . "GMT");
        header("Content-Disposition: attachment; filename=".$showname);
        header("Content-Length: ".$length);
        header("Content-type: ".$type);
        header('Content-Encoding: none');
        header("Content-Transfer-Encoding: binary" );
        if($content == '' ) {
            readfile($filename);
        }else {
            echo($content);
        }
        exit();
    }



}