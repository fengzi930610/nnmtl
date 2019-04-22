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
  'closed' => 
  array (
    'label' => '网站状态',
    'field' => 'closed',
    'type' => 'boolean',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => true,
  ),
  'closed_reason' => 
  array (
    'label' => '关闭原因',
    'field' => 'closedreason',
    'type' => 'textarea',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => true,
  )
);