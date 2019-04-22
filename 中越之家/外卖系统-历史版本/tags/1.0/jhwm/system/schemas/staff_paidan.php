<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/14
 * Time: 10:05
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
//,pai,accept,refuse,day,dateline
return array(
    'paidan_id' =>
        array (
            'field' => 'paidan_id',
            'label' => '派单id',
            'pk' => true,
            'add' => true,
            'edit' => false,
            'html' => false,
            'empty' => true,
            'show' => true,
            'list' => false,
            'type' => 'int',
            'comment' => '',
            'default' => '',
            'SO' => '=',
        ),
    'staff_id'=>array(
            'field' => 'staff_id',
            'label' => '配送员id',
            'pk' => false,
            'add' => true,
            'edit' => false,
            'html' => false,
            'empty' => false,
            'show' => true,
            'list' => false,
            'type' => 'int',
            'comment' => '',
            'default' => '',
            'SO' => '',
    )
    ,'pai'=>array(
        'field' => 'pai',
        'label' => '派单数量',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => false,
        'type' => 'int',
        'comment' => '',
        'default' => '',
        'SO' => '',
    )
   ,'accept'=>array(
        'field' => 'accept',
        'label' => '接受数量',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => false,
        'type' => 'int',
        'comment' => '',
        'default' => '',
        'SO' => '',
    ),'refuse'=>array(
        'field' => 'refuse',
        'label' => '拒绝数量',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => false,
        'type' => 'int',
        'comment' => '',
        'default' => '',
        'SO' => '',
    ),
    'day'=>array(
        'field' => 'day',
        'label' => '日期',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => false,
        'type' => 'int',
        'comment' => '',
        'default' => '',
        'SO' => '',
    ),
    'dateline'=>array(
        'field' => 'day',
        'label' => '日期戳',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => false,
        'type' => 'dateline',
        'comment' => '',
        'default' => '',
        'SO' => '',
    ),

);