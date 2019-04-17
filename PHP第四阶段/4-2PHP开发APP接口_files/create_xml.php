<?php
error_reporting (E_ALL & ~E_NOTICE);
/*定义一个json生成类*/
class Response{
	/*
	*按XML方式输出通信数据
	@param integet  $status 状态码
	*@param  string $info 提示信息
	*@param array $data 数据
	*return string
	*/
	public static function xmlEncode($status,$info='',$data=array()){
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";		//头文件
		$xml .= "<root>\n";											//根节点
		$xml .= "<status>".$status."</status>\n";					//状态码
		$xml .= "<info>".$info."</info>\n";							//提示消息
		//开始拼装数据节点
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

	private static function create_item($arr){
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
header("content-type:text/xml");         //以XML格式输出
echo Response::xmlEncode(200,'ok',$arr);






