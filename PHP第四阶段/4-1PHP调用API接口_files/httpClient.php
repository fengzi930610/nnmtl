<?php
class httpClient
{   
    static public function request($str_apiurl,$arr_param=array(),$str_returnType='array',$str_requestType='get'){
        if(!$str_apiurl){
            exit('request url is empty 请求地址不正确');
        }
        if($str_requestType=='get'){
            return self::getRequest($str_apiurl,$arr_param,$str_returnType);    //get请求
        }elseif($str_requestType=='post'){
            return self::postRequest($str_apiurl,$arr_param,$str_returnType);   //post请求
        }
    }
    
    static public function getRequest($str_apiurl,$arr_param=array(),$str_returnType='array'){
        if(!$str_apiurl){
            exit('request url is empty 请求地址不正确');
        }

        //url拼装
        if(is_array($arr_param) && count($arr_param)>0){
            $tmp_param = http_build_query($arr_param);
            if(strpos($str_apiurl, '?') !== false){
                $str_apiurl .= "&".$tmp_param;
            }else{
                $str_apiurl .= "?" . $tmp_param;
            }
        }elseif (is_string($arr_param)){
            if(strpos($str_apiurl, '?') !== false){
                $str_apiurl .= "&".$arr_param;
            }else{
                $str_apiurl .= "?" . $arr_param;
            }
        }

        //请求头
        $this_header = array(
            "content-type: application/x-www-form-urlencoded; charset=UTF-8"
        );

        $ch = curl_init();  //初始curl
        curl_setopt($ch,CURLOPT_URL,$str_apiurl);   //需要获取的 URL 地址
        curl_setopt($ch,CURLOPT_HEADER,0);          //启用时会将头文件的信息作为数据流输出, 此处禁止输出头信息
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  //获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30); //连接超时时间
        curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);  //头信息
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip'); 
        $res = curl_exec($ch);                      //执行curl请求
        $response_code = curl_getinfo($ch);
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch)."<br>";
            //echo $res;
            var_dump($response_code);
        }

        //请求成功
        if($response_code['http_code'] == 200){
            if($str_returnType == 'array'){
                //echo $res;
                return json_decode($res,true);
            }else{
                return $res;
            }
        }else{
            $code = $response_code['http_code'];
            switch ($code) {
                case '404':
                    exit('请求的页面不存在');
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    }

    static public function postRequest($str_apiurl,$arr_param=array(),$str_returnType='array'){
        if(!$str_apiurl){
            exit('request url is empty 请求地址不正确');
        }


        $ch = curl_init();  //初始curl
        curl_setopt($ch,CURLOPT_URL,$str_apiurl);   //需要获取的 URL 地址
        curl_setopt($ch,CURLOPT_HEADER,0);          //启用时会将头文件的信息作为数据流输出, 此处禁止输出头信息
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  //获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30); //连接超时时间
        //curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);  //头信息
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip'); 
        curl_setopt($ch, CURLOPT_POST, 1);          //post请求
        //curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); //  PHP 5.6.0 后必须开启
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr_param);
        $res = curl_exec($ch);                      //执行curl请求
        $response_code = curl_getinfo($ch);

        //请求出错
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch)."<br>";
            //echo $res;
            var_dump($response_code);
        }

        //请求成功
        if($response_code['http_code'] == 200){
            if($str_returnType == 'array'){
                return json_decode($res,true);
            }else{
                return $res;
            }
        }else{
            $code = $response_code['http_code'];
            switch ($code) {
                case '404':
                    exit('请求的页面不存在');
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    }

}