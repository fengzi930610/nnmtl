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
  'start_price' => 
  array (
    'label' => '起步价格',
    'field' => 'start_price',
    'type' => 'number',
    'default' => '8',
    'comment' => '',
    'html' => false,
    'empty' => false,
  ),
  'start_km' => 
  array (
    'label' => '起步里程',
    'field' => 'start_km',
    'type' => 'number',
    'default' => '5',
    'comment' => '',
    'html' => false,
    'empty' => false,
  ),
  'start_kg' => 
  array (
    'label' => '起步重量',
    'field' => 'start_kg',
    'type' => 'number',
    'default' => '1',
    'comment' => '',
    'html' => false,
    'empty' => false,
  ),
  'addkm_price' => 
  array (
    'label' => '每超过起步里程每KM的价格',
    'field' => 'addkm_price',
    'type' => 'number',
    'default' => '1',
    'comment' => '',
    'html' => false,
    'empty' => false,
  ),
  'addkg_price' => 
  array (
    'label' => '每超过起步重量每KG的价格',
    'field' => 'addkg_price',
    'type' => 'number',
    'default' => '1',
    'comment' => '',
    'html' => false,
    'empty' => false,
  ),
);