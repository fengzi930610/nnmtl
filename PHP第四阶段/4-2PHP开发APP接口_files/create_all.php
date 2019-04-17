<?php
/*$arr = array(
	"name" => 'echo',
	"age"  => 18,
	"sex"  => 'man'
	);

$str  = "中国";
$json = json_encode($arr);
echo $str;
$str  = iconv('utf-8', 'gbk', $str);
$js   = json_encode($str);
echo $js;*/
// echo  $json;


error_reporting (E_ALL & ~E_NOTICE);
/*定义一个json生成类*/
class Response{
	/**
	 * @param  integet
	 * @param  string
	 * @param  array
	 * @param  string
	 * @return string
	 */
	public static function encodeApi($status,$info,$data=array(),$format='json'){
		$format = strtolower($format);	//转为小写
		if($format != 'json' && $format != 'xml'){
			$format = 'json';  //设置一默认的格式
		}
		$return = "";	//访问数据
		if($format == "json"){
			$return = self::json($status,$info,$data);
		}elseif($format=="xml"){
			$return = self::xmlEncode($status,$info,$data);
		}
		return $return;
	}


	/*
	*按JSON方式输出通信数据
	@param integet  $status 状态码
	*@param  string $info 提示信息
	*@param array $data 数据
	*return string
	*/
	public static function json($status,$info='',$data=array()){
		if(!is_numeric($status)){
			return '';
		}
		$return = array(
			'status'  => $status,
			'message' => $info,
			'data'    => $data
			);
		return json_encode($return);
	}


	/*
	*按XML方式输出通信数据
	@param integet  $status 状态码
	*@param  string $info 提示信息
	*@param array $data 数据
	*return string
	*/
	public static function xmlEncode($status,$info='',$data=array()){
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<root>\n";
		$xml .= "<status>".$status."</status>\n";
		$xml .= "<info>".$info."</info>\n";
		$xml .= "<data>\n";
		if(is_array($data)){
			foreach ($data as $k=>$v) {
				if(is_array($v)){
					$xml .= "<".$k.">\n";
					$xml .= self::create_item($v);
					$xml .= "</".$k.">\n";
				}else{
					$xml .= "<".$k.">".$v."</".$k.">\n";
				}
			}
		}
		$xml .= "</data>\n";
		$xml .= "</root>\n";
		return $xml;
	}

	public static function create_item($arr){
		$xml = "";
		foreach ($arr as $key => $value) {
			if(is_array($value)){
				$xml .= "<".$key.">\n";
				$xml .= self::create_item($value);  //递归调用
				$xml .= "</".$key.">\n";
			}else{
				$xml .= "<{$key}>{$value}</{$key}>\n";
			}
		}
		return $xml;
	}
}



$arr = array(
	"name" => 'echo',
	"age"  => 18,
	"sex"  => 'man',
	"list" => array(
		'guojia'=>'中国',
		'lang'=>'语言'
		)
	);
//echo Response::json(200,'生成成功',$arr);  //输出JSON
//header("content-type:text/xml");
echo Response::encodeApi(200,'ok',$arr,'JSON');






