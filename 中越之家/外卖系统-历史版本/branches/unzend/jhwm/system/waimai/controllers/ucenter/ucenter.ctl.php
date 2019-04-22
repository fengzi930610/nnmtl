<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 9:26
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Ucenter extends Ctl {
    //外卖用户中心 用于其他控制器继承
    public function __construct(&$system)
    {
        parent::__construct($system);
        $this->check_login();
    }

   

}