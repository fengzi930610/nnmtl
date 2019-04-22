<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/3
 * Time: 11:09
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
return array (
    'photo_id' =>
        array (
            'field' => 'photo_id',
            'label' => 'photo_id',
            'pk' => true,
            'add' => true,
            'edit' => false,
            'html' => false,
            'empty' => true,
            'show' => false,
            'list' => true,
            'type' => 'int',
            'comment' => '',
            'default' => '',
            'SO' => false,
        ),
    'shop_id'=>
        array (
            'field' => 'shop_id',
            'label' => '商户ID',
            'pk' => false,
            'add' => true,
            'edit' => false,
            'html' => false,
            'empty' => false,
            'show' => false,
            'list' => true,
            'type' => 'int',
            'comment' => '',
            'default' => '',
            'SO' => false,
        ),
    'photo'=>
        array (
            'field' => 'photo',
            'label' => '照片',
            'pk' => false,
            'add' => true,
            'edit' => false,
            'html' => false,
            'empty' => false,
            'show' => false,
            'list' => true,
            'type' => 'text',
            'comment' => '',
            'default' => '',
            'SO' => false,
        ),
    'dateline' =>
        array (
            'field' => 'dateline',
            'label' => '时间',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => false,
            'show' => false,
            'list' => true,
            'type' => 'dateline',
            'comment' => '',
            'default' => '',
            'SO' => false,
        ),
);