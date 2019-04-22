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
    'empty' => true,
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
    'label' => '真实姓名',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '真实姓名',
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
    'empty' => true,
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
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '身份证图',
    'default' => '',
    'SO' => false,
  ),
  'verify' => 
  array (
    'field' => 'verify',
    'label' => '身份认证状态',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '身份认证状态 0:待审核，1:通过认证, 2:认证被拒绝',
    'default' => '',
    'SO' => '=',
  ),
  'verify_time' => 
  array (
    'field' => 'verify_time',
    'label' => '认证时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'dateline',
    'comment' => '认证时间',
    'default' => '',
    'SO' => 'betweendate',
  ),
  'refuse' => 
  array (
    'field' => 'refuse',
    'label' => '拒绝原因',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '拒绝原因',
    'default' => '',
    'SO' => false,
  ),
  'updatetime' => 
  array (
    'field' => 'updatetime',
    'label' => '申请时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'dateline',
    'comment' => '申请时间，重新申请时间会更新',
    'default' => '',
    'SO' => 'betweendate',
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
    'show' => true,
    'list' => false,
    'type' => 'dateline',
    'comment' => '创建时间',
    'default' => '',
    'SO' => 'betweendate',
  ),
);