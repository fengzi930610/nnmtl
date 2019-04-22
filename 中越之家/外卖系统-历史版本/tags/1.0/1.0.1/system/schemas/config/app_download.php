<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * #fileid#
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

return array (
  'waimai_version' => 
  array (
    'label' => '客户端版本',
    'field' => 'waimai_version',
    'type' => 'text',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => false,
  ),
  'waimai_download' => 
  array (
    'label' => '客户端下载地址',
    'field' => 'waimai_download',
    'type' => 'text',
    'default' => '',
    'comment' => '客户端下载地址 http:// 开头',
    'html' => false,
    'empty' => false,
  ),
  'waimai_intro' => 
  array (
    'label' => '客户端版本更新说明',
    'field' => 'waimai_intro',
    'type' => 'textarea',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => false,
  ),
  'biz_version' => 
  array (
    'label' => '商户端版本',
    'field' => 'biz_version',
    'type' => 'text',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => false,
  ),
  'biz_download' => 
  array (
    'label' => '商户端下载地址',
    'field' => 'biz_download',
    'type' => 'text',
    'default' => '',
    'comment' => '商户端下载地址 http:// 开头',
    'html' => false,
    'empty' => false,
  ),
  'biz_intro' => 
  array (
    'label' => '商户端版本更新说明',
    'field' => 'biz_intro',
    'type' => 'textarea',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => false,
  ),
  'staff_version' => 
  array (
    'label' => '配送端版本',
    'field' => 'staff_version',
    'type' => 'text',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => false,
  ),
  'staff_download' => 
  array (
    'label' => '配送端下载地址',
    'field' => 'staff_download',
    'type' => 'text',
    'default' => '',
    'comment' => '配送端下载地址 http:// 开头',
    'html' => false,
    'empty' => false,
  ),
  'staff_intro' => 
  array (
    'label' => '配送端版本更新说明',
    'field' => 'staff_intro',
    'type' => 'textarea',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => false,
  ),
);