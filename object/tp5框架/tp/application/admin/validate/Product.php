<?php
namespace app\admin\validate;
use think\Validate;

class Product extends Validate{
	protected $rule=[
//		'name' => 'require',
//		'introduction' => 'require',
//		'categoryId' => 'notIn:0',
//		'brandId' => 'notIn:0',
//		'number' => 'require',
//		'market' => 'require',
//		'price' => 'require',
//		'num' => 'require|number',
//		'content' => 'require',
		
	];
	protected $message  =   [
	    'name.require' => '商品名称不能为空',
	    'introduction.require' => '商品简介不能为空',
	    'categoryId.notIn' => '请选择商品分类',
	    'categoryId.notIn' => '请选择商品品牌',
	    'number.require' => '商品货号不能为空',
	    'market.require' => '商品市场价格不能为空',
	    'price.require' => '商品销售价格不能为空',
	    'num.require' => '商品库存不能为空',
	    'num.number' => '商品库存必须为数字',
	    'content.require' => '商品详情不能为空',
   ];
	
}




?>