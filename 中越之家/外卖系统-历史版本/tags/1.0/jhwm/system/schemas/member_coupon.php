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
  'cid' => 
  array (
    'field' => 'cid',
    'label' => 'cid',
    'pk' => true,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'coupon_id' => 
  array (
    'field' => 'coupon_id',
    'label' => 'coupon_id',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'number',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'uid' => 
  array (
    'field' => 'uid',
    'label' => 'uid',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'use_time' => 
  array (
    'field' => 'use_time',
    'label' => 'use_time',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '使用时间',
    'default' => '',
    'SO' => false,
  ),
  'order_id' => 
  array (
    'field' => 'order_id',
    'label' => 'order_id',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '使用时的订单ID',
    'default' => '',
    'SO' => false,
  ),
  'status' => 
  array (
    'field' => 'status',
    'label' => 'status',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '使用状态 0：未使用 1：已使用',
    'default' => '',
    'SO' => false,
  ),
  'dateline' => 
  array (
    'field' => 'dateline',
    'label' => 'dateline',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '优惠券领取时间',
    'default' => '',
    'SO' => false,
  ),
  'ltime' => 
  array (
    'field' => 'ltime',
    'label' => 'ltime',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '过期时间冗余，优惠券一旦有用户领取，商家不应该再可以修改优惠券的过期时间。',
    'default' => '',
    'SO' => false,
  ),
  'order_amount' => 
  array (
    'field' => 'order_amount',
    'label' => 'order_amount',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '订单最小金额, 满足金额才能使用',
    'default' => '0.00',
    'SO' => false,
  ),
  'coupon_amount' => 
  array (
    'field' => 'coupon_amount',
    'label' => 'coupon_amount',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '券面值',
    'default' => '0.00',
    'SO' => false,
  ),
  'shop_id' => 
  array (
    'field' => 'shop_id',
    'label' => 'shop_id',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
);