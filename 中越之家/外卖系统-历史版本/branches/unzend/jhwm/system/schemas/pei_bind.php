<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/27
 * Time: 14:09
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
return array(
    'bind_id'=>array(
        'field' => 'bind_id',
        'label' => 'ID',
        'pk' => true,
        'add' => false,
        'edit' => false,
        'html' => false,
        'empty' => false,
        'show' => true,
        'list' => true,
        'type' => 'int',
        'comment' => '',
        'default' => '',
        'SO' =>  "",
    ),
    'group_id'=>array(
        'field' => 'group_id',
        'label' => '配送站ID',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => false,
        'show' => true,
        'list' => true,
        'type' => 'int',
        'comment' => '',
        'default' => '',
        'SO' =>  "",
    ),
    'shop_id'=>array(
        'field' => 'shop_id',
        'label' => '商家ID',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => false,
        'show' => true,
        'list' => true,
        'type' => 'int',
        'comment' => '',
        'default' => '',
        'SO' => "",
    ),
    'addr'=>array(
        'field' => 'addr',
        'label' => '地址',
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
        'SO' =>  "",
    ),
    'lng'=>
        array(
            'field'   => 'lng',
            'label'   => '经度',
            'pk'      => false,
            'add'     => true,
            'edit'    => true,
            'html'    => false,
            'empty'   => false,
            'show'    => true,
            'list'    => true,
            'type'    => 'number',
            'comment' => '',
            'default' => '',
            'SO'      => '',
        ),
    'lat' =>
        array(
            'field'   => 'lat',
            'label'   => '维度',
            'pk'      => false,
            'add'     => true,
            'edit'    => true,
            'html'    => false,
            'empty'   => false,
            'show'    => true,
            'list'    => true,
            'type'    => 'number',
            'comment' => '',
            'default' => '',
            'SO'      => '',
        ),
    'dateline' =>
        array(
            'field'   => 'dateline',
            'label'   => '创建时间',
            'pk'      => false,
            'add'     => true,
            'edit'    => true,
            'html'    => false,
            'empty'   => true,
            'show'    => true,
            'list'    => true,
            'type'    => 'dateline',
            'comment' => '',
            'default' => '',
            'SO'      => '',
        ),
    'title'=>array(
        'field' => 'title',
        'label' => '商户名称',
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
        'SO' =>  "",
    ),
    'contact'=>array(
        'field' => 'contact',
        'label' => '联系人',
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
        'SO' =>  "",
    ),
    'mobile'=>array(
        'field' => 'mobile',
        'label' => '手机号码',
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
        'SO' =>  "",
    ),



);