<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/13
 * Time: 10:59
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
return array (
    'cate_id' =>
        array (
            'field' => 'cate_id',
            'label' => 'ID',
            'pk' => true,
            'add' => true,
            'edit' => false,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'number',
            'comment' => '',
            'default' => '',
            'SO' => false,
        ),
    'title' =>
        array (
            'field' => 'title',
            'label' => 'title',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'text',
            'comment' => '',
            'default' => '',
            'SO' => false,
        ),
    'order_by' =>
        array (
            'field' => 'order_by',
            'label' => '排序',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => true,
            'show' => true,
            'list' => true,
            'type' => 'number',
            'comment' => '',
            'default' => '',
            'SO' => false,
        ),


);