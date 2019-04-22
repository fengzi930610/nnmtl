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
  'voice_id' => 
  array (
    'field' => 'voice_id',
    'label' => '语音ID',
    'pk' => true,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => false,
    'type' => 'int',
    'comment' => '语音ID',
    'default' => '',
    'SO' => false,
  ),
  'order_id' => 
  array (
    'field' => 'order_id',
    'label' => '订单ID',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => false,
    'type' => 'int',
    'comment' => '订单ID',
    'default' => '',
    'SO' => '=',
  ),
  'voice' => 
  array (
    'field' => 'voice',
    'label' => '语音文件',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => false,
    'type' => 'text',
    'comment' => '语音文件',
    'default' => '',
    'SO' => false,
  ),
  'voice_time' => 
  array (
    'field' => 'voice_time',
    'label' => '语音时间',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => false,
    'type' => 'int',
    'comment' => '语音时间(长度:秒)',
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
    'empty' => false,
    'show' => false,
    'list' => false,
    'type' => 'dateline',
    'comment' => '创建时间',
    'default' => '',
    'SO' => 'betweendate',
  ),
);