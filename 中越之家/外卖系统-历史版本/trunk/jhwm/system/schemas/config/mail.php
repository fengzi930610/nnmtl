<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: mail.php 9343 2015-03-24 07:07:00Z youyi $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

return array (
  'mode' => 
  array (
    'label' => '邮件发送模式',
    'field' => 'mode',
    'type' => 'radio',
    'default' => '',
    'comment' => '如果您选择了采用服务器内置的 Mail 服务，您不需要填写下面的内容。',
    'html' => false,
    'empty' => false,
  ),
  'smtp' => 
  array (
    'label' => 'SMTP 服务器',
    'field' => 'smtp',
    'type' => 'text',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => true,
  ),
  'port' => 
  array (
    'label' => 'SMTP 端口',
    'field' => 'port',
    'type' => 'text',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => true,
  ),
  'sender' => 
  array (
    'label' => '发件人地址',
    'field' => 'sender',
    'type' => 'text',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => false,
  ),
  'uname' => 
  array (
    'label' => 'SMTP 身份验证用户名',
    'field' => 'uname',
    'type' => 'text',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => true,
  ),
  'passwd' => 
  array (
    'label' => 'SMTP 身份验证密码 ',
    'field' => 'passwd',
    'type' => 'text',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => true,
  ),
    'email' => 
  array (
    'label' => '通知管理员的EMAIL ',
    'field' => 'email',
    'type' => 'text',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => true,
  ),
);