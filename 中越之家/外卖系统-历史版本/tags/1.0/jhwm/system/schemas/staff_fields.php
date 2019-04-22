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
  'staff_id' => 
  array (
    'field' => 'staff_id',
    'label' => '服务ID',
    'pk' => true,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => false,
    'type' => 'int',
    'comment' => '服务ID',
    'default' => '',
    'SO' => '=',
  ),
  'id_name' => 
  array (
    'field' => 'id_name',
    'label' => '实名',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '实名',
    'default' => '',
    'SO' => '=',
  ),
  'id_number' => 
  array (
    'field' => 'id_number',
    'label' => '身份证号',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '身份证号',
    'default' => '',
    'SO' => '=',
  ),
  'id_photo' => 
  array (
    'field' => 'id_photo',
    'label' => '身份证图',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '身份证图',
    'default' => '',
    'SO' => false,
  ),
  'verify_photo' => 
  array (
    'field' => 'verify_photo',
    'label' => '手持身份证',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '手持身份证',
    'default' => '',
    'SO' => false,
  ),
  'account_type' => 
  array (
    'field' => 'account_type',
    'label' => '账户类型',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '账户类型',
    'default' => '',
    'SO' => 'like',
  ),
  'account_name' => 
  array (
    'field' => 'account_name',
    'label' => '账户名',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '账户名',
    'default' => '',
    'SO' => false,
  ),
  'account_number' => 
  array (
    'field' => 'account_number',
    'label' => '账户号',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '账户号',
    'default' => '',
    'SO' => '=',
  ),
  'info' => 
  array (
    'field' => 'info',
    'label' => '介绍资料',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '介绍资料',
    'default' => '',
    'SO' => false,
  ),
);