<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/26
 * Time: 13:38
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

return array(
    'bills_id' =>
        array (
            'field' => 'bills_id',
            'label' => '对账单id',
            'pk' => true,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => true,
            'show' => true,
            'list' => true,
            'type' => 'int',
            'comment' => '',
            'default' => '',
            'SO' => '=',
        ),
    'amount'=>array(
        'field' => 'amount',
        'label' => '金额',
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
        'SO' => '',
    ),
    'jifen'=>array(
        'field' => 'jifen',
        'label' => '积分',
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
        'SO' => '',
    ),
    'bills_sn'=>array(
        'field' => 'bills_sn',
        'label' => '日期',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => true,
        'type' => 'int',
        'comment' => '',
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
        'list' => true,
        'type' => 'int',
        'comment' => '',
        'default' => '',
        'SO' => '',
    ),'fee'=>array(
    'field' => 'fee',
    'label' => '金额',
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
    'SO' => '',
),

   /* ',amount,jifen,bills_sn,dateline';*/
);