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
  'order_id' => 
  array (
    'field' => 'order_id',
    'label' => 'ID',
    'pk' => true,
    'add' => false,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => 'ID',
    'default' => '',
    'SO' => '=',
  ),
  'qiang_id' => 
  array (
    'field' => 'qiang_id',
    'label' => '抢购ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '抢购ID',
    'default' => '',
    'SO' => '=',
  ),
  'qiang_title' => 
  array (
    'field' => 'qiang_title',
    'label' => '标题',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '标题',
    'default' => '',
    'SO' => 'like',
  ),
  'qiang_price' => 
  array (
    'field' => 'qiang_price',
    'label' => '价格',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'number',
    'comment' => '价格',
    'default' => '0.00',
    'SO' => 'between',
  ),
  'qiang_discount_price' => 
  array (
    'field' => 'qiang_discount_price',
    'label' => '折后价',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'number',
    'comment' => '折后价',
    'default' => '0.00',
    'SO' => 'between',
  ),
  'qiang_freight' => 
  array (
    'field' => 'qiang_freight',
    'label' => '运费',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'number',
    'comment' => '运费',
    'default' => '0.00',
    'SO' => 'between',
  ),
  'qiang_number' => 
  array (
    'field' => 'qiang_number',
    'label' => '数量',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '数量',
    'default' => '',
    'SO' => 'between',
  ),
  'photo' => 
  array (
    'field' => 'photo',
    'label' => '图片',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '图片',
    'default' => '',
    'SO' => '',
  ),
  'number' => 
  array (
    'field' => 'number',
    'label' => '密码',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => false,
    'type' => 'text',
    'comment' => '密码',
    'default' => '',
    'SO' => false,
  ),
  'use_time' => 
  array (
    'field' => 'use_time',
    'label' => '使用时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '使用时间',
    'default' => '',
    'SO' => 'betweendate',
  ),
  'type' => 
  array (
    'field' => 'type',
    'label' => '类型',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '类型： goods:实物,ticket:电子券',
    'default' => '',
    'SO' => '',
  ), 
  'info' => 
  array (
    'field' => 'info',
    'label' => '商品详情',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'editor',
    'comment' => '商品详情',
    'default' => '',
    'SO' => false,
  ),
  'rules' => 
  array (
    'field' => 'rules',
    'label' => '规则',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'editor',
    'comment' => '规则',
    'default' => '',
    'SO' => false,
  ),
  'notes' => 
  array (
    'field' => 'notes',
    'label' => '商品备注信息',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => true,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '商品备注信息',
    'default' => '',
    'SO' => 'false',
  ), 
    'use_ltime'=>array(
        'field' => 'use_ltime',
        'label' => '消费截止时间',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => true,
        'type' => 'text',
        'comment' => '',
        'default' => '',
        'SO' => false,
    ), 
      'is_ticket' => 
      array (
        'field' => 'is_ticket',
        'label' => '是否有券',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => true,
        'type' => 'int',
        'comment' => '0:无,1:有',
        'default' => '0',
        'SO' => false,
      ),
      'bl'=>array(
        'field' => 'bl',
        'label' => '抽佣比例',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => true,
        'list' => true,
        'type' => 'int',
        'comment' => 'bl',
        'default' => '',
        'SO' => 'false',
    )
);