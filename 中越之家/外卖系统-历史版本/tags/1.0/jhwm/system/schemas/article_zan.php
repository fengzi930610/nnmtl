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
    'zan_id' =>
        array (
            'field' => 'zan_id',
            'label' => 'ID',
            'pk' => true,
            'add' => false,
            'edit' => false,
            'html' => false,
            'empty' => false,
            'show' => false,
            'list' => true,
            'type' => 'number',
            'comment' => '',
            'default' => '',
            'SO' => '=',
        ),
    'article_id' =>
        array (
            'field' => 'article_id',
            'label' => '文章id',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => false,
            'show' => false,
            'list' => true,
            'type' => 'number',
            'comment' => '',
            'default' => '',
            'SO' => '',
        ),
    'uid' =>
        array (
            'field' => 'uid',
            'label' => '用户id',
            'pk' => false,
            'add' => true,
            'edit' => true,
            'html' => false,
            'empty' => false,
            'show' => false,
            'list' => true,
            'type' => 'number',
            'comment' => '',
            'default' => '',
            'SO' => '',
        ),
    'dateline' =>
        array (
            'field' => 'dateline',
            'label' => '添加时间',
            'pk' => false,
            'add' => false,
            'edit' => false,
            'html' => false,
            'empty' => false,
            'show' => false,
            'list' => true,
            'type' => 'dateline',
            'comment' => '',
            'default' => '',
            'SO' => 'betweendate',
        ),
);