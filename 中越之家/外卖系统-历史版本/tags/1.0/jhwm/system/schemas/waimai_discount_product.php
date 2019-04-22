<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/13
 * Time: 10:53
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

return array(
    'product_id'    =>
    array(
        'field'   => 'product_id',
        'label'   => '商品ID',
        'pk'      => true,
        'add'     => true,
        'edit'    => false,
        'html'    => false,
        'empty'   => false,
        'show'    => true,
        'list'    => true,
        'type'    => 'int',
        'comment' => '',
        'default' => '',
        'SO'      => false,
    ),
    'huodong_id' =>
    array(
        'field'   => 'huodong_id',
        'label'   => '活动ID',
        'pk'      => true,
        'add'     => true,
        'edit'    => false,
        'html'    => false,
        'empty'   => true,
        'show'    => true,
        'list'    => true,
        'type'    => 'int',
        'comment' => '',
        'default' => '',
        'SO'      => false,
    ),
    'discount_value'=>
    array(
        'field'   => 'discount_value',
        'label'   => '折扣值',
        'pk'      => true,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => true,
        'show'    => true,
        'list'    => true,
        'type'    => 'int',
        'comment' => '',
        'default' => '',
        'SO'      => false,
    ),
    'sale_sku'    =>
    array(
        'field'   => 'sale_sku',
        'label'   => '库存',
        'pk'      => true,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => true,
        'show'    => true,
        'list'    => true,
        'type'    => 'int',
        'comment' => '',
        'default' => '',
        'SO'      => false,
    ),
    'sale_count'    =>
    array(
        'field'   => 'sale_count',
        'label'   => '已售',
        'pk'      => true,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => true,
        'show'    => true,
        'list'    => true,
        'type'    => 'int',
        'comment' => '',
        'default' => '0',
        'SO'      => false,
    ),
);
