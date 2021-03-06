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
  'bills_id' => 
  array (
    'field' => 'bills_id',
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
    'SO' => '=',
  ),
  'bills_sn' => 
  array (
    'field' => 'bills_sn',
    'label' => '帐单日期',
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
    'SO' => '=',
  ),
  'shop_id' => 
  array (
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
    'SO' => '=',
  ),
  'status' => 
  array (
    'field' => 'status',
    'label' => '入账状态',
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
    'SO' => '',
  ),
  'amount' => 
  array (
    'field' => 'amount',
    'label' => '金额',
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
    'SO' => '',
  ),
  'fee' => 
  array (
    'field' => 'fee',
    'label' => '平台服务费',
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
    'SO' => '',
  ),
  'dateline' => 
  array (
    'field' => '时间',
    'label' => 'dateline',
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
    'SO' => '',
  ),
    'freight'=>array(
        'field' => 'freight',
        'label' => '配送',
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
        'SO' => '',
    ),
    'user_amount'=>array(
        'field' => 'user_amount',
        'label' => '用户支付',
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
        'SO' => '',
    )
);