<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/17
 * Time: 10:18
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Staff extends Ctl {

    public function __construct(&$system)
    {
        parent::__construct($system);
        $this->check_login();

    }

}