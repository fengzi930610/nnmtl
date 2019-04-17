<?php
namespace app\admin\controller;
use app\admin\controller\Commond;
	
	class Index extends Commond{
		public function index(){
			$this->assign('title','后台首页');
			return $this->fetch();
		}
	}
	
?>