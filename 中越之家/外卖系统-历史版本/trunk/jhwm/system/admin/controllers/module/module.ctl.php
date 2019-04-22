<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: module.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Module_Module extends Ctl
{
	
	public function index()
	{
		$this->pagedata['_OO_'] = 'admin:module/index.html';
		$this->output();
	}
	//添加控制模块
	public function create()
	{
	}
	public function save()
	{
		
	}
	public function remove()
	{
	}
}