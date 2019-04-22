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
    'order_id' =>
        array (
            'field' => 'order_id',
            'label' => 'order_id',
            'pk' => true,
            'add' => true,
            'edit' => false,
            'html' => true,
            'empty' => false,
            'show' => true,
            'list' => true,
            'type' => 'int',
            'comment' => 'order_id',
            'default' => '',
            'SO' => '=',
        ),
    'from' =>
    array(
        'field'   => 'from',
        'label'   => '创建者',
        'pk'      => false,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => false,
        'show'    => true,
        'list'    => true,
        'type'    => 'text',
        'comment' => '创建者：shop,member,admin',
        'default' => '',
        'SO'      => '=',
    ),
    'uid' =>
    array(
        'field'   => 'uid',
        'label'   => '用户UID',
        'pk'      => false,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => false,
        'show'    => true,
        'list'    => true,
        'type'    => 'int',
        'comment' => '用户UID',
        'default' => '',
        'SO'      => '=',
    ),
    'shop_id'        =>
    array(
        'field'   => 'shop_id',
        'label'   => '商户ID',
        'pk'      => false,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => true,
        'show'    => true,
        'list'    => true,
        'type'    => 'int',
        'comment' => '商户ID',
        'default' => '',
        'SO'      => '=',
    ),
    'reflect' =>
    array(
        'field'   => 'reflect',
        'label'   => '顾客反映说明',
        'pk'      => false,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => false,
        'show'    => true,
        'list'    => true,
        'type'    => 'text',
        'comment' => '顾客反映说明',
        'default' => '',
        'SO'      => '',
    ),
    'reply' =>
    array(
        'field'   => 'reply',
        'label'   => '回复内容',
        'pk'      => false,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => true,
        'show'    => true,
        'list'    => true,
        'type'    => 'text',
        'comment' => '回复内容',
        'default' => '',
        'SO'      => '',
    ),
    'reply_time' =>
    array (
        'field' => 'reply_time',
        'label' => '回复时间',
        'pk' => false,
        'add' => false,
        'edit' => false,
        'html' => false,
        'empty' => false,
        'show' => true,
        'list' => true,
        'type' => 'int',
        'comment' => '回复时间',
        'default' => '',
        'SO' => 'betweendate',
    ),
    'refund_price' =>
    array(
        'field'   => 'refund_price',
        'label'   => '退款金额',
        'pk'      => false,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => true,
        'show'    => true,
        'list'    => true,
        'type'    => 'number',
        'comment' => '退款金额',
        'default' => '',
        'SO'      => false,
    ),
    'status'   =>
    array(
        'field'   => 'status',
        'label'   => '退款状态',
        'pk'      => false,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => true,
        'show'    => true,
        'list'    => true,
        'type'    => 'int',
        'comment' => '退款状态 0：默认（申请） 1：已退款 2：已拒绝',
        'default' => '',
        'SO'      => '=',
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