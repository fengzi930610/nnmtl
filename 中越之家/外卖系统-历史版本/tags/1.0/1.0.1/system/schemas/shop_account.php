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
  'shop_id' => 
  array (
    'field' => 'shop_id',
    'label' => '商家',
    'pk' => true,
    'add' => true,
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
  'account_type' => 
  array (
    'field' => 'account_type',
    'label' => '帐户类型',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '帐户类型，如支付宝、工商银行等',
    'default' => '',
    'SO' => false,
  ),
  'account_name' => 
  array (
    'field' => 'account_name',
    'label' => '开户人',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '开户人',
    'default' => '',
    'SO' => false,
  ),
  'account_number' => 
  array (
    'field' => 'account_number',
    'label' => '帐号',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '帐号',
    'default' => '',
    'SO' => false,
  ),
);