<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: menu.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Module_Menu extends Ctl
{
	
	public function index()
	{
		$this->pagedata['tree'] = K::M('module/view')->tree();
		$this->tmpl = 'admin:module/menu/index.html';	
	}
	public function create($pid=null)
	{
		if($PID = intval($pid)){
			$pager['PID'] = $PID;
			$this->pagedata['pager'] = $pager;
		}
		$this->pagedata['tree'] = K::M('module/view')->tree();
		$this->tmpl = 'admin:module/menu/create.html';
	}
	
	public function edit($pid)
	{
		if(!$ID = intval($pid)){
			$this->msgbox->add('未指定要修改的菜单',201);
		}else if(!$menu= K::M('module/view')->module($ID)){
			$this->msgbox->add('要修改的菜单不存在',202);
		}else if($menu['module'] == 'module'){
			$this->msgbox->add('要修改的菜单不存在',203);
		}else{
			$this->pagedata['tree'] = K::M('module/view')->tree();
			$this->pagedata['menu'] = $menu;
			$this->tmpl = 'admin:module/menu/create.html';
		}
	}
	public function save()
	{
		if(!$data = $this->GP('data')){
			$this->msgbox->add('非法的数据提交', 201);
		}else if($ID = $this->GP('ID')){
			if(!$menu = K::M('module/view')->module($ID)){
				$this->msgbox->add('要修改的菜单不存在', 202);
			}else if($menu['module'] == 'module'){
				$this->msgbox->add('修改的不是导航菜单', 203);
			}else if(K::M('module/menu')->update($ID, $data)){
				$this->msgbox->add('修改导航菜单成功');
			}
		}else if($ID = K::M('module/menu')->create($data)){
			$this->msgbox->add('添加导航菜单成功');
		}
	}
	public function update()
	{
		if('orderby' == $this->GP('batch')){
			if($orderbys = $this->GP('orderby')){
				$oMenu = K::M('module/menu');
				foreach($orderbys as $k=>$v){
					if(($k = intval($k)) && ($v = intval($v))){
						$oMenu->update($k, array('orderby'=>$v));
					}
				}
				$this->msgbox->add('修改菜单排序成功');
			}
		}
	}
	public function remove($mid)
	{
		if(($ID = intval($mid)) || ($ID = $this->GP('IDS'))){
			if(K::M('module/menu')->remove($ID)){
				$this->msgbox->add('删除导航菜单成功');
			}
		}else {
			$this->msgbox->add('非法请求!',201);
		}		
	}
}