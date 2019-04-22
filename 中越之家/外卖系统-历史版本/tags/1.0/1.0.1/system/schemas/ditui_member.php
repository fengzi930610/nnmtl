<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

return array (
  'mid' => 
  array (
    'field' => 'mid',
    'label' => '人员ID',
    'pk' => true,
    'add' => false,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'int',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'ditui_id' => 
  array (
    'field' => 'ditui_id',
    'label' => '地推人员ID',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'int',
    'comment' => '地推人员ID',
    'default' => '',
    'SO' => '=',
  ),
  'uid' => 
  array (
    'field' => 'uid',
    'label' => '会员ID',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'int',
    'comment' => '会员ID',
    'default' => '',
    'SO' => false,
  ),
  'signup_amount' => 
  array (
    'field' => 'signup_amount',
    'label' => '注册奖励金额',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'number',
    'comment' => '注册奖励金额',
    'default' => '0.00',
    'SO' => false,
  ),
  'first_amount' => 
  array (
    'field' => 'first_amount',
    'label' => '首单奖励金额',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'number',
    'comment' => '首单奖励金额',
    'default' => '0.00',
    'SO' => false,
  ),
  'first_order_id' => 
  array (
    'field' => 'first_order_id',
    'label' => '首单订单号',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'int',
    'comment' => '首单订单号',
    'default' => '',
    'SO' => false,
  ),
  'first_order_amount' => 
  array (
    'field' => 'first_order_amount',
    'label' => '首单金额',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'number',
    'comment' => '首单金额（在线支付且订单完成时）',
    'default' => '0.00',
    'SO' => false,
  ),
  'first_order_time' => 
  array (
    'field' => 'first_order_time',
    'label' => '首单时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'unixtime',
    'comment' => '首单时间',
    'default' => '',
    'SO' => false,
  ),
  'clientip' => 
  array (
    'field' => 'clientip',
    'label' => '创建ip',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'clientip',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'dateline' => 
  array (
    'field' => 'dateline',
    'label' => '创建时间',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'dateline',
    'comment' => '绑定时间',
    'default' => '',
    'SO' => false,
  ),
    'day'=>array(
        'field' => 'day',
        'label' => '创建日期',
        'pk' => false,
        'add' => true,
        'edit' => false,
        'html' => false,
        'empty' => true,
        'show' => false,
        'list' => true,
        'type' => 'int',
        'comment' => '',
        'default' => '',
        'SO' => false,
    )
);