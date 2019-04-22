<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/12
 * Time: 14:48
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
return array (
    'log_id' =>
        array (
            'field' => 'log_id',
            'label' => 'log_id',
            'pk' => true,
            'add' => true,
            'edit' => false,
            'html' => false,
            'empty' => true,
            'show' => true,
            'list' => true,
            'type' => 'int',
            'comment' => 'log_id',
            'default' => '',
            'SO' => '=',
        ),
    'order_id' =>
        array (
            'field' => 'order_id',
            'label' => '订单ID',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'int',
            'comment' => '订单ID',
            'default' => '0',
            'SO' => '=',
        ),
    'from' =>
        array (
            'field' => 'from',
            'label' => '订单来源',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'text',
            'comment' => '订单来源',
            'default' => '',
            'SO' => '',
        ),
    'log' =>
        array (
            'field' => 'log',
            'label' => '消息',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'text',
            'comment' => '消息',
            'default' => '',
            'SO' => '',
        ),
    'type' =>
        array (
            'field' => 'type',
            'label' => '运费',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => true,
            'show' => true,
            'list' => true,
            'type' => 'int',
            'comment' => '状态',
            'default' => '0',
            'SO' => '',
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