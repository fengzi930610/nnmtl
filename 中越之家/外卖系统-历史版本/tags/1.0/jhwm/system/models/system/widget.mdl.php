<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: widget.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_System_Widget extends Model
{
	
	public function __construct(&$system)
	{
		parent::__construct($system);
		/*
		$this->smarty = K::M('system/frontend');
        if(!isset($this->smarty->tpl_vars)){
            $pager['res'] = __CFG::RES_URL;
            $pager['img'] = __CFG::IMG_URL;
            $this->smarty->assign('pager',$pager);
        }
        */
	}
	public function load($params, &$smarty)
	{		
		$params['id'] = str_replace('/','.',$params['id']);
		if(strpos($params['id'],'.')){
			$w = explode('.',$params['id']);
			$params['widget'] = $w[0];
			$params['method'] = $w[1];
		}else{
			$params['widget'] = $params['id'];
			$params['method'] = 'index';
		}
		if(!file_exists(__CFG::DIR.'plugins/widgets/'.$params['widget'].'/widget.php')){
			return false;
		}
        $data = $smarty->tpl_vars;
		//$this->smarty->clearAllAssign();
		$content = $this->fetch($params, $smarty);
		$smarty->tpl_vars = $data;
        return $content;
	}
	
	public function fetch($widget, &$smarty)
	{
		$file = __CFG::DIR."plugins/widgets/".$widget['widget'].'/widget.php';
		if(!$widget['method']){
			$widget['method'] = 'index';
		}
		$wdt = K::W($widget['widget']);
        $wdt->smarty = &$smarty;
		if(!method_exists($wdt,$widget['method'])){
            $widget['act'] = $widget['method'];
			$widget['method'] = 'index';
		}
		if(!$data = $wdt->$widget['method']($widget)){
			return '';
		}
		$smarty->assign('data', $data);
        $widget['GUID'] = K::GUID('widget');
		if(!$widget['tpl']){
			$widget['tpl'] = 'widget:/default/default.html';
		}
        $widget['HASH'] = md5($widget['tpl'].$widget['id']);
		$smarty->assign('widget',$widget);		
		if(strpos($widget['tpl'],':') === false && strpos($widget['tpl'], '/') === false){
			$tmpl = "widget:{$widget['widget']}/{$widget['tpl']}";
		}else{
			$tmpl = $widget['tpl'];
		}
		return $smarty->fetch($tmpl);
	}
}