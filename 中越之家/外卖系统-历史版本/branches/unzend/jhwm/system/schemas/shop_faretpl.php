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
  'tpl_id' => 
  array (
    'field' => 'tpl_id',
    'label' => '运费模板ID',
    'pk' => true,
    'add' => true,
    'edit' => false,
    'html' => true,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '运费模板ID',
    'default' => '',
    'SO' => false,
  ),
  'shop_id' => 
  array (
    'field' => 'shop_id',
    'label' => '商户ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => true,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '商户ID',
    'default' => '',
    'SO' => false,
  ),
  'title' => 
  array (
    'field' => 'title',
    'label' => '标题',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => true,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '标题',
    'default' => '',
    'SO' => false,
  ),
  'lasttime' => 
  array (
    'field' => 'lasttime',
    'label' => '最后编辑时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => true,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'unixtime',
    'comment' => '最后编辑时间',
    'default' => '',
    'SO' => false,
  ),
    'orderby' => 
  array (
    'field' => 'orderby',
    'label' => '排序',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '排序',
    'default' => '50',
    'SO' => '=',
  ),
  'closed' => 
  array (
    'field' => 'closed',
    'label' => '删除标识',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => true,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '删除标识',
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
    'html' => true,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'dateline',
    'comment' => '创建时间',
    'default' => '',
    'SO' => false,
  ),
);