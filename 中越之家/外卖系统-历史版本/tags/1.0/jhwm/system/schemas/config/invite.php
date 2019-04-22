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
  'invite_reg_money' => 
  array (
    'label' => '注册奖励',
    'field' => 'invite_money',
    'type' => 'number',
    'default' => '10',
    'comment' => '用户首单后奖励',
    'html' => false,
    'empty' => true,
  ),  
  'invite_order_money' => 
  array (
    'label' => '首单奖励',
    'field' => 'invite_money',
    'type' => 'number',
    'default' => '10',
    'comment' => '用户首单后奖励',
    'html' => false,
    'empty' => true,
  ),
  'hongbao_amount' => 
  array (
    'label' => '红包金额',
    'field' => 'hongbao_amount',
    'type' => 'number',
    'default' => '10',
    'comment' => '奖励被邀请的好友的红包',
    'html' => false,
    'empty' => true,
  ),
  'hongbao_min_amount' => 
  array (
    'label' => '最低消费',
    'field' => 'hongbao_min_amount',
    'type' => 'number',
    'default' => '20',
    'comment' => '',
    'html' => false,
    'empty' => true,
  ),
  'share_photo' => 
  array (
    'label' => '分享图片',
    'field' => 'share_photo',
    'type' => 'photo',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => true,
  ),
  'share_title' => 
  array (
    'label' => '分享标题',
    'field' => 'share_title',
    'type' => 'text',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => true,
  ),
  'intro' => 
  array (
    'label' => '活动描述',
    'field' => 'intro',
    'type' => 'text',
    'default' => '',
    'comment' => '',
    'html' => false,
    'empty' => true,
  ),



);