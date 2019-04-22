<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @xiayufeng
 * $Id: ylyun.mdl.php 10216 2016-05-04 12:44:44
 */
Import::L('yilianyun/YLYTokenClient.php');
Import::L('yilianyun/YLYOpenApiClient.php');
class Mdl_Printer_Ylyun extends Model
{
    protected $url = "http://open.10ss.net:8888";
    public function __construct()
    {

    }
	/**
	 * 生成签名sign
	 * @param  array $params 参数
	 * @param  string $apiKey API密钥
	 * @param  string $msign 打印机密钥
	 * @return   string sign
	 */

	/* 打印接口
	* @param  int $partner     用户ID
	* @param  string $machine_code 打印机终端号
	* @param  string $content      打印内容
	* @param  string $apiKey       API密钥
	* @param  string $msign       打印机密钥
	*/
    /*public function send_print($partner,$apiKey,$machine_code,$mKey,$content)
    {
    	$p = '';
    	$params = $data = array();
    	$params['time'] = __TIME;
    	$params['partner'] = $partner;
    	$params['machine_code'] = $machine_code;
		$sign = $this->generateSign($params,$apiKey,$mKey);
		$params['sign'] = $sign;
		$params['content'] = $content;
		foreach ($params as $k => $v) {
		    $p .= $k.'='.$v.'&';
		}
		$data = rtrim($p, '&');

		return $this->liansuo_post($this->url,$data);
    }*/
    public function generateSign($params, $apiKey, $msign)
	{
	    //所有请求参数按照字母先后顺序排
	    ksort($params);
	    //定义字符串开始所包括的字符串
	    $stringToBeSigned = $apiKey;
	    //把所有参数名和参数值串在一起
	    foreach ($params as $k => $v)
	    {
	        $stringToBeSigned .= urldecode($k.$v);
	    }
	    unset($k, $v);
	    //定义字符串结尾所包括的字符串
	    $stringToBeSigned .= $msign;
	    //使用MD5进行加密，再转化成大写
	    return strtoupper(md5($stringToBeSigned));
	}
	function liansuo_post($url,$data)
	{   // 模拟提交数据函数
	    $curl = curl_init(); // 启动一个CURL会话
	    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检测
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
	    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览
	    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:')); //解决数据包大不能提交
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
	    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
	    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
	    curl_setopt($curl, CURLOPT_COOKIEFILE, $GLOBALS['cookie_file']); // 读取上面所储存的Cookie信息
	    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循
	    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

	    $tmpInfo = curl_exec($curl); // 执行操作
	    if (curl_errno($curl)) {
	       echo 'Errno'.curl_error($curl);
	    }
	    curl_close($curl); // 关键CURL会话
	    return $tmpInfo; // 返回数据
	}

	public function getAccessToken (){
		$token = new YLYTokenClient();
		if(!$access_token_data = K::M('cache/cache')->get('yilian.api.component_access_token')){
			//获取token;
			$grantType = 'client_credentials';  //自有模式(client_credentials) || 开放模式(authorization_code)
			$scope = 'all';                     //权限
			$timesTamp = time();                //当前服务器时间戳(10位)
			$getAccessToken = $token->GetToken($grantType,$scope,$timesTamp);
			if($data = json_decode($getAccessToken,true)){
				if($data['error']==0){
					$access_token = $data['body']['access_token'];
					$expire_time = __TIME+$data['body']['expires_in'];
					$reflash_token = $data['body']['refresh_token'];
					K::M('cache/cache')->set('yilian.api.component_access_token',array(
						'access_token'=>$access_token,
						'expire_time'=>$expire_time,
						'reflash_token'=>$reflash_token
					));
					return $access_token;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			$expire_time = $access_token_data['expire_time'];

			if($expire_time>__TIME){
				return $access_token_data['access_token'];
			}else{
				//刷新token;
				$grantType = 'refresh_token';       //自有模式或开放模式一致
				$scope = 'all';                     //权限
				$timesTamp = time();                //当前服务器时间戳(10位)
				$RefreshToken = $access_token_data['reflash_token'];                 //刷新token的密钥
				$reflash_tokeen_data =  $token->RefreshToken($grantType,$scope,$timesTamp,$RefreshToken);
				if($data = json_decode($reflash_tokeen_data,true)){
					if($data['error']==0){
						$access_token = $data['body']['access_token'];
						$expire_time = __TIME+$data['body']['expires_in'];
						$reflash_token = $data['body']['refresh_token'];
						K::M('cache/cache')->set('yilian.api.component_access_token',array(
							'access_token'=>$access_token,
							'expire_time'=>$expire_time,
							'reflash_token'=>$reflash_token
						));
						return $access_token;

					}else{
						return false;
					}
				}else{
					return false;
				}
				//{"error":"0","error_description":"success","body":{"access_token":"6ddb66568a094ee794ea4da0cb62703a","refresh_token":"3d2930abc68d46bc802182ed9d8d1752","machine_code":null,"expires_in":2592000,"scope":"all"}}
			}
		}
	}

	public function addAndOpenPrint($machineCode,$msign){
		if($access_token =  $this->getAccessToken()){
			if($addprint = YLYOpenApiClient::addPrint($machineCode, $msign, $access_token, __TIME)){
				if($data = json_decode($addprint,true)){
					if($data['error']==0){
						if($data_open = YLYOpenApiClient::btnPrint($machineCode,$access_token,'btnopen',__TIME)){
							if($arr_data_open = json_decode($data_open,true)){
								if($arr_data_open['error']==0){
								   	return true;
								}else{
									return false;
								}
							}else{
								return false;
							}
						}else{
							return false;
						}
					}else{
					 	return false;
					}
				}else{
				  	return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function send_print($machineCode, $content, $originId){
		if($accessToken = $this->getAccessToken()){
			if($json_data = YLYOpenApiClient::printIndex($machineCode,$accessToken,$content,$originId,__TIME)){
				if($data = json_decode($json_data,true)){
					return $data;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function cancelAll($machion_code){
		if($accessToken = $this->getAccessToken()){
			if($data = YLYOpenApiClient::cancelAll($machion_code,$accessToken,__TIME)){
				if($arr_data =  json_decode($data,true)){
					if($arr_data['error']==0){
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}else{
              return false;
			}
		}else{
			return false;
		}
	}

	/* 设置接单/拒单
	* @param  string $machine_code   打印机终端号
	* @param  string $responseType   接单:open, 拒单:close
	*/
	public function getOrder($machion_code, $responseType='open'){
		if($accessToken = $this->getAccessToken()){
			if($data = YLYOpenApiClient::getOrder($machion_code, $accessToken, $responseType, __TIME)){
				if($arr_data =  json_decode($data,true)){
					if($arr_data['error']==0){
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}else{
              return false;
			}
		}else{
			return false;
		}
	}

}