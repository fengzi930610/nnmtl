<?php
namespace app\admin\validate;
use think\Validate;

class Role extends Validate{
	protected $rule=[
		'name' => 'require|max:18',
		'Id' => 'require',
	];
	protected $message  =   [
	    'name.require' => '角色名称不能为空',
	    'name.max'     => '角色名称最多不能超过18个字符',	
	    'Id.require' => '请选择角色权限',
   ];
	
}
?>