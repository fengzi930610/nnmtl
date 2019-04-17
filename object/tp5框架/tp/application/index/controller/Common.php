<?php
namespace app\index\controller;//命名空间

use think\Controller;//引用
class Common extends Controller{
	
	public function __construct(){
		parent::__construct();
		$navlist = db('nav')->select();
		$this->assign('navlist',$navlist);
		
		if(!empty(input('nav'))){
			$nav = input('nav');
			$this->assign('nav',$nav);
		}
		
		$categorylist = db('category')->select();
		$this->assign('categorylist',$categorylist);
	}
	
}

?>