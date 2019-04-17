<?php
namespace app\admin\validate;
use think\Validate;

class Brand extends Validate{
	protected $rule=[
		'name' => 'require|max:18',
		'description' => 'require',
	];
	protected $message  =   [
	    'name.require' => '品牌名称不能为空',
	    'name.max'     => '品牌名称最多不能超过18个字符',	
	    'description.require' => '品牌描述不能为空',
   ];
	
}
?>