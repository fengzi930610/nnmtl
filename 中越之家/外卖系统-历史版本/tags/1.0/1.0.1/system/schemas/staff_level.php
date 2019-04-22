<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/13
 * Time: 10:35
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
return array (
    'level_id' =>
        array (
            'field' => 'level_id',
            'label' => '配送等级ID',
            'pk' => true,
            'add' => true,
            'edit' => false,
            'html' => false,
            'empty' => true,
            'show' => true,
            'list' => false,
            'type' => 'int',
            'comment' => '配送等级ID',
            'default' => '',
            'SO' => '',
        ),
    'title'=>array(
        'field' => 'title',
        'label' => '等级标题',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => false,
        'type' => 'text',
        'comment' => '等级标题',
        'default' => '',
        'SO' => '',
    ),
    'config_waimai'=>array(
        'field' => 'config_waimai',
        'label' => '外卖配置',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => false,
        'type' => 'text',
        'comment' => '外卖配置',
        'default' => '',
        'SO' => '',
    ),
    'config_paotui'=>array(
        'field' => 'config_paotui',
        'label' => '跑腿配置',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => false,
        'type' => 'text',
        'comment' => '跑腿配置',
        'default' => '',
        'SO' => '',
    ),
    'dateline'=>array(
        'field' => 'dateline',
        'label' => '创建时间',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => false,
        'type' => 'dateline',
        'comment' => '创建时间',
        'default' => '',
        'SO' => '',
    ),


);