<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: modifier.qq.php 9343 2015-03-24 07:07:00Z youyi $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

function smarty_modifier_qq($qqs, $limit=10, $title='')
{
	if(empty($qqs)){
		return false;
	}
	/*
	static $site = null;
	if(empty($title)){
		if($site === null){
			$site = K::$system->config->get('site');
		}
		$title = $site['title'];
	}
	*/
	if(!is_array($qqs)){
		$qqs = explode(',', $qqs);
	}
	$content = '';
	$index = 0;
	$host = $_SERVER['HTTP_HOST'];
	$res = __CFG::RES_URL;
	foreach((array)$qqs as $qq){
		$index ++;
		if($index > $limit){
			break;
		}
		$title = $title ? $title : '点击咨询';
		$content .= '<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin='.$qq.'&site=qq&menu=yes" title="'.$title.'"><img border="0" src="'.$res.'/images/icon/qq.png" style="vertical-align:middle"/></a>';
	}
	return $content;
}