<?php
namespace app\wechat\controller;
use think\Controller;

class Commom extends Controller{
	private $token = 'weixin';
	protected $fromUsername;
	protected $toUsername;
	protected $keyword;
	protected $msgType;
	protected $event;
	protected $ctime;
	public function __construct(){
		
		parent::__construct();
		$echoStr = input('echostr');
		if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
		
	}
	protected function getattr(){
		$postStr = file_get_contents("php://input");
		//保存消息到数据库
		db('xml')->insert(['xml'=>$postStr]);
		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->fromUsername = $postObj->FromUserName;
        $this->toUsername = $postObj->ToUserName;
		$this->msgType = $postObj->MsgType;
        $this->keyword = empty(trim($postObj->Content))?'':trim($postObj->Content);
		$this->event = empty(trim($postObj->Event))?'':trim($postObj->Event);
		$this->ctime = $postObj->CreateTime;
	}
	
	private function checkSignature(){
	
        $signature = input('signature');
        $timestamp = input('timestamp');
        $nonce = input('nonce');	
        		
		$token = $this->token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	
	//模拟get请求
	public function getRequst($str_api,$arr_param=array(),$str_returnType='array'){
		if(!$str_api){
			exit('request url is empty 请求地址不正确');
		}
		//url拼接
		if(is_array($arr_param) && count($arr_param)>0){
			$tmp_param = http_build_query($arr_param);
			if(strpos($str_api, '?') !== false){
				$str_api .= "&".$tmp_param;
			}else{
				$str_api .= "?".$tmp_param;
			}
		}else if(is_string($arr_param)){
			if(strpos($str_api, '?') !== false){
				$str_api .= "&".$arr_param;
			}else{
				$str_api .= "?".$arr_param;
			}
		}
		
		//请求头
		$this_header = array("content-type:application/x-www-form-urlencoded;charset=utf-8");
		
		$ch = curl_init();//初始curl
		curl_setopt($ch, CURLOPT_URL, $str_api);  //需要获取的URL地址
		curl_setopt($ch, CURLOPT_HEADER, 0);  //启动时会将头文件的信息作为数据流输出，此处禁止头信息输出
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //获取的信息以字符串返回，而不是直接输出
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);  //连接超时时间
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this_header);  //头信息
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		$res = curl_exec($ch);  //执行curl请求
		$response_code = curl_getinfo($ch);
		//请求出错
		if(curl_error($ch)){
			exit('curl error:'.curl_error($ch))
		}
		//请求成功
		if($response_code['http_code'] == 200){
			if($str_returnType == 'array'){
				//成功则存储数据
				$arrtoken = ['type'=>'get','acess_token'=>$res];
				db('token')->insert($arrtoken);
				
				return json_decode($res,true);
			}else{
				return $res;
			}
		}else{
			$code = $response_code['http_code'];
			switch ($code) {
				case '404':
					exit("请求页面不存在");
					break;
				
				default:
					
					break;
			}
		}
	}

	//模拟post请求
	public function postRequst($str_api,$post_data,$str_returnType='array'){
		if(!$str_api){
			exit('request url is empty 请求地址不正确');
		}
		
		
		
		$ch = curl_init();//初始curl
		curl_setopt($ch, CURLOPT_URL, $str_api);  //需要获取的URL地址
		curl_setopt($ch, CURLOPT_HEADER, 0);  //启动时会将头文件的信息作为数据流输出，此处禁止头信息输出
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //获取的信息以字符串返回，而不是直接输出
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);  //连接超时时间
//		curl_setopt($ch, CURLOPT_HTTPHEADER, $this_header);  //头信息
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		curl_setopt($ch, CURLOPT_POST, 1);  //POST请求
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		
		$res = curl_exec($ch);  //执行curl请求
		$response_code = curl_getinfo($ch);
		//请求出错
		if(curl_error($ch)){
			echo 'curl error:'.curl_error($ch)."</br>";
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
					exit("请求页面不存在");
					break;
				
				default:
					
					break;
			}
		}
	}
	
}
	
?>