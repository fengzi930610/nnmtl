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
echo Response::json(200,'生成成功',$arr);  //输出JSON







