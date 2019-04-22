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
  'msg_id' => 
  array (
    'field' => 'msg_id',
    'label' => '消息ID',
    'pk' => true,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'int',
    'comment' => '消息ID',
    'default' => '',
    'SO' => '=',
  ),
  'staff_id' => 
  array (
    'field' => 'staff_id',
    'label' => '服务员ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => false,
    'type' => 'int',
    'comment' => '会员UID',
    'default' => '',
    'SO' => '=',
  ),
  'title' => 
  array (
    'field' => 'title',
    'label' => '消息标题',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '消息标题',
    'default' => '',
    'SO' => false,
  ),
  'content' => 
  array (
    'field' => 'content',
    'label' => '消息内容',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '消息内容',
    'default' => '',
    'SO' => false,
  ),
  'is_read' => 
  array (
    'field' => 'is_read',
    'label' => '消息状态',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'int',
    'comment' => '0:新消息,1:已读,2:所有',
    'default' => '',
    'SO' => '=',
  ),
  'clientip' => 
  array (
    'field' => 'clientip',
    'label' => '创建IP',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '客户IP',
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
    'show' => true,
    'list' => false,
    'type' => 'text',
    'comment' => '创建时间',
    'default' => '',
    'SO' => 'betweendate',
  ),
);