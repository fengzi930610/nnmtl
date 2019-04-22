<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/13
 * Time: 10:53
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

return array(
    'huodong_id' =>
    array(
        'field'   => 'huodong_id',
        'label'   => 'ID',
        'pk'      => true,
        'add'     => true,
        'edit'    => false,
        'html'    => false,
        'empty'   => true,
        'show'    => true,
        'list'    => true,
        'type'    => 'int',
        'comment' => '',
        'default' => '',
        'SO'      => false,
    ),
    'banner'     =>
    array(
        'field'   => 'banner',
        'label'   => 'banner图',
        'pk'      => false,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => true,
        'show'    => true,
        'list'    => true,
        'type'    => 'text',
        'comment' => '',
        'default' => '',
        'SO'      => false,
    ),
    'title' =>
    array(
        'field'   => 'title',
        'label'   => '标题',
        'pk'      => false,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => false,
        'show'    => true,
        'list'    => true,
        'type'    => 'text',
        'comment' => '',
        'default' => '',
        'SO'      => false,
    ),
    'tmpl'  =>
    array(
        'field'   => 'tmpl',
        'label'   => '模板',
        'pk'      => false,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => false,
        'show'    => true,
        'list'    => true,
        'type'    => 'text',
        'comment' => '',
        'default' => '',
        'SO'      => false,
    ),
    'stime' =>
    array(
        'field'   => 'stime',
        'label'   => '开始时间',
        'pk'      => false,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => false,
        'show'    => true,
        'list'    => true,
        'type'    => 'int',
        'comment' => '',
        'default' => '',
        'SO'      => false,
    ),
    'ltime' =>
    array(
        'field'   => 'ltime',
        'label'   => '结束时间',
        'pk'      => false,
        'add'     => true,
        'edit'    => true,
        'html'    => false,
        'empty'   => false,
        'show'    => true,
        'list'    => true,
        'type'    => 'int',
        'comment' => '',
        'default' => '',
        'SO'      => false,
    ),
    'dateline' =>
    array(
        'field'   => 'dateline',
        'label'   => '添加时间',
        'pk'      => false,
        'add'     => false,
        'edit'    => false,
        'html'    => false,
        'empty'   => false,
        'show'    => true,
        'list'    => true,
        'type'    => 'dateline',
        'comment' => '',
        'default' => '',
        'SO'      => false,
    ),
    'clientip' =>
    array(
        'field'   => 'clientip',
        'label'   => 'clientip',
        'pk'      => false,
        'add'     => false,
        'edit'    => false,
        'html'    => false,
        'empty'   => false,
        'show'    => false,
        'list'    => true,
        'type'    => 'clientip',
        'comment' => '',
        'default' => '',
        'SO'      => false,
    ),
);
