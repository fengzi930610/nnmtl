<?php
namespace app\admin\validate;
use think\Validate;

class Category extends Validate{
	protected $rule=[
		'name' => 'require',
	];
	protected $message  =   [
	    'name.require' => '分类名称不能为空',
   ];
	
}




?>