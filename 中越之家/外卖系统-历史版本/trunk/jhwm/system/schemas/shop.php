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
  'shop_id' =>
  array (
    'field' => 'shop_id',
    'label' => '商家ID',
    'pk' => true,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'int',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
    'city_id' =>
  array (
    'field' => 'city_id',
    'label' => '城市ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'int',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'contact' =>
  array (
    'field' => 'contact',
    'label' => '联系人',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '联系人',
    'default' => '',
    'SO' => false,
  ),
  'mobile' =>
  array (
    'field' => 'mobile',
    'label' => '手机号',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'mobile',
    'comment' => '手机号，登录用',
    'default' => '',
    'SO' => '=',
  ),
   'business_time' =>
  array (
    'field' => 'business_time',
    'label' => '营业时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '营业时间',
    'default' => '',
    'SO' => false,
  ),
  'phone' =>
  array (
    'field' => 'phone',
    'label' => '联系电话',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'phone',
    'comment' => '联系电话',
    'default' => '',
    'SO' => false,
  ),
  'title' =>
  array (
    'field' => 'title',
    'label' => '商家名称',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => 'like',
  ),
  'passwd' =>
  array (
    'field' => 'passwd',
    'label' => '密码',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'money' =>
  array (
    'field' => 'money',
    'label' => '余额',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'number',
    'comment' => '余额',
    'default' => '0.00',
    'SO' => false,
  ),
  'total_money' =>
  array (
      'field' => 'total_money',
      'label' => '总收入',
      'pk' => false,
      'add' => true,
      'edit' => true,
      'html' => false,
      'empty' => true,
      'show' => false,
      'list' => true,
      'type' => 'number',
      'comment' => '总收入',
      'default' => '0.00',
      'SO' => false,
    ), 
  'tixian_money' =>
  array (
      'field' => 'tixian_money',
      'label' => '提现金额',
      'pk' => false,
      'add' => true,
      'edit' => true,
      'html' => false,
      'empty' => true,
      'show' => false,
      'list' => true,
      'type' => 'number',
      'comment' => '提现金额',
      'default' => '0.00',
      'SO' => false,
    ),  
  'tixian_percent' =>
  array (
    'field' => 'tixian_percent',
    'label' => '商户提现比例',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'int',
    'comment' => '商户提现比例',
    'default' => '',
    'SO' => false,
  ),
  'have_waimai' =>
  array (
    'field' => 'have_waimai',
    'label' => '外卖商超功能',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '外卖商超功能',
    'default' => '',
    'SO' => false,
  ),
  'have_tuan' =>
  array (
    'field' => 'have_tuan',
    'label' => '团购功能',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '团购功能',
    'default' => '',
    'SO' => false,
  ),
  'have_quan' =>
  array (
    'field' => 'have_quan',
    'label' => '代金券功能',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '代金券功能',
    'default' => '',
    'SO' => false,
  ),
  'have_maidan' =>
  array (
    'field' => 'have_maidan',
    'label' => '优惠买单功能',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '优惠买单功能',
    'default' => '',
    'SO' => false,
  ),
    'have_dingzuo' =>
        array (
            'field' => 'have_dingzuo',
            'label' => '订座功能',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => true,
            'show' => true,
            'list' => true,
            'type' => 'int',
            'comment' => '订座功能',
            'default' => '',
            'SO' => false,
        ),
    'have_paidui' =>
        array (
            'field' => 'have_paidui',
            'label' => '排队功能',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => true,
            'show' => true,
            'list' => true,
            'type' => 'int',
            'comment' => '排队功能',
            'default' => '',
            'SO' => false,
        ),
    'have_diancan' =>
        array (
            'field' => 'have_diancan',
            'label' => '点餐功能',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => true,
            'show' => true,
            'list' => true,
            'type' => 'int',
            'comment' => '点餐功能',
            'default' => '',
            'SO' => false,
        ),
    'have_weidian' =>
  array (
    'field' => 'have_weidian',
    'label' => '微店功能',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '微店功能',
    'default' => '',
    'SO' => false,
  ),
     'have_fenxiao' =>
  array (
    'field' => 'have_fenxiao',
    'label' => '微店分销',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '微店分销',
    'default' => '',
    'SO' => false,
  ),
     'have_fenxiao' =>
  array (
    'field' => 'fenxiao_type',
    'label' => '分销类型',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '分销类型',
    'default' => '',
    'SO' => false,
  ),
  'audit' =>
  array (
    'field' => 'audit',
    'label' => '审核',
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
  'clientip' =>
  array (
    'field' => 'clientip',
    'label' => '创建ip',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'clientip',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'dateline' =>
  array (
    'field' => 'dateline',
    'label' => '创建时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'dateline',
    'comment' => '',
    'default' => '',
    'SO' => 'betweendate',
  ),
  'business_id' =>
  array (
    'field' => 'business_id',
    'label' => '商户所属的商圈ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '商户所属的商圈ID',
    'default' => '',
    'SO' => false,
  ),
  'area_id' =>
  array (
    'field' => 'area_id',
    'label' => '区县ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '区县ID',
    'default' => '',
    'SO' => false,
  ),
  'avg_amount' =>
  array (
    'field' => 'avg_amount',
    'label' => '人均消费',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '人均消费',
    'default' => '',
    'SO' => false,
  ),
'comments' =>
  array (
    'field' => 'comments',
    'label' => '总评论数',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '总评论数',
    'default' => '',
    'SO' => false,
  ),
  'lat' => 
  array (
    'field' => 'lat',
    'label' => '经度',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'number',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'lng' => 
  array (
    'field' => 'lng',
    'label' => '纬度',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => false,
    'type' => 'number',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),  
    'banner' =>
  array (
    'field' => 'banner',
    'label' => 'banner',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'photo',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'logo' =>
  array (
    'field' => 'logo',
    'label' => 'logo',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'photo',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
    'intro' =>
  array (
    'field' => 'intro',
    'label' => '店铺简介',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => '',
  ),
     'info' =>
  array (
    'field' => 'info',
    'label' => '店铺公告',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => '',
  ),
  'orderby' =>
  array (
    'field' => 'orderby',
    'label' => 'orderby',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'int',
    'comment' => '排序',
    'default' => '',
    'SO' => false,
  ),
  'verify_name' =>
  array (
    'field' => 'verify_name',
    'label' => 'verify_name',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'int',
    'comment' => '是否认证',
    'default' => '',
    'SO' => false,
  ),
    'is_wifi' =>
        array (
            'field' => 'is_wifi',
            'label' => 'is_wifi',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => true,
            'show' => false,
            'list' => true,
            'type' => 'int',
            'comment' => '是否有wifi',
            'default' => '',
            'SO' => false,
        ),
    'is_cart' =>
        array (
            'field' => 'is_cart',
            'label' => 'is_cart',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => true,
            'show' => false,
            'list' => true,
            'type' => 'int',
            'comment' => '是否有停车位',
            'default' => '',
            'SO' => false,
        ),
    'refuse'=> array (
        'field' => 'refuse',
        'label' => '拒绝原因',
        'pk' => false,
        'add' => true,
        'edit' => true,
        'html' => false,
        'empty' => true,
        'show' => false,
        'list' => true,
        'type' => 'text',
        'comment' => '拒绝原因',
        'default' => '',
        'SO' => false,
    ),
);
