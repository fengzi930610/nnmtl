<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 9:18
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Paotui extends Ctl {

    public function index(){
		$this->pagedata['TOKEN'] = $this->cookie->get("TOKEN");
        $this->tmpl = "paotui/index.html";
    }


}