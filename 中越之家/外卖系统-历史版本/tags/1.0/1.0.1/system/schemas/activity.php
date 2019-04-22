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

return array (
    'active_id' =>
        array (
            'field' => 'active_id',
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
    'banner1' =>
        array (
            'field' => 'banner1',
            'label' => '轮播图1',
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
    'banner2' =>
        array (
            'field' => 'banner2',
            'label' => '轮播图2',
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
    'banner3' =>
        array (
            'field' => 'banner3',
            'label' => '轮播图3',
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
    'title' =>
        array (
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
        ),
    'intro'=>
        array (
            'field' => 'intro',
            'label' => '介绍',
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
    'stime' =>
        array (
            'field' => 'stime',
            'label' => '上架时间',
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
    'ltime' =>
        array (
            'field' => 'ltime',
            'label' => '下架时间',
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
    'cate_id'=>
        array (
            'field' => 'cate_id',
            'label' => '活动分类ID',
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
    

);
