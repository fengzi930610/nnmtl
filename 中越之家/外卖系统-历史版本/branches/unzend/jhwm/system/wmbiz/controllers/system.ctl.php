<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/24
 * Time: 18:52
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_System extends Ctl {

    public function index(){
        $this->tmpl = 'system/index.html';
        
    }
    public function index2(){
        $this->tmpl = 'system/index2.html';
    }


}