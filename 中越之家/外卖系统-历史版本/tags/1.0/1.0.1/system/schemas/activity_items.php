<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/13
 * Time: 11:02
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

return array (
    'item_id' =>
        array (
            'field' => 'item_id',
            'label' => 'ID',
            'pk' => true,
            'add' => true,
            'edit' => false,
            'html' => false,
            'empty' => true,
            'show' => true,
            'list' => true,
            'type' => 'number',
            'comment' => '',
            'default' => '',
            'SO' => false,
        ),
    'active_id' =>
        array (
            'field' => 'active_id',
            'label' => '活动id',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'number',
            'comment' => '',
            'default' => '',
            'SO' => false,
        ),
    'can_id' =>
        array (
            'field' => 'can_id',
            'label' => 'canID',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'number',
            'comment' => '',
            'default' => '',
            'SO' => false,
        ),
    'type'=>
       array(
           'field' => 'type',
           'label' => 'type',
           'pk' => false,
           'add' => true,
           'edit' => true,
           'html' => false,
           'empty' => false,
           'show' => true,
           'list' => true,
           'type' => 'number',
           'comment' => '',
           'default' => '',
           'SO' => false,

       ),
    'order_by'=>
        array(
            'field' => 'order_by',
            'label' => '排序',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'number',
            'comment' => '',
            'default' => '',
            'SO' => false,

        ),
    'dateline' =>
        array (
            'field' => 'dateline',
            'label' => '添加时间',
            'pk' => false,
            'add' => false,
            'edit' => false,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'dateline',
            'comment' => '',
            'default' => '',
            'SO' => false,
        ),
    'photo'=>
        array (
            'field' => 'photo',
            'label' => '图片',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => true,
            'show' => true,
            'list' => true,
            'type' => 'text',
            'comment' => '',
            'default' => '',
            'SO' => false,
        ),
    'title'=>array(
        'field' => 'title',
        'label' => '标题',
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

    )
    

);