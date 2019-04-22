<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/26
 * Time: 16:01
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

return array (
    'photo_id' =>
        array (
            'field' => 'photo_id',
            'label' => '图片ID',
            'pk' => true,
            'add' => false,
            'edit' => false,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'int',
            'comment' => '图片ID',
            'default' => '',
            'SO' => false,
        ),
    'complaint_id' =>
        array (
            'field' => 'complaint_id',
            'label' => '评价ID',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'int',
            'comment' => '评价ID',
            'default' => '',
            'SO' => '=',
        ),
    'photo' =>
        array (
            'field' => 'photo',
            'label' => '图片',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'photo',
            'comment' => '图片',
            'default' => '',
            'SO' => false,
        ),
    'dateline' =>
        array (
            'field' => 'dateline',
            'label' => '创建时间',
            'pk' => false,
            'add' => false,
            'edit' => false,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'dateline',
            'comment' => '创建时间',
            'default' => '',
            'SO' => 'betweendate',
        ),
);