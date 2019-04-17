<?php
namespace app\admin\validate;
use think\Validate;

class Level extends Validate{
	protected $rule=[
		'name' => 'require',
		'model' => 'require|alphaDash',
		'controller' => 'require|alphaDash',
		'state' => 'require',
		
	];
	protected $message  =   [
	    'name.require' => '权限名称不能为空',
	    'model.require' => '模块名称不能为空',
	    'controller.require' => '控制器名称不能为空',
	    'state.require' => '显示状态不能为空',
   ];
	
}




?>